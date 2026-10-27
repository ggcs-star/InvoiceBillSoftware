@extends('layouts.app')

@section('title', 'Menu Items')
@section('header', 'Menu Items')

@section('content')
<div x-data="{
        addOpen: false,
        restaurantId: '',
        branchId: '',
        openVariationsFor: null,
        editingVariationId: null,
        addVariationOpen: false,
        openViewFor: null,
        openEditFor: null,
        branches: @js($branches->map(fn ($b) => [
            'id' => $b->id,
            'name' => $b->name,
            'restaurant_id' => $b->restaurant_id,
            'categories' => $b->categories->map(fn ($c) => ['id' => $c->id, 'name' => $c->name])->values(),
        ])),
        get branchOptions() {
            return this.branches.filter((b) => String(b.restaurant_id) === String(this.restaurantId));
        },
        get categoryOptions() {
            const branch = this.branches.find((b) => String(b.id) === String(this.branchId));
            return branch ? branch.categories : [];
        },
        get storeUrl() {
            return '/admin/restaurants/' + this.restaurantId + '/branches/' + this.branchId + '/menu-items';
        },
     }">
    <div class="flex items-start justify-between gap-4 mb-6">
        <div>
        </div>
        <div class="flex shrink-0 gap-2">
            <a href="{{ route('admin.menu-items.export', request()->query()) }}" class="inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700">
                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 16V4"/>
                    <path d="M7 11l5 5 5-5"/>
                    <path d="M4 16v3a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-3"/>
                </svg>
                Export Menu Items
            </a>
            <a href="{{ route('admin.menu-items.bulk-import') }}" class="inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700">
                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 4v12"/>
                    <path d="M7 9l5-5 5 5"/>
                    <path d="M4 16v3a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-3"/>
                </svg>
                Bulk Upload
            </a>
            <button type="button" @click="addOpen = true" class="rounded-lg bg-brand-600 px-4 py-2 text-white text-sm font-medium hover:bg-brand-700">
                + Add Menu Item
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-6">
        <div class="flex items-center gap-4 rounded-2xl bg-white p-5 shadow-card border border-ink-100">
            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-600">
                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M6 3v8a2 2 0 0 0 2 2h0a2 2 0 0 0 2-2V3"/>
                    <line x1="8" y1="13" x2="8" y2="21"/>
                    <path d="M17 3c-1.7 0-3 2-3 5s1.3 5 3 5v8"/>
                </svg>
            </span>
            <div class="min-w-0">
                <div class="text-xs text-ink-500">Total Menu Items</div>
                <div class="text-xl font-bold text-ink-900">{{ number_format($stats['total_items']) }}</div>
                @if ($stats['new_this_week'] > 0)
                    <div class="text-xs font-medium text-green-600">+{{ $stats['new_this_week'] }} new this week</div>
                @endif
            </div>
        </div>
        <div class="flex items-center gap-4 rounded-2xl bg-white p-5 shadow-card border border-ink-100">
            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-green-50 text-green-600">
                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 10 5 4h14l1 6"/>
                    <path d="M4 10a2.5 2.5 0 0 0 5 0 2.5 2.5 0 0 0 5 0 2.5 2.5 0 0 0 5 0"/>
                    <path d="M5 10v9h14v-9"/>
                </svg>
            </span>
            <div class="min-w-0">
                <div class="text-xs text-ink-500">Restaurants</div>
                <div class="text-xl font-bold text-ink-900">{{ $stats['restaurants'] }}</div>
                <div class="text-xs text-ink-400">All active</div>
            </div>
        </div>
        <div class="flex items-center gap-4 rounded-2xl bg-white p-5 shadow-card border border-ink-100">
            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-violet-50 text-violet-600">
                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="8" height="8" rx="1.5"/>
                    <rect x="13" y="3" width="8" height="8" rx="1.5"/>
                    <rect x="3" y="13" width="8" height="8" rx="1.5"/>
                    <rect x="13" y="13" width="8" height="8" rx="1.5"/>
                </svg>
            </span>
            <div class="min-w-0">
                <div class="text-xs text-ink-500">Categories</div>
                <div class="text-xl font-bold text-ink-900">{{ $stats['categories'] }}</div>
                <div class="text-xs text-ink-400">Active categories</div>
            </div>
        </div>
        <div class="flex items-center gap-4 rounded-2xl bg-white p-5 shadow-card border border-ink-100">
            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-600 text-base font-bold">
                ₹
            </span>
            <div class="min-w-0">
                <div class="text-xs text-ink-500">Total Price Range</div>
                <div class="text-xl font-bold text-ink-900">₹{{ number_format($stats['price_min'], 0) }} - ₹{{ number_format($stats['price_max'], 0) }}</div>
                <div class="text-xs text-ink-400">Across all menu items</div>
            </div>
        </div>
    </div>

    {{-- Add Menu Item modal --}}
    <div x-show="addOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-ink-900/50 p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-xl overflow-hidden my-8" @click.outside="addOpen = false">
            <div class="flex items-center justify-between px-6 py-4 border-b border-ink-100">
                <h3 class="text-lg font-bold text-ink-900">Add Menu Item</h3>
                <button type="button" @click="addOpen = false" class="text-ink-400 hover:text-ink-700">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"/>
                        <line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>
            <form method="POST" :action="storeUrl" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-4">
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
                </div>

                <div>
                    <label class="block text-xs font-medium text-ink-500 mb-1">Category</label>
                    <select name="category_id" required :disabled="!branchId" class="w-full rounded-lg border-ink-200 shadow-sm text-sm disabled:bg-ink-50">
                        <option value="" disabled selected>Select a category</option>
                        <template x-for="category in categoryOptions" :key="category.id">
                            <option :value="category.id" x-text="category.name"></option>
                        </template>
                    </select>
                    <p class="text-xs text-ink-400 mt-1" x-show="branchId && !categoryOptions.length">
                        This branch has no categories yet — add one from its Categories page first.
                    </p>
                </div>

                <div class="grid grid-cols-6 gap-3 items-end">
                    <div class="col-span-3">
                        <label class="block text-xs font-medium text-ink-500 mb-1">Item Name</label>
                        <input type="text" name="name" required placeholder="e.g. Margherita Pizza" class="w-full rounded-lg border-ink-200 shadow-sm text-sm">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-medium text-ink-500 mb-1">Price</label>
                        <input type="number" step="0.01" name="price" required placeholder="249.00" class="w-full rounded-lg border-ink-200 shadow-sm text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-ink-500 mb-1">GST %</label>
                        <input type="number" step="0.01" name="gst_percentage" required placeholder="5" value="5" class="w-full rounded-lg border-ink-200 shadow-sm text-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-ink-500 mb-1">Image</label>
                    <input type="file" name="image" accept="image/*" class="w-full text-sm rounded-lg border-ink-200 shadow-sm file:mr-3 file:rounded-lg file:border-0 file:bg-ink-900 file:px-3 file:py-1.5 file:text-white file:text-xs">
                </div>

                <div>
                    <label class="block text-xs font-medium text-ink-500 mb-1">Description</label>
                    <textarea name="description" rows="2" placeholder="Short description shown to customers" class="w-full rounded-lg border-ink-200 shadow-sm text-sm"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-medium text-ink-500 mb-2">Food Type</label>
                    <div class="flex gap-3">
                        @foreach (\App\Enums\FoodType::cases() as $foodType)
                            <label class="flex items-center gap-2 rounded-lg border border-ink-200 px-3 py-2 text-sm cursor-pointer has-[:checked]:border-brand-400 has-[:checked]:bg-brand-50">
                                <input type="radio" name="food_type" value="{{ $foodType->value }}" {{ $loop->first ? 'checked' : '' }} class="text-brand-600">
                                <x-food-type-icon :type="$foodType" />
                                {{ $foodType->label() }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="flex items-center gap-6">
                    <label class="flex items-center gap-2 text-sm text-ink-700">
                        <input type="hidden" name="is_available" value="0">
                        <input type="checkbox" name="is_available" value="1" checked class="rounded border-ink-300 text-brand-600">
                        Available
                    </label>
                    <label class="flex items-center gap-2 text-sm text-ink-700">
                        <input type="hidden" name="show_on_customer_site" value="0">
                        <input type="checkbox" name="show_on_customer_site" value="1" checked class="rounded border-ink-300 text-brand-600">
                        Show on Customer Site
                    </label>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="addOpen = false" class="rounded-lg border border-ink-200 px-4 py-2 text-sm font-medium text-ink-600 hover:bg-ink-50">Cancel</button>
                    <button type="submit" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700">Add Item</button>
                </div>
            </form>
        </div>
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
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search your menu item here"
                           class="w-full rounded-lg border-ink-200 bg-white pl-10 pr-4 py-2 shadow-sm text-sm">
                </div>
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
                <label class="block text-xs font-medium text-ink-500 mb-1">Category</label>
                <x-searchable-select name="category" placeholder="All Categories"
                    :options="$categoryNames->map(fn ($name) => ['value' => $name, 'label' => $name])"
                    :selected="request('category')" />
            </div>
            <div>
                <label class="block text-xs font-medium text-ink-500 mb-1">Availability</label>
                <x-searchable-select name="availability" placeholder="All Availability"
                    :options="[['value' => 'available', 'label' => 'Available'], ['value' => 'unavailable', 'label' => 'Unavailable']]"
                    :selected="request('availability')" />
            </div>
            <button type="submit" class="rounded-lg bg-brand-600 px-4 py-2 text-white text-sm font-medium hover:bg-brand-700">Filter</button>
            @if (request()->anyFilled(['search', 'restaurant_id', 'branch_id', 'category', 'availability']))
                <a href="{{ route('admin.menu-items.index') }}" class="rounded-lg border border-ink-200 px-4 py-2 text-sm font-medium text-ink-600 hover:bg-ink-50">Clear</a>
            @endif
        </form>
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
                    <th class="px-5 py-3">Customer Site</th>
                    <th class="px-5 py-3 text-right">Action</th>
                </tr>
            </thead>
            @forelse ($menuItems as $item)
                <tbody>
                    <tr class="border-b last:border-0">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                @if ($item->image_path)
                                    <img src="{{ asset('storage/' . $item->image_path) }}" class="h-11 w-11 shrink-0 rounded-lg object-cover">
                                @else
                                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg {{ $item->is_available ? 'bg-green-50 text-green-600' : 'bg-ink-50 text-ink-400' }}">
                                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M6 3v8a2 2 0 0 0 2 2h0a2 2 0 0 0 2-2V3"/>
                                            <line x1="8" y1="13" x2="8" y2="21"/>
                                            <path d="M17 3c-1.7 0-3 2-3 5s1.3 5 3 5v8"/>
                                        </svg>
                                    </span>
                                @endif
                                <div class="flex items-center gap-1.5 min-w-0">
                                    <x-food-type-icon :type="$item->food_type" />
                                    <span class="font-medium text-ink-900 truncate">{{ $item->name }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3">₹{{ number_format($item->price, 2) }}</td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center rounded-full bg-brand-50 px-2.5 py-1 text-xs font-medium text-brand-600">{{ $item->category->name }}</span>
                        </td>
                        <td class="px-5 py-3">
                            <a href="{{ route('admin.restaurants.show', $item->branch->restaurant) }}" class="text-brand-600 hover:underline">
                                {{ $item->branch->restaurant->name }}
                            </a>
                        </td>
                        <td class="px-5 py-3 text-ink-500">{{ $item->branch->name }}</td>
                        <td class="px-5 py-3">
                            <form method="POST" action="{{ route('admin.branches.menu-items.toggle', [$item->branch->restaurant, $item->branch, $item]) }}">
                                @csrf @method('PATCH')
                                <button type="submit" title="Click to toggle availability"
                                        class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium {{ $item->is_available ? 'bg-green-100 text-green-700' : 'bg-ink-100 text-ink-500' }}">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $item->is_available ? 'bg-green-500' : 'bg-ink-300' }}"></span>
                                    {{ $item->is_available ? 'Available' : 'Unavailable' }}
                                </button>
                            </form>
                        </td>
                        <td class="px-5 py-3">
                            <form method="POST" action="{{ route('admin.branches.menu-items.toggle-customer-site', [$item->branch->restaurant, $item->branch, $item]) }}">
                                @csrf @method('PATCH')
                                <button type="submit" title="Click to toggle customer site visibility"
                                        class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium {{ $item->show_on_customer_site ? 'bg-green-100 text-green-700' : 'bg-ink-100 text-ink-500' }}">
                                    {{ $item->show_on_customer_site ? 'Yes' : 'No' }}
                                </button>
                            </form>
                        </td>
                        <td class="px-5 py-3 text-right">
                            <div class="inline-flex gap-2">
                                <button type="button" title="View" @click="openViewFor = {{ $item->id }}"
                                        class="flex h-7 w-7 items-center justify-center rounded-lg border border-ink-200 text-ink-500 hover:bg-ink-50">
                                    <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7Z"/>
                                        <circle cx="12" cy="12" r="3"/>
                                    </svg>
                                </button>
                                <button type="button" title="Edit" @click="openEditFor = {{ $item->id }}"
                                        class="flex h-7 w-7 items-center justify-center rounded-lg border border-blue-200 bg-blue-50 text-blue-600 hover:bg-blue-100">
                                    <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 20h9"/>
                                        <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/>
                                    </svg>
                                </button>
                                @if ($item->variations->count())
                                    <button type="button" title="Variations ({{ $item->variations->count() }})"
                                            @click="openVariationsFor = {{ $item->id }}; editingVariationId = null; addVariationOpen = false"
                                            class="flex h-7 w-7 items-center justify-center rounded-lg border border-violet-200 bg-violet-50 text-violet-600 hover:bg-violet-100">
                                        <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="3" y="3" width="7" height="7" rx="1.5"/>
                                            <rect x="14" y="3" width="7" height="7" rx="1.5"/>
                                            <rect x="3" y="14" width="7" height="7" rx="1.5"/>
                                            <rect x="14" y="14" width="7" height="7" rx="1.5"/>
                                        </svg>
                                    </button>
                                @endif
                                <form method="POST" action="{{ route('admin.branches.menu-items.destroy', [$item->branch->restaurant, $item->branch, $item]) }}" onsubmit="return confirm('Remove this item?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" title="Delete" class="flex h-7 w-7 items-center justify-center rounded-lg border border-red-200 bg-red-50 text-red-600 hover:bg-red-100">
                                        <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="3 6 5 6 21 6"/>
                                            <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                </tbody>
            @empty
                <tbody>
                    <tr><td colspan="8" class="px-5 py-10 text-center text-ink-400">No menu items match these filters.</td></tr>
                </tbody>
            @endforelse
        </table>
    </div>

    {{-- View modals (one per item, kept outside the <table> so they're valid HTML) --}}
    @foreach ($menuItems as $item)
        <div x-show="openViewFor === {{ $item->id }}" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-ink-900/50 p-4">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden" @click.outside="openViewFor = null">
                <div class="flex items-center justify-between px-6 py-4 border-b border-ink-100">
                    <h3 class="text-lg font-bold text-ink-900">Menu Item Details</h3>
                    <button type="button" @click="openViewFor = null" class="text-ink-400 hover:text-ink-700">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"/>
                            <line x1="6" y1="6" x2="18" y2="18"/>
                        </svg>
                    </button>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-center gap-4">
                        @if ($item->image_path)
                            <img src="{{ asset('storage/' . $item->image_path) }}" class="h-16 w-16 shrink-0 rounded-xl object-cover">
                        @else
                            <span class="flex h-16 w-16 shrink-0 items-center justify-center rounded-xl {{ $item->is_available ? 'bg-green-50 text-green-600' : 'bg-ink-50 text-ink-400' }}">
                                <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M6 3v8a2 2 0 0 0 2 2h0a2 2 0 0 0 2-2V3"/>
                                    <line x1="8" y1="13" x2="8" y2="21"/>
                                    <path d="M17 3c-1.7 0-3 2-3 5s1.3 5 3 5v8"/>
                                </svg>
                            </span>
                        @endif
                        <div class="min-w-0">
                            <div class="flex items-center gap-1.5">
                                <x-food-type-icon :type="$item->food_type" />
                                <span class="text-base font-bold text-ink-900 truncate">{{ $item->name }}</span>
                            </div>
                            <div class="text-sm text-ink-500">₹{{ number_format($item->price, 2) }} &middot; GST {{ rtrim(rtrim(number_format($item->gst_percentage, 2), '0'), '.') }}%</div>
                        </div>
                    </div>

                    @if ($item->description)
                        <p class="text-sm text-ink-600">{{ $item->description }}</p>
                    @endif

                    <dl class="grid grid-cols-2 gap-x-4 gap-y-3 text-sm">
                        <div>
                            <dt class="text-xs text-ink-400">Category</dt>
                            <dd class="font-medium text-ink-900">{{ $item->category->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-ink-400">Restaurant</dt>
                            <dd class="font-medium text-ink-900">{{ $item->branch->restaurant->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-ink-400">Branch</dt>
                            <dd class="font-medium text-ink-900">{{ $item->branch->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-ink-400">Availability</dt>
                            <dd class="font-medium {{ $item->is_available ? 'text-green-600' : 'text-ink-500' }}">{{ $item->is_available ? 'Available' : 'Unavailable' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-ink-400">Customer Site</dt>
                            <dd class="font-medium text-ink-900">{{ $item->show_on_customer_site ? 'Visible' : 'Hidden' }}</dd>
                        </div>
                        @if ($item->variations->count())
                            <div>
                                <dt class="text-xs text-ink-400">Variations</dt>
                                <dd class="font-medium text-ink-900">{{ $item->variations->count() }}</dd>
                            </div>
                        @endif
                    </dl>

                    <div class="flex justify-end pt-2">
                        <button type="button" @click="openViewFor = null" class="rounded-lg border border-ink-200 px-4 py-2 text-sm font-medium text-ink-600 hover:bg-ink-50">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    {{-- Edit modals (one per item, kept outside the <table> so they're valid HTML) --}}
    @foreach ($menuItems as $item)
        <div x-show="openEditFor === {{ $item->id }}" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-ink-900/50 p-4 overflow-y-auto">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-xl overflow-hidden my-8" @click.outside="openEditFor = null">
                <div class="flex items-center justify-between px-6 py-4 border-b border-ink-100">
                    <h3 class="text-lg font-bold text-ink-900">Edit Menu Item</h3>
                    <button type="button" @click="openEditFor = null" class="text-ink-400 hover:text-ink-700">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"/>
                            <line x1="6" y1="6" x2="18" y2="18"/>
                        </svg>
                    </button>
                </div>
                <form method="POST" action="{{ route('admin.branches.menu-items.update', [$item->branch->restaurant, $item->branch, $item]) }}" enctype="multipart/form-data" class="p-6 space-y-4">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-ink-500 mb-1">Category</label>
                            <select name="category_id" required class="w-full rounded-lg border-ink-200 shadow-sm text-sm">
                                @foreach ($item->branch->categories as $category)
                                    <option value="{{ $category->id }}" {{ $item->category_id === $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-ink-500 mb-1">Item Name</label>
                            <input type="text" name="name" value="{{ $item->name }}" required class="w-full rounded-lg border-ink-200 shadow-sm text-sm">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-ink-500 mb-1">Price</label>
                            <input type="number" step="0.01" name="price" value="{{ $item->price }}" required class="w-full rounded-lg border-ink-200 shadow-sm text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-ink-500 mb-1">GST %</label>
                            <input type="number" step="0.01" name="gst_percentage" value="{{ $item->gst_percentage }}" required class="w-full rounded-lg border-ink-200 shadow-sm text-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-ink-500 mb-1">Image</label>
                        @if ($item->image_path)
                            <div class="mb-2 flex items-center gap-2">
                                <img src="{{ asset('storage/' . $item->image_path) }}" class="h-10 w-10 rounded-lg object-cover">
                                <span class="text-xs text-ink-400">Current image — upload a new one to replace it</span>
                            </div>
                        @endif
                        <input type="file" name="image" accept="image/*" class="w-full text-sm rounded-lg border-ink-200 shadow-sm file:mr-3 file:rounded-lg file:border-0 file:bg-ink-900 file:px-3 file:py-1.5 file:text-white file:text-xs">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-ink-500 mb-1">Description</label>
                        <textarea name="description" rows="2" placeholder="Short description shown to customers" class="w-full rounded-lg border-ink-200 shadow-sm text-sm">{{ $item->description }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-ink-500 mb-2">Food Type</label>
                        <div class="flex gap-3">
                            @foreach (\App\Enums\FoodType::cases() as $foodType)
                                <label class="flex items-center gap-2 rounded-lg border border-ink-200 px-3 py-2 text-sm cursor-pointer has-[:checked]:border-brand-400 has-[:checked]:bg-brand-50">
                                    <input type="radio" name="food_type" value="{{ $foodType->value }}" {{ $item->food_type === $foodType ? 'checked' : '' }} class="text-brand-600">
                                    <x-food-type-icon :type="$foodType" />
                                    {{ $foodType->label() }}
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex items-center gap-6">
                        <label class="flex items-center gap-2 text-sm text-ink-700">
                            <input type="hidden" name="is_available" value="0">
                            <input type="checkbox" name="is_available" value="1" {{ $item->is_available ? 'checked' : '' }} class="rounded border-ink-300 text-brand-600">
                            Available
                        </label>
                        <label class="flex items-center gap-2 text-sm text-ink-700">
                            <input type="hidden" name="show_on_customer_site" value="0">
                            <input type="checkbox" name="show_on_customer_site" value="1" {{ $item->show_on_customer_site ? 'checked' : '' }} class="rounded border-ink-300 text-brand-600">
                            Show on Customer Site
                        </label>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="openEditFor = null" class="rounded-lg border border-ink-200 px-4 py-2 text-sm font-medium text-ink-600 hover:bg-ink-50">Cancel</button>
                        <button type="submit" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach

    {{-- Variations modals (one per item, kept outside the <table> so they're valid HTML) --}}
    @foreach ($menuItems as $item)
        <div x-show="openVariationsFor === {{ $item->id }}" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-ink-900/50 p-4">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden" @click.outside="openVariationsFor = null">
                <div class="flex items-center justify-between px-6 py-4 border-b border-ink-100">
                    <div>
                        <h3 class="text-lg font-bold text-ink-900">Variations</h3>
                        <p class="text-xs text-ink-400">{{ $item->name }} — e.g. Half / Full, Small / Medium / Large</p>
                    </div>
                    <button type="button" @click="openVariationsFor = null" class="text-ink-400 hover:text-ink-700">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"/>
                            <line x1="6" y1="6" x2="18" y2="18"/>
                        </svg>
                    </button>
                </div>

                <div class="p-6">
                    <div class="flex justify-end mb-3">
                        <button type="button" @click="addVariationOpen = !addVariationOpen" class="inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-brand-700">
                            + Add Variation
                        </button>
                    </div>

                    <div x-show="addVariationOpen" x-cloak class="mb-4 rounded-lg border border-ink-100 bg-ink-50/40 p-3">
                        <form method="POST" action="{{ route('admin.branches.menu-items.variations.store', [$item->branch->restaurant, $item->branch, $item]) }}" class="flex gap-2">
                            @csrf
                            <input type="text" name="name" required placeholder="e.g. Half" class="flex-1 rounded-lg border-ink-200 shadow-sm text-sm">
                            <input type="number" step="0.01" name="price" required placeholder="Price" class="w-28 rounded-lg border-ink-200 shadow-sm text-sm">
                            <button type="submit" class="rounded-lg bg-brand-600 px-3 py-2 text-white text-xs font-medium hover:bg-brand-700">Add</button>
                        </form>
                    </div>

                    <div class="rounded-lg border border-ink-100 overflow-hidden">
                        <table class="w-full text-sm">
                            <thead class="bg-ink-50/60 text-left text-ink-500 text-xs">
                                <tr>
                                    <th class="px-4 py-2 font-bold">Name</th>
                                    <th class="px-4 py-2 font-bold">Price</th>
                                    <th class="px-4 py-2 font-bold text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-ink-100">
                                @forelse ($item->variations as $variation)
                                    <tr>
                                        <td class="px-4 py-2" colspan="3" x-show="editingVariationId === {{ $variation->id }}" x-cloak>
                                            <form method="POST" action="{{ route('admin.branches.menu-items.variations.update', [$item->branch->restaurant, $item->branch, $item, $variation]) }}" class="flex items-center gap-2">
                                                @csrf @method('PUT')
                                                <input type="text" name="name" value="{{ $variation->name }}" required class="flex-1 rounded-lg border-ink-200 shadow-sm text-sm">
                                                <input type="number" step="0.01" name="price" value="{{ $variation->price }}" required class="w-24 rounded-lg border-ink-200 shadow-sm text-sm">
                                                <button type="submit" class="rounded-lg bg-brand-600 px-3 py-1.5 text-white text-xs font-medium hover:bg-brand-700">Save</button>
                                                <button type="button" @click="editingVariationId = null" class="rounded-lg border border-ink-200 px-3 py-1.5 text-xs font-medium text-ink-600 hover:bg-ink-50">Cancel</button>
                                            </form>
                                        </td>
                                        <td class="px-4 py-2 font-medium text-ink-900" x-show="editingVariationId !== {{ $variation->id }}">{{ $variation->name }}</td>
                                        <td class="px-4 py-2 text-ink-600" x-show="editingVariationId !== {{ $variation->id }}">₹{{ number_format($variation->price, 2) }}</td>
                                        <td class="px-4 py-2 text-right" x-show="editingVariationId !== {{ $variation->id }}">
                                            <div class="inline-flex gap-1.5">
                                                <button type="button" @click="editingVariationId = {{ $variation->id }}"
                                                        class="flex h-6 w-6 items-center justify-center rounded-md border border-blue-200 bg-blue-50 text-blue-600 hover:bg-blue-100">
                                                    <svg viewBox="0 0 24 24" class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M12 20h9"/>
                                                        <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/>
                                                    </svg>
                                                </button>
                                                <form method="POST" action="{{ route('admin.branches.menu-items.variations.destroy', [$item->branch->restaurant, $item->branch, $item, $variation]) }}" onsubmit="return confirm('Remove this variation?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="flex h-6 w-6 items-center justify-center rounded-md border border-red-200 bg-red-50 text-red-600 hover:bg-red-100">
                                                        <svg viewBox="0 0 24 24" class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                                            <polyline points="3 6 5 6 21 6"/>
                                                            <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-4 py-6 text-center text-ink-400 text-sm">No variations yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <x-pagination :paginator="$menuItems" />
</div>
@endsection
