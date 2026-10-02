<?php

namespace App\Filament\Resources\SiteSettings;

use App\Filament\Resources\SiteSettings\Pages\ManageSiteSettings;
use App\Models\SiteSetting;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class SiteSettingResource extends Resource
{
    protected static ?string $model = SiteSetting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $navigationLabel = 'Site content';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->required()->maxLength(100),
                TextInput::make('professional_title')->required()->maxLength(150),
                TextInput::make('availability')->maxLength(100),
                TextInput::make('hero_heading')->required()->maxLength(150)->columnSpanFull(),
                TextInput::make('hero_accent')->required()->maxLength(100)->columnSpanFull(),
                Textarea::make('hero_intro')->required()->rows(4)->maxLength(700)->columnSpanFull(),
                TextInput::make('about_heading')->required()->maxLength(150)->columnSpanFull(),
                FileUpload::make('profile_photo')
                    ->label('About page portrait')
                    ->image()
                    ->imageEditor()
                    ->disk('public')
                    ->directory('profile')
                    ->visibility('public')
                    ->columnSpanFull(),
                Textarea::make('about_body')->required()->rows(12)->maxLength(5000)->columnSpanFull(),
                TextInput::make('email')->email()->required(),
                TextInput::make('linkedin_url')->url(),
                TextInput::make('instagram_url')->url(),
                TextInput::make('behance_url')->url(),
                TextInput::make('github_url')->url(),
                TextInput::make('upwork_url')->url(),
                TextInput::make('contact_heading')->required()->maxLength(150)->columnSpanFull(),
                Textarea::make('contact_body')->required()->rows(4)->maxLength(700)->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('professional_title'),
                TextColumn::make('updated_at')->dateTime()->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }

    public static function canCreate(): bool
    {
        return ! SiteSetting::query()->exists();
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageSiteSettings::route('/'),
        ];
    }
}
