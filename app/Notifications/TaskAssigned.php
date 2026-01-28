<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskAssigned extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Task $task,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $dueDate = $this->task->due_date ? $this->task->due_date->format('F j, Y') : 'Not set';

        return (new MailMessage)
            ->greeting("Hello {$notifiable->name},")
            ->subject("New Task Assigned: {$this->task->title}")
            ->line('You have been assigned a new task.')
            ->line("**Title:** {$this->task->title}")
            ->line("**Status:** {$this->task->status->name}")
            ->line("**Severity:** {$this->task->severity->name}")
            ->line("**Due Date:** {$dueDate}")
            ->lineIf($this->task->description, "**Description:** {$this->task->description}")
            ->action('View Task', url('/tasks/'.$this->task->id))
            ->line('Thank you for your attention to this task.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'task_id' => $this->task->id,
            'task_title' => $this->task->title,
            'status' => $this->task->status->name,
            'severity' => $this->task->severity->name,
            'due_date' => $this->task->due_date,
        ];
    }
}
