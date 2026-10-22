@extends('layouts.app')

@section('title', 'Restaurants')
@section('header', 'Restaurants')

@section('content')
    <div class="bg-white rounded-2xl border border-ink-100 shadow-card p-4 mb-4">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-medium text-ink-500 mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Restaurant name..."
                       class="w-full rounded-lg border-ink-200 shadow-sm text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-ink-500 mb-1">Status</label>
                <select name="status" class="rounded-lg border-ink-200 shadow-sm text-sm">
                    <option value="">All</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <button type="submit" class="rounded-lg bg-brand-600 px-4 py-2 text-white text-sm font-medium hover:bg-brand-700">Filter</button>
            @if (request()->anyFilled(['search', 'status']))
                <a href="{{ route('admin.restaurants.index') }}" class="rounded-lg border border-ink-200 px-4 py-2 text-sm font-medium text-ink-600 hover:bg-ink-50">Clear</a>
            @endif
            <a href="{{ route('admin.restaurants.create') }}"
               class="ml-auto rounded-lg bg-ink-900 px-4 py-2 text-white text-sm font-medium hover:bg-ink-700">
                + Add Restaurant
            </a>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-ink-100 shadow-card overflow-hidden">
        <table class="w-full text-sm">
            <thead class="text-left text-ink-500 border-b">
                <tr>
                    <th class="px-5 py-3">Name</th>
                    <th class="px-5 py-3">Branches</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($restaurants as $restaurant)
                    <tr onclick="window.location='{{ route('admin.restaurants.edit', $restaurant) }}'"
                        class="border-b last:border-0 cursor-pointer hover:bg-brand-50/50">
                        <td class="px-5 py-3 font-medium">{{ $restaurant->name }}</td>
                        <td class="px-5 py-3">{{ $restaurant->branches_count }}</td>
                        <td class="px-5 py-3">
                            <span class="px-2 py-0.5 rounded-full text-xs {{ $restaurant->is_active ? 'bg-green-100 text-green-700' : 'bg-brand-50 text-ink-500' }}">
                                {{ $restaurant->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-right">
                            <a href="{{ route('admin.restaurants.edit', $restaurant) }}" class="text-brand-600 hover:underline">Manage</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-5 py-6 text-center text-ink-400">No restaurants yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $restaurants->links() }}</div>
@endsection
