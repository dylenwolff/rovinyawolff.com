<x-filament-widgets::widget>
    <x-filament::section heading="Quick actions" description="Move directly to the work that keeps the studio visible and the outreach pipeline moving.">
        <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
            <x-filament::button tag="a" :href="\App\Filament\Resources\Organizations\OrganizationResource::getUrl('index')" icon="heroicon-m-building-office-2" outlined>
                Review prospects
            </x-filament::button>
            <x-filament::button tag="a" :href="\App\Filament\Resources\OutreachEmails\OutreachEmailResource::getUrl('index')" icon="heroicon-m-paper-airplane" outlined>
                Open email queue
            </x-filament::button>
            <x-filament::button tag="a" :href="\App\Filament\Resources\CrmTasks\CrmTaskResource::getUrl('index')" icon="heroicon-m-check-circle" outlined>
                Manage follow-ups
            </x-filament::button>
            <x-filament::button tag="a" :href="\App\Filament\Resources\Projects\ProjectResource::getUrl('create')" icon="heroicon-m-plus" outlined>
                Add portfolio project
            </x-filament::button>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
