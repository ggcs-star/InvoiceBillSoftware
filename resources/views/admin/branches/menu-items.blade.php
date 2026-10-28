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

    <div x-data="{
            addOpen: false,
            openViewFor: null,
            openEditFor: null,
            openVariationsFor: null,
            editingVariationId: null,
            addVariationOpen: false,
         }" class="space-y-4">
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
        <div x-show="addOpen" x-cloak class="fixed inset-0 z-50 flex items-start justify-center bg-ink-900/50 p-4 overflow-y-auto">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-xl overflow-hidden my-8" @click.outside="addOpen = false">
                <div class="flex items-center justify-between px-4 py-4 border-b border-ink-100 sm:px-6">
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
                    <form method="POST" action="{{ route('admin.branches.menu-items.store', [$restaurant, $branch]) }}" enctype="multipart/form-data" class="p-4 space-y-4 sm:p-6">
                        @csrf
                        <div class="grid grid-cols-1 gap-3 items-end sm:grid-cols-2">
                            <div>
                                <label class="block text-xs font-medium text-ink-500 mb-1">Category</label>
                                <x-searchable-select name="category_id" placeholder="Select a category"
                                    :options="$categories->map(fn ($c) => ['value' => $c->id, 'label' => $c->name])" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-ink-500 mb-1">Item Name</label>
                                <input type="text" name="name" required placeholder="e.g. Margherita Pizza" class="w-full rounded-lg border-ink-200 px-3 py-2 shadow-sm text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-ink-500 mb-1">Price</label>
                                <input type="number" step="0.01" name="price" required placeholder="249.00" class="w-full rounded-lg border-ink-200 px-3 py-2 shadow-sm text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-ink-500 mb-1">GST %</label>
                                <input type="number" step="0.01" name="gst_percentage" required placeholder="5" value="5" class="w-full rounded-lg border-ink-200 px-3 py-2 shadow-sm text-sm">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-ink-500 mb-1">Image</label>
                            <input type="file" name="image" accept="image/*" class="w-full text-sm rounded-lg border-ink-200 px-3 py-2 shadow-sm file:mr-3 file:rounded-lg file:border-0 file:bg-ink-900 file:px-3 file:py-1.5 file:text-white file:text-xs">
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-ink-500 mb-1">Description</label>
                            <textarea name="description" rows="2" placeholder="Short description shown to customers" class="w-full rounded-lg border-ink-200 px-3 py-2 shadow-sm text-sm"></textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-ink-500 mb-2">Food Type</label>
                            <div class="flex flex-wrap gap-3">
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
                <thead class="text-left text-ink-500 border-b bg-ink-50/60 font-bold">
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
                <tbody>
                    @forelse ($menuItems as $item)
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
                            <td class="px-5 py-3">₹{{ number_format($item->price, 2) }}</td>
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center rounded-full bg-brand-50 px-2.5 py-1 text-xs font-medium text-brand-600">{{ $item->category->name }}</span>
                            </td>
                            <td class="px-5 py-3 text-ink-500">{{ $restaurant->name }}</td>
                            <td class="px-5 py-3 text-ink-500">{{ $branch->name }}</td>
                            <td class="px-5 py-3">
                                <form method="POST" action="{{ route('admin.branches.menu-items.toggle', [$restaurant, $branch, $item]) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" title="Click to toggle availability"
                                            class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium {{ $item->is_available ? 'bg-green-100 text-green-700' : 'bg-ink-100 text-ink-500' }}">
                                        <span class="h-1.5 w-1.5 rounded-full {{ $item->is_available ? 'bg-green-500' : 'bg-ink-300' }}"></span>
                                        {{ $item->is_available ? 'Available' : 'Unavailable' }}
                                    </button>
                                </form>
                            </td>
                            <td class="px-5 py-3">
                                <form method="POST" action="{{ route('admin.branches.menu-items.toggle-customer-site', [$restaurant, $branch, $item]) }}">
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
                                    <form method="POST" action="{{ route('admin.branches.menu-items.destroy', [$restaurant, $branch, $item]) }}" onsubmit="return confirm('Remove this item?')">
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
                    @empty
                        <tr><td colspan="8" class="px-5 py-10 text-center text-ink-400">No menu items match these filters.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-pagination :paginator="$menuItems" />

        {{-- View modals (one per item) --}}
        @foreach ($menuItems as $item)
            <div x-show="openViewFor === {{ $item->id }}" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-ink-900/50 p-4">
                <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden" @click.outside="openViewFor = null">
                    <div class="flex items-center justify-between px-4 py-4 border-b border-ink-100 sm:px-6">
                        <h3 class="text-lg font-bold text-ink-900">Menu Item Details</h3>
                        <button type="button" @click="openViewFor = null" class="text-ink-400 hover:text-ink-700">
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="18" y1="6" x2="6" y2="18"/>
                                <line x1="6" y1="6" x2="18" y2="18"/>
                            </svg>
                        </button>
                    </div>
                    <div class="p-4 space-y-4 sm:p-6">
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

                        <dl class="grid grid-cols-1 gap-x-4 gap-y-3 text-sm sm:grid-cols-2">
                            <div>
                                <dt class="text-xs text-ink-400">Category</dt>
                                <dd class="font-medium text-ink-900">{{ $item->category->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-ink-400">Branch</dt>
                                <dd class="font-medium text-ink-900">{{ $branch->name }}</dd>
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

        {{-- Edit modals (one per item) --}}
        @foreach ($menuItems as $item)
            <div x-show="openEditFor === {{ $item->id }}" x-cloak class="fixed inset-0 z-50 flex items-start justify-center bg-ink-900/50 p-4 overflow-y-auto">
                <div class="bg-white rounded-2xl shadow-xl w-full max-w-xl overflow-hidden my-8" @click.outside="openEditFor = null">
                    <div class="flex items-center justify-between px-4 py-4 border-b border-ink-100 sm:px-6">
                        <h3 class="text-lg font-bold text-ink-900">Edit Menu Item</h3>
                        <button type="button" @click="openEditFor = null" class="text-ink-400 hover:text-ink-700">
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="18" y1="6" x2="6" y2="18"/>
                                <line x1="6" y1="6" x2="18" y2="18"/>
                            </svg>
                        </button>
                    </div>
                    <form method="POST" action="{{ route('admin.branches.menu-items.update', [$restaurant, $branch, $item]) }}" enctype="multipart/form-data" class="p-4 space-y-4 sm:p-6">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label class="block text-xs font-medium text-ink-500 mb-1">Category</label>
                                <x-searchable-select name="category_id" placeholder="Select a category"
                                    :options="$categories->map(fn ($c) => ['value' => $c->id, 'label' => $c->name])"
                                    :selected="$item->category_id" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-ink-500 mb-1">Item Name</label>
                                <input type="text" name="name" value="{{ $item->name }}" required class="w-full rounded-lg border-ink-200 px-3 py-2 shadow-sm text-sm">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label class="block text-xs font-medium text-ink-500 mb-1">Price</label>
                                <input type="number" step="0.01" name="price" value="{{ $item->price }}" required class="w-full rounded-lg border-ink-200 px-3 py-2 shadow-sm text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-ink-500 mb-1">GST %</label>
                                <input type="number" step="0.01" name="gst_percentage" value="{{ $item->gst_percentage }}" required class="w-full rounded-lg border-ink-200 px-3 py-2 shadow-sm text-sm">
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
                            <input type="file" name="image" accept="image/*" class="w-full text-sm rounded-lg border-ink-200 px-3 py-2 shadow-sm file:mr-3 file:rounded-lg file:border-0 file:bg-ink-900 file:px-3 file:py-1.5 file:text-white file:text-xs">
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-ink-500 mb-1">Description</label>
                            <textarea name="description" rows="2" placeholder="Short description shown to customers" class="w-full rounded-lg border-ink-200 px-3 py-2 shadow-sm text-sm">{{ $item->description }}</textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-ink-500 mb-2">Food Type</label>
                            <div class="flex flex-wrap gap-3">
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

        {{-- Variations modals (one per item) --}}
        @foreach ($menuItems as $item)
            <div x-show="openVariationsFor === {{ $item->id }}" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-ink-900/50 p-4">
                <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden" @click.outside="openVariationsFor = null">
                    <div class="flex items-center justify-between px-4 py-4 border-b border-ink-100 sm:px-6">
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

                    <div class="p-4 sm:p-6">
                        <div class="flex justify-end mb-3">
                            <button type="button" @click="addVariationOpen = !addVariationOpen" class="inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-brand-700">
                                + Add Variation
                            </button>
                        </div>

                        <div x-show="addVariationOpen" x-cloak class="mb-4 rounded-lg border border-ink-100 bg-ink-50/40 p-3">
                            <form method="POST" action="{{ route('admin.branches.menu-items.variations.store', [$restaurant, $branch, $item]) }}" class="flex flex-col gap-2 sm:flex-row">
                                @csrf
                                <input type="text" name="name" required placeholder="e.g. Half" class="flex-1 rounded-lg border-ink-200 px-3 py-2 shadow-sm text-sm">
                                <input type="number" step="0.01" name="price" required placeholder="Price" class="w-full rounded-lg border-ink-200 px-3 py-2 shadow-sm text-sm sm:w-28">
                                <button type="submit" class="w-full rounded-lg bg-brand-600 px-3 py-2 text-white text-xs font-medium hover:bg-brand-700 sm:w-auto">Add</button>
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
                                                <form method="POST" action="{{ route('admin.branches.menu-items.variations.update', [$restaurant, $branch, $item, $variation]) }}" class="flex items-center gap-2">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="text" name="name" value="{{ $variation->name }}" required class="flex-1 rounded-lg border-ink-200 px-3 py-2 shadow-sm text-sm">
                                                    <input type="number" step="0.01" name="price" value="{{ $variation->price }}" required class="w-24 rounded-lg border-ink-200 px-3 py-2 shadow-sm text-sm">
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
                                                    <form method="POST" action="{{ route('admin.branches.menu-items.variations.destroy', [$restaurant, $branch, $item, $variation]) }}" onsubmit="return confirm('Remove this variation?')">
                                                        @csrf
                                                        @method('DELETE')
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
    </div>
@endsection
