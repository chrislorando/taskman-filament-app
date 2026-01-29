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
class TaskAssigned extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Task $task,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'broadcast', 'database'];
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
            ->line("**Description:**")
            ->line(str($this->task->description)->markdown()->stripTags()->limit(100))
            ->action('View Task', url('/tasks/' . $this->task->id))
            ->line('Thank you for your attention to this task.');
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return FilamentNotification::make()
            ->id('broadcast-notification')
            ->title("New Task Assigned {$this->task->title}")
            ->body(str($this->task->description)->markdown()->stripTags()->limit(50))
            ->actions([
                Action::make('view')
                    ->button()
                    ->markAsRead()
                    ->url(fn(): string => TaskResource::getUrl('view', ['record' => $this->task])),

            ])
            ->info()
            ->persistent()
            ->getBroadcastMessage();
    }

    public function toArray(User $notifiable): array
    {
        return FilamentNotification::make()
            ->id('db-notification')
            ->title("New Task Assigned {$this->task->title}")
            ->body(str($this->task->description)->markdown()->stripTags()->limit(70))
            ->actions([
                Action::make('view')
                    ->button()
                    ->markAsRead()
                    ->url(TaskResource::getUrl('view', ['record' => $this->task])),
            ])
            ->info()
            ->getDatabaseMessage();
    }
}
