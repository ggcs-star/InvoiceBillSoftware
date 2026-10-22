<?php

namespace App\Policies\Concerns;

use App\Enums\UserRole;
use App\Models\User;

trait BelongsToSameBranch
{
    protected function sharesBranch(User $user, int $branchId): bool
    {
        return $user->role === UserRole::Admin || $user->branch_id === $branchId;
    }
}
