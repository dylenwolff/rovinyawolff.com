<?php

namespace App\Filament\Resources\Awards;

use App\Filament\Resources\Awards\Pages\CreateAward;
use App\Filament\Resources\Awards\Pages\EditAward;
use App\Filament\Resources\Awards\Pages\ListAwards;
use App\Models\Award;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AwardResource extends Resource
{
    protected static ?string $model = Award::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTrophy;
    protected static ?string $navigationLabel = 'Awards & recognition';
    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')->required()->maxLength(180),
            TextInput::make('placement')->placeholder('Winner, 1st Runner-Up, Finalist')->maxLength(100),
            TextInput::make('issuer')->required()->maxLength(180),
            DatePicker::make('awarded_at')->label('Award date'),
            TextInput::make('recognition_for')->label('Recognised person or project')->maxLength(180),
            Textarea::make('description')->rows(4)->maxLength(800)->columnSpanFull(),
            FileUpload::make('image_path')->label('Certificate or event photograph')->helperText('The uploaded image is resized and converted to AVIF automatically.')->image()->imageEditor()->disk('public')->directory('awards')->visibility('public')->columnSpanFull(),
            TextInput::make('proof_url')->label('Proof or announcement URL')->url()->columnSpanFull(),
            TextInput::make('sort_order')->numeric()->default(0)->required(),
            Toggle::make('is_featured')->label('Show on homepage')->default(false),
            Toggle::make('is_published')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_path')->disk('public')->label('Image'),
                TextColumn::make('title')->searchable()->sortable(),
                TextColumn::make('placement'),
                TextColumn::make('issuer')->toggleable(),
                TextColumn::make('awarded_at')->date()->sortable(),
                IconColumn::make('is_featured')->boolean()->label('Featured'),
                IconColumn::make('is_published')->boolean()->label('Published'),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAwards::route('/'),
            'create' => CreateAward::route('/create'),
            'edit' => EditAward::route('/{record}/edit'),
        ];
    }
}
