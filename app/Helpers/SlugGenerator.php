<?php

namespace App\Helpers;

use Illuminate\Support\Str;

/**
 * Shared "make me a unique slug" routine for any model, used by both
 * restaurant (globally unique) and menu/category (per-restaurant unique) slugs.
 */
class SlugGenerator
{
    public static function unique(string $modelClass, string $name, array $scope = [], ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $suffix = 1;

        while (
            $modelClass::query()
                ->where('slug', $slug)
                ->when($scope, fn ($query) => $query->where($scope))
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }
}
