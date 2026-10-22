@extends('layouts.app')

@section('title', 'Orders — ' . $branch->name)
@section('header', $restaurant->name . ' — ' . $branch->name)

@section('content')
    @include('admin.branches._nav')

    <div class="bg-white rounded-2xl border border-ink-100 shadow-card p-4 mb-4">
        <form method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-3 items-end">
            <div class="sm:col-span-2">
                <label class="block text-xs font-medium text-ink-500 mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Order # or customer name"
                       class="w-full rounded-lg border-ink-200 shadow-sm text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-ink-500 mb-1">Status</label>
                <select name="status" class="w-full rounded-lg border-ink-200 shadow-sm text-sm">
                    <option value="">All</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status->value }}" {{ request('status') === $status->value ? 'selected' : '' }}>
                            {{ $status->label() }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block text-xs font-medium text-ink-500 mb-1">From</label>
                    <input type="date" name="from" value="{{ request('from') }}" class="w-full rounded-lg border-ink-200 shadow-sm text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-ink-500 mb-1">To</label>
                    <input type="date" name="to" value="{{ request('to') }}" class="w-full rounded-lg border-ink-200 shadow-sm text-sm">
                </div>
            </div>
            <div class="sm:col-span-4 flex gap-2">
                <button type="submit" class="rounded-lg bg-brand-600 px-4 py-2 text-white text-sm font-medium hover:bg-brand-700">Filter</button>
                @if (request()->anyFilled(['search', 'status', 'from', 'to']))
                    <a href="{{ route('admin.branches.orders.index', [$restaurant, $branch]) }}" class="rounded-lg border border-ink-200 px-4 py-2 text-sm font-medium text-ink-600 hover:bg-ink-50">Clear</a>
                @endif
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-ink-100 shadow-card overflow-hidden">
        <table class="w-full text-sm">
            <thead class="text-left text-ink-500 border-b">
                <tr>
                    <th class="px-5 py-3">Order #</th>
                    <th class="px-5 py-3">Customer</th>
                    <th class="px-5 py-3">Cashier</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3 text-right">Total</th>
                    <th class="px-5 py-3">Date</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orders as $order)
                    <tr class="border-b last:border-0">
                        <td class="px-5 py-3 font-medium">{{ $order->order_number }}</td>
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
                    <tr><td colspan="7" class="px-5 py-10 text-center text-ink-400">No orders match these filters.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $orders->links() }}</div>
@endsection
