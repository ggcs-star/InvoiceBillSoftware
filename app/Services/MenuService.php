<?php

namespace App\Services;

use App\Helpers\SlugGenerator;
use App\Models\Branch;
use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

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

    public function createMenuItem(Branch $branch, array $data, ?UploadedFile $image = null): MenuItem
    {
        if ($image) {
            $data['image_path'] = $image->store('menu-items', 'public');
        }

        return $branch->menuItems()->create([
            ...$data,
            'slug' => SlugGenerator::unique(MenuItem::class, $data['name'], ['branch_id' => $branch->id]),
        ]);
    }

    public function updateMenuItem(MenuItem $menuItem, array $data, ?UploadedFile $image = null): MenuItem
    {
        if (isset($data['name']) && $data['name'] !== $menuItem->name) {
            $data['slug'] = SlugGenerator::unique(
                MenuItem::class,
                $data['name'],
                ['branch_id' => $menuItem->branch_id],
                $menuItem->id
            );
        }

        if ($image) {
            if ($menuItem->image_path) {
                Storage::disk('public')->delete($menuItem->image_path);
            }

            $data['image_path'] = $image->store('menu-items', 'public');
        }

        $menuItem->update($data);

        return $menuItem;
    }

    public function toggleAvailability(MenuItem $menuItem): MenuItem
    {
        $menuItem->update(['is_available' => ! $menuItem->is_available]);

        return $menuItem;
    }

    public function toggleCustomerSiteVisibility(MenuItem $menuItem): MenuItem
    {
        $menuItem->update(['show_on_customer_site' => ! $menuItem->show_on_customer_site]);

        return $menuItem;
    }
}
