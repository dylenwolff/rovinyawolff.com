<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class StudioQuickActions extends Widget
{
    protected static ?int $sort = 2;

    protected string $view = 'filament.widgets.studio-quick-actions';

    protected int|string|array $columnSpan = 'full';
}
