<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\SlugGenerator;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BranchRequest;
use App\Models\Branch;
use App\Models\Restaurant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BranchController extends Controller
{
    /**
     * Platform-wide branch directory — every branch across every restaurant,
     * searchable/filterable/paginated. The sidebar never lists this, since
     * that doesn't scale; this table is the actual way to find one.
     */
    public function all(Request $request): View
    {
        $this->authorize('viewAny', Branch::class);

        $branches = Branch::with('restaurant')
            ->withCount('staff')
            ->when($request->filled('search'), fn ($query) => $query->where('name', 'like', '%' . $request->string('search') . '%'))
            ->when($request->filled('restaurant_id'), fn ($query) => $query->where('restaurant_id', $request->integer('restaurant_id')))
            ->when($request->filled('status'), fn ($query) => $query->where('is_active', $request->input('status') === 'active'))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $restaurants = Restaurant::orderBy('name')->get(['id', 'name']);

        return view('admin.branches.all', compact('branches', 'restaurants'));
    }

    public function store(BranchRequest $request, Restaurant $restaurant): RedirectResponse
    {
        $this->authorize('create', Branch::class);

        $branch = $restaurant->branches()->create([
            ...$request->validated(),
            'slug' => SlugGenerator::unique(Branch::class, $request->input('name'), ['restaurant_id' => $restaurant->id]),
        ]);

        return redirect()->route('admin.branches.edit', [$restaurant, $branch])
            ->with('status', 'Branch created. Add a printer and build its menu to get it ready for billing.');
    }

    /**
     * Read-only overview: branch summary plus a preview of its categories,
     * menu items, and orders. No add/edit/delete lives here on purpose —
     * that all happens on the dedicated pages reached from the sidebar.
     */
    public function show(Restaurant $restaurant, Branch $branch): View
    {
        $this->authorize('view', $branch);

        $branch->loadCount(['printers', 'staff']);

        $categories = $branch->categories()->withCount('menuItems')->orderBy('sort_order')->take(10)->get();
        $menuItems = $branch->menuItems()->with('category')->orderBy('sort_order')->take(10)->get();
        $orders = $branch->orders()->with(['cashier', 'invoice'])->latest()->take(10)->get();
        $staff = $branch->staff()->orderBy('name')->take(10)->get();

        return view('admin.branches.show', compact('restaurant', 'branch', 'categories', 'menuItems', 'orders', 'staff'));
    }

    public function edit(Restaurant $restaurant, Branch $branch): View
    {
        $this->authorize('update', $branch);

        return view('admin.branches.edit', compact('restaurant', 'branch'));
    }

    public function update(BranchRequest $request, Restaurant $restaurant, Branch $branch): RedirectResponse
    {
        $this->authorize('update', $branch);

        $data = $request->validated();

        if ($data['name'] !== $branch->name) {
            $data['slug'] = SlugGenerator::unique(Branch::class, $data['name'], ['restaurant_id' => $restaurant->id], $branch->id);
        }

        $branch->update($data);

        return back()->with('status', 'Branch updated.');
    }

    public function destroy(Restaurant $restaurant, Branch $branch): RedirectResponse
    {
        $this->authorize('delete', $branch);

        $branch->delete();

        return redirect()->route('admin.restaurants.show', [$restaurant, 'tab' => 'branches'])->with('status', 'Branch removed.');
    }
}
