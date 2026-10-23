@extends('layouts.app')

@section('title', $branch->name)
@section('header', $restaurant->name . ' — ' . $branch->name)

@section('content')
    <a href="{{ route('admin.restaurants.show', [$restaurant, 'tab' => 'branches']) }}" class="inline-flex items-center gap-1 text-sm text-ink-500 hover:text-brand-600 mb-4">
        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
        Back to {{ $restaurant->name }}
    </a>

    <div x-data="{ tab: 'details' }" class="space-y-6">
        <div class="flex items-center justify-between">
            <div class="flex flex-wrap gap-2 border-b">
                @foreach (['details' => 'Details', 'categories' => 'Categories', 'menu-items' => 'Menu Items', 'orders' => 'Orders', 'staff' => 'Staff'] as $key => $label)
                    <button @click="tab = '{{ $key }}'"
                            :class="tab === '{{ $key }}' ? 'border-brand-600 text-brand-600' : 'border-transparent text-ink-500'"
                            class="px-4 py-2 text-sm font-medium border-b-2 -mb-px whitespace-nowrap">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
            <a href="{{ route('admin.branches.edit', [$restaurant, $branch]) }}"
               class="flex items-center gap-1.5 rounded-lg border border-ink-200 px-3 py-1.5 text-sm font-medium text-ink-600 hover:bg-ink-50 whitespace-nowrap ml-4">
                <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 20h9"/>
                    <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/>
                </svg>
                Edit Branch
            </a>
        </div>

        {{-- Details --}}
        <div x-show="tab === 'details'" class="bg-white rounded-2xl border border-ink-100 shadow-card p-6 max-w-xl">
            <div class="flex items-center gap-4 mb-6">
                <span class="flex h-14 w-14 items-center justify-center rounded-xl bg-brand-50 text-brand-600">
                    <svg viewBox="0 0 24 24" class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="9"/>
                        <line x1="12" y1="11" x2="12" y2="16"/>
                        <circle cx="12" cy="7.5" r="0.5" fill="currentColor"/>
                    </svg>
                </span>
                <div>
                    <div class="text-lg font-bold text-ink-900">{{ $branch->name }}</div>
                    <div class="text-sm text-ink-500">{{ $branch->locality ?: ($branch->address ?: 'No area set') }}</div>
                </div>
            </div>

            <dl class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <dt class="text-ink-400">Status</dt>
                    <dd class="mt-1">
                        <span class="px-2 py-0.5 rounded-full text-xs {{ $branch->is_active ? 'bg-green-100 text-green-700' : 'bg-ink-100 text-ink-500' }}">
                            {{ $branch->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-ink-400">Area / Locality</dt>
                    <dd class="mt-1 font-medium text-ink-900">{{ $branch->locality ?: '—' }}</dd>
                </div>
                <div>
                    <dt class="text-ink-400">Phone</dt>
                    <dd class="mt-1 font-medium text-ink-900">{{ $branch->phone ?: '—' }}</dd>
                </div>
                <div>
                    <dt class="text-ink-400">Email</dt>
                    <dd class="mt-1 font-medium text-ink-900">{{ $branch->email ?: '—' }}</dd>
                </div>
                <div>
                    <dt class="text-ink-400">GSTIN</dt>
                    <dd class="mt-1 font-medium text-ink-900">{{ $branch->gstin ?: '—' }}</dd>
                </div>
                <div>
                    <dt class="text-ink-400">Printers</dt>
                    <dd class="mt-1 font-medium text-ink-900">{{ $branch->printers_count }}</dd>
                </div>
                <div>
                    <dt class="text-ink-400">Staff</dt>
                    <dd class="mt-1 font-medium text-ink-900">{{ $branch->staff_count }}</dd>
                </div>
            </dl>
        </div>

        {{-- Categories (read-only preview) --}}
        <div x-show="tab === 'categories'" x-cloak class="bg-white rounded-2xl border border-ink-100 shadow-card overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-ink-100">
                <h3 class="font-medium text-ink-900">Categories</h3>
                <a href="{{ route('admin.branches.categories.index', [$restaurant, $branch]) }}" class="text-sm font-medium text-brand-600 hover:underline">Manage Categories &rarr;</a>
            </div>
            <table class="w-full text-sm">
                <thead class="text-left text-ink-500 border-b bg-ink-50/60">
                    <tr>
                        <th class="px-5 py-3">Category</th>
                        <th class="px-5 py-3">Menu Items</th>
                        <th class="px-5 py-3">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categories as $category)
                        <tr class="border-b last:border-0">
                            <td class="px-5 py-3 font-medium text-ink-900">{{ $category->name }}</td>
                            <td class="px-5 py-3 text-ink-500">{{ $category->menu_items_count }}</td>
                            <td class="px-5 py-3">
                                <span class="px-2 py-0.5 rounded-full text-xs {{ $category->is_active ? 'bg-green-100 text-green-700' : 'bg-ink-100 text-ink-500' }}">
                                    {{ $category->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="px-5 py-10 text-center text-ink-400">No categories yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Menu Items (read-only preview) --}}
        <div x-show="tab === 'menu-items'" x-cloak class="bg-white rounded-2xl border border-ink-100 shadow-card overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-ink-100">
                <h3 class="font-medium text-ink-900">Menu Items</h3>
                <a href="{{ route('admin.branches.menu-items.index', [$restaurant, $branch]) }}" class="text-sm font-medium text-brand-600 hover:underline">Manage Menu Items &rarr;</a>
            </div>
            <table class="w-full text-sm">
                <thead class="text-left text-ink-500 border-b bg-ink-50/60">
                    <tr>
                        <th class="px-5 py-3">Item</th>
                        <th class="px-5 py-3">Category</th>
                        <th class="px-5 py-3">Price</th>
                        <th class="px-5 py-3">Available</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($menuItems as $item)
                        <tr class="border-b last:border-0">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-1.5">
                                    <x-food-type-icon :type="$item->food_type" />
                                    <span class="font-medium text-ink-900">{{ $item->name }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-3 text-ink-500">{{ $item->category->name }}</td>
                            <td class="px-5 py-3">₹{{ number_format($item->price, 2) }}</td>
                            <td class="px-5 py-3">
                                <span class="px-2 py-0.5 rounded-full text-xs {{ $item->is_available ? 'bg-green-100 text-green-700' : 'bg-ink-100 text-ink-500' }}">
                                    {{ $item->is_available ? 'Available' : 'Unavailable' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-5 py-10 text-center text-ink-400">No menu items yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Orders (read-only preview) --}}
        <div x-show="tab === 'orders'" x-cloak class="bg-white rounded-2xl border border-ink-100 shadow-card overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-ink-100">
                <h3 class="font-medium text-ink-900">Recent Orders</h3>
                <a href="{{ route('admin.branches.orders.index', [$restaurant, $branch]) }}" class="text-sm font-medium text-brand-600 hover:underline">View All Orders &rarr;</a>
            </div>
            <table class="w-full text-sm">
                <thead class="text-left text-ink-500 border-b bg-ink-50/60">
                    <tr>
                        <th class="px-5 py-3">Order #</th>
                        <th class="px-5 py-3">Cashier</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Total</th>
                        <th class="px-5 py-3">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr class="border-b last:border-0">
                            <td class="px-5 py-3 font-medium text-ink-900">{{ $order->order_number }}</td>
                            <td class="px-5 py-3 text-ink-500">{{ $order->cashier->name }}</td>
                            <td class="px-5 py-3">
                                <span class="px-2 py-0.5 rounded-full text-xs
                                    {{ $order->status->value === 'completed' ? 'bg-green-100 text-green-700' : ($order->status->value === 'cancelled' ? 'bg-red-100 text-red-700' : 'bg-brand-50 text-brand-600') }}">
                                    {{ $order->status->label() }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-right">₹{{ number_format($order->grand_total, 2) }}</td>
                            <td class="px-5 py-3 text-ink-500">{{ $order->created_at->format('d M, h:i A') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-10 text-center text-ink-400">No orders yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Staff (read-only preview) --}}
        <div x-show="tab === 'staff'" x-cloak class="bg-white rounded-2xl border border-ink-100 shadow-card overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-ink-100">
                <h3 class="font-medium text-ink-900">Staff</h3>
                <a href="{{ route('admin.branches.staff.index', [$restaurant, $branch]) }}" class="text-sm font-medium text-brand-600 hover:underline">Manage Staff &rarr;</a>
            </div>
            <table class="w-full text-sm">
                <thead class="text-left text-ink-500 border-b bg-ink-50/60">
                    <tr>
                        <th class="px-5 py-3">Name</th>
                        <th class="px-5 py-3">Email</th>
                        <th class="px-5 py-3">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($staff as $member)
                        <tr class="border-b last:border-0">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-brand-100 text-xs font-semibold text-brand-700">
                                        {{ Str::of($member->name)->explode(' ')->map(fn ($p) => Str::substr($p, 0, 1))->take(2)->implode('') }}
                                    </span>
                                    <span class="font-medium text-ink-900">{{ $member->name }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-3 text-ink-500">{{ $member->email }}</td>
                            <td class="px-5 py-3">
                                <span class="px-2 py-0.5 rounded-full text-xs {{ $member->is_active ? 'bg-green-100 text-green-700' : 'bg-ink-100 text-ink-500' }}">
                                    {{ $member->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="px-5 py-10 text-center text-ink-400">No staff accounts yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
