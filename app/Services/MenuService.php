<?php

namespace App\Services;

use App\Helpers\SlugGenerator;
use App\Models\Branch;
use App\Models\Category;
use App\Models\MenuItem;

class MenuService
{
    public function createCategory(Branch $branch, array $data): Category
    {
        return $branch->categories()->create([
            ...$data,
            'slug' => SlugGenerator::unique(Category::class, $data['name'], ['branch_id' => $branch->id]),
        ]);
    }

    public function updateCategory(Category $category, array $data): Category
    {
        if (isset($data['name']) && $data['name'] !== $category->name) {
            $data['slug'] = SlugGenerator::unique(
                Category::class,
                $data['name'],
                ['branch_id' => $category->branch_id],
                $category->id
            );
        }

        $category->update($data);

        return $category;
    }

    public function createMenuItem(Branch $branch, array $data): MenuItem
    {
        return $branch->menuItems()->create([
            ...$data,
            'slug' => SlugGenerator::unique(MenuItem::class, $data['name'], ['branch_id' => $branch->id]),
        ]);
    }

    public function updateMenuItem(MenuItem $menuItem, array $data): MenuItem
    {
        if (isset($data['name']) && $data['name'] !== $menuItem->name) {
            $data['slug'] = SlugGenerator::unique(
                MenuItem::class,
                $data['name'],
                ['branch_id' => $menuItem->branch_id],
                $menuItem->id
            );
        }

        $menuItem->update($data);

        return $menuItem;
    }

    public function toggleAvailability(MenuItem $menuItem): MenuItem
    {
        $menuItem->update(['is_available' => ! $menuItem->is_available]);

        return $menuItem;
    }
}
