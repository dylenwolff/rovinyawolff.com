<?php

namespace App\Filament\Resources\OutreachEmails;

use App\Filament\Resources\OutreachEmails\Pages\ManageOutreachEmails;
use App\Models\Activity;
use App\Models\OutreachEmail;
use App\Services\NativeMailService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;
use Throwable;

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
        ])->defaultSort('created_at', 'desc')->recordActions([
            Action::make('send')
                ->label('Send email')
                ->icon(Heroicon::OutlinedPaperAirplane)
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Send this outreach email?')
                ->modalDescription(fn (OutreachEmail $record): string => "Send “{$record->subject}” to {$record->recipient_email}? A copy will be saved in Roundcube Sent.")
                ->modalSubmitActionLabel('Send now')
                ->visible(fn (OutreachEmail $record): bool => in_array($record->status, ['ready', 'approved'], true) && blank($record->sent_at))
                ->action(function (OutreachEmail $record, NativeMailService $mailer): void {
                    try {
                        $messageId = $mailer->send(
                            $record->recipient_email,
                            $record->subject,
                            $record->body,
                            $record->recipient_name,
                        );

                        $record->update([
                            'status' => 'sent',
                            'sent_at' => now(),
                            'external_message_id' => $messageId,
                            'follow_up_at' => $record->follow_up_at ?? now()->addDays(4),
                        ]);

                        $record->lead?->update([
                            'status' => 'contacted',
                            'next_follow_up_at' => $record->follow_up_at,
                        ]);

                        Activity::create([
                            'contact_id' => $record->contact_id,
                            'lead_id' => $record->lead_id,
                            'type' => 'email',
                            'subject' => $record->subject,
                            'details' => 'Outreach email sent to '.$record->recipient_email.'. Message ID: '.$messageId,
                            'occurred_at' => now(),
                        ]);

                        Notification::make()
                            ->title('Email sent')
                            ->body('The CRM and Roundcube Sent folder have been updated.')
                            ->success()
                            ->send();
                    } catch (Throwable $exception) {
                        report($exception);

                        Notification::make()
                            ->title('Email could not be sent')
                            ->body($exception->getMessage())
                            ->danger()
                            ->persistent()
                            ->send();
                    }
                }),
            EditAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageOutreachEmails::route('/')];
    }
}
