<?php

namespace App\Policies;

use App\Models\User;
use App\Policies\Concerns\BelongsToSameBranch;

class StaffPolicy
{
    use BelongsToSameBranch;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, User $staff): bool
    {
        return $this->sharesBranch($user, $staff->branch_id);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, User $staff): bool
    {
        return $this->sharesBranch($user, $staff->branch_id);
    }

    public function delete(User $user, User $staff): bool
    {
        return $this->sharesBranch($user, $staff->branch_id);
    }
}
