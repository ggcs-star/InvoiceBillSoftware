<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\SlugGenerator;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RestaurantRequest;
use App\Models\Restaurant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RestaurantController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Restaurant::class);

        $restaurants = Restaurant::withCount('branches')
            ->when($request->filled('search'), fn ($query) => $query->where('name', 'like', '%' . $request->string('search') . '%'))
            ->when($request->filled('status'), fn ($query) => $query->where('is_active', $request->string('status') === 'active'))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.restaurants.index', compact('restaurants'));
    }

    public function create(): View
    {
        $this->authorize('create', Restaurant::class);

        return view('admin.restaurants.create');
    }

    public function store(RestaurantRequest $request): RedirectResponse
    {
        $this->authorize('create', Restaurant::class);

        $restaurant = Restaurant::create([
            ...$request->validated(),
            'slug' => SlugGenerator::unique(Restaurant::class, $request->input('name')),
        ]);

        return redirect()->route('admin.restaurants.edit', $restaurant)
            ->with('status', 'Restaurant created. Add a branch to start taking orders.');
    }

    public function edit(Request $request, Restaurant $restaurant): View
    {
        $this->authorize('update', $restaurant);

        $branches = $restaurant->branches()
            ->withCount(['menuItems', 'staff'])
            ->when($request->filled('search'), fn ($query) => $query->where('name', 'like', '%' . $request->string('search') . '%'))
            ->when($request->filled('status'), fn ($query) => $query->where('is_active', $request->string('status') === 'active'))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.restaurants.edit', compact('restaurant', 'branches'));
    }

    public function update(RestaurantRequest $request, Restaurant $restaurant): RedirectResponse
    {
        $this->authorize('update', $restaurant);

        $data = $request->validated();

        if ($data['name'] !== $restaurant->name) {
            $data['slug'] = SlugGenerator::unique(Restaurant::class, $data['name'], [], $restaurant->id);
        }

        $restaurant->update($data);

        return back()->with('status', 'Restaurant updated.');
    }

    public function destroy(Restaurant $restaurant): RedirectResponse
    {
        $this->authorize('delete', $restaurant);

        $restaurant->delete();

        return redirect()->route('admin.restaurants.index')->with('status', 'Restaurant removed.');
    }
}
