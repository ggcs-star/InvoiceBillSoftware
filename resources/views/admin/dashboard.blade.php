@extends('layouts.app')

@section('title', 'Dashboard')
@section('header', 'Dashboard')

@section('content')
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 mb-6">
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-brand-500 to-brand-600 p-5 text-white shadow-card">
            <div class="absolute -right-6 -top-6 h-24 w-24 rounded-full bg-white/10"></div>
            <div class="relative text-sm text-white/80">Today's Revenue</div>
            <div class="relative mt-2 text-2xl font-bold">₹{{ number_format($stats['revenue_today'], 2) }}</div>
            <div class="relative mt-1 text-xs text-white/70">{{ $stats['invoices_today'] }} invoice(s) billed today</div>
        </div>

        <div class="rounded-2xl bg-white p-5 shadow-card border border-ink-100">
            <div class="text-sm text-ink-500">Last 30 Days</div>
            <div class="mt-2 text-2xl font-bold text-ink-900">₹{{ number_format($stats['revenue_30d'], 2) }}</div>
            <div class="mt-1 text-xs text-ink-400">Total revenue across all branches</div>
        </div>

        <div class="rounded-2xl bg-white p-5 shadow-card border border-ink-100">
            <div class="text-sm text-ink-500">Avg. Order Value</div>
            <div class="mt-2 text-2xl font-bold text-ink-900">₹{{ number_format($stats['avg_order_value_30d'], 2) }}</div>
            <div class="mt-1 text-xs text-ink-400">Based on last 30 days</div>
        </div>

        <div class="rounded-2xl bg-white p-5 shadow-card border border-ink-100">
            <div class="text-sm text-ink-500">Open Orders</div>
            <div class="mt-2 text-2xl font-bold text-ink-900">{{ $stats['open_orders'] }}</div>
            <div class="mt-1 text-xs text-ink-400">{{ $stats['orders_today'] }} started today</div>
        </div>

        <div class="rounded-2xl bg-white p-5 shadow-card border border-ink-100">
            <div class="text-sm text-ink-500">Orders (30 Days)</div>
            <div class="mt-2 text-2xl font-bold text-ink-900">{{ number_format($stats['orders_30d']) }}</div>
            <div class="mt-1 text-xs text-ink-400">Across all branches</div>
        </div>

        <div class="rounded-2xl bg-white p-5 shadow-card border border-ink-100">
            <div class="text-sm text-ink-500">Restaurants &amp; Branches</div>
            <div class="mt-2 text-2xl font-bold text-ink-900">{{ $stats['restaurants'] }} / {{ $stats['branches'] }}</div>
            <div class="mt-1 text-xs text-ink-400">{{ $stats['active_staff'] }} active staff account(s)</div>
        </div>
    </div>

    {{-- Revenue trend --}}
    <div class="rounded-2xl bg-white shadow-card border border-ink-100 mb-6">
        <div class="flex items-center justify-between px-6 py-4 border-b border-ink-100">
            <div>
                <h2 class="font-semibold text-ink-900">Revenue Trend</h2>
                <p class="text-xs text-ink-400">Last 30 days, all restaurants &amp; branches combined</p>
            </div>
        </div>
        <div class="p-6">
            <div class="h-72">
                <canvas id="revenueTrendChart"></canvas>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2 mb-6">
        <div class="rounded-2xl bg-white shadow-card border border-ink-100">
            <div class="px-6 py-4 border-b border-ink-100">
                <h2 class="font-semibold text-ink-900">Orders by Status</h2>
                <p class="text-xs text-ink-400">Last 30 days</p>
            </div>
            <div class="p-6">
                <div class="h-64">
                    <canvas id="orderStatusChart"></canvas>
                </div>
            </div>
        </div>

        <div class="rounded-2xl bg-white shadow-card border border-ink-100">
            <div class="px-6 py-4 border-b border-ink-100">
                <h2 class="font-semibold text-ink-900">Payment Methods</h2>
                <p class="text-xs text-ink-400">Successful payments, last 30 days</p>
            </div>
            <div class="p-6">
                <div class="h-64">
                    <canvas id="paymentMethodChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Branch performance --}}
    <div class="rounded-2xl bg-white shadow-card border border-ink-100 mb-6">
        <div class="flex items-center justify-between px-6 py-4 border-b border-ink-100">
            <div>
                <h2 class="font-semibold text-ink-900">Branch Performance</h2>
                <p class="text-xs text-ink-400">Last 30 days, ranked by revenue</p>
            </div>
            <a href="{{ route('admin.branches.index') }}" class="text-sm font-medium text-brand-600 hover:text-brand-700">Manage branches &rarr;</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-ink-100 text-left text-xs font-medium uppercase tracking-wide text-ink-400">
                        <th class="px-6 py-3">Branch</th>
                        <th class="px-6 py-3">Restaurant</th>
                        <th class="px-6 py-3 text-right">Orders</th>
                        <th class="px-6 py-3 text-right">Revenue</th>
                        <th class="px-6 py-3 text-right">Avg. Order</th>
                        <th class="px-6 py-3 text-right">Staff</th>
                        <th class="px-6 py-3 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ink-100">
                    @forelse ($branchPerformance as $row)
                        <tr class="hover:bg-brand-50/40">
                            <td class="px-6 py-3.5 font-medium text-ink-900">{{ $row->branch->name }}</td>
                            <td class="px-6 py-3.5 text-ink-500">{{ $row->branch->restaurant->name }}</td>
                            <td class="px-6 py-3.5 text-right text-ink-700">{{ number_format($row->orders) }}</td>
                            <td class="px-6 py-3.5 text-right font-semibold text-ink-900">₹{{ number_format($row->revenue, 2) }}</td>
                            <td class="px-6 py-3.5 text-right text-ink-700">₹{{ number_format($row->avg_order_value, 2) }}</td>
                            <td class="px-6 py-3.5 text-right text-ink-700">{{ $row->branch->staff_count }}</td>
                            <td class="px-6 py-3.5 text-center">
                                @if ($row->branch->is_active)
                                    <span class="inline-flex items-center rounded-full bg-green-50 px-2.5 py-0.5 text-xs font-medium text-green-700">Active</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-ink-100 px-2.5 py-0.5 text-xs font-medium text-ink-500">Inactive</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-sm text-ink-400">No branches yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        {{-- Top selling items --}}
        <div class="rounded-2xl bg-white shadow-card border border-ink-100">
            <div class="px-6 py-4 border-b border-ink-100">
                <h2 class="font-semibold text-ink-900">Top Selling Items</h2>
                <p class="text-xs text-ink-400">Last 30 days, all branches</p>
            </div>
            <ul class="divide-y divide-ink-100">
                @forelse ($topItems as $item)
                    <li class="flex items-center gap-4 px-6 py-3.5">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-brand-50 text-xs font-semibold text-brand-600">
                            {{ Str::substr($item->item_name, 0, 1) }}
                        </span>
                        <div class="min-w-0 flex-1">
                            <div class="truncate text-sm font-medium text-ink-900">{{ $item->item_name }}</div>
                            <div class="text-xs text-ink-400">{{ (int) $item->qty }} sold</div>
                        </div>
                        <div class="text-sm font-semibold text-ink-900">₹{{ number_format($item->revenue, 2) }}</div>
                    </li>
                @empty
                    <li class="px-6 py-10 text-center text-sm text-ink-400">No sales data yet.</li>
                @endforelse
            </ul>
        </div>

        {{-- Recent transactions --}}
        <div class="rounded-2xl bg-white shadow-card border border-ink-100">
            <div class="flex items-center justify-between px-6 py-4 border-b border-ink-100">
                <h2 class="font-semibold text-ink-900">Recent Transactions</h2>
                <a href="{{ route('admin.restaurants.index') }}" class="text-sm font-medium text-brand-600 hover:text-brand-700">Manage restaurants &rarr;</a>
            </div>

            <ul class="divide-y divide-ink-100">
                @forelse ($recentInvoices as $invoice)
                    @php $firstItem = $invoice->order->items->first(); @endphp
                    <li class="flex items-center gap-4 px-6 py-3.5">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-brand-50 text-xs font-semibold text-brand-600">
                            {{ Str::substr($firstItem->item_name ?? $invoice->branch->name, 0, 1) }}
                        </span>
                        <div class="min-w-0 flex-1">
                            <div class="truncate text-sm font-medium text-ink-900">
                                {{ $firstItem->item_name ?? 'Order ' . $invoice->order->order_number }}
                                @if ($invoice->order->items->count() > 1)
                                    <span class="text-ink-400 font-normal">+{{ $invoice->order->items->count() - 1 }} more</span>
                                @endif
                            </div>
                            <div class="text-xs text-ink-400">{{ $invoice->branch->restaurant->name }} — {{ $invoice->branch->name }} &middot; {{ $invoice->created_at->format('h:i A') }}</div>
                        </div>
                        <div class="text-sm font-semibold text-ink-900">₹{{ number_format($invoice->grand_total, 2) }}</div>
                    </li>
                @empty
                    <li class="px-6 py-10 text-center text-sm text-ink-400">No invoices yet.</li>
                @endforelse
            </ul>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.9/dist/chart.umd.min.js"></script>
    <script>
        const inkMuted = '#898781';
        const inkSecondary = '#52514e';
        const gridColor = '#e1e0d9';

        Chart.defaults.font.family = "'Figtree', ui-sans-serif, system-ui, -apple-system, 'Segoe UI', sans-serif";
        Chart.defaults.color = inkSecondary;

        new Chart(document.getElementById('revenueTrendChart'), {
            type: 'line',
            data: {
                labels: @json($revenueTrend['labels']),
                datasets: [{
                    label: 'Revenue',
                    data: @json($revenueTrend['data']),
                    borderColor: '#f97316',
                    backgroundColor: 'rgba(249, 115, 22, 0.1)',
                    borderWidth: 2,
                    pointRadius: 0,
                    pointHoverRadius: 4,
                    pointBackgroundColor: '#f97316',
                    tension: 0.35,
                    fill: true,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => '₹' + ctx.parsed.y.toLocaleString('en-IN', { minimumFractionDigits: 2 }),
                        },
                    },
                },
                scales: {
                    x: { grid: { display: false }, ticks: { color: inkMuted, maxTicksLimit: 10 } },
                    y: {
                        grid: { color: gridColor, drawTicks: false },
                        border: { display: false },
                        ticks: { color: inkMuted, callback: (v) => '₹' + v.toLocaleString('en-IN') },
                    },
                },
            },
        });

        new Chart(document.getElementById('orderStatusChart'), {
            type: 'doughnut',
            data: {
                labels: @json(array_column($orderStatusBreakdown, 'label')),
                datasets: [{
                    data: @json(array_column($orderStatusBreakdown, 'count')),
                    backgroundColor: ['#2a78d6', '#eb6834', '#1baf7a', '#4a3aa7'],
                    borderColor: '#ffffff',
                    borderWidth: 2,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: { position: 'bottom', labels: { usePointStyle: true, pointStyle: 'circle', color: inkSecondary } },
                },
            },
        });

        new Chart(document.getElementById('paymentMethodChart'), {
            type: 'doughnut',
            data: {
                labels: @json(array_column($paymentBreakdown, 'label')),
                datasets: [{
                    data: @json(array_column($paymentBreakdown, 'total')),
                    backgroundColor: ['#2a78d6', '#eb6834', '#1baf7a', '#4a3aa7'],
                    borderColor: '#ffffff',
                    borderWidth: 2,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: { position: 'bottom', labels: { usePointStyle: true, pointStyle: 'circle', color: inkSecondary } },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => ctx.label + ': ₹' + ctx.parsed.toLocaleString('en-IN', { minimumFractionDigits: 2 }),
                        },
                    },
                },
            },
        });
    </script>
@endsection
