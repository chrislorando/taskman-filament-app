<?php

namespace App\Filament\Pages;

use App\Models\Task;
use Filament\Widgets\StatsOverviewWidget;
use Illuminate\Database\Eloquent\Builder;
use Filament\Facades\Filament;

class Dashboard extends \Filament\Pages\Dashboard
{
    protected int|string|array $columnSpan = [
        'sm' => 1,
        'md' => 2,
        'xl' => 3,
    ];

    public function getColumns(): int|string|array
    {
        return [
            'sm' => 1,
        ];
    }
}