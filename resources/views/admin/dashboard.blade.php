@extends('layouts.app')

@section('title', 'Dashboard')
@section('header', 'Dashboard')

@section('content')
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-6">
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
            <div class="text-sm text-ink-500">Open Orders</div>
            <div class="mt-2 text-2xl font-bold text-ink-900">{{ $stats['open_orders'] }}</div>
            <div class="mt-1 text-xs text-ink-400">{{ $stats['orders_today'] }} started today</div>
        </div>

        <div class="rounded-2xl bg-white p-5 shadow-card border border-ink-100">
            <div class="text-sm text-ink-500">Restaurants</div>
            <div class="mt-2 text-2xl font-bold text-ink-900">{{ $stats['restaurants'] }}</div>
            <div class="mt-1 text-xs text-ink-400">{{ $stats['branches'] }} branch(es) across all restaurants</div>
        </div>
    </div>

    <div class="rounded-2xl bg-white shadow-card border border-ink-100">
        <div class="flex items-center justify-between px-6 py-4 border-b border-ink-100">
            <h2 class="font-semibold text-ink-900">Recent Transactions</h2>
            <a href="{{ route('admin.restaurants.index') }}" class="text-sm font-medium text-brand-600 hover:text-brand-700">Manage restaurants &rarr;</a>
        </div>

        <ul class="divide-y divide-ink-100">
            @forelse ($recentInvoices as $invoice)
                @php $firstItem = $invoice->order->items->first(); @endphp
                <li class="flex items-center gap-4 px-6 py-4">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-brand-50 text-sm font-semibold text-brand-600">
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
@endsection
