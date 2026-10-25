<?php

namespace App\Filament\Resources\OutreachEmails\Pages;

use App\Filament\Resources\OutreachEmails\OutreachEmailResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageOutreachEmails extends ManageRecords
{
    protected static string $resource = OutreachEmailResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
