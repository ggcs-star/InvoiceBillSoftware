<?php

namespace App\Http\Controllers\Admin;

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
    public function index(Request $request, Restaurant $restaurant, Branch $branch): View
    {
        $this->authorize('viewAny', Printer::class);

        $printers = $branch->printers()
            ->when($request->filled('search'), fn ($query) => $query->where('name', 'like', '%' . $request->string('search') . '%'))
            ->paginate(10)
            ->withQueryString();

        return view('admin.branches.printers', compact('restaurant', 'branch', 'printers'));
    }

    public function store(PrinterRequest $request, Restaurant $restaurant, Branch $branch): RedirectResponse
    {
        $this->authorize('create', Printer::class);

        $data = $request->validated();

        if ($request->boolean('is_default')) {
            $branch->printers()->update(['is_default' => false]);
        }

        $branch->printers()->create($data);

        return back()->with('status', 'Printer added.');
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

    public function destroy(Restaurant $restaurant, Branch $branch, Printer $printer): RedirectResponse
    {
        $this->authorize('delete', $printer);

        $printer->delete();

        return back()->with('status', 'Printer removed.');
    }
}
