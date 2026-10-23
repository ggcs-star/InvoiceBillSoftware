<?php

namespace App\Policies;

use App\Models\Printer;
use App\Models\User;
use App\Policies\Concerns\BelongsToSameBranch;

class PrinterPolicy
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

    /**
     * Branch staff requested a printer and it's still pending — they can
     * still adjust it. Once Admin approves it, only Admin may touch it, so a
     * live printer can't be silently reconfigured by whoever's at the till.
     */
    public function update(User $user, Printer $printer): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $this->sharesBranch($user, $printer->branch_id) && ! $printer->is_approved;
    }

    public function delete(User $user, Printer $printer): bool
    {
        return $this->update($user, $printer);
    }

    public function approve(User $user): bool
    {
        return $user->isAdmin();
    }
}
