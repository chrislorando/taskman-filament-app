<?php

namespace App\Filament\Resources\TaskResource\Pages;

use App\Enums\UserRole;
use App\Filament\Resources\TaskResource;
use App\Models\Status;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListTasks extends ListRecords
{
    protected static string $resource = TaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        $statuses = Status::orderBy('sort_order')->get();

        $tabs = [
            'all' => Tab::make(),
        ];

        foreach ($statuses as $status) {
            $tabs[str($status->name)->slug()->toString()] = Tab::make($status->name)
                ->badge(function () use ($status) {
                    return \App\Models\Task::when(auth()->user()->role === UserRole::Developer, function (Builder $query) {
                        $query->where('developer_id', auth()->id());
                    })->where('status_id', $status->id)->count();
                })
                ->badgeColor($status->color->value)
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status_id', $status->id));
        }

        return $tabs;
    }
}
