<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\CrmTasks\CrmTaskResource;
use App\Models\CrmTask;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class PriorityFollowUps extends TableWidget
{
    protected static ?int $sort = 3;

    protected static ?string $heading = 'Priority follow-ups';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                CrmTask::query()
                    ->with(['contact', 'lead'])
                    ->whereNot('status', 'completed')
                    ->orderByRaw("case priority when 'urgent' then 1 when 'high' then 2 when 'medium' then 3 else 4 end")
                    ->orderByRaw('case when due_at is null then 1 else 0 end')
                    ->orderBy('due_at')
                    ->limit(6)
            )
            ->columns([
                TextColumn::make('title')->searchable()->weight('medium'),
                TextColumn::make('contact.full_name')->label('Contact')->placeholder('Unassigned'),
                TextColumn::make('priority')->badge()->color(fn (string $state): string => match ($state) {
                    'urgent' => 'danger',
                    'high' => 'warning',
                    'medium' => 'info',
                    default => 'gray',
                }),
                TextColumn::make('due_at')
                    ->label('Due')
                    ->dateTime('M j, Y')
                    ->placeholder('No date')
                    ->color(fn (CrmTask $record): string => $record->due_at?->isPast() ? 'danger' : 'gray'),
                TextColumn::make('status')->badge(),
            ])
            ->recordUrl(fn (CrmTask $record): string => CrmTaskResource::getUrl('index', ['tableSearch' => $record->title]))
            ->emptyStateHeading('No follow-ups need attention')
            ->emptyStateDescription('New CRM tasks will appear here automatically.');
    }
}
