<?php

namespace App\Enums;

enum SequenceType: string
{
    case Order = 'order';
    case Invoice = 'invoice';

    public function prefix(): string
    {
        return match ($this) {
            self::Order => 'ORD',
            self::Invoice => 'INV',
        };
    }
}
