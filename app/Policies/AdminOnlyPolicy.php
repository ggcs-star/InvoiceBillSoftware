<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\User;

/**
 * Base for every resource that only the platform Admin may manage
 * (restaurants, categories, menu items, printers, staff accounts).
 * Centralising this here means each concrete policy is a one-liner
 * instead of repeating the same five checks.
 */
abstract class AdminOnlyPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role === UserRole::Admin;
    }

    public function view(User $user, mixed $model): bool
    {
        return $user->role === UserRole::Admin;
    }

    public function create(User $user): bool
    {
        return $user->role === UserRole::Admin;
    }

    public function update(User $user, mixed $model): bool
    {
        return $user->role === UserRole::Admin;
    }

    public function delete(User $user, mixed $model): bool
    {
        return $user->role === UserRole::Admin;
    }
}
