<?php

namespace App\Http\Controllers\Billing;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $branch = $request->user()->branch;
        $windowStart = now()->subDays(29)->startOfDay();

        $stats = [
            'open_orders' => $branch->orders()->where('status', OrderStatus::Open)->count(),
            'orders_today' => $branch->orders()->whereDate('created_at', today())->count(),
            'revenue_today' => (float) $branch->invoices()->whereDate('created_at', today())->sum('grand_total'),
            'revenue_30d' => (float) $branch->invoices()->where('created_at', '>=', $windowStart)->sum('grand_total'),
            'invoices_today' => $branch->invoices()->whereDate('created_at', today())->count(),
            'menu_items' => $branch->menuItems()->available()->count(),
        ];

        $recentInvoices = $branch->invoices()
            ->with(['order.items', 'order.cashier'])
            ->latest()
            ->take(7)
            ->get();

        return view('billing.dashboard', compact('branch', 'stats', 'recentInvoices'));
    }
}
