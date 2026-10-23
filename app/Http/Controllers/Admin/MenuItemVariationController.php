<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\AuthorizesBranchAccess;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MenuItemVariationRequest;
use App\Models\Branch;
use App\Models\MenuItem;
use App\Models\MenuItemVariation;
use App\Models\Restaurant;
use Illuminate\Http\RedirectResponse;

class MenuItemVariationController extends Controller
{
    use AuthorizesBranchAccess;

    public function store(MenuItemVariationRequest $request, Restaurant $restaurant, Branch $branch, MenuItem $menuItem): RedirectResponse
    {
        $this->authorizeBranch($request, $branch);

        $menuItem->variations()->create([
            ...$request->validated(),
            'sort_order' => $menuItem->variations()->max('sort_order') + 1,
        ]);

        return back()->with('status', 'Variation added.');
    }

    public function update(MenuItemVariationRequest $request, Restaurant $restaurant, Branch $branch, MenuItem $menuItem, MenuItemVariation $variation): RedirectResponse
    {
        $this->authorize('update', $variation);

        $variation->update($request->validated());

        return back()->with('status', 'Variation updated.');
    }

    public function destroy(Restaurant $restaurant, Branch $branch, MenuItem $menuItem, MenuItemVariation $variation): RedirectResponse
    {
        $this->authorize('delete', $variation);

        $variation->delete();

        return back()->with('status', 'Variation removed.');
    }
}
