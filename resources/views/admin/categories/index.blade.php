@extends('layouts.app')

@section('title', 'Item Categories')
@section('header', 'Item Categories')

@section('content')
<div x-data="{
        addOpen: false,
        restaurantId: '',
        branchId: '',
        branches: @js($branches->map(fn ($b) => ['id' => $b->id, 'name' => $b->name, 'restaurant_id' => $b->restaurant_id])),
        get branchOptions() {
            return this.branches.filter((b) => String(b.restaurant_id) === String(this.restaurantId));
        },
        get storeUrl() {
            return '/admin/restaurants/' + this.restaurantId + '/branches/' + this.branchId + '/categories';
        },
     }">
    <div class="flex justify-end mb-4">
        <button type="button" @click="addOpen = true" class="rounded-lg bg-brand-600 px-4 py-2 text-white text-sm font-medium hover:bg-brand-700">
            + Add Category
        </button>
    </div>

    {{-- Add Category modal --}}
    <div x-show="addOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-ink-900/50 p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden" @click.outside="addOpen = false">
            <div class="flex items-center justify-between px-6 py-4 border-b border-ink-100">
                <h3 class="text-lg font-bold text-ink-900">Add Category</h3>
                <button type="button" @click="addOpen = false" class="text-ink-400 hover:text-ink-700">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"/>
                        <line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>
            <form method="POST" :action="storeUrl" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-ink-500 mb-1">Restaurant</label>
                    <select x-model="restaurantId" @change="branchId = ''" required class="w-full rounded-lg border-ink-200 shadow-sm text-sm">
                        <option value="" disabled>Select a restaurant</option>
                        @foreach ($restaurants as $restaurant)
                            <option value="{{ $restaurant->id }}">{{ $restaurant->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-ink-500 mb-1">Branch</label>
                    <select x-model="branchId" required :disabled="!restaurantId" class="w-full rounded-lg border-ink-200 shadow-sm text-sm disabled:bg-ink-50">
                        <option value="" disabled>Select a branch</option>
                        <template x-for="branch in branchOptions" :key="branch.id">
                            <option :value="branch.id" x-text="branch.name"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-ink-500 mb-1">Category Name</label>
                    <input type="text" name="name" required placeholder="e.g. Pizzas" class="w-full rounded-lg border-ink-200 shadow-sm text-sm">
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="addOpen = false" class="rounded-lg border border-ink-200 px-4 py-2 text-sm font-medium text-ink-600 hover:bg-ink-50">Cancel</button>
                    <button type="submit" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700">Add Category</button>
                </div>
            </form>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-ink-100 shadow-card p-4 mb-4">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-medium text-ink-500 mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Category name..."
                       class="w-full rounded-lg border-ink-200 shadow-sm text-sm">
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
                    :options="[['value' => 'active', 'label' => 'Active'], ['value' => 'inactive', 'label' => 'Inactive']]"
                    :selected="request('status')" />
            </div>
            <button type="submit" class="rounded-lg bg-brand-600 px-4 py-2 text-white text-sm font-medium hover:bg-brand-700">Filter</button>
            @if (request()->anyFilled(['search', 'restaurant_id', 'branch_id', 'status']))
                <a href="{{ route('admin.categories.index') }}" class="rounded-lg border border-ink-200 px-4 py-2 text-sm font-medium text-ink-600 hover:bg-ink-50">Clear</a>
            @endif
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-ink-100 shadow-card overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="text-left text-ink-500 border-b bg-ink-50/60">
                <tr>
                    <th class="px-5 py-3">Category Name</th>
                    <th class="px-5 py-3">Restaurant</th>
                    <th class="px-5 py-3">Branch</th>
                    <th class="px-5 py-3">Items Count</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3 text-right">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($categories as $category)
                    <tr onclick="window.location='{{ route('admin.branches.categories.index', [$category->branch->restaurant, $category->branch]) }}'"
                        class="border-b last:border-0 cursor-pointer hover:bg-brand-50/50">
                        <td class="px-5 py-3 font-medium text-ink-900">
                            <div class="flex items-center gap-3">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-brand-50 text-xs font-semibold text-brand-600">
                                    {{ Str::substr($category->name, 0, 1) }}
                                </span>
                                {{ $category->name }}
                            </div>
                        </td>
                        <td class="px-5 py-3">
                            <a href="{{ route('admin.restaurants.show', $category->branch->restaurant) }}" onclick="event.stopPropagation()" class="text-brand-600 hover:underline">
                                {{ $category->branch->restaurant->name }}
                            </a>
                        </td>
                        <td class="px-5 py-3 text-ink-500">{{ $category->branch->name }}</td>
                        <td class="px-5 py-3">
                            <span class="px-2 py-0.5 rounded-full text-xs bg-brand-50 text-brand-600">{{ $category->menu_items_count }} Menu Items</span>
                        </td>
                        <td class="px-5 py-3">
                            <span class="px-2 py-0.5 rounded-full text-xs {{ $category->is_active ? 'bg-green-100 text-green-700' : 'bg-ink-100 text-ink-500' }}">
                                {{ $category->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-right" onclick="event.stopPropagation()">
                            <div class="inline-flex gap-2">
                                <a href="{{ route('admin.branches.categories.index', [$category->branch->restaurant, $category->branch]) }}"
                                   class="flex h-7 w-7 items-center justify-center rounded-lg border border-ink-200 text-ink-500 hover:bg-ink-50">
                                    <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 20h9"/>
                                        <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/>
                                    </svg>
                                </a>
                                <form method="POST" action="{{ route('admin.branches.categories.destroy', [$category->branch->restaurant, $category->branch, $category]) }}" onsubmit="return confirm('Remove this category?')">
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
                    <tr><td colspan="6" class="px-5 py-10 text-center text-ink-400">No categories match these filters.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <x-pagination :paginator="$categories" />
</div>
@endsection
