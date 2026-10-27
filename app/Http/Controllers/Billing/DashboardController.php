<?php

namespace App\Http\Controllers\Billing;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $branch = $request->user()->branch;
        $windowStart = now()->subDays(29)->startOfDay();

        $invoicesWindow = $branch->invoices()
            ->where('created_at', '>=', $windowStart)
            ->get(['id', 'grand_total', 'created_at']);

        $ordersWindow = $branch->orders()
            ->where('created_at', '>=', $windowStart)
            ->get(['id', 'status', 'created_at']);

        $revenue30d = (float) $invoicesWindow->sum('grand_total');

        $stats = [
            'open_orders' => $branch->orders()->where('status', OrderStatus::Open)->count(),
            'orders_today' => $branch->orders()->whereDate('created_at', today())->count(),
            'revenue_today' => (float) $branch->invoices()->whereDate('created_at', today())->sum('grand_total'),
            'revenue_30d' => $revenue30d,
            'invoices_today' => $branch->invoices()->whereDate('created_at', today())->count(),
            'menu_items' => $branch->menuItems()->available()->count(),
            'orders_30d' => $ordersWindow->count(),
            'avg_order_value_30d' => $invoicesWindow->count() > 0 ? $revenue30d / $invoicesWindow->count() : 0.0,
        ];

        $revenueTrend = $this->dailyRevenueTrend($invoicesWindow, $windowStart);

        $orderStatusBreakdown = $this->orderStatusBreakdown($ordersWindow);

        $paymentBreakdown = Payment::query()
            ->join('orders', 'orders.id', '=', 'payments.order_id')
            ->where('orders.branch_id', $branch->id)
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

        $topItems = OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.branch_id', $branch->id)
            ->where('orders.created_at', '>=', $windowStart)
            ->selectRaw('order_items.item_name as item_name, SUM(order_items.quantity) as qty, SUM(order_items.line_total) as revenue')
            ->groupBy('order_items.item_name')
            ->orderByDesc('qty')
            ->limit(5)
            ->get();

        $recentInvoices = $branch->invoices()
            ->with(['order.items', 'order.cashier'])
            ->latest()
            ->take(7)
            ->get();

        return view('billing.dashboard', compact(
            'branch',
            'stats',
            'revenueTrend',
            'orderStatusBreakdown',
            'paymentBreakdown',
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
