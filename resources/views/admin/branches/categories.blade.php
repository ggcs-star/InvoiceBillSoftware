@extends('layouts.app')

@section('title', 'Categories — ' . $branch->name)
@section('header', $restaurant->name . ' — ' . $branch->name)

@section('content')
    @if ($errors->any())
        <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    @include('admin.branches._nav')

    <div x-data="{ open: false }" class="space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-ink-900">Item Categories</h2>
            <button @click="open = !open" class="rounded-lg bg-brand-600 px-4 py-2 text-white text-sm font-medium hover:bg-brand-700">
                + Add Item Category
            </button>
        </div>

        <div x-show="open" x-cloak class="bg-white rounded-2xl border border-ink-100 shadow-card p-6">
            <form method="POST" action="{{ route('admin.branches.categories.store', [$restaurant, $branch]) }}" class="flex gap-3">
                @csrf
                <input type="text" name="name" required placeholder="e.g. Pizzas" class="flex-1 rounded-lg border-ink-200 shadow-sm">
                <button type="submit" class="rounded-lg bg-brand-600 px-4 py-2 text-white font-medium hover:bg-brand-700">Add</button>
            </form>
        </div>

        <div class="bg-white rounded-2xl border border-ink-100 shadow-card p-4">
            <form method="GET" class="flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-[180px]">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search categories..."
                           class="w-full rounded-lg border-ink-200 shadow-sm text-sm">
                </div>
                <x-searchable-select name="status" placeholder="All Status"
                    :options="[['value' => 'active', 'label' => 'Active'], ['value' => 'inactive', 'label' => 'Inactive']]"
                    :selected="request('status')" />
                <button type="submit" class="rounded-lg border border-ink-200 px-4 py-2 text-sm font-medium text-ink-600 hover:bg-ink-50">Search</button>
                @if (request()->anyFilled(['search', 'status']))
                    <a href="{{ route('admin.branches.categories.index', [$restaurant, $branch]) }}" class="rounded-lg border border-ink-200 px-4 py-2 text-sm font-medium text-ink-600 hover:bg-ink-50">Clear</a>
                @endif
            </form>
        </div>

        <div class="bg-white rounded-2xl border border-ink-100 shadow-card overflow-hidden">
            <table class="w-full text-sm">
                <thead class="text-left text-ink-500 border-b">
                    <tr>
                        <th class="px-5 py-3">Item Category</th>
                        <th class="px-5 py-3">Menu Items</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categories as $category)
                        <tr class="border-b last:border-0">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-brand-50 text-xs font-semibold text-brand-600">
                                        {{ Str::substr($category->name, 0, 1) }}
                                    </span>
                                    <span class="font-medium">{{ $category->name }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-3">{{ $category->menu_items_count }} item(s)</td>
                            <td class="px-5 py-3">
                                <span class="px-2 py-0.5 rounded-full text-xs {{ $category->is_active ? 'bg-green-100 text-green-700' : 'bg-brand-50 text-ink-500' }}">
                                    {{ $category->is_active ? 'Enabled' : 'Disabled' }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-right space-x-3">
                                <a href="{{ route('admin.branches.menu-items.index', [$restaurant, $branch]) }}" class="text-brand-600 hover:underline">View Items</a>
                                <form method="POST" action="{{ route('admin.branches.categories.destroy', [$restaurant, $branch, $category]) }}" onsubmit="return confirm('Remove this category and its items?')" class="inline">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:underline">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-5 py-10 text-center text-ink-400">No categories match.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-pagination :paginator="$categories" />
    </div>
@endsection
