<?php

namespace App\Http\Controllers\Admin;

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
    public function __construct(private readonly MenuService $menu)
    {
    }

    public function index(Request $request, Restaurant $restaurant, Branch $branch): View
    {
        $this->authorize('viewAny', Category::class);

        $categories = $branch->categories()
            ->withCount('menuItems')
            ->when($request->filled('search'), fn ($query) => $query->where('name', 'like', '%' . $request->string('search') . '%'))
            ->orderBy('sort_order')
            ->paginate(10)
            ->withQueryString();

        return view('admin.branches.categories', compact('restaurant', 'branch', 'categories'));
    }

    public function store(CategoryRequest $request, Restaurant $restaurant, Branch $branch): RedirectResponse
    {
        $this->authorize('create', Category::class);

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
