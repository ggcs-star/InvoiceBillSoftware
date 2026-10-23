<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Concerns\AuthorizesBranchAccess;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Order;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    use AuthorizesBranchAccess;

    /**
     * Platform-wide order ledger across every restaurant and branch.
     */
    public function all(Request $request): View
    {
        $this->authorize('viewAny', Order::class);

        $orders = Order::with(['branch.restaurant', 'cashier', 'invoice'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search');
                $query->where(fn ($q) => $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%"));
            })
            ->when($request->filled('restaurant_id'), fn ($query) => $query->whereHas(
                'branch',
                fn ($branchQuery) => $branchQuery->where('restaurant_id', $request->integer('restaurant_id'))
            ))
            ->when($request->filled('branch_id'), fn ($query) => $query->where('branch_id', $request->integer('branch_id')))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->when($request->filled('from'), fn ($query) => $query->whereDate('created_at', '>=', $request->date('from')))
            ->when($request->filled('to'), fn ($query) => $query->whereDate('created_at', '<=', $request->date('to')))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $restaurants = Restaurant::orderBy('name')->get(['id', 'name']);
        $branches = Branch::with('restaurant')->orderBy('name')->get(['id', 'name', 'restaurant_id']);
        $statuses = OrderStatus::cases();

        return view('admin.orders.index', compact('orders', 'restaurants', 'branches', 'statuses'));
    }

    public function index(Request $request, Restaurant $restaurant, Branch $branch): View
    {
        $this->authorizeBranch($request, $branch);

        $orders = $branch->orders()
            ->with(['cashier', 'invoice'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search');
                $query->where(fn ($q) => $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%"));
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->when($request->filled('from'), fn ($query) => $query->whereDate('created_at', '>=', $request->date('from')))
            ->when($request->filled('to'), fn ($query) => $query->whereDate('created_at', '<=', $request->date('to')))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $statuses = OrderStatus::cases();

        return view('admin.branches.orders', compact('restaurant', 'branch', 'orders', 'statuses'));
    }
}
