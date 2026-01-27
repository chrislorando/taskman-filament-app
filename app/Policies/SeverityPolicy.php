<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Severity;
use App\Models\User;

class SeverityPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role === UserRole::Admin;
    }

    public function view(User $user, Severity $severity): bool
    {
        return $user->role === UserRole::Admin;
    }

    public function create(User $user): bool
    {
        return $user->role === UserRole::Admin;
    }

    public function update(User $user, Severity $severity): bool
    {
        return $user->role === UserRole::Admin;
    }

    public function delete(User $user, Severity $severity): bool
    {
        return $user->role === UserRole::Admin;
    }

    public function restore(User $user, Severity $severity): bool
    {
        return $user->role === UserRole::Admin;
    }

    public function forceDelete(User $user, Severity $severity): bool
    {
        return $user->role === UserRole::Admin;
    }
}
