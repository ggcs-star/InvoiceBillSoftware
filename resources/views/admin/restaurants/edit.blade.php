@extends('layouts.app')

@section('title', $restaurant->name)
@section('header', $restaurant->name)

@section('content')
    @if ($errors->any())
        <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-2xl border border-ink-100 shadow-card p-6">
                <h3 class="font-medium mb-4">Brand Details</h3>
                <form method="POST" action="{{ route('admin.restaurants.update', $restaurant) }}" class="space-y-4">
                    @csrf @method('PUT')

                    <div>
                        <label class="block text-sm font-medium text-ink-700">Name</label>
                        <input type="text" name="name" value="{{ old('name', $restaurant->name) }}" required
                               class="mt-1 block w-full rounded-lg border-ink-200 shadow-sm">
                    </div>

                    <label class="flex items-center gap-2 text-sm text-ink-700">
                        <input type="checkbox" name="is_active" value="1" {{ $restaurant->is_active ? 'checked' : '' }} class="rounded border-ink-200">
                        Active
                    </label>

                    <button type="submit" class="rounded-lg bg-brand-600 px-4 py-2 text-white font-medium hover:bg-brand-700">
                        Save Changes
                    </button>
                </form>
            </div>

            <div class="bg-white rounded-2xl border border-ink-100 shadow-card p-6">
                <h3 class="font-medium mb-4">Add Branch</h3>
                <form method="POST" action="{{ route('admin.branches.store', $restaurant) }}" class="space-y-3">
                    @csrf
                    <input type="text" name="name" required placeholder="e.g. MG Road" class="block w-full rounded-lg border-ink-200 shadow-sm text-sm">
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
                    <div class="flex-1 min-w-[180px]">
                        <label class="block text-xs font-medium text-ink-500 mb-1">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Branch name..."
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
                        <a href="{{ route('admin.restaurants.edit', $restaurant) }}" class="rounded-lg border border-ink-200 px-4 py-2 text-sm font-medium text-ink-600 hover:bg-ink-50">Clear</a>
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
                            <tr class="border-b last:border-0">
                                <td class="px-5 py-3 text-ink-500">{{ $restaurant->name }}</td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-3">
                                        <span class="flex h-9 w-9 items-center justify-center rounded-full bg-brand-50 text-xs font-semibold text-brand-600">
                                            {{ Str::substr($branch->name, 0, 1) }}
                                        </span>
                                        <div>
                                            <div class="font-medium text-ink-900">{{ $branch->name }}</div>
                                            <div class="text-xs text-ink-400">{{ $branch->address ?: 'No address set' }}</div>
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

            <div>{{ $branches->links() }}</div>
        </div>
    </div>
@endsection
