<?php

namespace App\Policies;

use App\Models\MenuItem;
use App\Models\User;
use App\Policies\Concerns\BelongsToSameBranch;

class MenuItemPolicy
{
    use BelongsToSameBranch;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, MenuItem $menuItem): bool
    {
        return $this->sharesBranch($user, $menuItem->branch_id);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, MenuItem $menuItem): bool
    {
        return $this->sharesBranch($user, $menuItem->branch_id);
    }

    public function delete(User $user, MenuItem $menuItem): bool
    {
        return $this->sharesBranch($user, $menuItem->branch_id);
    }
}
