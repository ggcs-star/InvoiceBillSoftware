@extends('layouts.app')

@section('title', 'Restaurants')
@section('header', 'Restaurants')

@section('content')
    <div class="flex items-start justify-between mb-5">
        <div>
            <h2 class="text-xl font-bold text-ink-900">Restaurants Management</h2>
            <p class="text-sm text-ink-500 mt-0.5">Manage all registered food chains and brand entities.</p>
        </div>
        <a href="{{ route('admin.restaurants.create') }}"
           class="flex items-center gap-1.5 rounded-lg bg-brand-600 px-4 py-2.5 text-white text-sm font-medium hover:bg-brand-700">
            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"/>
                <line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Add Restaurant
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-ink-100 shadow-card p-4 mb-4">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[200px]">
                <div class="relative">
                    <svg viewBox="0 0 24 24" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-ink-400" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="7"/>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search restaurants..."
                           class="w-full rounded-lg border-ink-200 bg-ink-50/60 pl-9 shadow-sm text-sm">
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
                <a href="{{ route('admin.restaurants.index') }}" class="rounded-lg border border-ink-200 px-4 py-2 text-sm font-medium text-ink-600 hover:bg-ink-50">Clear</a>
            @endif
            <span class="ml-auto self-center text-sm text-ink-500">{{ $restaurants->total() }} {{ Str::plural('Restaurant', $restaurants->total()) }} found</span>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-ink-100 shadow-card overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="text-left text-ink-500 border-b bg-ink-50/60">
                <tr>
                    <th class="px-5 py-3">Restaurant Brand</th>
                    <th class="px-5 py-3">Cuisine Type</th>
                    <th class="px-5 py-3">Branches</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($restaurants as $restaurant)
                    <tr onclick="window.location='{{ route('admin.restaurants.show', $restaurant) }}'"
                        class="border-b last:border-0 cursor-pointer hover:bg-brand-50/50">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-brand-50 text-brand-600">
                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M4 10 5 4h14l1 6"/>
                                        <path d="M4 10a2.5 2.5 0 0 0 5 0 2.5 2.5 0 0 0 5 0 2.5 2.5 0 0 0 5 0"/>
                                        <path d="M5 10v9h14v-9"/>
                                    </svg>
                                </span>
                                <span class="font-semibold text-ink-900">{{ $restaurant->name }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-ink-500">{{ $restaurant->cuisine_type ?: '—' }}</td>
                        <td class="px-5 py-3">
                            <span class="px-2 py-0.5 rounded-full text-xs bg-brand-50 text-brand-600">
                                {{ $restaurant->branches_count }} {{ Str::plural('Branch', $restaurant->branches_count) }}
                            </span>
                        </td>
                        <td class="px-5 py-3">
                            <span class="px-2 py-0.5 rounded-full text-xs {{ $restaurant->is_active ? 'bg-green-100 text-green-700' : 'bg-ink-100 text-ink-500' }}">
                                {{ $restaurant->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-right" onclick="event.stopPropagation()">
                            <div class="inline-flex gap-2">
                                <a href="{{ route('admin.restaurants.show', $restaurant) }}" title="View"
                                   class="flex h-7 w-7 items-center justify-center rounded-lg border border-ink-200 text-ink-500 hover:bg-ink-50">
                                    <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7Z"/>
                                        <circle cx="12" cy="12" r="3"/>
                                    </svg>
                                </a>
                                <a href="{{ route('admin.restaurants.edit', $restaurant) }}" title="Edit"
                                   class="flex h-7 w-7 items-center justify-center rounded-lg border border-ink-200 text-ink-500 hover:bg-ink-50">
                                    <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 20h9"/>
                                        <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/>
                                    </svg>
                                </a>
                                <form method="POST" action="{{ route('admin.restaurants.destroy', $restaurant) }}" onsubmit="return confirm('Remove this restaurant and all its branches?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="flex h-7 w-7 items-center justify-center rounded-lg border border-red-200 text-red-600 hover:bg-red-50">
                                        <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="3 6 5 6 21 6"/>
                                            <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-6 text-center text-ink-400">No restaurants yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <x-pagination :paginator="$restaurants" />
@endsection
