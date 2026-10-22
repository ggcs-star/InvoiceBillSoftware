@extends('layouts.app')

@section('title', 'Menu Items — ' . $branch->name)
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

    <div x-data="{ addOpen: false }" class="space-y-4">
        <div class="bg-white rounded-2xl border border-ink-100 shadow-card p-4">
            <form method="GET" class="flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-[180px]">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search your menu item here"
                           class="w-full rounded-lg border-ink-200 shadow-sm text-sm">
                </div>
                <select name="category_id" class="rounded-lg border-ink-200 shadow-sm text-sm" onchange="this.form.submit()">
                    <option value="">All Categories</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ (string) request('category_id') === (string) $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                <select name="availability" class="rounded-lg border-ink-200 shadow-sm text-sm" onchange="this.form.submit()">
                    <option value="">All Availability</option>
                    <option value="available" {{ request('availability') === 'available' ? 'selected' : '' }}>Available</option>
                    <option value="unavailable" {{ request('availability') === 'unavailable' ? 'selected' : '' }}>Unavailable</option>
                </select>
                <button type="submit" class="rounded-lg border border-ink-200 px-4 py-2 text-sm font-medium text-ink-600 hover:bg-ink-50">Search</button>
                @if (request()->anyFilled(['search', 'category_id', 'availability']))
                    <a href="{{ route('admin.branches.menu-items.index', [$restaurant, $branch]) }}" class="rounded-lg border border-ink-200 px-4 py-2 text-sm font-medium text-ink-600 hover:bg-ink-50">Clear</a>
                @endif
                <button type="button" @click="addOpen = !addOpen"
                        class="ml-auto rounded-lg bg-brand-600 px-4 py-2 text-white text-sm font-medium hover:bg-brand-700">
                    + Add Menu Item
                </button>
            </form>
        </div>

        <div x-show="addOpen" x-cloak class="bg-white rounded-2xl border border-ink-100 shadow-card p-6">
            <h3 class="font-medium mb-4">Add Menu Item</h3>

            @if ($categories->isEmpty())
                <p class="text-sm text-ink-500">
                    Add an <a href="{{ route('admin.branches.categories.index', [$restaurant, $branch]) }}" class="text-brand-600 hover:underline">item category</a> first before adding menu items.
                </p>
            @else
                <form method="POST" action="{{ route('admin.branches.menu-items.store', [$restaurant, $branch]) }}" class="grid grid-cols-6 gap-3 items-end">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-ink-500 mb-1">Category</label>
                        <select name="category_id" required class="w-full rounded-lg border-ink-200 shadow-sm text-sm">
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-medium text-ink-500 mb-1">Item Name</label>
                        <input type="text" name="name" required placeholder="e.g. Margherita Pizza" class="w-full rounded-lg border-ink-200 shadow-sm text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-ink-500 mb-1">Price</label>
                        <input type="number" step="0.01" name="price" required placeholder="249.00" class="w-full rounded-lg border-ink-200 shadow-sm text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-ink-500 mb-1">GST %</label>
                        <input type="number" step="0.01" name="gst_percentage" required placeholder="5" value="5" class="w-full rounded-lg border-ink-200 shadow-sm text-sm">
                    </div>
                    <button type="submit" class="rounded-lg bg-brand-600 px-3 py-2 text-white text-sm font-medium hover:bg-brand-700">Add Item</button>
                </form>
            @endif
        </div>

        <div class="bg-white rounded-2xl border border-ink-100 shadow-card overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-left text-ink-500 border-b bg-ink-50/60">
                    <tr>
                        <th class="px-5 py-3">Item Name</th>
                        <th class="px-5 py-3">Price</th>
                        <th class="px-5 py-3">Item Category</th>
                        <th class="px-5 py-3">Restaurant</th>
                        <th class="px-5 py-3">Branch</th>
                        <th class="px-5 py-3">Available</th>
                        <th class="px-5 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($menuItems as $item)
                        <tr x-data="{ editing: false }" class="border-b last:border-0">
                            <td class="px-5 py-3" colspan="7" x-show="editing" x-cloak>
                                <form method="POST" action="{{ route('admin.branches.menu-items.update', [$restaurant, $branch, $item]) }}" class="grid grid-cols-6 gap-3 items-end">
                                    @csrf @method('PUT')
                                    <select name="category_id" required class="w-full rounded-lg border-ink-200 shadow-sm text-sm">
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}" {{ $item->category_id === $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    <input type="text" name="name" value="{{ $item->name }}" required class="col-span-2 w-full rounded-lg border-ink-200 shadow-sm text-sm">
                                    <input type="number" step="0.01" name="price" value="{{ $item->price }}" required class="w-full rounded-lg border-ink-200 shadow-sm text-sm">
                                    <input type="number" step="0.01" name="gst_percentage" value="{{ $item->gst_percentage }}" required class="w-full rounded-lg border-ink-200 shadow-sm text-sm">
                                    <div class="flex gap-2">
                                        <button type="submit" class="rounded-lg bg-brand-600 px-3 py-2 text-white text-sm font-medium hover:bg-brand-700">Save</button>
                                        <button type="button" @click="editing = false" class="rounded-lg border border-ink-200 px-3 py-2 text-sm font-medium text-ink-600 hover:bg-ink-50">Cancel</button>
                                    </div>
                                </form>
                            </td>

                            <td class="px-5 py-3" x-show="!editing">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full {{ $item->is_available ? 'bg-green-50 text-green-600' : 'bg-ink-50 text-ink-400' }}">
                                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M6 3v8a2 2 0 0 0 2 2h0a2 2 0 0 0 2-2V3"/>
                                            <line x1="8" y1="13" x2="8" y2="21"/>
                                            <path d="M17 3c-1.7 0-3 2-3 5s1.3 5 3 5v8"/>
                                        </svg>
                                    </span>
                                    <span class="font-medium text-ink-900">{{ $item->name }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-3" x-show="!editing">₹{{ number_format($item->price, 2) }}</td>
                            <td class="px-5 py-3 text-ink-500" x-show="!editing">{{ $item->category->name }}</td>
                            <td class="px-5 py-3 text-ink-500" x-show="!editing">{{ $restaurant->name }}</td>
                            <td class="px-5 py-3 text-ink-500" x-show="!editing">{{ $branch->name }}</td>
                            <td class="px-5 py-3" x-show="!editing">
                                <form method="POST" action="{{ route('admin.branches.menu-items.toggle', [$restaurant, $branch, $item]) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" title="{{ $item->is_available ? 'Available — click to mark unavailable' : 'Unavailable — click to mark available' }}"
                                            class="flex h-5 w-5 items-center justify-center rounded border-2 {{ $item->is_available ? 'bg-brand-600 border-brand-600 text-white' : 'border-ink-300 text-transparent' }}">
                                        <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M20 6 9 17l-5-5"/>
                                        </svg>
                                    </button>
                                </form>
                            </td>
                            <td class="px-5 py-3 text-right" x-show="!editing">
                                <div class="inline-flex gap-2">
                                    <button type="button" @click="editing = true" class="inline-flex items-center gap-1 rounded-lg border border-ink-200 px-3 py-1.5 text-xs font-medium text-ink-600 hover:bg-ink-50">
                                        <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M12 20h9"/>
                                            <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/>
                                        </svg>
                                        Update
                                    </button>
                                    <form method="POST" action="{{ route('admin.branches.menu-items.destroy', [$restaurant, $branch, $item]) }}" onsubmit="return confirm('Remove this item?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="flex h-7 w-7 items-center justify-center rounded-lg border border-red-200 text-red-600 hover:bg-red-50">
                                            <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="3 6 5 6 21 6"/>
                                                <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                                <path d="M10 11v6"/>
                                                <path d="M14 11v6"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-5 py-10 text-center text-ink-400">No menu items match these filters.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($menuItems->total() > 0)
            <div class="flex items-center justify-between text-sm text-ink-500">
                <span>Showing {{ $menuItems->firstItem() }} to {{ $menuItems->lastItem() }} of {{ $menuItems->total() }} results</span>
                <div>{{ $menuItems->links() }}</div>
            </div>
        @endif
    </div>
@endsection
