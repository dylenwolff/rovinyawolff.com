<?php

namespace App\Filament\Widgets;

use App\Models\CrmTask;
use App\Models\Lead;
use App\Models\Organization;
use App\Models\OutreachEmail;
use App\Models\Project;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StudioOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected ?string $heading = 'Studio overview';

    protected ?string $description = 'Portfolio publishing and client outreach at a glance.';

    protected function getStats(): array
    {
        $openTasks = CrmTask::query()->whereNot('status', 'completed')->count();
        $overdueTasks = CrmTask::query()
            ->whereNot('status', 'completed')
            ->whereNotNull('due_at')
            ->where('due_at', '<', now())
            ->count();

        return [
            Stat::make('Prospect organizations', Organization::query()->count())
                ->description(Organization::query()->where('status', 'prospect')->count().' ready to qualify')
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('primary'),
            Stat::make('Active leads', Lead::query()->whereNotIn('status', ['won', 'lost', 'archived'])->count())
                ->description('Potential client conversations')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info'),
            Stat::make('Email queue', OutreachEmail::query()->whereNull('sent_at')->count())
                ->description(OutreachEmail::query()->whereNotNull('sent_at')->count().' sent so far')
                ->descriptionIcon('heroicon-m-paper-airplane')
                ->color('success'),
            Stat::make('Follow-ups', $openTasks)
                ->description($overdueTasks ? $overdueTasks.' overdue' : 'Nothing overdue')
                ->descriptionIcon($overdueTasks ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-circle')
                ->color($overdueTasks ? 'danger' : 'success'),
            Stat::make('Published projects', Project::query()->where('is_published', true)->count())
                ->description('Live portfolio case studies')
                ->descriptionIcon('heroicon-m-photo')
                ->color('warning'),
        ];
    }
}
