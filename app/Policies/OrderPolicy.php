<?php

namespace App\Policies;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use App\Policies\Concerns\BelongsToSameBranch;

class OrderPolicy
{
    use BelongsToSameBranch;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Order $order): bool
    {
        return $this->sharesBranch($user, $order->branch_id);
    }

    public function create(User $user): bool
    {
        return $user->branch_id !== null || $user->isAdmin();
    }

    public function update(User $user, Order $order): bool
    {
        return $this->sharesBranch($user, $order->branch_id) && $order->status === OrderStatus::Open;
    }

    public function checkout(User $user, Order $order): bool
    {
        return $this->sharesBranch($user, $order->branch_id) && $order->status === OrderStatus::Open;
    }
}
