<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewComment extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Comment $comment,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $task = $this->comment->task;
        $commenter = $this->comment->user;

        return (new MailMessage)
            ->subject("New Comment on Task: {$task->title}")
            ->greeting("**{$commenter->name}** has commented on the task.")
            ->line("{$commenter->name} has commented on task.")
            ->line("**Status:** {$task->status->name} | **Severity:** {$task->severity->name}")
            ->line(str($this->comment->body)->markdown()->stripTags()->limit(100))
            ->lineIf($this->comment->parent_id, 'This is a reply to another comment.')
            ->action('View Task', url('/tasks/'.$task->id));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'comment_id' => $this->comment->id,
            'task_id' => $this->comment->task_id,
            'task_title' => $this->comment->task->title,
            'commenter_name' => $this->comment->user->name,
            'body' => $this->comment->body,
            'parent_id' => $this->comment->parent_id,
        ];
    }
}
