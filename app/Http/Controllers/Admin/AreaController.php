<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AreaRequest;
use App\Models\Area;
use App\Models\Branch;
use App\Models\Restaurant;
use App\Services\AreaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AreaController extends Controller
{
    public function __construct(private readonly AreaService $areas)
    {
    }

    public function index(Request $request, Restaurant $restaurant, Branch $branch): View
    {
        $this->authorize('viewAny', Area::class);

        $areas = $branch->areas()
            ->when($request->filled('search'), fn ($query) => $query->where('name', 'like', '%' . $request->string('search') . '%'))
            ->orderBy('sort_order')
            ->paginate(10)
            ->withQueryString();

        return view('admin.branches.areas', compact('restaurant', 'branch', 'areas'));
    }

    public function store(AreaRequest $request, Restaurant $restaurant, Branch $branch): RedirectResponse
    {
        $this->authorize('create', Area::class);

        $this->areas->create($branch, $request->safe()->except('image'), $request->file('image'));

        return back()->with('status', 'Area added.');
    }

    public function update(AreaRequest $request, Restaurant $restaurant, Branch $branch, Area $area): RedirectResponse
    {
        $this->authorize('update', $area);

        $this->areas->update($area, $request->safe()->except('image'), $request->file('image'));

        return back()->with('status', 'Area updated.');
    }

    public function destroy(Restaurant $restaurant, Branch $branch, Area $area): RedirectResponse
    {
        $this->authorize('delete', $area);

        $area->delete();

        return back()->with('status', 'Area removed.');
    }
}
