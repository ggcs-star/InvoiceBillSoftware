<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $windowStart = now()->subDays(29)->startOfDay();

        $invoicesWindow = Invoice::where('created_at', '>=', $windowStart)
            ->get(['id', 'branch_id', 'grand_total', 'created_at']);

        $ordersWindow = Order::where('created_at', '>=', $windowStart)
            ->get(['id', 'branch_id', 'status', 'created_at']);

        $revenue30d = (float) $invoicesWindow->sum('grand_total');

        $stats = [
            'restaurants' => Restaurant::count(),
            'branches' => Branch::count(),
            'active_staff' => User::where('is_active', true)->count(),
            'open_orders' => Order::where('status', OrderStatus::Open)->count(),
            'orders_today' => Order::whereDate('created_at', today())->count(),
            'revenue_today' => (float) Invoice::whereDate('created_at', today())->sum('grand_total'),
            'revenue_30d' => $revenue30d,
            'invoices_today' => Invoice::whereDate('created_at', today())->count(),
            'orders_30d' => $ordersWindow->count(),
            'avg_order_value_30d' => $invoicesWindow->count() > 0 ? $revenue30d / $invoicesWindow->count() : 0.0,
        ];

        $revenueTrend = $this->dailyRevenueTrend($invoicesWindow, $windowStart);

        $orderStatusBreakdown = $this->orderStatusBreakdown($ordersWindow);

        $paymentBreakdown = Payment::query()
            ->join('orders', 'orders.id', '=', 'payments.order_id')
            ->where('payments.status', PaymentStatus::Success)
            ->where('payments.created_at', '>=', $windowStart)
            ->selectRaw('payments.method as method, SUM(payments.amount) as total, COUNT(*) as cnt')
            ->groupBy('payments.method')
            ->get()
            ->map(fn (Payment $row) => [
                'label' => $row->method->label(),
                'total' => (float) $row->total,
                'count' => (int) $row->cnt,
            ])
            ->values()
            ->all();

        $invoiceAgg = Invoice::where('created_at', '>=', $windowStart)
            ->selectRaw('branch_id, COUNT(*) as cnt, SUM(grand_total) as revenue')
            ->groupBy('branch_id')
            ->get()
            ->keyBy('branch_id');

        $orderAgg = Order::where('created_at', '>=', $windowStart)
            ->selectRaw('branch_id, COUNT(*) as cnt')
            ->groupBy('branch_id')
            ->get()
            ->keyBy('branch_id');

        $branchPerformance = Branch::with('restaurant')
            ->withCount('staff')
            ->get()
            ->map(function (Branch $branch) use ($invoiceAgg, $orderAgg) {
                $inv = $invoiceAgg->get($branch->id);
                $revenue = $inv ? (float) $inv->revenue : 0.0;
                $invoiceCount = $inv ? (int) $inv->cnt : 0;

                return (object) [
                    'branch' => $branch,
                    'orders' => (int) ($orderAgg->get($branch->id)->cnt ?? 0),
                    'revenue' => $revenue,
                    'avg_order_value' => $invoiceCount > 0 ? $revenue / $invoiceCount : 0.0,
                ];
            })
            ->sortByDesc('revenue')
            ->values();

        $topItems = OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.created_at', '>=', $windowStart)
            ->selectRaw('order_items.item_name as item_name, SUM(order_items.quantity) as qty, SUM(order_items.line_total) as revenue')
            ->groupBy('order_items.item_name')
            ->orderByDesc('qty')
            ->limit(5)
            ->get();

        $recentInvoices = Invoice::with(['branch.restaurant', 'order.items', 'order.cashier'])
            ->latest()
            ->take(7)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'revenueTrend',
            'orderStatusBreakdown',
            'paymentBreakdown',
            'branchPerformance',
            'topItems',
            'recentInvoices',
        ));
    }

    private function dailyRevenueTrend($invoices, Carbon $windowStart): array
    {
        $byDate = $invoices->groupBy(fn ($invoice) => $invoice->created_at->format('Y-m-d'));

        $labels = [];
        $data = [];

        for ($i = 0; $i < 30; $i++) {
            $date = $windowStart->copy()->addDays($i);
            $key = $date->format('Y-m-d');

            $labels[] = $date->format('M j');
            $data[] = round((float) $byDate->get($key, collect())->sum('grand_total'), 2);
        }

        return ['labels' => $labels, 'data' => $data];
    }

    private function orderStatusBreakdown($orders): array
    {
        $byStatus = $orders->groupBy(fn ($order) => $order->status->value);

        return collect(OrderStatus::cases())
            ->map(fn (OrderStatus $status) => [
                'label' => $status->label(),
                'count' => $byStatus->get($status->value, collect())->count(),
            ])
            ->filter(fn ($row) => $row['count'] > 0)
            ->values()
            ->all();
    }
}
