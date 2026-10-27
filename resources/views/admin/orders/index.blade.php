@extends('layouts.app')

@section('title', 'Orders')
@section('header', 'Orders')

@section('content')
    <div class="bg-white rounded-2xl border border-ink-100 shadow-card p-4 mb-4">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[180px]">
                <label class="block text-xs font-medium text-ink-500 mb-1">Search</label>
                <div class="relative">
                    <svg viewBox="0 0 24 24" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-ink-400" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="7"/>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Order # or customer name"
                           class="w-full rounded-lg border-ink-200 bg-white pl-10 pr-4 py-2 shadow-sm text-sm">
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-ink-500 mb-1">Restaurant</label>
                <x-searchable-select name="restaurant_id" placeholder="All Restaurants"
                    :options="$restaurants->map(fn ($r) => ['value' => $r->id, 'label' => $r->name])"
                    :selected="request('restaurant_id')" />
            </div>
            <div>
                <label class="block text-xs font-medium text-ink-500 mb-1">Branch</label>
                <x-searchable-select name="branch_id" placeholder="All Branches"
                    :options="$branches->map(fn ($b) => ['value' => $b->id, 'label' => $b->restaurant->name . ' — ' . $b->name])"
                    :selected="request('branch_id')" />
            </div>
            <div>
                <label class="block text-xs font-medium text-ink-500 mb-1">Status</label>
                <x-searchable-select name="status" placeholder="All Status"
                    :options="collect($statuses)->map(fn ($s) => ['value' => $s->value, 'label' => $s->label()])"
                    :selected="request('status')" />
            </div>
            <div>
                <label class="block text-xs font-medium text-ink-500 mb-1">From</label>
                <input type="date" name="from" value="{{ request('from') }}" class="rounded-lg border-ink-200 px-3 py-2 shadow-sm text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-ink-500 mb-1">To</label>
                <input type="date" name="to" value="{{ request('to') }}" class="rounded-lg border-ink-200 px-3 py-2 shadow-sm text-sm">
            </div>
            <button type="submit" class="rounded-lg bg-brand-600 px-4 py-2 text-white text-sm font-medium hover:bg-brand-700">Filter</button>
            @if (request()->anyFilled(['search', 'restaurant_id', 'branch_id', 'status', 'from', 'to']))
                <a href="{{ route('admin.orders.index') }}" class="rounded-lg border border-ink-200 px-4 py-2 text-sm font-medium text-ink-600 hover:bg-ink-50">Clear</a>
            @endif
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-ink-100 shadow-card overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="text-left text-ink-500 border-b bg-ink-50/60">
                <tr>
                    <th class="px-5 py-3">Order #</th>
                    <th class="px-5 py-3">Restaurant</th>
                    <th class="px-5 py-3">Branch</th>
                    <th class="px-5 py-3">Customer</th>
                    <th class="px-5 py-3">Cashier</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3 text-right">Total</th>
                    <th class="px-5 py-3">Date</th>
                    <th class="px-5 py-3 text-right">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orders as $order)
                    <tr class="border-b last:border-0">
                        <td class="px-5 py-3 font-medium text-ink-900">{{ $order->order_number }}</td>
                        <td class="px-5 py-3">
                            <a href="{{ route('admin.restaurants.show', $order->branch->restaurant) }}" class="text-brand-600 hover:underline">
                                {{ $order->branch->restaurant->name }}
                            </a>
                        </td>
                        <td class="px-5 py-3 text-ink-500">{{ $order->branch->name }}</td>
                        <td class="px-5 py-3">{{ $order->customer_name ?: '—' }}</td>
                        <td class="px-5 py-3 text-ink-500">{{ $order->cashier->name }}</td>
                        <td class="px-5 py-3">
                            <span class="px-2 py-0.5 rounded-full text-xs
                                {{ $order->status->value === 'completed' ? 'bg-green-100 text-green-700' : ($order->status->value === 'cancelled' ? 'bg-red-100 text-red-700' : 'bg-brand-50 text-brand-600') }}">
                                {{ $order->status->label() }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-right">₹{{ number_format($order->grand_total, 2) }}</td>
                        <td class="px-5 py-3 text-ink-500">{{ $order->created_at->format('d M, h:i A') }}</td>
                        <td class="px-5 py-3 text-right">
                            @if ($order->invoice)
                                <a href="{{ route('billing.invoices.print', $order->invoice) }}" target="_blank" class="text-brand-600 hover:underline">View Invoice</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="px-5 py-10 text-center text-ink-400">No orders match these filters.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <x-pagination :paginator="$orders" />
@endsection
