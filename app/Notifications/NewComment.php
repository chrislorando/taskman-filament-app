<?php

namespace App\Notifications;

use App\Filament\Resources\TaskResource;
use App\Models\Comment;
use App\Models\User;
use Filament\Notifications\Actions\Action;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Notification as FilamentNotification;

class NewComment extends Notification implements ShouldQueue, ShouldBroadcast
{
    use Queueable;

    public function __construct(
        public Comment $comment,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'broadcast', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $task = $this->comment->task;
        $commenter = $this->comment->user;

        return (new MailMessage)
            ->subject("New Comment on Task: {$task->title}")
            ->greeting("**{$commenter->name}** has commented on the task.")
            ->line("**Status:** {$task->status->name} | **Severity:** {$task->severity->name}")
            ->line(str($this->comment->body)->markdown()->stripTags()->limit(100))
            ->lineIf($this->comment->parent_id, 'This is a reply to another comment.')
            ->action('View Task', url('/tasks/'.$task->id));
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        $task = $this->comment->task;
        $commenter = $this->comment->user;
        
        return FilamentNotification::make()
            ->id('broadcast-notification')
            ->title("New Comment from {$commenter->name}")
            ->body(str($this->comment->body)->markdown()->stripTags()->limit(70))
            ->actions([
                Action::make('view')
                    ->button()
                    ->markAsRead() 
                    ->url(fn (): string => TaskResource::getUrl('view', ['record' => $task])),  
          
            ])
            ->info()
            ->persistent()
            ->getBroadcastMessage();
    }

    public function toArray(User $notifiable): array
    {
        $task = $this->comment->task;
        $commenter = $this->comment->user;

        return FilamentNotification::make()
            ->id('db-notification')
            ->title("New Comment from {$commenter->name}")
            ->body(str($this->comment->body)->markdown()->stripTags()->limit(100))
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
