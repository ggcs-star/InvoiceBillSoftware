@php
    $tabs = [
        'admin.branches.edit' => ['label' => 'Details', 'route' => route('admin.branches.edit', [$restaurant, $branch])],
        'admin.branches.areas.index' => ['label' => 'Areas', 'route' => route('admin.branches.areas.index', [$restaurant, $branch])],
        'admin.branches.printers.index' => ['label' => 'Printers', 'route' => route('admin.branches.printers.index', [$restaurant, $branch])],
        'admin.branches.categories.index' => ['label' => 'Categories', 'route' => route('admin.branches.categories.index', [$restaurant, $branch])],
        'admin.branches.menu-items.index' => ['label' => 'Menu Items', 'route' => route('admin.branches.menu-items.index', [$restaurant, $branch])],
        'admin.branches.orders.index' => ['label' => 'Orders', 'route' => route('admin.branches.orders.index', [$restaurant, $branch])],
        'admin.branches.staff.index' => ['label' => 'Staff', 'route' => route('admin.branches.staff.index', [$restaurant, $branch])],
    ];
@endphp

<a href="{{ route('admin.restaurants.edit', $restaurant) }}" class="inline-flex items-center gap-1 text-sm text-ink-500 hover:text-brand-600 mb-4">
    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
    Back to {{ $restaurant->name }}
</a>

<div class="flex gap-2 border-b overflow-x-auto mb-6">
    @foreach ($tabs as $routeName => $tab)
        <a href="{{ $tab['route'] }}"
           class="px-4 py-2 text-sm font-medium border-b-2 -mb-px whitespace-nowrap {{ request()->routeIs($routeName) ? 'border-brand-600 text-brand-600' : 'border-transparent text-ink-500 hover:text-ink-700' }}">
            {{ $tab['label'] }}
        </a>
    @endforeach
</div>
