<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PrinterConnectionType;
use App\Http\Controllers\Concerns\AuthorizesBranchAccess;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PrinterRequest;
use App\Models\Branch;
use App\Models\Printer;
use App\Models\Restaurant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PrinterController extends Controller
{
    use AuthorizesBranchAccess;

    /**
     * Platform-wide printer directory across every restaurant and branch.
     */
    public function all(Request $request): View
    {
        $this->authorize('viewAny', Printer::class);

        $printers = Printer::with('branch.restaurant')
            ->when($request->filled('search'), fn ($query) => $query->where('name', 'like', '%' . $request->string('search') . '%'))
            ->when($request->filled('restaurant_id'), fn ($query) => $query->whereHas(
                'branch',
                fn ($branchQuery) => $branchQuery->where('restaurant_id', $request->integer('restaurant_id'))
            ))
            ->when($request->filled('branch_id'), fn ($query) => $query->where('branch_id', $request->integer('branch_id')))
            ->when($request->filled('connection_type'), fn ($query) => $query->where('connection_type', $request->string('connection_type')))
            ->when($request->filled('status'), fn ($query) => $query->where(
                'is_approved',
                $request->input('status') === 'approved'
            ))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $restaurants = Restaurant::orderBy('name')->get(['id', 'name']);
        $branches = Branch::with('restaurant')->orderBy('name')->get(['id', 'name', 'restaurant_id']);
        $connectionTypes = PrinterConnectionType::cases();

        return view('admin.printers.index', compact('printers', 'restaurants', 'branches', 'connectionTypes'));
    }

    public function index(Request $request, Restaurant $restaurant, Branch $branch): View
    {
        $this->authorizeBranch($request, $branch);

        $printers = $branch->printers()
            ->when($request->filled('search'), fn ($query) => $query->where('name', 'like', '%' . $request->string('search') . '%'))
            ->when($request->filled('connection_type'), fn ($query) => $query->where('connection_type', $request->string('connection_type')))
            ->when($request->filled('status'), fn ($query) => $query->where('is_active', $request->input('status') === 'active'))
            ->paginate(10)
            ->withQueryString();

        $connectionTypes = PrinterConnectionType::cases();

        return view('admin.branches.printers', compact('restaurant', 'branch', 'printers', 'connectionTypes'));
    }

    public function store(PrinterRequest $request, Restaurant $restaurant, Branch $branch): RedirectResponse
    {
        $this->authorizeBranch($request, $branch);

        $data = $request->validated();
        $data['is_approved'] = false;

        if ($request->boolean('is_default')) {
            $branch->printers()->update(['is_default' => false]);
        }

        $branch->printers()->create($data);

        return back()->with('status', 'Printer submitted — waiting on approval before it can be used.');
    }

    public function update(PrinterRequest $request, Restaurant $restaurant, Branch $branch, Printer $printer): RedirectResponse
    {
        $this->authorize('update', $printer);

        $data = $request->validated();

        if ($request->boolean('is_default')) {
            $branch->printers()->where('id', '!=', $printer->id)->update(['is_default' => false]);
        }

        $printer->update($data);

        return back()->with('status', 'Printer updated.');
    }

    public function approve(Restaurant $restaurant, Branch $branch, Printer $printer): RedirectResponse
    {
        $this->authorize('approve', Printer::class);

        $printer->update(['is_approved' => true]);

        return back()->with('status', 'Printer approved — it can now receive print jobs.');
    }

    public function destroy(Restaurant $restaurant, Branch $branch, Printer $printer): RedirectResponse
    {
        $this->authorize('delete', $printer);

        $printer->delete();

        return back()->with('status', 'Printer removed.');
    }
}
