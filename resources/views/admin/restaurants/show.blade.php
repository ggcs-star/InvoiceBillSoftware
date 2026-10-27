@extends('layouts.app')

@section('title', $restaurant->name)
@section('header', $restaurant->name)

@section('content')
    @php
        $initialTab = request('tab') === 'branches' || request()->anyFilled(['search', 'status']) ? 'branches' : 'details';
    @endphp
    <div x-data="{ tab: '{{ $initialTab }}' }" class="space-y-6">
        <div class="flex items-center justify-between">
            <div class="flex gap-2 border-b">
                @foreach (['details' => 'Details', 'branches' => 'Branches'] as $key => $label)
                    <button @click="tab = '{{ $key }}'"
                            :class="tab === '{{ $key }}' ? 'border-brand-600 text-brand-600' : 'border-transparent text-ink-500'"
                            class="px-4 py-2 text-sm font-medium border-b-2 -mb-px">
                        {{ $label }}
                        @if ($key === 'branches')
                            <span class="ml-1 text-xs text-ink-400">({{ $branches->total() }})</span>
                        @endif
                    </button>
                @endforeach
            </div>
            <a href="{{ route('admin.restaurants.edit', $restaurant) }}"
               class="flex items-center gap-1.5 rounded-lg border border-ink-200 px-3 py-1.5 text-sm font-medium text-ink-600 hover:bg-ink-50">
                <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 20h9"/>
                    <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/>
                </svg>
                Edit Restaurant
            </a>
        </div>

        {{-- Details --}}
        <div x-show="tab === 'details'" class="bg-white rounded-2xl border border-ink-100 shadow-card p-6 max-w-xl">
            <div class="flex items-center gap-4 mb-6">
                <span class="flex h-14 w-14 items-center justify-center rounded-xl bg-brand-50 text-brand-600">
                    <svg viewBox="0 0 24 24" class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 10 5 4h14l1 6"/>
                        <path d="M4 10a2.5 2.5 0 0 0 5 0 2.5 2.5 0 0 0 5 0 2.5 2.5 0 0 0 5 0"/>
                        <path d="M5 10v9h14v-9"/>
                    </svg>
                </span>
                <div>
                    <div class="text-lg font-bold text-ink-900">{{ $restaurant->name }}</div>
                    <div class="text-sm text-ink-500">{{ $restaurant->cuisine_type ?: 'No cuisine type set' }}</div>
                </div>
            </div>

            <dl class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <dt class="text-ink-400">Status</dt>
                    <dd class="mt-1">
                        <span class="px-2 py-0.5 rounded-full text-xs {{ $restaurant->is_active ? 'bg-green-100 text-green-700' : 'bg-ink-100 text-ink-500' }}">
                            {{ $restaurant->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-ink-400">Branches</dt>
                    <dd class="mt-1 font-medium text-ink-900">{{ $branches->total() }}</dd>
                </div>
                <div>
                    <dt class="text-ink-400">Added On</dt>
                    <dd class="mt-1 font-medium text-ink-900">{{ $restaurant->created_at->format('d M Y') }}</dd>
                </div>
            </dl>
        </div>

        {{-- Branches --}}
        <div x-show="tab === 'branches'" x-cloak class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl border border-ink-100 shadow-card p-6">
                    <h3 class="font-medium mb-4">Add Branch</h3>
                    <form method="POST" action="{{ route('admin.branches.store', $restaurant) }}" class="space-y-3">
                        @csrf
                        <input type="text" name="name" required placeholder="e.g. branch1" class="block w-full rounded-lg border-ink-200 shadow-sm text-sm">
                        <input type="text" name="locality" placeholder="Area / Locality, e.g. Indiranagar" class="block w-full rounded-lg border-ink-200 shadow-sm text-sm">
                        <input type="text" name="address" placeholder="Address" class="block w-full rounded-lg border-ink-200 shadow-sm text-sm">
                        <div class="grid grid-cols-2 gap-3">
                            <input type="text" name="phone" placeholder="Phone" class="block w-full rounded-lg border-ink-200 shadow-sm text-sm">
                            <input type="text" name="gstin" placeholder="GSTIN" class="block w-full rounded-lg border-ink-200 shadow-sm text-sm">
                        </div>
                        <button type="submit" class="w-full rounded-lg bg-ink-900 px-4 py-2 text-white text-sm font-medium hover:bg-ink-700">+ Add Branch</button>
                    </form>
                </div>
            </div>

            <div class="lg:col-span-2 space-y-4">
                <div class="bg-white rounded-2xl border border-ink-100 shadow-card p-4">
                    <form method="GET" class="flex flex-wrap gap-3 items-end">
                        <input type="hidden" name="tab" value="branches">
                        <div class="flex-1 min-w-[180px]">
                            <label class="block text-xs font-medium text-ink-500 mb-1">Search</label>
                            <div class="relative">
                                <svg viewBox="0 0 24 24" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-ink-400" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="11" cy="11" r="7"/>
                                    <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                                </svg>
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Branch name..."
                                       class="w-full rounded-lg border-ink-200 bg-white pl-10 pr-4 py-2 shadow-sm text-sm">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-ink-500 mb-1">Status</label>
                            <x-searchable-select name="status" placeholder="All Status"
                                :options="[['value' => 'active', 'label' => 'Active'], ['value' => 'inactive', 'label' => 'Inactive']]"
                                :selected="request('status')" />
                        </div>
                        <button type="submit" class="rounded-lg bg-brand-600 px-4 py-2 text-white text-sm font-medium hover:bg-brand-700">Filter</button>
                        @if (request()->anyFilled(['search', 'status']))
                            <a href="{{ route('admin.restaurants.show', $restaurant) }}" class="rounded-lg border border-ink-200 px-4 py-2 text-sm font-medium text-ink-600 hover:bg-ink-50">Clear</a>
                        @endif
                    </form>
                </div>

                <div class="bg-white rounded-2xl border border-ink-100 shadow-card overflow-hidden">
                    <table class="w-full text-sm">
                        <thead class="text-left text-ink-500 border-b">
                            <tr>
                                <th class="px-5 py-3">Restaurant</th>
                                <th class="px-5 py-3">Branch</th>
                                <th class="px-5 py-3">Menu Items</th>
                                <th class="px-5 py-3">Staff</th>
                                <th class="px-5 py-3">Status</th>
                                <th class="px-5 py-3 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($branches as $branch)
                                <tr onclick="window.location='{{ route('admin.branches.edit', [$restaurant, $branch]) }}'"
                                    class="border-b last:border-0 cursor-pointer hover:bg-brand-50/50">
                                    <td class="px-5 py-3 text-ink-500">{{ $restaurant->name }}</td>
                                    <td class="px-5 py-3">
                                        <div class="flex items-center gap-3">
                                            <span class="flex h-9 w-9 items-center justify-center rounded-full bg-brand-50 text-xs font-semibold text-brand-600">
                                                {{ Str::substr($branch->name, 0, 1) }}
                                            </span>
                                            <div>
                                                <div class="font-medium text-ink-900">{{ $branch->name }}</div>
                                                <div class="text-xs text-ink-400">{{ $branch->locality ?: ($branch->address ?: 'No area set') }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3">{{ $branch->menu_items_count }}</td>
                                    <td class="px-5 py-3">{{ $branch->staff_count }}</td>
                                    <td class="px-5 py-3">
                                        <span class="px-2 py-0.5 rounded-full text-xs {{ $branch->is_active ? 'bg-green-100 text-green-700' : 'bg-brand-50 text-ink-500' }}">
                                            {{ $branch->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 text-right">
                                        <a href="{{ route('admin.branches.edit', [$restaurant, $branch]) }}" class="text-brand-600 hover:underline">Manage</a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="px-5 py-10 text-center text-ink-400">No branches match. Add one on the left to start setting up its menu and printer.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <x-pagination :paginator="$branches" />
            </div>
        </div>
    </div>
@endsection
