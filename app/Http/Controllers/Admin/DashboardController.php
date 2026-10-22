<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Restaurant;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $windowStart = now()->subDays(29)->startOfDay();

        $stats = [
            'restaurants' => Restaurant::count(),
            'branches' => Branch::count(),
            'open_orders' => Order::where('status', OrderStatus::Open)->count(),
            'orders_today' => Order::whereDate('created_at', today())->count(),
            'revenue_today' => (float) Invoice::whereDate('created_at', today())->sum('grand_total'),
            'revenue_30d' => (float) Invoice::where('created_at', '>=', $windowStart)->sum('grand_total'),
            'invoices_today' => Invoice::whereDate('created_at', today())->count(),
        ];

        $recentInvoices = Invoice::with(['branch.restaurant', 'order.items', 'order.cashier'])
            ->latest()
            ->take(7)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentInvoices'));
    }
}
