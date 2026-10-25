<?php

namespace App\Filament\Resources\Organizations;

use App\Filament\Resources\Organizations\Pages\ManageOrganizations;
use App\Models\Organization;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class OrganizationResource extends Resource
{
    protected static ?string $model = Organization::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static string|UnitEnum|null $navigationGroup = 'Client outreach';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(255), TextInput::make('industry')->maxLength(255),
            TextInput::make('country')->maxLength(100)->datalist([
                'Australia', 'Singapore', 'Sri Lanka', 'United Arab Emirates', 'United Kingdom', 'United States',
            ]),
            TextInput::make('website')->url(), TextInput::make('email')->email(), TextInput::make('phone')->tel(),
            Select::make('status')->options(['prospect' => 'Prospect', 'active' => 'Active client', 'past' => 'Past client', 'do_not_contact' => 'Do not contact'])->required(),
            Textarea::make('notes')->rows(5)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->searchable()->sortable(), TextColumn::make('industry')->searchable(),
            TextColumn::make('country')->searchable()->sortable()->badge(),
            TextColumn::make('status')->badge()->sortable(), TextColumn::make('email')->searchable(),
            TextColumn::make('updated_at')->dateTime()->sortable(),
        ])->filters([
            SelectFilter::make('country')->options(fn (): array => Organization::query()
                ->whereNotNull('country')
                ->distinct()
                ->orderBy('country')
                ->pluck('country', 'country')
                ->all()),
        ])->recordActions([EditAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageOrganizations::route('/')];
    }
}
