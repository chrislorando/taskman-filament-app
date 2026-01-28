<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    public function viewAny(User $user): bool
    {
        if ($user->role === UserRole::Admin) {
            return true;
        }

        return $user->role === UserRole::Developer;
    }

    public function view(User $user, Task $task): bool
    {
        if ($user->role === UserRole::Admin) {
            return true;
        }

        return $user->role === UserRole::Developer && $task->developer_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->role === UserRole::Admin;
    }

    public function update(User $user, Task $task): bool
    {
        if ($user->role === UserRole::Admin) {
            return true;
        }

        return $user->role === UserRole::Developer && $task->developer_id === $user->id;
    }

    public function assignDeveloper(User $user, Task $task): bool
    {
        return $user->role === UserRole::Admin;
    }

    public function updateStatus(User $user, Task $task, string $status): bool
    {
        if ($user->role === UserRole::Admin) {
            return true;
        }

        if ($user->role === UserRole::Developer && $task->developer_id === $user->id) {
            return in_array($status, ['In Progress', 'Completed']);
        }

        return false;
    }

    public function comment(User $user, Task $task): bool
    {
        if ($user->role === UserRole::Admin) {
            return true;
        }

        return $user->role === UserRole::Developer && $task->developer_id === $user->id;
    }

    public function delete(User $user, Task $task): bool
    {
        return $user->role === UserRole::Admin;
    }

    public function restore(User $user, Task $task): bool
    {
        return $user->role === UserRole::Admin;
    }

    public function forceDelete(User $user, Task $task): bool
    {
        return $user->role === UserRole::Admin;
    }
}
