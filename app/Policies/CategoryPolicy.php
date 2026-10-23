<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;
use App\Policies\Concerns\BelongsToSameBranch;

class CategoryPolicy
{
    use BelongsToSameBranch;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Category $category): bool
    {
        return $this->sharesBranch($user, $category->branch_id);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Category $category): bool
    {
        return $this->sharesBranch($user, $category->branch_id);
    }

    public function delete(User $user, Category $category): bool
    {
        return $this->sharesBranch($user, $category->branch_id);
    }
}
