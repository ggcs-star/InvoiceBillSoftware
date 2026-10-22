<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Open = 'open';
    case AwaitingPayment = 'awaiting_payment';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Open => 'Open',
            self::AwaitingPayment => 'Awaiting Payment',
            self::Completed => 'Completed',
            self::Cancelled => 'Cancelled',
        };
    }
}
