<?php

namespace App\Enums;

enum FoodType: string
{
    case Veg = 'veg';
    case NonVeg = 'non_veg';
    case Egg = 'egg';

    public function label(): string
    {
        return match ($this) {
            self::Veg => 'Vegetarian',
            self::NonVeg => 'Non-Vegetarian',
            self::Egg => 'Contains Egg',
        };
    }

    /**
     * Shape for the standard Indian food-safety mark: a round dot for veg,
     * a triangle for non-veg/egg — the shape itself carries the meaning,
     * not just the color, same as the official symbol.
     */
    public function isTriangle(): bool
    {
        return $this !== self::Veg;
    }

    public function colorClass(): string
    {
        return match ($this) {
            self::Veg => 'text-green-600',
            self::NonVeg => 'text-red-700',
            self::Egg => 'text-amber-600',
        };
    }
}
