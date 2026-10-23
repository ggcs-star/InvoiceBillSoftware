@extends('layouts.app')

@section('title', 'Menu Items')
@section('header', 'Menu Items')

@section('content')
<div x-data="{
        addOpen: false,
        restaurantId: '',
        branchId: '',
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
    <div class="flex justify-end gap-2 mb-4">
        <a href="{{ route('admin.menu-items.export', request()->query()) }}" class="inline-flex items-center gap-1.5 rounded-lg border border-ink-200 px-4 py-2 text-sm font-medium text-ink-600 hover:bg-ink-50">
            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 16V4"/>
                <path d="M7 11l5 5 5-5"/>
                <path d="M4 16v3a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-3"/>
            </svg>
            Export Menu Items
        </a>
        <a href="{{ route('admin.menu-items.bulk-import') }}" class="inline-flex items-center gap-1.5 rounded-lg border border-ink-200 px-4 py-2 text-sm font-medium text-ink-600 hover:bg-ink-50">
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
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search your menu item here"
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
                <label class="block text-xs font-medium text-ink-500 mb-1">Availability</label>
                <x-searchable-select name="availability" placeholder="All Availability"
                    :options="[['value' => 'available', 'label' => 'Available'], ['value' => 'unavailable', 'label' => 'Unavailable']]"
                    :selected="request('availability')" />
            </div>
            <button type="submit" class="rounded-lg bg-brand-600 px-4 py-2 text-white text-sm font-medium hover:bg-brand-700">Filter</button>
            @if (request()->anyFilled(['search', 'restaurant_id', 'branch_id', 'availability']))
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
                <tbody x-data="{ showVariations: false }">
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
                        <td class="px-5 py-3 text-ink-500">{{ $item->category->name }}</td>
                        <td class="px-5 py-3">
                            <a href="{{ route('admin.restaurants.show', $item->branch->restaurant) }}" class="text-brand-600 hover:underline">
                                {{ $item->branch->restaurant->name }}
                            </a>
                        </td>
                        <td class="px-5 py-3 text-ink-500">{{ $item->branch->name }}</td>
                        <td class="px-5 py-3">
                            <form method="POST" action="{{ route('admin.branches.menu-items.toggle', [$item->branch->restaurant, $item->branch, $item]) }}">
                                @csrf @method('PATCH')
                                <button type="submit" title="{{ $item->is_available ? 'Available — click to mark unavailable' : 'Unavailable — click to mark available' }}"
                                        class="flex h-5 w-5 items-center justify-center rounded border-2 {{ $item->is_available ? 'bg-brand-600 border-brand-600 text-white' : 'border-ink-300 text-transparent' }}">
                                    <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20 6 9 17l-5-5"/>
                                    </svg>
                                </button>
                            </form>
                        </td>
                        <td class="px-5 py-3">
                            <form method="POST" action="{{ route('admin.branches.menu-items.toggle-customer-site', [$item->branch->restaurant, $item->branch, $item]) }}">
                                @csrf @method('PATCH')
                                <button type="submit" title="{{ $item->show_on_customer_site ? 'Visible on customer site — click to hide' : 'Hidden from customer site — click to show' }}"
                                        class="flex h-5 w-5 items-center justify-center rounded border-2 {{ $item->show_on_customer_site ? 'bg-brand-600 border-brand-600 text-white' : 'border-ink-300 text-transparent' }}">
                                    <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20 6 9 17l-5-5"/>
                                    </svg>
                                </button>
                            </form>
                        </td>
                        <td class="px-5 py-3 text-right">
                            <div class="inline-flex gap-2">
                                <button type="button" @click="showVariations = !showVariations" class="inline-flex items-center gap-1 rounded-lg border border-ink-200 px-3 py-1.5 text-xs font-medium text-ink-600 hover:bg-ink-50">
                                    Variations
                                    @if ($item->variations->count())
                                        <span class="text-ink-400">({{ $item->variations->count() }})</span>
                                    @endif
                                </button>
                                <a href="{{ route('admin.branches.menu-items.index', [$item->branch->restaurant, $item->branch]) }}"
                                   class="flex h-7 w-7 items-center justify-center rounded-lg border border-ink-200 text-ink-500 hover:bg-ink-50">
                                    <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 20h9"/>
                                        <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/>
                                    </svg>
                                </a>
                                <form method="POST" action="{{ route('admin.branches.menu-items.destroy', [$item->branch->restaurant, $item->branch, $item]) }}" onsubmit="return confirm('Remove this item?')">
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
                    <tr x-show="showVariations" x-cloak class="border-b last:border-0 bg-ink-50/40">
                        <td colspan="8" class="px-5 py-4">
                            <div class="text-xs font-semibold uppercase tracking-wide text-ink-400 mb-2">
                                Variations for {{ $item->name }} <span class="normal-case font-normal">— e.g. Half / Full, Small / Medium / Large</span>
                            </div>

                            @if ($item->variations->isNotEmpty())
                                <div class="space-y-1 mb-3">
                                    @foreach ($item->variations as $variation)
                                        <div class="flex items-center gap-3 bg-white rounded-lg border border-ink-100 px-3 py-2 text-sm">
                                            <span class="flex-1 font-medium text-ink-900">{{ $variation->name }}</span>
                                            <span class="text-ink-600">₹{{ number_format($variation->price, 2) }}</span>
                                            <form method="POST" action="{{ route('admin.branches.menu-items.variations.destroy', [$item->branch->restaurant, $item->branch, $item, $variation]) }}" onsubmit="return confirm('Remove this variation?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:underline text-xs">Remove</button>
                                            </form>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <form method="POST" action="{{ route('admin.branches.menu-items.variations.store', [$item->branch->restaurant, $item->branch, $item]) }}" class="flex gap-2 max-w-md">
                                @csrf
                                <input type="text" name="name" required placeholder="e.g. Half" class="flex-1 rounded-lg border-ink-200 shadow-sm text-sm">
                                <input type="number" step="0.01" name="price" required placeholder="Price" class="w-28 rounded-lg border-ink-200 shadow-sm text-sm">
                                <button type="submit" class="rounded-lg bg-brand-600 px-3 py-2 text-white text-xs font-medium hover:bg-brand-700">+ Add</button>
                            </form>
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

    <x-pagination :paginator="$menuItems" />
</div>
@endsection
