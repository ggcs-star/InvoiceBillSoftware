<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\SlugGenerator;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BranchRequest;
use App\Models\Branch;
use App\Models\Restaurant;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BranchController extends Controller
{
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

        return redirect()->route('admin.restaurants.edit', $restaurant)->with('status', 'Branch removed.');
    }
}
