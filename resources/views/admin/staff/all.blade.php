@extends('layouts.app')

@section('title', 'Staff')
@section('header', 'Staff')

@section('content')
<div x-data="{
        addOpen: false,
        restaurantId: '',
        branchId: '',
        restaurantOpen: false,
        restaurantQuery: '',
        branchOpen: false,
        branchQuery: '',
        restaurants: @js($restaurants->map(fn ($r) => ['id' => $r->id, 'name' => $r->name])),
        branches: @js($branches->map(fn ($b) => ['id' => $b->id, 'name' => $b->name, 'restaurant_id' => $b->restaurant_id])),
        get branchOptions() {
            return this.branches.filter((b) => String(b.restaurant_id) === String(this.restaurantId));
        },
        get filteredRestaurants() {
            if (!this.restaurantQuery) return this.restaurants;
            const needle = this.restaurantQuery.toLowerCase();
            return this.restaurants.filter((r) => r.name.toLowerCase().includes(needle));
        },
        get filteredBranchOptions() {
            if (!this.branchQuery) return this.branchOptions;
            const needle = this.branchQuery.toLowerCase();
            return this.branchOptions.filter((b) => b.name.toLowerCase().includes(needle));
        },
        get selectedRestaurantName() {
            return (this.restaurants.find((r) => String(r.id) === this.restaurantId) || {}).name ?? 'Select a restaurant';
        },
        get selectedBranchName() {
            return (this.branches.find((b) => String(b.id) === this.branchId) || {}).name ?? 'Select a branch';
        },
        selectRestaurant(id, name) {
            this.restaurantId = String(id);
            this.branchId = '';
            this.restaurantOpen = false;
            this.restaurantQuery = '';
        },
        selectBranch(id, name) {
            this.branchId = String(id);
            this.branchOpen = false;
            this.branchQuery = '';
        },
        get storeUrl() {
            return '/admin/restaurants/' + this.restaurantId + '/branches/' + this.branchId + '/staff';
        },
     }">
    <div class="flex items-start justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-ink-900">Staff</h1>
            <p class="mt-1 text-sm text-ink-500">Every branch staff account across all restaurants, in one place.</p>
        </div>
        <button type="button" @click="addOpen = true" class="shrink-0 rounded-lg bg-brand-600 px-4 py-2 text-white text-sm font-medium hover:bg-brand-700">
            + Add Staff
        </button>
    </div>

    {{-- Add Staff modal --}}
    <div x-show="addOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-ink-900/50 p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden" @click.outside="addOpen = false">
            <div class="flex items-center justify-between px-6 py-4 border-b border-ink-100">
                <h3 class="text-lg font-bold text-ink-900">Add Staff Account</h3>
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
                    <div class="relative" @click.outside="restaurantOpen = false">
                        <button type="button" @click="restaurantOpen = !restaurantOpen; branchOpen = false"
                                class="w-full flex items-center justify-between gap-2 rounded-lg border border-ink-200 bg-white px-3 py-2 text-sm text-left shadow-sm">
                            <span x-text="selectedRestaurantName" class="truncate" :class="restaurantId ? 'text-ink-900' : 'text-ink-500'"></span>
                            <svg viewBox="0 0 24 24" class="h-4 w-4 shrink-0 text-ink-400" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M6 9l6 6 6-6"/>
                            </svg>
                        </button>
                        <div x-show="restaurantOpen" x-cloak x-transition class="absolute z-30 mt-1 w-full rounded-lg border border-ink-100 bg-white shadow-lg">
                            <div class="p-2 border-b border-ink-100">
                                <div class="relative">
                                    <svg viewBox="0 0 24 24" class="pointer-events-none absolute left-2.5 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-ink-400" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="11" cy="11" r="7"/>
                                        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                                    </svg>
                                    <input type="text" x-model="restaurantQuery" placeholder="Search restaurants..."
                                           class="w-full rounded-md border-ink-200 pl-8 pr-2 py-1.5 text-sm">
                                </div>
                            </div>
                            <ul class="max-h-56 overflow-y-auto py-1">
                                <template x-for="r in filteredRestaurants" :key="r.id">
                                    <li @click="selectRestaurant(r.id, r.name)" x-text="r.name"
                                        class="px-3 py-1.5 text-sm cursor-pointer hover:bg-brand-50"
                                        :class="String(r.id) === restaurantId ? 'bg-brand-50 text-brand-600 font-medium' : 'text-ink-700'"></li>
                                </template>
                                <li x-show="filteredRestaurants.length === 0" class="px-3 py-1.5 text-sm text-ink-400">No matches</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-ink-500 mb-1">Branch</label>
                    <div class="relative" @click.outside="branchOpen = false">
                        <button type="button" :disabled="!restaurantId" @click="branchOpen = !branchOpen; restaurantOpen = false"
                                class="w-full flex items-center justify-between gap-2 rounded-lg border border-ink-200 bg-white px-3 py-2 text-sm text-left shadow-sm disabled:bg-ink-50 disabled:cursor-not-allowed">
                            <span x-text="selectedBranchName" class="truncate" :class="branchId ? 'text-ink-900' : 'text-ink-500'"></span>
                            <svg viewBox="0 0 24 24" class="h-4 w-4 shrink-0 text-ink-400" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M6 9l6 6 6-6"/>
                            </svg>
                        </button>
                        <div x-show="branchOpen" x-cloak x-transition class="absolute z-30 mt-1 w-full rounded-lg border border-ink-100 bg-white shadow-lg">
                            <div class="p-2 border-b border-ink-100">
                                <div class="relative">
                                    <svg viewBox="0 0 24 24" class="pointer-events-none absolute left-2.5 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-ink-400" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="11" cy="11" r="7"/>
                                        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                                    </svg>
                                    <input type="text" x-model="branchQuery" placeholder="Search branches..."
                                           class="w-full rounded-md border-ink-200 pl-8 pr-2 py-1.5 text-sm">
                                </div>
                            </div>
                            <ul class="max-h-56 overflow-y-auto py-1">
                                <template x-for="b in filteredBranchOptions" :key="b.id">
                                    <li @click="selectBranch(b.id, b.name)" x-text="b.name"
                                        class="px-3 py-1.5 text-sm cursor-pointer hover:bg-brand-50"
                                        :class="String(b.id) === branchId ? 'bg-brand-50 text-brand-600 font-medium' : 'text-ink-700'"></li>
                                </template>
                                <li x-show="filteredBranchOptions.length === 0" class="px-3 py-1.5 text-sm text-ink-400">No matches</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-ink-500 mb-1">Name</label>
                    <input type="text" name="name" required class="w-full rounded-lg border-ink-200 px-3 py-2 shadow-sm text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-ink-500 mb-1">Email</label>
                    <input type="email" name="email" required class="w-full rounded-lg border-ink-200 px-3 py-2 shadow-sm text-sm">
                </div>
                <div x-data="{ showPassword: false }">
                    <label class="block text-xs font-medium text-ink-500 mb-1">Password</label>
                    <div class="relative">
                        <input :type="showPassword ? 'text' : 'password'" name="password" required minlength="8" class="w-full rounded-lg border-ink-200 px-3 py-2 pr-10 shadow-sm text-sm">
                        <button type="button" @click="showPassword = !showPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-ink-400 hover:text-ink-600">
                            <svg x-show="!showPassword" viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7Z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                            <svg x-show="showPassword" x-cloak viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17.94 17.94A10.94 10.94 0 0 1 12 20c-7 0-11-8-11-8a21.6 21.6 0 0 1 5.06-6.44M9.9 4.24A10.4 10.4 0 0 1 12 4c7 0 11 8 11 8a21.6 21.6 0 0 1-2.16 3.19M14.12 14.12a3 3 0 1 1-4.24-4.24"/>
                                <line x1="1" y1="1" x2="23" y2="23"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <label class="flex items-center gap-2 text-sm text-ink-700">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" checked class="rounded border-ink-300 text-brand-600">
                    Active
                </label>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="addOpen = false" class="rounded-lg border border-ink-200 px-4 py-2 text-sm font-medium text-ink-600 hover:bg-ink-50">Cancel</button>
                    <button type="submit" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700">Create Account</button>
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
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or email..."
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
                <label class="block text-xs font-medium text-ink-500 mb-1">Status</label>
                <x-searchable-select name="status" placeholder="All Status"
                    :options="[['value' => 'active', 'label' => 'Active'], ['value' => 'inactive', 'label' => 'Inactive']]"
                    :selected="request('status')" />
            </div>
            <button type="submit" class="flex items-center gap-1.5 rounded-lg bg-brand-600 px-4 py-2 text-white text-sm font-medium hover:bg-brand-700">
                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 5h16l-6 8v6l-4 2v-8L4 5Z"/>
                </svg>
                Filter
            </button>
            @if (request()->anyFilled(['search', 'restaurant_id', 'branch_id', 'status']))
                <a href="{{ route('admin.staff.index') }}" class="rounded-lg border border-ink-200 px-4 py-2 text-sm font-medium text-ink-600 hover:bg-ink-50">Clear</a>
            @endif
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-ink-100 shadow-card overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="text-left text-ink-500 border-b bg-ink-50/60 font-bold">
                <tr>
                    <th class="px-5 py-3">Staff</th>
                    <th class="px-5 py-3">Email</th>
                    <th class="px-5 py-3">Restaurant</th>
                    <th class="px-5 py-3">Branch</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3 text-right">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($staff as $user)
                    <tr class="border-b last:border-0">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-brand-50 text-xs font-semibold text-brand-600">
                                    {{ Str::of($user->name)->explode(' ')->map(fn ($p) => Str::substr($p, 0, 1))->take(2)->implode('') }}
                                </span>
                                <span class="font-medium text-ink-900">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-ink-500">{{ $user->email }}</td>
                        <td class="px-5 py-3">
                            @if ($user->branch)
                                <a href="{{ route('admin.restaurants.show', $user->branch->restaurant) }}" class="text-brand-600 hover:underline">
                                    {{ $user->branch->restaurant->name }}
                                </a>
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-5 py-3 text-ink-500">{{ $user->branch->name ?? '—' }}</td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center gap-1.5 text-xs font-medium {{ $user->is_active ? 'text-green-700' : 'text-ink-500' }}">
                                <span class="h-1.5 w-1.5 rounded-full {{ $user->is_active ? 'bg-green-500' : 'bg-ink-300' }}"></span>
                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-right">
                            @if ($user->branch)
                                <a href="{{ route('admin.branches.staff.index', [$user->branch->restaurant, $user->branch]) }}" title="View"
                                   class="inline-flex h-7 w-7 items-center justify-center rounded-lg border border-ink-200 text-ink-500 hover:bg-ink-50">
                                    <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7Z"/>
                                        <circle cx="12" cy="12" r="3"/>
                                    </svg>
                                </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-10 text-center text-ink-400">No staff accounts match these filters.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <x-pagination :paginator="$staff" />
</div>
@endsection
