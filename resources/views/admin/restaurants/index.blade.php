@extends('layouts.app')

@section('title', 'Restaurants')
@section('header', 'Restaurants')

@section('content')
    <div class="flex items-start justify-between mb-6 gap-4">
        <div>
        </div>
        <a href="{{ route('admin.restaurants.create') }}"
           class="flex shrink-0 items-center gap-1.5 rounded-lg bg-brand-600 px-4 py-2.5 text-white text-sm font-medium hover:bg-brand-700">
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
                <label class="block text-xs font-medium text-ink-500 mb-1">Search</label>
                <div class="relative">
                    <svg viewBox="0 0 24 24" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-ink-400" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="7"/>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search restaurants..."
                           class="w-full rounded-lg border-ink-200 bg-white pl-10 pr-4 py-2 shadow-sm text-sm">
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-ink-500 mb-1">Status</label>
                <x-searchable-select name="status" placeholder="All Status"
                    :options="[['value' => 'active', 'label' => 'Active'], ['value' => 'inactive', 'label' => 'Inactive']]"
                    :selected="request('status')" />
            </div>
            <button type="submit" class="flex items-center gap-1.5 rounded-lg bg-brand-600 px-4 py-2 text-white text-sm font-medium hover:bg-brand-700">
                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 5h16l-6 8v6l-4 2v-8L4 5Z"/>
                </svg>
                Filter
            </button>
            @if (request()->anyFilled(['search', 'status']))
                <a href="{{ route('admin.restaurants.index') }}" class="rounded-lg border border-ink-200 px-4 py-2 text-sm font-medium text-ink-600 hover:bg-ink-50">Clear</a>
            @endif
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-ink-100 shadow-card overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="text-left text-ink-500 border-b bg-ink-50/60 font-bold">
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
                                @if ($restaurant->logo_path)
                                    <img src="{{ asset('storage/' . $restaurant->logo_path) }}" alt="" class="h-10 w-10 shrink-0 rounded-xl object-cover">
                                @else
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-sm font-bold text-brand-600">
                                        {{ Str::substr($restaurant->name, 0, 1) }}
                                    </span>
                                @endif
                                <div class="min-w-0">
                                    <div class="font-semibold text-ink-900">{{ $restaurant->name }}</div>
                                    <div class="truncate text-xs text-ink-400">{{ $restaurant->cuisine_type ?: 'Cuisine not set' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-ink-500">{{ $restaurant->cuisine_type ?: '—' }}</td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-brand-50 px-2.5 py-1 text-xs font-medium text-brand-600">
                                <svg viewBox="0 0 24 24" class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 21V5a1 1 0 0 1 1-1h8a1 1 0 0 1 1 1v16"/>
                                    <path d="M14 10h5a1 1 0 0 1 1 1v10"/>
                                    <path d="M7 7h2M7 11h2M7 15h2"/>
                                </svg>
                                {{ $restaurant->branches_count }} {{ Str::plural('Branch', $restaurant->branches_count) }}
                            </span>
                        </td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center gap-1.5 text-xs font-medium {{ $restaurant->is_active ? 'text-green-700' : 'text-ink-500' }}">
                                <span class="h-1.5 w-1.5 rounded-full {{ $restaurant->is_active ? 'bg-green-500' : 'bg-ink-300' }}"></span>
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
                                   class="flex h-7 w-7 items-center justify-center rounded-lg border border-blue-200 bg-blue-50 text-blue-600 hover:bg-blue-100">
                                    <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 20h9"/>
                                        <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/>
                                    </svg>
                                </a>
                                <form method="POST" action="{{ route('admin.restaurants.destroy', $restaurant) }}" onsubmit="return confirm('Remove this restaurant and all its branches?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="flex h-7 w-7 items-center justify-center rounded-lg border border-red-200 bg-red-50 text-red-600 hover:bg-red-100">
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
