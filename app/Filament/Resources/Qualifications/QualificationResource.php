<?php

namespace App\Filament\Resources\Qualifications;

use App\Filament\Resources\Qualifications\Pages\CreateQualification;
use App\Filament\Resources\Qualifications\Pages\EditQualification;
use App\Filament\Resources\Qualifications\Pages\ListQualifications;
use App\Models\Qualification;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class QualificationResource extends Resource
{
    protected static ?string $model = Qualification::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    protected static ?string $navigationLabel = 'Qualifications';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')->required()->maxLength(180),
            TextInput::make('institution')->required()->maxLength(180),
            TextInput::make('credential_type')->label('Type or level')->maxLength(100),
            TextInput::make('start_year')->numeric()->minValue(1950)->maxValue(2100),
            TextInput::make('end_year')->numeric()->minValue(1950)->maxValue(2100),
            Textarea::make('description')->rows(4)->maxLength(800)->columnSpanFull(),
            TextInput::make('credential_url')->label('Credential or institution URL')->url()->columnSpanFull(),
            TextInput::make('sort_order')->numeric()->default(0)->required(),
            Toggle::make('is_published')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->searchable()->sortable(),
                TextColumn::make('institution')->searchable()->toggleable(),
                TextColumn::make('credential_type')->label('Type'),
                TextColumn::make('end_year')->label('Year')->sortable(),
                IconColumn::make('is_published')->boolean()->label('Published'),
            ])
            ->defaultSort('sort_order')
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListQualifications::route('/'),
            'create' => CreateQualification::route('/create'),
            'edit' => EditQualification::route('/{record}/edit'),
        ];
    }
}
