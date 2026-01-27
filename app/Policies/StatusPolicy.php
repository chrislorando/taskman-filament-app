<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Status;
use App\Models\User;

class StatusPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role === UserRole::Admin;
    }

    public function view(User $user, Status $status): bool
    {
        return $user->role === UserRole::Admin;
    }

    public function create(User $user): bool
    {
        return $user->role === UserRole::Admin;
    }

    public function update(User $user, Status $status): bool
    {
        return $user->role === UserRole::Admin;
    }

    public function delete(User $user, Status $status): bool
    {
        return $user->role === UserRole::Admin;
    }

    public function restore(User $user, Status $status): bool
    {
        return $user->role === UserRole::Admin;
    }

    public function forceDelete(User $user, Status $status): bool
    {
        return $user->role === UserRole::Admin;
    }
}
