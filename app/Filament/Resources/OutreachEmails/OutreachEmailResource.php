<?php

namespace App\Filament\Resources\OutreachEmails;

use App\Filament\Resources\OutreachEmails\Pages\ManageOutreachEmails;
use App\Models\OutreachEmail;
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

class OutreachEmailResource extends Resource
{
    protected static ?string $model = OutreachEmail::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPaperAirplane;

    protected static string|UnitEnum|null $navigationGroup = 'Client outreach';

    protected static ?string $navigationLabel = 'Email queue';

    protected static ?string $recordTitleAttribute = 'subject';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('organization_id')->relationship('organization', 'name')->searchable()->preload(), Select::make('contact_id')->relationship('contact', 'first_name')->searchable()->preload(),
            Select::make('lead_id')->relationship('lead', 'title')->searchable()->preload(), TextInput::make('recipient_name'), TextInput::make('recipient_email')->email()->required(),
            TextInput::make('subject')->required()->columnSpanFull(), Textarea::make('body')->required()->rows(14)->columnSpanFull(),
            Select::make('status')->options(['draft' => 'Draft', 'ready' => 'Ready for review', 'approved' => 'Approved', 'sent' => 'Sent', 'replied' => 'Replied', 'bounced' => 'Bounced', 'do_not_contact' => 'Do not contact'])->required(),
            DateTimePicker::make('sent_at'), DateTimePicker::make('follow_up_at'), Textarea::make('notes')->rows(4)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('organization.name')->searchable(), TextColumn::make('recipient_email')->searchable(), TextColumn::make('subject')->limit(50)->searchable(),
            TextColumn::make('status')->badge()->sortable(), TextColumn::make('follow_up_at')->dateTime()->sortable(), TextColumn::make('sent_at')->dateTime()->sortable(),
        ])->recordActions([EditAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageOutreachEmails::route('/')];
    }
}
