<?php

namespace App\Filament\Resources\Projects;

use App\Filament\Resources\Projects\Pages\CreateProject;
use App\Filament\Resources\Projects\Pages\EditProject;
use App\Filament\Resources\Projects\Pages\ListProjects;
use App\Models\Project;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Toggle;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBriefcase;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')->required()->maxLength(120),
                TextInput::make('slug')->helperText('Leave blank to generate it from the title.')->unique(ignoreRecord: true)->maxLength(160),
                Select::make('type')->options(['publication' => 'Publication / flipbook', 'poster' => 'Poster or campaign', 'social' => 'Social media design', 'branding' => 'Brand identity', 'other' => 'Other'])->default('publication')->required(),
                TextInput::make('category')->helperText('Example: Club Directory, Annual Report, Social Campaign')->required()->maxLength(80),
                Textarea::make('summary')->required()->rows(4)->maxLength(600)->columnSpanFull(),
                TextInput::make('url')->url()->maxLength(255)->columnSpanFull(),
                FileUpload::make('image_path')
                    ->label('Cover image')
                    ->helperText('Uploaded images are resized and converted to AVIF automatically.')
                    ->disk('public')
                    ->directory('projects/covers')
                    ->visibility('public')
                    ->image()
                    ->imageEditor()
                    ->imageResizeMode('contain')
                    ->imageResizeTargetWidth('1800')
                    ->maxSize(5120)
                    ->columnSpanFull(),
                FileUpload::make('pdf_path')
                    ->label('Publication PDF')
                    ->helperText('Upload a PDF to display it as an interactive flipbook.')
                    ->disk('public')
                    ->directory('projects/publications')
                    ->visibility('public')
                    ->acceptedFileTypes(['application/pdf'])
                    ->maxSize(102400)
                    ->columnSpanFull(),
                FileUpload::make('gallery')
                    ->label('Design gallery')
                    ->helperText('Uploaded images are resized and converted to AVIF automatically.')
                    ->disk('public')
                    ->directory('projects/gallery')
                    ->visibility('public')
                    ->image()
                    ->multiple()
                    ->reorderable()
                    ->appendFiles()
                    ->imageEditor()
                    ->imageResizeMode('contain')
                    ->imageResizeTargetWidth('1800')
                    ->maxFiles(12)
                    ->maxSize(5120)
                    ->columnSpanFull(),
                TextInput::make('client')->maxLength(120),
                TextInput::make('organization')->maxLength(120),
                TextInput::make('status')->maxLength(120),
                TextInput::make('published_at')->type('date')->label('Project date'),
                Textarea::make('challenge')->rows(5)->maxLength(1500)->columnSpanFull(),
                Textarea::make('solution')->rows(7)->maxLength(2500)->columnSpanFull(),
                Textarea::make('responsibilities')->rows(5)->maxLength(2000)->columnSpanFull(),
                TagsInput::make('technologies')->label('Tools and skills')->columnSpanFull(),
                Select::make('accent')->options(['plum' => 'Plum', 'coral' => 'Coral', 'sage' => 'Sage', 'gold' => 'Gold'])->default('plum')->required(),
                TextInput::make('sort_order')->numeric()->default(0)->required(),
                Toggle::make('is_featured')->label('Featured case study')->default(false),
                Toggle::make('is_downloadable')->label('Allow PDF download')->default(false),
                Toggle::make('is_published')->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->searchable()->sortable(),
                TextColumn::make('category')->searchable(),
                TextColumn::make('type')->badge(),
                IconColumn::make('is_featured')->label('Featured')->boolean(),
                TextColumn::make('sort_order')->label('Order')->sortable(),
                IconColumn::make('is_published')->label('Published')->boolean(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProjects::route('/'),
            'create' => CreateProject::route('/create'),
            'edit' => EditProject::route('/{record}/edit'),
        ];
    }
}
