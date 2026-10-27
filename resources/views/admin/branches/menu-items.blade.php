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
                <x-searchable-select name="category_id" placeholder="All Categories"
                    :options="$categories->map(fn ($c) => ['value' => $c->id, 'label' => $c->name])"
                    :selected="request('category_id')" />
                <x-searchable-select name="availability" placeholder="All Availability"
                    :options="[['value' => 'available', 'label' => 'Available'], ['value' => 'unavailable', 'label' => 'Unavailable']]"
                    :selected="request('availability')" />
                <button type="submit" class="rounded-lg border border-ink-200 px-4 py-2 text-sm font-medium text-ink-600 hover:bg-ink-50">Search</button>
                @if (request()->anyFilled(['search', 'category_id', 'availability']))
                    <a href="{{ route('admin.branches.menu-items.index', [$restaurant, $branch]) }}" class="rounded-lg border border-ink-200 px-4 py-2 text-sm font-medium text-ink-600 hover:bg-ink-50">Clear</a>
                @endif
                <button type="button" @click="addOpen = true"
                        class="ml-auto rounded-lg bg-brand-600 px-4 py-2 text-white text-sm font-medium hover:bg-brand-700">
                    + Add Menu Item
                </button>
            </form>
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

                @if ($categories->isEmpty())
                    <p class="p-6 text-sm text-ink-500">
                        Add an <a href="{{ route('admin.branches.categories.index', [$restaurant, $branch]) }}" class="text-brand-600 hover:underline">item category</a> first before adding menu items.
                    </p>
                @else
                    <form method="POST" action="{{ route('admin.branches.menu-items.store', [$restaurant, $branch]) }}" enctype="multipart/form-data" class="p-6 space-y-4">
                        @csrf
                        <div class="grid grid-cols-4 gap-3 items-end">
                            <div>
                                <label class="block text-xs font-medium text-ink-500 mb-1">Category</label>
                                <x-searchable-select name="category_id" placeholder="Select a category"
                                    :options="$categories->map(fn ($c) => ['value' => $c->id, 'label' => $c->name])" />
                            </div>
                            <div>
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
                @endif
            </div>
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
                    <tbody x-data="{ editing: false, showVariations: false }">
                        <tr class="border-b last:border-0">
                            <td class="px-5 py-3" colspan="8" x-show="editing" x-cloak>
                                <form method="POST" action="{{ route('admin.branches.menu-items.update', [$restaurant, $branch, $item]) }}" enctype="multipart/form-data" class="space-y-3">
                                    @csrf @method('PUT')
                                    <div class="grid grid-cols-6 gap-3 items-end">
                                        <select name="category_id" required class="w-full rounded-lg border-ink-200 shadow-sm text-sm">
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}" {{ $item->category_id === $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                        <input type="text" name="name" value="{{ $item->name }}" required class="col-span-2 w-full rounded-lg border-ink-200 shadow-sm text-sm">
                                        <input type="number" step="0.01" name="price" value="{{ $item->price }}" required class="w-full rounded-lg border-ink-200 shadow-sm text-sm">
                                        <input type="number" step="0.01" name="gst_percentage" value="{{ $item->gst_percentage }}" required class="w-full rounded-lg border-ink-200 shadow-sm text-sm">
                                        <input type="file" name="image" accept="image/*" class="w-full text-xs rounded-lg border-ink-200 shadow-sm file:mr-2 file:rounded-lg file:border-0 file:bg-ink-900 file:px-2 file:py-1.5 file:text-white file:text-xs">
                                    </div>
                                    <div class="flex flex-wrap items-center gap-4">
                                        <div class="flex gap-2">
                                            @foreach (\App\Enums\FoodType::cases() as $foodType)
                                                <label class="flex items-center gap-1.5 rounded-lg border border-ink-200 px-2.5 py-1.5 text-xs cursor-pointer has-[:checked]:border-brand-400 has-[:checked]:bg-brand-50">
                                                    <input type="radio" name="food_type" value="{{ $foodType->value }}" {{ $item->food_type === $foodType ? 'checked' : '' }} class="text-brand-600">
                                                    <x-food-type-icon :type="$foodType" />
                                                    {{ $foodType->label() }}
                                                </label>
                                            @endforeach
                                        </div>
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
                                        <div class="ml-auto flex gap-2">
                                            <button type="submit" class="rounded-lg bg-brand-600 px-3 py-2 text-white text-sm font-medium hover:bg-brand-700">Save</button>
                                            <button type="button" @click="editing = false" class="rounded-lg border border-ink-200 px-3 py-2 text-sm font-medium text-ink-600 hover:bg-ink-50">Cancel</button>
                                        </div>
                                    </div>
                                </form>
                            </td>

                            <td class="px-5 py-3" x-show="!editing">
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
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-1.5">
                                            <x-food-type-icon :type="$item->food_type" />
                                            <span class="font-medium text-ink-900 truncate">{{ $item->name }}</span>
                                        </div>
                                        @if ($item->description)
                                            <div class="text-xs text-ink-400 truncate max-w-xs">{{ $item->description }}</div>
                                        @endif
                                    </div>
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
                            <td class="px-5 py-3" x-show="!editing">
                                <form method="POST" action="{{ route('admin.branches.menu-items.toggle-customer-site', [$restaurant, $branch, $item]) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" title="{{ $item->show_on_customer_site ? 'Visible on customer site — click to hide' : 'Hidden from customer site — click to show' }}"
                                            class="flex h-5 w-5 items-center justify-center rounded border-2 {{ $item->show_on_customer_site ? 'bg-brand-600 border-brand-600 text-white' : 'border-ink-300 text-transparent' }}">
                                        <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M20 6 9 17l-5-5"/>
                                        </svg>
                                    </button>
                                </form>
                            </td>
                            <td class="px-5 py-3 text-right" x-show="!editing">
                                <div class="inline-flex gap-2">
                                    <button type="button" @click="showVariations = !showVariations" class="inline-flex items-center gap-1 rounded-lg border border-ink-200 px-3 py-1.5 text-xs font-medium text-ink-600 hover:bg-ink-50">
                                        Variations
                                        @if ($item->variations->count())
                                            <span class="text-ink-400">({{ $item->variations->count() }})</span>
                                        @endif
                                    </button>
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
                                                <form method="POST" action="{{ route('admin.branches.menu-items.variations.destroy', [$restaurant, $branch, $item, $variation]) }}" onsubmit="return confirm('Remove this variation?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:underline text-xs">Remove</button>
                                                </form>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                <form method="POST" action="{{ route('admin.branches.menu-items.variations.store', [$restaurant, $branch, $item]) }}" class="flex gap-2 max-w-md">
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
