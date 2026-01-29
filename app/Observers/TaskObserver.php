<?php

namespace App\Observers;

use App\Models\Status;
use App\Models\Task;
use App\Notifications\TaskAssigned;
use App\Notifications\TaskChangeStatus;

class TaskObserver
{
    public function saving(Task $task): void
    {
        // Pake load() buat mastiin relasi status ada isinya
        $statusName = Status::find($task->status_id)?->name;

        if (in_array($statusName, ['Completed', 'Closed'])) {
            // Kalau statusnya Completed/Closed, dan finish_date masih kosong, kasih tanggal
            $task->finish_date = $task->finish_date ?? now();
        } else {
            // Kalau balik ke status lain, kosongin lagi
            $task->finish_date = null;
        }
    }

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
