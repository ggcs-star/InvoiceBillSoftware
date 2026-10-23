<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\AuthorizesBranchAccess;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryRequest;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Restaurant;
use App\Services\MenuService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    use AuthorizesBranchAccess;

    public function __construct(private readonly MenuService $menu)
    {
    }

    /**
     * Platform-wide category directory across every restaurant and branch.
     */
    public function all(Request $request): View
    {
        $this->authorize('viewAny', Category::class);

        $categories = Category::with('branch.restaurant')
            ->withCount('menuItems')
            ->when($request->filled('search'), fn ($query) => $query->where('name', 'like', '%' . $request->string('search') . '%'))
            ->when($request->filled('restaurant_id'), fn ($query) => $query->whereHas(
                'branch',
                fn ($branchQuery) => $branchQuery->where('restaurant_id', $request->integer('restaurant_id'))
            ))
            ->when($request->filled('branch_id'), fn ($query) => $query->where('branch_id', $request->integer('branch_id')))
            ->when($request->filled('status'), fn ($query) => $query->where('is_active', $request->input('status') === 'active'))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $restaurants = Restaurant::orderBy('name')->get(['id', 'name']);
        $branches = Branch::with('restaurant')->orderBy('name')->get(['id', 'name', 'restaurant_id']);

        return view('admin.categories.index', compact('categories', 'restaurants', 'branches'));
    }

    public function index(Request $request, Restaurant $restaurant, Branch $branch): View
    {
        $this->authorizeBranch($request, $branch);

        $categories = $branch->categories()
            ->withCount('menuItems')
            ->when($request->filled('search'), fn ($query) => $query->where('name', 'like', '%' . $request->string('search') . '%'))
            ->when($request->filled('status'), fn ($query) => $query->where('is_active', $request->input('status') === 'active'))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.branches.categories', compact('restaurant', 'branch', 'categories'));
    }

    public function store(CategoryRequest $request, Restaurant $restaurant, Branch $branch): RedirectResponse
    {
        $this->authorizeBranch($request, $branch);

        $this->menu->createCategory($branch, $request->validated());

        return back()->with('status', 'Category added.');
    }

    public function update(CategoryRequest $request, Restaurant $restaurant, Branch $branch, Category $category): RedirectResponse
    {
        $this->authorize('update', $category);

        $this->menu->updateCategory($category, $request->validated());

        return back()->with('status', 'Category updated.');
    }

    public function destroy(Restaurant $restaurant, Branch $branch, Category $category): RedirectResponse
    {
        $this->authorize('delete', $category);

        $category->delete();

        return back()->with('status', 'Category removed.');
    }
}
