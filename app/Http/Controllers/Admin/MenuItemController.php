<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MenuItemRequest;
use App\Models\Branch;
use App\Models\MenuItem;
use App\Models\Restaurant;
use App\Services\MenuService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MenuItemController extends Controller
{
    public function __construct(private readonly MenuService $menu)
    {
    }

    public function index(Request $request, Restaurant $restaurant, Branch $branch): View
    {
        $this->authorize('viewAny', MenuItem::class);

        $menuItems = $branch->menuItems()
            ->with('category')
            ->when($request->filled('search'), fn ($query) => $query->where('name', 'like', '%' . $request->string('search') . '%'))
            ->when($request->filled('category_id'), fn ($query) => $query->where('category_id', $request->integer('category_id')))
            ->when($request->filled('availability'), fn ($query) => $query->where('is_available', $request->string('availability') === 'available'))
            ->orderBy('sort_order')
            ->paginate(15)
            ->withQueryString();

        $categories = $branch->categories()->orderBy('sort_order')->get();

        return view('admin.branches.menu-items', compact('restaurant', 'branch', 'menuItems', 'categories'));
    }

    public function store(MenuItemRequest $request, Restaurant $restaurant, Branch $branch): RedirectResponse
    {
        $this->authorize('create', MenuItem::class);

        $this->menu->createMenuItem($branch, $request->validated());

        return back()->with('status', 'Menu item added.');
    }

    public function update(MenuItemRequest $request, Restaurant $restaurant, Branch $branch, MenuItem $menuItem): RedirectResponse
    {
        $this->authorize('update', $menuItem);

        $this->menu->updateMenuItem($menuItem, $request->validated());

        return back()->with('status', 'Menu item updated.');
    }

    public function toggleAvailability(Restaurant $restaurant, Branch $branch, MenuItem $menuItem): RedirectResponse
    {
        $this->authorize('update', $menuItem);

        $this->menu->toggleAvailability($menuItem);

        return back()->with('status', 'Availability updated.');
    }

    public function destroy(Restaurant $restaurant, Branch $branch, MenuItem $menuItem): RedirectResponse
    {
        $this->authorize('delete', $menuItem);

        $menuItem->delete();

        return back()->with('status', 'Menu item removed.');
    }
}
