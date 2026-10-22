<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\User;
use App\Policies\Concerns\BelongsToSameBranch;

class InvoicePolicy
{
    use BelongsToSameBranch;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Invoice $invoice): bool
    {
        return $this->sharesBranch($user, $invoice->branch_id);
    }

    public function print(User $user, Invoice $invoice): bool
    {
        return $this->sharesBranch($user, $invoice->branch_id);
    }
}
