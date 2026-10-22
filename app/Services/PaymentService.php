<?php

namespace App\Services;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Payment;

class PaymentService
{
    public function record(Order $order, PaymentMethod $method, string $amount, ?string $reference = null): Payment
    {
        return $order->payments()->create([
            'method' => $method,
            'amount' => $amount,
            'status' => PaymentStatus::Success,
            'transaction_reference' => $reference,
            'paid_at' => now(),
        ]);
    }

    public function totalPaid(Order $order): string
    {
        return (string) $order->payments()
            ->where('status', PaymentStatus::Success)
            ->sum('amount');
    }

    public function isFullyPaid(Order $order): bool
    {
        return bccomp($this->totalPaid($order), (string) $order->grand_total, 2) >= 0;
    }
}
