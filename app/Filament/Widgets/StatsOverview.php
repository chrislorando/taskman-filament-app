<?php

namespace App\Filament\Widgets;

use App\Enums\UserRole;
use App\Models\Status;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Task;

class StatsOverview extends BaseWidget
{
    public function getStats(): array
    {
        $stats = [];
        $user = auth()->user();

        $stats[] = Stat::make(
            label: 'Total Tasks',
            value: Task::query()
                ->when($user->role == UserRole::Developer, fn($query) => $query->where('developer_id', auth()->id()))
                ->count(),
        )
            ->description('All assigned tasks')
            ->descriptionIcon('heroicon-m-clipboard-document-list')
            ->color('primary');

        $statuses = Status::withCount([
            'tasks' => function ($query) use ($user) {
                $query->when($user->role == UserRole::Developer, fn($query) => $query->where('developer_id', auth()->id()));
            }
        ])->get();

        foreach ($statuses as $status) {
            $stats[] = Stat::make(
                label: $status->name,
                value: $status->tasks_count,
            )
                ->description($status->name . ' Tasks')
                ->color($status->color?->value ?? 'gray');
        }

        return $stats;
    }
}
