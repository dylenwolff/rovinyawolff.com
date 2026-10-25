<?php

namespace App\Filament\Resources\Activities;

use App\Filament\Resources\Activities\Pages\ManageActivities;
use App\Models\Activity;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class ActivityResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected static string|UnitEnum|null $navigationGroup = 'Client outreach';

    protected static ?string $recordTitleAttribute = 'subject';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('type')->options(['email' => 'Email', 'call' => 'Call', 'meeting' => 'Meeting', 'note' => 'Note'])->required(),
            TextInput::make('subject')->required(),
            Select::make('contact_id')->relationship('contact', 'first_name')->searchable()->preload(),
            Select::make('lead_id')->relationship('lead', 'title')->searchable()->preload(),
            Select::make('opportunity_id')->relationship('opportunity', 'name')->searchable()->preload(),
            DateTimePicker::make('occurred_at')->default(now()),
            Textarea::make('details')->rows(7)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('type')->badge(), TextColumn::make('subject')->searchable(),
            TextColumn::make('contact.full_name')->label('Contact'), TextColumn::make('occurred_at')->dateTime()->sortable(),
        ])->recordActions([EditAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageActivities::route('/')];
    }
}
