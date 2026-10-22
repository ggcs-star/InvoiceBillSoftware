<?php

namespace App\Helpers;

/**
 * Single source of truth for GST line-item math. Uses bcmath so money
 * never drifts from float rounding, since every total in this app is
 * derived from these two calculations.
 */
class TaxCalculator
{
    public static function lineAmount(string $unitPrice, int $quantity): string
    {
        return bcmul($unitPrice, (string) $quantity, 2);
    }

    public static function lineTax(string $unitPrice, int $quantity, string $gstPercentage): string
    {
        $amount = self::lineAmount($unitPrice, $quantity);

        return bcdiv(bcmul($amount, $gstPercentage, 4), '100', 2);
    }

    public static function lineTotal(string $unitPrice, int $quantity, string $gstPercentage): string
    {
        return bcadd(
            self::lineAmount($unitPrice, $quantity),
            self::lineTax($unitPrice, $quantity, $gstPercentage),
            2
        );
    }
}
