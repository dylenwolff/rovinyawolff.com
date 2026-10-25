<?php

namespace App\Filament\Resources\Contacts;

use App\Filament\Resources\Contacts\Pages\ManageContacts;
use App\Models\Contact;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class ContactResource extends Resource
{
    protected static ?string $model = Contact::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static string|UnitEnum|null $navigationGroup = 'Client outreach';

    protected static ?string $recordTitleAttribute = 'first_name';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('organization_id')->relationship('organization', 'name')->searchable()->preload(),
            TextInput::make('first_name')->required(), TextInput::make('last_name'), TextInput::make('email')->email(),
            TextInput::make('phone')->tel(), TextInput::make('job_title'),
            Select::make('status')->options(['prospect' => 'Prospect', 'contacted' => 'Contacted', 'active' => 'Active', 'do_not_contact' => 'Do not contact'])->required(),
            Textarea::make('notes')->rows(5)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('full_name')->label('Name')->searchable(['first_name', 'last_name']), TextColumn::make('organization.name')->searchable(),
            TextColumn::make('job_title'), TextColumn::make('email')->searchable(), TextColumn::make('status')->badge()->sortable(),
        ])->recordActions([EditAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageContacts::route('/')];
    }
}
