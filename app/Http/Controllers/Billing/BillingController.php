<?php

namespace App\Http\Controllers\Billing;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BillingController extends Controller
{
    public function index(Request $request): View
    {
        $branch = $request->user()->branch;

        $categories = $branch->categories()
            ->active()
            ->with([
                'menuItems' => fn ($query) => $query->available()->orderBy('sort_order'),
                'menuItems.variations' => fn ($query) => $query->where('is_active', true)->orderBy('sort_order'),
            ])
            ->orderBy('sort_order')
            ->get();

        $openOrders = $branch->orders()
            ->where('status', OrderStatus::Open)
            ->latest()
            ->get();

        return view('billing.index', [
            'branch' => $branch->load('restaurant'),
            'categories' => $categories,
            'openOrders' => $openOrders,
            'apiToken' => session('api_token'),
        ]);
    }

    public function show(Request $request, Order $order): View
    {
        $this->authorize('view', $order);

        $order->load(['items', 'invoice']);

        $branch = $order->branch->load('restaurant');

        $categories = $branch->categories()
            ->active()
            ->with(['menuItems' => fn ($query) => $query->available()->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get();

        return view('billing.index', [
            'branch' => $branch,
            'categories' => $categories,
            'openOrders' => $branch->orders()->where('status', OrderStatus::Open)->latest()->get(),
            'activeOrder' => new OrderResource($order),
            'apiToken' => session('api_token'),
        ]);
    }
}
