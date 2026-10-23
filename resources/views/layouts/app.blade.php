<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="api-token" content="{{ session('api_token') }}">
    <title>@yield('title', 'Jagdai Cloud Kitchen')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-brand-50/40 text-ink-900 antialiased">
    <div class="min-h-screen flex">
        {{-- Sidebar --}}
        <aside class="w-64 shrink-0 bg-white border-r border-ink-100 flex flex-col">
            <div class="flex items-center gap-2.5 px-6 py-5">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-brand-500 text-white shadow-sm shadow-brand-500/40">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2c-3.5 0-6 2.8-6 6.2 0 4.4 6 13.3 6 13.3s6-8.9 6-13.3C18 4.8 15.5 2 12 2Z"/>
                        <circle cx="12" cy="8.2" r="2.2"/>
                    </svg>
                </span>
                <span class="text-lg font-bold tracking-tight text-ink-900">Jagdai Cloud Kitchen</span>
            </div>

            <nav class="flex-1 overflow-y-auto px-4 py-2 space-y-1 text-sm">
                @auth
                    @php
                        $navItem = function (string $route, string $label, string $icon, bool $active) {
                            $activeClasses = 'bg-brand-500 text-white shadow-sm shadow-brand-500/30';
                            $idleClasses = 'text-ink-500 hover:bg-brand-50 hover:text-brand-600';
                            return compact('route', 'label', 'icon', 'active', 'activeClasses', 'idleClasses');
                        };
                    @endphp

                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}"
                           class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition {{ request()->routeIs('admin.dashboard') ? 'bg-brand-500 text-white shadow-sm shadow-brand-500/30' : 'text-ink-500 hover:bg-brand-50 hover:text-brand-600' }}">
                            <svg viewBox="0 0 24 24" class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="7.5" height="7.5" rx="1.8"/>
                                <rect x="13.5" y="3" width="7.5" height="7.5" rx="1.8"/>
                                <rect x="3" y="13.5" width="7.5" height="7.5" rx="1.8"/>
                                <rect x="13.5" y="13.5" width="7.5" height="7.5" rx="1.8"/>
                            </svg>
                            Dashboard
                        </a>
                        <a href="{{ route('admin.restaurants.index') }}"
                           class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition {{ request()->routeIs('admin.restaurants.*') && ! request()->routeIs('admin.branches.*') ? 'bg-brand-500 text-white shadow-sm shadow-brand-500/30' : 'text-ink-500 hover:bg-brand-50 hover:text-brand-600' }}">
                            <svg viewBox="0 0 24 24" class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 10 5 4h14l1 6"/>
                                <path d="M4 10a2.5 2.5 0 0 0 5 0 2.5 2.5 0 0 0 5 0 2.5 2.5 0 0 0 5 0"/>
                                <path d="M5 10v9h14v-9"/>
                                <rect x="9.5" y="14" width="5" height="5"/>
                            </svg>
                            Restaurants
                        </a>
                        <a href="{{ route('admin.branches.index') }}"
                           class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition {{ request()->routeIs('admin.branches.index') ? 'bg-brand-500 text-white shadow-sm shadow-brand-500/30' : 'text-ink-500 hover:bg-brand-50 hover:text-brand-600' }}">
                            <svg viewBox="0 0 24 24" class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="6" cy="6" r="3"/>
                                <circle cx="6" cy="18" r="3"/>
                                <circle cx="18" cy="6" r="3"/>
                                <path d="M6 9v6"/>
                                <path d="M9 6h6a3 3 0 0 1 3 3v6"/>
                            </svg>
                            Branches
                        </a>
                        <a href="{{ route('admin.categories.index') }}"
                           class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition {{ request()->routeIs('admin.categories.index') ? 'bg-brand-500 text-white shadow-sm shadow-brand-500/30' : 'text-ink-500 hover:bg-brand-50 hover:text-brand-600' }}">
                            <svg viewBox="0 0 24 24" class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="8" height="8" rx="1.5"/>
                                <rect x="13" y="3" width="8" height="8" rx="1.5"/>
                                <rect x="3" y="13" width="8" height="8" rx="1.5"/>
                                <rect x="13" y="13" width="8" height="8" rx="1.5"/>
                            </svg>
                            Categories
                        </a>
                        <a href="{{ route('admin.menu-items.index') }}"
                           class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition {{ request()->routeIs('admin.menu-items.index') ? 'bg-brand-500 text-white shadow-sm shadow-brand-500/30' : 'text-ink-500 hover:bg-brand-50 hover:text-brand-600' }}">
                            <svg viewBox="0 0 24 24" class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M6 3v8a2 2 0 0 0 2 2h0a2 2 0 0 0 2-2V3"/>
                                <line x1="8" y1="13" x2="8" y2="21"/>
                                <path d="M17 3c-1.7 0-3 2-3 5s1.3 5 3 5v8"/>
                            </svg>
                            Menu Items
                        </a>
                        <a href="{{ route('admin.printers.index') }}"
                           class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition {{ request()->routeIs('admin.printers.index') ? 'bg-brand-500 text-white shadow-sm shadow-brand-500/30' : 'text-ink-500 hover:bg-brand-50 hover:text-brand-600' }}">
                            <svg viewBox="0 0 24 24" class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="6 9 6 2 18 2 18 9"/>
                                <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                                <rect x="6" y="14" width="12" height="8"/>
                            </svg>
                            Printers
                        </a>
                        <a href="{{ route('admin.orders.index') }}"
                           class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition {{ request()->routeIs('admin.orders.index') ? 'bg-brand-500 text-white shadow-sm shadow-brand-500/30' : 'text-ink-500 hover:bg-brand-50 hover:text-brand-600' }}">
                            <svg viewBox="0 0 24 24" class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="9" cy="20" r="1.4"/>
                                <circle cx="17" cy="20" r="1.4"/>
                                <path d="M3 4h2l1.7 10.2a2 2 0 0 0 2 1.8h7.9a2 2 0 0 0 2-1.6L20 8H6.2"/>
                            </svg>
                            Orders
                        </a>

                        {{--
                            Scale note: the sidebar only ever renders the ONE restaurant you're
                            currently inside (via the {restaurant} route param) and its handful of
                            branches — never every restaurant in the system. Finding/switching among
                            many restaurants happens on the searchable, paginated Restaurants table
                            above, not by scrolling a sidebar tree.
                        --}}
                        @if (($sidebarRestaurant = request()->route('restaurant')) instanceof \App\Models\Restaurant)
                            @php
                                $activeBranch = request()->route('branch');
                                $sidebarBranches = $sidebarRestaurant->branches()->orderBy('name')->get();
                                $menuActive = request()->routeIs('admin.branches.categories.*') || request()->routeIs('admin.branches.menu-items.*');
                            @endphp
                            <div class="pt-4 mt-3 border-t border-ink-100" x-data="{ menuOpen: {{ $menuActive ? 'true' : 'false' }} }">
                                <div class="px-3 pb-2 text-xs font-semibold uppercase tracking-wide text-ink-400 truncate">{{ $sidebarRestaurant->name }}</div>

                                @forelse ($sidebarBranches as $sidebarBranch)
                                    @if ($activeBranch && $activeBranch->id === $sidebarBranch->id)
                                        {{-- Expanded: this is the branch currently being managed --}}
                                        <a href="{{ route('admin.branches.show', [$sidebarRestaurant, $sidebarBranch]) }}"
                                           class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-semibold transition {{ request()->routeIs('admin.branches.show') || request()->routeIs('admin.branches.edit') ? 'bg-brand-500 text-white shadow-sm shadow-brand-500/30' : 'bg-brand-50 text-brand-600' }}">
                                            <svg viewBox="0 0 24 24" class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="12" cy="12" r="9"/>
                                                <line x1="12" y1="11" x2="12" y2="16"/>
                                                <circle cx="12" cy="7.5" r="0.5" fill="currentColor"/>
                                            </svg>
                                            {{ $sidebarBranch->name }}
                                        </a>
                                        <div class="ml-6 mt-0.5 mb-1 space-y-0.5 border-l border-ink-100 pl-3">
                                            <a href="{{ route('admin.branches.printers.index', [$sidebarRestaurant, $sidebarBranch]) }}"
                                               class="block rounded-lg px-3 py-1.5 text-sm transition {{ request()->routeIs('admin.branches.printers.*') ? 'font-semibold text-brand-600' : 'text-ink-400 hover:text-ink-700' }}">
                                                Printers
                                            </a>

                                            <button type="button" @click="menuOpen = !menuOpen"
                                                    class="w-full flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-sm transition {{ $menuActive ? 'font-semibold text-brand-600' : 'text-ink-400 hover:text-ink-700' }}">
                                                <span class="flex-1 text-left">Menu</span>
                                                <svg viewBox="0 0 24 24" class="h-3 w-3 shrink-0 transition-transform" :class="menuOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M6 9l6 6 6-6"/>
                                                </svg>
                                            </button>
                                            <div x-show="menuOpen" x-cloak class="ml-3 space-y-0.5 border-l border-ink-100 pl-3">
                                                <a href="{{ route('admin.branches.categories.index', [$sidebarRestaurant, $sidebarBranch]) }}"
                                                   class="block rounded-lg px-3 py-1 text-sm transition {{ request()->routeIs('admin.branches.categories.*') ? 'font-semibold text-brand-600' : 'text-ink-400 hover:text-ink-700' }}">
                                                    Item Categories
                                                </a>
                                                <a href="{{ route('admin.branches.menu-items.index', [$sidebarRestaurant, $sidebarBranch]) }}"
                                                   class="block rounded-lg px-3 py-1 text-sm transition {{ request()->routeIs('admin.branches.menu-items.*') ? 'font-semibold text-brand-600' : 'text-ink-400 hover:text-ink-700' }}">
                                                    Menu Items
                                                </a>
                                            </div>

                                            <a href="{{ route('admin.branches.orders.index', [$sidebarRestaurant, $sidebarBranch]) }}"
                                               class="block rounded-lg px-3 py-1.5 text-sm transition {{ request()->routeIs('admin.branches.orders.*') ? 'font-semibold text-brand-600' : 'text-ink-400 hover:text-ink-700' }}">
                                                Orders
                                            </a>
                                            <a href="{{ route('admin.branches.staff.index', [$sidebarRestaurant, $sidebarBranch]) }}"
                                               class="block rounded-lg px-3 py-1.5 text-sm transition {{ request()->routeIs('admin.branches.staff.*') ? 'font-semibold text-brand-600' : 'text-ink-400 hover:text-ink-700' }}">
                                                Staff
                                            </a>
                                        </div>
                                    @else
                                        <a href="{{ route('admin.branches.show', [$sidebarRestaurant, $sidebarBranch]) }}"
                                           class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium text-ink-500 hover:bg-brand-50 hover:text-brand-600">
                                            <svg viewBox="0 0 24 24" class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="12" cy="12" r="9"/>
                                                <line x1="12" y1="11" x2="12" y2="16"/>
                                                <circle cx="12" cy="7.5" r="0.5" fill="currentColor"/>
                                            </svg>
                                            {{ $sidebarBranch->name }}
                                        </a>
                                    @endif
                                @empty
                                    <div class="px-3 py-2 text-xs text-ink-400">No branches yet.</div>
                                @endforelse

                                <a href="{{ route('admin.restaurants.show', [$sidebarRestaurant, 'tab' => 'branches']) }}"
                                   class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium text-brand-600 hover:bg-brand-50">
                                    <svg viewBox="0 0 24 24" class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <line x1="12" y1="5" x2="12" y2="19"/>
                                        <line x1="5" y1="12" x2="19" y2="12"/>
                                    </svg>
                                    Add Branch
                                </a>
                            </div>
                        @endif
                    @else
                        @php
                            $myBranch = auth()->user()->branch;
                            $myRestaurant = $myBranch?->restaurant;
                        @endphp

                        <a href="{{ route('billing.dashboard') }}"
                           class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition {{ request()->routeIs('billing.dashboard') ? 'bg-brand-500 text-white shadow-sm shadow-brand-500/30' : 'text-ink-500 hover:bg-brand-50 hover:text-brand-600' }}">
                            <svg viewBox="0 0 24 24" class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="7.5" height="7.5" rx="1.8"/>
                                <rect x="13.5" y="3" width="7.5" height="7.5" rx="1.8"/>
                                <rect x="3" y="13.5" width="7.5" height="7.5" rx="1.8"/>
                                <rect x="13.5" y="13.5" width="7.5" height="7.5" rx="1.8"/>
                            </svg>
                            Dashboard
                        </a>

                        @if ($myBranch && $myRestaurant)
                            <a href="{{ route('admin.branches.categories.index', [$myRestaurant, $myBranch]) }}"
                               class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition {{ request()->routeIs('admin.branches.categories.*') ? 'bg-brand-500 text-white shadow-sm shadow-brand-500/30' : 'text-ink-500 hover:bg-brand-50 hover:text-brand-600' }}">
                                <svg viewBox="0 0 24 24" class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="3" width="8" height="8" rx="1.5"/>
                                    <rect x="13" y="3" width="8" height="8" rx="1.5"/>
                                    <rect x="3" y="13" width="8" height="8" rx="1.5"/>
                                    <rect x="13" y="13" width="8" height="8" rx="1.5"/>
                                </svg>
                                Categories
                            </a>
                            <a href="{{ route('admin.branches.menu-items.index', [$myRestaurant, $myBranch]) }}"
                               class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition {{ request()->routeIs('admin.branches.menu-items.*') ? 'bg-brand-500 text-white shadow-sm shadow-brand-500/30' : 'text-ink-500 hover:bg-brand-50 hover:text-brand-600' }}">
                                <svg viewBox="0 0 24 24" class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M6 3v8a2 2 0 0 0 2 2h0a2 2 0 0 0 2-2V3"/>
                                    <line x1="8" y1="13" x2="8" y2="21"/>
                                    <path d="M17 3c-1.7 0-3 2-3 5s1.3 5 3 5v8"/>
                                </svg>
                                Menu Items
                            </a>
                        @endif

                        <a href="{{ route('billing.index') }}"
                           class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition {{ request()->routeIs('billing.index') || request()->routeIs('billing.orders.show') ? 'bg-brand-500 text-white shadow-sm shadow-brand-500/30' : 'text-ink-500 hover:bg-brand-50 hover:text-brand-600' }}">
                            <svg viewBox="0 0 24 24" class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="16" rx="2"/>
                                <line x1="3" y1="9" x2="21" y2="9"/>
                                <line x1="9" y1="9" x2="9" y2="20"/>
                            </svg>
                            POS
                        </a>

                        @if ($myBranch && $myRestaurant)
                            <a href="{{ route('admin.branches.orders.index', [$myRestaurant, $myBranch]) }}"
                               class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition {{ request()->routeIs('admin.branches.orders.*') ? 'bg-brand-500 text-white shadow-sm shadow-brand-500/30' : 'text-ink-500 hover:bg-brand-50 hover:text-brand-600' }}">
                                <svg viewBox="0 0 24 24" class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="9" cy="20" r="1.4"/>
                                    <circle cx="17" cy="20" r="1.4"/>
                                    <path d="M3 4h2l1.7 10.2a2 2 0 0 0 2 1.8h7.9a2 2 0 0 0 2-1.6L20 8H6.2"/>
                                </svg>
                                Orders
                            </a>
                            <a href="{{ route('admin.branches.printers.index', [$myRestaurant, $myBranch]) }}"
                               class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition {{ request()->routeIs('admin.branches.printers.*') ? 'bg-brand-500 text-white shadow-sm shadow-brand-500/30' : 'text-ink-500 hover:bg-brand-50 hover:text-brand-600' }}">
                                <svg viewBox="0 0 24 24" class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="6 9 6 2 18 2 18 9"/>
                                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                                    <rect x="6" y="14" width="12" height="8"/>
                                </svg>
                                Printers
                            </a>
                        @endif

                        <a href="{{ route('billing.invoices.index') }}"
                           class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition {{ request()->routeIs('billing.invoices.*') ? 'bg-brand-500 text-white shadow-sm shadow-brand-500/30' : 'text-ink-500 hover:bg-brand-50 hover:text-brand-600' }}">
                            <svg viewBox="0 0 24 24" class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M7 3h10v17l-2.5-1.5L12 20l-2.5-1.5L7 20Z"/>
                                <line x1="9.5" y1="8" x2="14.5" y2="8"/>
                                <line x1="9.5" y1="11.5" x2="14.5" y2="11.5"/>
                            </svg>
                            Invoices
                        </a>
                    @endif
                @endauth
            </nav>

            <div class="mt-auto border-t border-ink-100 p-4">
                <div class="flex items-center gap-3 rounded-xl px-2 py-2">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-brand-100 text-sm font-semibold text-brand-700">
                        {{ Str::of(auth()->user()->name)->explode(' ')->map(fn ($p) => Str::substr($p, 0, 1))->take(2)->implode('') }}
                    </span>
                    <div class="min-w-0 flex-1">
                        <div class="truncate text-sm font-medium text-ink-900">{{ auth()->user()->name }}</div>
                        <div class="truncate text-xs text-ink-400">{{ auth()->user()->role->label() }}</div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" title="Log out" class="rounded-lg p-2 text-ink-400 hover:bg-red-50 hover:text-red-500">
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                                <polyline points="16 17 21 12 16 7"/>
                                <line x1="21" y1="12" x2="9" y2="12"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <div class="flex-1 flex flex-col min-w-0">
            {{-- Topbar --}}
            <header class="bg-white border-b border-ink-100 px-8 py-4">
                <div class="flex items-center justify-between gap-6">
                    <h1 class="text-xl font-semibold text-ink-900">@yield('header', 'Jagdai Cloud Kitchen')</h1>

                    <div class="flex items-center gap-4">
                        <div class="relative hidden md:block">
                            <svg viewBox="0 0 24 24" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-ink-400" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="7"/>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                            </svg>
                            <input type="text" placeholder="Search here..."
                                   class="w-60 rounded-full border-transparent bg-brand-50/60 pl-10 pr-4 py-2 text-sm text-ink-700 placeholder:text-ink-400 focus:border-brand-300 focus:bg-white focus:ring-brand-300">
                        </div>

                        <button class="relative rounded-full p-2 text-ink-400 hover:bg-brand-50 hover:text-brand-600">
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M6 9a6 6 0 0 1 12 0c0 3.2 1 4.8 1.6 5.5.3.4 0 1-.5 1H4.9c-.5 0-.8-.6-.5-1C5 13.8 6 12.2 6 9Z"/>
                                <path d="M10 19a2 2 0 0 0 4 0"/>
                            </svg>
                            <span class="absolute right-1.5 top-1.5 h-2 w-2 rounded-full bg-brand-500 ring-2 ring-white"></span>
                        </button>

                        <div class="flex items-center gap-2.5 border-l border-ink-100 pl-4">
                            <span class="flex h-9 w-9 items-center justify-center rounded-full bg-brand-100 text-sm font-semibold text-brand-700">
                                {{ Str::of(auth()->user()->name)->explode(' ')->map(fn ($p) => Str::substr($p, 0, 1))->take(2)->implode('') }}
                            </span>
                            <span class="hidden text-sm font-medium text-ink-700 sm:block">{{ auth()->user()->name }}</span>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 p-8">
                @if (session('status'))
                    <div class="mb-5 flex items-center gap-2 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                        <svg viewBox="0 0 24 24" class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 6 9 17l-5-5"/>
                        </svg>
                        {{ session('status') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    @yield('scripts')
</body>
</html>
