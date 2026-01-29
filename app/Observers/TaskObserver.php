<?php

namespace App\Observers;

use App\Models\Task;
use App\Notifications\TaskAssigned;
use App\Notifications\TaskChangeStatus;

class TaskObserver
{
    /**
     * Handle the Task "creating" event.
     */
    public function creating(Task $task): void
    {
        if (auth()->check() && is_null($task->created_by)) {
            $task->created_by = auth()->id();
        }

    }

    /**
     * Handle the Task "created" event.
     */
    public function created(Task $task): void
    {
        if ($task->developer_id) {
            $task?->developer->notify(new TaskAssigned($task));
        }
    }

    /**
     * Handle the Task "updated" event.
     */
    public function updated(Task $task): void
    {
        if ($task->status->name == 'Completed' || $task->status->name == 'In Progress') {
            $task?->creator->notify(new TaskChangeStatus($task));
        }

        if ($task->isDirty('developer_id')) {
            $oldDeveloperId = $task->getOriginal('developer_id');
            $newDeveloperId = $task->developer_id;

            if ($newDeveloperId != $oldDeveloperId) {
                $task?->developer->notify(new TaskAssigned($task));
            }
        }

    }

    /**
     * Handle the Task "deleted" event.
     */
    public function deleted(Task $task): void
    {
        //
    }

    /**
     * Handle the Task "restored" event.
     */
    public function restored(Task $task): void
    {
        //
    }

    /**
     * Handle the Task "force deleted" event.
     */
    public function forceDeleted(Task $task): void
    {
        //
    }
}
