@php
    $isAdmin = auth()->user()->isAdmin();

    $tabs = collect([
        'admin.branches.edit' => $isAdmin ? ['label' => 'Details', 'route' => route('admin.branches.edit', [$restaurant, $branch])] : null,
        'admin.branches.printers.index' => ['label' => 'Printers', 'route' => route('admin.branches.printers.index', [$restaurant, $branch])],
        'admin.branches.categories.index' => ['label' => 'Categories', 'route' => route('admin.branches.categories.index', [$restaurant, $branch])],
        'admin.branches.menu-items.index' => ['label' => 'Menu Items', 'route' => route('admin.branches.menu-items.index', [$restaurant, $branch])],
        'admin.branches.orders.index' => ['label' => 'Orders', 'route' => route('admin.branches.orders.index', [$restaurant, $branch])],
        'admin.branches.staff.index' => $isAdmin ? ['label' => 'Staff', 'route' => route('admin.branches.staff.index', [$restaurant, $branch])] : null,
    ])->filter();
@endphp

<a href="{{ $isAdmin ? route('admin.restaurants.show', [$restaurant, 'tab' => 'branches']) : route('billing.index') }}"
   class="inline-flex items-center gap-1 text-sm text-ink-500 hover:text-brand-600 mb-4">
    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
    {{ $isAdmin ? 'Back to ' . $restaurant->name : 'Back to POS' }}
</a>

<div class="flex flex-wrap gap-2 border-b mb-6">
    @foreach ($tabs as $routeName => $tab)
        <a href="{{ $tab['route'] }}"
           class="px-4 py-2 text-sm font-medium border-b-2 -mb-px whitespace-nowrap {{ request()->routeIs($routeName) ? 'border-brand-600 text-brand-600' : 'border-transparent text-ink-500 hover:text-ink-700' }}">
            {{ $tab['label'] }}
        </a>
    @endforeach
</div>
