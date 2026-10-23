<?php

namespace App\Policies;

use App\Models\MenuItemVariation;
use App\Models\User;
use App\Policies\Concerns\BelongsToSameBranch;

class MenuItemVariationPolicy
{
    use BelongsToSameBranch;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, MenuItemVariation $variation): bool
    {
        return $this->sharesBranch($user, $variation->menuItem->branch_id);
    }

    public function delete(User $user, MenuItemVariation $variation): bool
    {
        return $this->sharesBranch($user, $variation->menuItem->branch_id);
    }
}
