<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskChangeStatus extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Task $task,
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $dueDate = $this->task->due_date ? $this->task->due_date->format('F j, Y') : 'Not set';

        return (new MailMessage)
            ->greeting("Hello {$notifiable->name},")
            ->subject("Task: {$this->task->title} {$this->task?->status->name}")
            ->line("**Title:** {$this->task->title}")
            ->line("**Status:** {$this->task->status->name}")
            ->line("**Severity:** {$this->task->severity->name}")
            ->line("**Developer:** {$this->task?->developer->name}")
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
