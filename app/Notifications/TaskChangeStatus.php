<?php

namespace App\Notifications;

use App\Filament\Resources\TaskResource;
use App\Models\Task;
use App\Models\User;
use Filament\Notifications\Actions\Action;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Notification as FilamentNotification;
class TaskChangeStatus extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Task $task,
    ) {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'broadcast', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $task = $this->task;
        $dueDate = $task->due_date ? $task->due_date->format('F j, Y') : 'Not set';

        return (new MailMessage)
            ->greeting("Hello {$notifiable->name},")
            ->subject("Task: {$task->title} {$task?->status->name}")
            ->line("**Title:** {$task->title}")
            ->line("**Status:** {$task->status->name}")
            ->line("**Severity:** {$task->severity->name}")
            ->line("**Developer:** {$task?->developer->name}")
            ->line("**Due Date:** {$dueDate}")
            ->lineIf($task->description, "**Description:** {$task->description}")
            ->action('View Task', url('/tasks/' . $this->task->id))
            ->line('Thank you for your attention to this task.');
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        $task = $this->task;
        return FilamentNotification::make()
            ->id('broadcast-notification')
            ->title("Task: {$task->title} {$task?->status->name}")
            ->body(str($task->description)->markdown()->stripTags()->limit(50))
            ->actions([
                Action::make('view')
                    ->button()
                    ->markAsRead()
                    ->url(TaskResource::getUrl('view', ['record' => $task])),
            ])
            ->info()
            ->persistent()
            ->getBroadcastMessage();
    }

    public function toArray(User $notifiable): array
    {
        $task = $this->task;
        return FilamentNotification::make()
            ->id('db-notification')
            ->title("Task: {$task->title} {$task?->status->name}")
            ->body(str($task->description)->markdown()->stripTags()->limit(70))
            ->actions([
                Action::make('view')
                    ->button()
                    ->markAsRead()
                    ->url(TaskResource::getUrl('view', ['record' => $task])),
            ])
            ->info()
            ->getDatabaseMessage();
    }
}
