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

    <div x-data="{ open: false, editOpen: null }" class="space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-ink-900">Item Categories</h2>
            <button type="button" @click="open = true" class="rounded-lg bg-brand-600 px-4 py-2 text-white text-sm font-medium hover:bg-brand-700">
                + Add Item Category
            </button>
        </div>

        {{-- Add Category modal --}}
        <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-ink-900/50 p-4">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden" @click.outside="open = false">
                <div class="flex items-center justify-between px-6 py-4 border-b border-ink-100">
                    <h3 class="text-lg font-bold text-ink-900">Add Item Category</h3>
                    <button type="button" @click="open = false" class="text-ink-400 hover:text-ink-700">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"/>
                            <line x1="6" y1="6" x2="18" y2="18"/>
                        </svg>
                    </button>
                </div>
                <form method="POST" action="{{ route('admin.branches.categories.store', [$restaurant, $branch]) }}" class="p-6 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-ink-500 mb-1">Category Name</label>
                        <input type="text" name="name" required placeholder="e.g. Pizzas" class="w-full rounded-lg border-ink-200 px-3 py-2 shadow-sm text-sm">
                    </div>
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="open = false" class="rounded-lg border border-ink-200 px-4 py-2 text-sm font-medium text-ink-600 hover:bg-ink-50">Cancel</button>
                        <button type="submit" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700">Add Category</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-ink-100 shadow-card p-4">
            <form method="GET" class="flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-[180px]">
                    <label class="block text-xs font-medium text-ink-500 mb-1">Search</label>
                    <div class="relative">
                        <svg viewBox="0 0 24 24" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-ink-400" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="7"/>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search categories..."
                               class="w-full rounded-lg border-ink-200 bg-white pl-10 pr-4 py-2 shadow-sm text-sm">
                    </div>
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

        <div class="bg-white rounded-2xl border border-ink-100 shadow-card overflow-x-auto">
            <table class="w-full text-sm whitespace-nowrap">
                <thead class="text-left text-ink-500 border-b bg-ink-50/60 font-bold">
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
                            <td class="px-5 py-3 text-right">
                                <div class="inline-flex gap-2">
                                    <a href="{{ route('admin.branches.menu-items.index', [$restaurant, $branch, 'category_id' => $category->id]) }}" title="View Items"
                                       class="flex h-7 w-7 items-center justify-center rounded-lg border border-ink-200 text-ink-500 hover:bg-ink-50">
                                        <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7Z"/>
                                            <circle cx="12" cy="12" r="3"/>
                                        </svg>
                                    </a>
                                    <button type="button" title="Edit" @click="editOpen = {{ $category->id }}"
                                            class="flex h-7 w-7 items-center justify-center rounded-lg border border-blue-200 bg-blue-50 text-blue-600 hover:bg-blue-100">
                                        <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M12 20h9"/>
                                            <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/>
                                        </svg>
                                    </button>
                                    <form method="POST" action="{{ route('admin.branches.categories.destroy', [$restaurant, $branch, $category]) }}" onsubmit="return confirm('Remove this category and its items?')">
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
                        <tr><td colspan="4" class="px-5 py-10 text-center text-ink-400">No categories match.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-pagination :paginator="$categories" />

        {{-- Edit modals (one per category) --}}
        @foreach ($categories as $category)
            <div x-show="editOpen === {{ $category->id }}" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-ink-900/50 p-4">
                <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden" @click.outside="editOpen = null">
                    <div class="flex items-center justify-between px-6 py-4 border-b border-ink-100">
                        <h3 class="text-lg font-bold text-ink-900">Edit Item Category</h3>
                        <button type="button" @click="editOpen = null" class="text-ink-400 hover:text-ink-700">
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="18" y1="6" x2="6" y2="18"/>
                                <line x1="6" y1="6" x2="18" y2="18"/>
                            </svg>
                        </button>
                    </div>
                    <form method="POST" action="{{ route('admin.branches.categories.update', [$restaurant, $branch, $category]) }}" class="p-6 space-y-4">
                        @csrf
                        @method('PUT')
                        <div>
                            <label class="block text-xs font-medium text-ink-500 mb-1">Category Name</label>
                            <input type="text" name="name" value="{{ $category->name }}" required class="w-full rounded-lg border-ink-200 px-3 py-2 shadow-sm text-sm">
                        </div>
                        <label class="flex items-center gap-2 text-sm text-ink-700">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" {{ $category->is_active ? 'checked' : '' }} class="rounded border-ink-300 text-brand-600">
                            Active
                        </label>
                        <div class="flex justify-end gap-2 pt-2">
                            <button type="button" @click="editOpen = null" class="rounded-lg border border-ink-200 px-4 py-2 text-sm font-medium text-ink-600 hover:bg-ink-50">Cancel</button>
                            <button type="submit" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
@endsection
