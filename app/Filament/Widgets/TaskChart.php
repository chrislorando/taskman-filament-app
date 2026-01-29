<?php

namespace App\Filament\Widgets;

use App\Enums\UserRole;
use App\Models\Task;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class TaskChart extends ChartWidget
{
    protected static ?string $heading = 'Monthly Workflow Trends';

    protected function getData(): array
    {
        $user = auth()->user();
        $colorMap = [
            'danger' => '#ef4444',
            'success' => '#22c55e',
            'warning' => '#f59e0b',
            'primary' => '#3b82f6',
            'gray' => '#64748b',
            'info' => '#0ea5e9',
        ];

        $statuses = \App\Models\Status::whereNotIn('name', ['Waiting', 'Pending'])->get();
        $datasets = [];
        $labels = [];

        foreach ($statuses as $status) {
            $data = Trend::query(Task::where('status_id', $status->id)->when($user->role == UserRole::Developer, fn($query) => $query->where('developer_id', auth()->id())))
                ->between(
                    start: now()->startOfYear(),
                    end: now()->endOfYear(),
                )
                ->perMonth()
                ->count();

            if (empty($labels)) {
                $labels = $data->map(fn(TrendValue $value) => $value->date);
            }

            $datasets[] = [
                'label' => $status->name,
                'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
                'borderColor' => $colorMap[$status->color->value] ?? '#cbd5e1',
                'backgroundColor' => ($colorMap[$status->color->value] ?? '#cbd5e1') . '33',
                'tension' => 0.4,
            ];
        }

        return [
            'datasets' => $datasets,
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
