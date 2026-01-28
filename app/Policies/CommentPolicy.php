<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Comment;
use App\Models\Task;
use App\Models\User;

class CommentPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Comment $comment): bool
    {
        if ($user->role === UserRole::Admin) {
            return true;
        }

        return $user->role === UserRole::Developer && $comment->task->developer_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function createOnTask(User $user, Task $task): bool
    {
        if ($user->role === UserRole::Admin) {
            return true;
        }

        if ($user->role === UserRole::Developer && $task->developer_id === $user->id) {
            return true;
        }

        return false;
    }

    public function update(User $user, Comment $comment): bool
    {
        if ($user->role === UserRole::Admin) {
            return true;
        }

        return $user->role === UserRole::Developer && $comment->user_id === $user->id;
    }

    public function delete(User $user, Comment $comment): bool
    {
        if ($user->role === UserRole::Admin) {
            return true;
        }

        return $user->role === UserRole::Developer && $comment->user_id === $user->id;
    }

    public function restore(User $user, Comment $comment): bool
    {
        if ($user->role === UserRole::Admin) {
            return true;
        }

        return $user->role === UserRole::Developer && $comment->user_id === $user->id;
    }

    public function forceDelete(User $user, Comment $comment): bool
    {
        if ($user->role === UserRole::Admin) {
            return true;
        }

        return $user->role === UserRole::Developer && $comment->user_id === $user->id;
    }

    public function deleteAny(User $user): bool
    {
        return $user->role === UserRole::Admin;
    }
}
