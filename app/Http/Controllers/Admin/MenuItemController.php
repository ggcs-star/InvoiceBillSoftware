<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\AuthorizesBranchAccess;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MenuItemRequest;
use App\Models\Branch;
use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Restaurant;
use App\Services\MenuService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MenuItemController extends Controller
{
    use AuthorizesBranchAccess;

    public function __construct(private readonly MenuService $menu)
    {
    }

    /**
     * Platform-wide menu item directory across every restaurant and branch.
     */
    public function all(Request $request): View
    {
        $this->authorize('viewAny', MenuItem::class);

        $menuItems = MenuItem::with(['branch.restaurant', 'branch.categories', 'category', 'variations' => fn ($query) => $query->orderBy('sort_order')])
            ->when($request->filled('search'), fn ($query) => $query->where('name', 'like', '%' . $request->string('search') . '%'))
            ->when($request->filled('restaurant_id'), fn ($query) => $query->whereHas(
                'branch',
                fn ($branchQuery) => $branchQuery->where('restaurant_id', $request->integer('restaurant_id'))
            ))
            ->when($request->filled('branch_id'), fn ($query) => $query->where('branch_id', $request->integer('branch_id')))
            ->when($request->filled('category'), fn ($query) => $query->whereHas(
                'category',
                fn ($categoryQuery) => $categoryQuery->where('name', $request->string('category'))
            ))
            ->when($request->filled('availability'), fn ($query) => $query->where('is_available', $request->input('availability') === 'available'))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $restaurants = Restaurant::orderBy('name')->get(['id', 'name']);
        $branches = Branch::with(['restaurant', 'categories' => fn ($query) => $query->orderBy('sort_order')])
            ->orderBy('name')
            ->get(['id', 'name', 'restaurant_id']);
        $categoryNames = Category::query()->distinct()->orderBy('name')->pluck('name');

        $stats = [
            'total_items' => MenuItem::count(),
            'new_this_week' => MenuItem::where('created_at', '>=', now()->startOfWeek())->count(),
            'restaurants' => Restaurant::where('is_active', true)->count(),
            'categories' => Category::where('is_active', true)->count(),
            'price_min' => (float) (MenuItem::min('price') ?? 0),
            'price_max' => (float) (MenuItem::max('price') ?? 0),
        ];

        return view('admin.menu-items.index', compact('menuItems', 'restaurants', 'branches', 'categoryNames', 'stats'));
    }

    /**
     * Downloads whatever the current filters on the directory are showing —
     * same query as all(), just unpaginated and streamed as CSV.
     */
    public function export(Request $request): StreamedResponse
    {
        $this->authorize('viewAny', MenuItem::class);

        $menuItems = MenuItem::with(['branch.restaurant', 'category'])
            ->when($request->filled('search'), fn ($query) => $query->where('name', 'like', '%' . $request->string('search') . '%'))
            ->when($request->filled('restaurant_id'), fn ($query) => $query->whereHas(
                'branch',
                fn ($branchQuery) => $branchQuery->where('restaurant_id', $request->integer('restaurant_id'))
            ))
            ->when($request->filled('branch_id'), fn ($query) => $query->where('branch_id', $request->integer('branch_id')))
            ->when($request->filled('availability'), fn ($query) => $query->where('is_available', $request->input('availability') === 'available'))
            ->orderBy('name')
            ->get();

        $filename = 'menu-items-' . now()->format('Y-m-d-His') . '.csv';

        return new StreamedResponse(function () use ($menuItems) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['item_name', 'category_name', 'restaurant', 'branch', 'price', 'gst_percentage', 'description', 'type', 'is_available', 'show_on_customer_site']);

            foreach ($menuItems as $item) {
                fputcsv($handle, [
                    $item->name,
                    $item->category->name,
                    $item->branch->restaurant->name,
                    $item->branch->name,
                    $item->price,
                    $item->gst_percentage,
                    $item->description,
                    $item->food_type->value,
                    $item->is_available ? 'yes' : 'no',
                    $item->show_on_customer_site ? 'yes' : 'no',
                ]);
            }

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Same export as export(), scoped to a single branch for that branch's
     * own staff login.
     */
    public function exportForBranch(Request $request, Restaurant $restaurant, Branch $branch): StreamedResponse
    {
        $this->authorizeBranch($request, $branch);

        $menuItems = $branch->menuItems()
            ->with(['category'])
            ->when($request->filled('search'), fn ($query) => $query->where('name', 'like', '%' . $request->string('search') . '%'))
            ->when($request->filled('category_id'), fn ($query) => $query->where('category_id', $request->integer('category_id')))
            ->when($request->filled('availability'), fn ($query) => $query->where('is_available', $request->input('availability') === 'available'))
            ->orderBy('name')
            ->get();

        $filename = 'menu-items-' . $branch->slug . '-' . now()->format('Y-m-d-His') . '.csv';

        return new StreamedResponse(function () use ($menuItems, $branch) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['item_name', 'category_name', 'restaurant', 'branch', 'price', 'gst_percentage', 'description', 'type', 'is_available', 'show_on_customer_site']);

            foreach ($menuItems as $item) {
                fputcsv($handle, [
                    $item->name,
                    $item->category->name,
                    $branch->restaurant->name,
                    $branch->name,
                    $item->price,
                    $item->gst_percentage,
                    $item->description,
                    $item->food_type->value,
                    $item->is_available ? 'yes' : 'no',
                    $item->show_on_customer_site ? 'yes' : 'no',
                ]);
            }

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function index(Request $request, Restaurant $restaurant, Branch $branch): View
    {
        $this->authorizeBranch($request, $branch);

        $menuItems = $branch->menuItems()
            ->with(['category', 'variations' => fn ($query) => $query->orderBy('sort_order')])
            ->when($request->filled('search'), fn ($query) => $query->where('name', 'like', '%' . $request->string('search') . '%'))
            ->when($request->filled('category_id'), fn ($query) => $query->where('category_id', $request->integer('category_id')))
            ->when($request->filled('availability'), fn ($query) => $query->where('is_available', $request->input('availability') === 'available'))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $categories = $branch->categories()->orderBy('sort_order')->get();

        return view('admin.branches.menu-items', compact('restaurant', 'branch', 'menuItems', 'categories'));
    }

    public function store(MenuItemRequest $request, Restaurant $restaurant, Branch $branch): RedirectResponse
    {
        $this->authorizeBranch($request, $branch);

        $this->menu->createMenuItem($branch, $request->safe()->except('image'), $request->file('image'));

        return back()->with('status', 'Menu item added.');
    }

    public function update(MenuItemRequest $request, Restaurant $restaurant, Branch $branch, MenuItem $menuItem): RedirectResponse
    {
        $this->authorize('update', $menuItem);

        $this->menu->updateMenuItem($menuItem, $request->safe()->except('image'), $request->file('image'));

        return back()->with('status', 'Menu item updated.');
    }

    public function toggleAvailability(Restaurant $restaurant, Branch $branch, MenuItem $menuItem): RedirectResponse
    {
        $this->authorize('update', $menuItem);

        $this->menu->toggleAvailability($menuItem);

        return back()->with('status', 'Availability updated.');
    }

    public function toggleCustomerSite(Restaurant $restaurant, Branch $branch, MenuItem $menuItem): RedirectResponse
    {
        $this->authorize('update', $menuItem);

        $this->menu->toggleCustomerSiteVisibility($menuItem);

        return back()->with('status', 'Customer site visibility updated.');
    }

    public function destroy(Restaurant $restaurant, Branch $branch, MenuItem $menuItem): RedirectResponse
    {
        $this->authorize('delete', $menuItem);

        $menuItem->delete();

        return back()->with('status', 'Menu item removed.');
    }
}
