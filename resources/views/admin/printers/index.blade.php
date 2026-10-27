@extends('layouts.app')

@section('title', 'Printers')
@section('header', 'Printers')

@section('content')
<div x-data="{
        addOpen: false,
        editOpen: false,
        restaurantId: '',
        branchId: '',
        branches: @js($branches->map(fn ($b) => ['id' => $b->id, 'name' => $b->name, 'restaurant_id' => $b->restaurant_id])),
        get branchOptions() {
            return this.branches.filter((b) => String(b.restaurant_id) === String(this.restaurantId));
        },
        get storeUrl() {
            return '/admin/restaurants/' + this.restaurantId + '/branches/' + this.branchId + '/printers';
        },
        editing: { id: null, name: '', connection_type: 'network', ip_address: '', port: '', is_default: false, updateUrl: '' },
        openEdit(printer) {
            this.editing = {
                id: printer.id,
                name: printer.name,
                connection_type: printer.connection_type,
                ip_address: printer.ip_address || '',
                port: printer.port || '',
                is_default: printer.is_default,
                updateUrl: '/admin/restaurants/' + printer.restaurant_id + '/branches/' + printer.branch_id + '/printers/' + printer.id,
            };
            this.editOpen = true;
        },
     }">
    <div class="flex justify-end mb-4">
        <button type="button" @click="addOpen = true" class="rounded-lg bg-brand-600 px-4 py-2 text-white text-sm font-medium hover:bg-brand-700">
            + Add Printer
        </button>
    </div>

    {{-- Add Printer modal --}}
    <div x-show="addOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-ink-900/50 p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden" @click.outside="addOpen = false">
            <div class="flex items-center justify-between px-6 py-4 border-b border-ink-100">
                <h3 class="text-lg font-bold text-ink-900">Add Printer</h3>
                <button type="button" @click="addOpen = false" class="text-ink-400 hover:text-ink-700">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"/>
                        <line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>
            <form method="POST" :action="storeUrl" class="p-6 space-y-4">
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
                    <label class="block text-xs font-medium text-ink-500 mb-1">Name</label>
                    <input type="text" name="name" required placeholder="e.g. Counter Printer" class="w-full rounded-lg border-ink-200 shadow-sm text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-ink-500 mb-1">Connection Type</label>
                    <select name="connection_type" class="w-full rounded-lg border-ink-200 shadow-sm text-sm">
                        @foreach ($connectionTypes as $type)
                            <option value="{{ $type->value }}">{{ $type->label() }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-ink-500 mt-1">Only Network/WiFi printers can be printed to directly from the server. USB/Bluetooth printers use the browser print fallback.</p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-ink-500 mb-1">IP Address</label>
                        <input type="text" name="ip_address" placeholder="192.168.1.50" class="w-full rounded-lg border-ink-200 shadow-sm text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-ink-500 mb-1">Port</label>
                        <input type="number" name="port" placeholder="9100" class="w-full rounded-lg border-ink-200 shadow-sm text-sm">
                    </div>
                </div>
                <label class="flex items-center gap-2 text-sm text-ink-700">
                    <input type="checkbox" name="is_default" value="1" class="rounded border-ink-200">
                    Default printer for this branch
                </label>
                <p class="text-xs text-amber-600">New printers need approval before they can receive print jobs.</p>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="addOpen = false" class="rounded-lg border border-ink-200 px-4 py-2 text-sm font-medium text-ink-600 hover:bg-ink-50">Cancel</button>
                    <button type="submit" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700">Add Printer</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Edit Printer modal --}}
    <div x-show="editOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-ink-900/50 p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden" @click.outside="editOpen = false">
            <div class="flex items-center justify-between px-6 py-4 border-b border-ink-100">
                <h3 class="text-lg font-bold text-ink-900">Edit Printer</h3>
                <button type="button" @click="editOpen = false" class="text-ink-400 hover:text-ink-700">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"/>
                        <line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>
            <form method="POST" :action="editing.updateUrl" class="p-6 space-y-4">
                @csrf @method('PUT')
                <div>
                    <label class="block text-xs font-medium text-ink-500 mb-1">Name</label>
                    <input type="text" name="name" x-model="editing.name" required class="w-full rounded-lg border-ink-200 shadow-sm text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-ink-500 mb-1">Connection Type</label>
                    <select name="connection_type" x-model="editing.connection_type" class="w-full rounded-lg border-ink-200 shadow-sm text-sm">
                        @foreach ($connectionTypes as $type)
                            <option value="{{ $type->value }}">{{ $type->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-ink-500 mb-1">IP Address</label>
                        <input type="text" name="ip_address" x-model="editing.ip_address" placeholder="192.168.1.50" class="w-full rounded-lg border-ink-200 shadow-sm text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-ink-500 mb-1">Port</label>
                        <input type="number" name="port" x-model="editing.port" placeholder="9100" class="w-full rounded-lg border-ink-200 shadow-sm text-sm">
                    </div>
                </div>
                <label class="flex items-center gap-2 text-sm text-ink-700">
                    <input type="checkbox" name="is_default" value="1" x-model="editing.is_default" class="rounded border-ink-200">
                    Default printer for this branch
                </label>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="editOpen = false" class="rounded-lg border border-ink-200 px-4 py-2 text-sm font-medium text-ink-600 hover:bg-ink-50">Cancel</button>
                    <button type="submit" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700">Save Changes</button>
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
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Printer name..."
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
                <label class="block text-xs font-medium text-ink-500 mb-1">Type</label>
                <x-searchable-select name="connection_type" placeholder="All Types"
                    :options="collect($connectionTypes)->map(fn ($t) => ['value' => $t->value, 'label' => $t->label()])"
                    :selected="request('connection_type')" />
            </div>
            <div>
                <label class="block text-xs font-medium text-ink-500 mb-1">Status</label>
                <x-searchable-select name="status" placeholder="All Status"
                    :options="[['value' => 'approved', 'label' => 'Approved'], ['value' => 'pending', 'label' => 'Pending Approval']]"
                    :selected="request('status')" />
            </div>
            <button type="submit" class="rounded-lg bg-brand-600 px-4 py-2 text-white text-sm font-medium hover:bg-brand-700">Filter</button>
            @if (request()->anyFilled(['search', 'restaurant_id', 'branch_id', 'connection_type', 'status']))
                <a href="{{ route('admin.printers.index') }}" class="rounded-lg border border-ink-200 px-4 py-2 text-sm font-medium text-ink-600 hover:bg-ink-50">Clear</a>
            @endif
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-ink-100 shadow-card overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="text-left text-ink-500 border-b bg-ink-50/60">
                <tr>
                    <th class="px-5 py-3">Name</th>
                    <th class="px-5 py-3">Type</th>
                    <th class="px-5 py-3">Restaurant</th>
                    <th class="px-5 py-3">Branch</th>
                    <th class="px-5 py-3">Default</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3 text-right">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($printers as $printer)
                    <tr class="border-b last:border-0">
                        <td class="px-5 py-3 font-medium text-ink-900">{{ $printer->name }}</td>
                        <td class="px-5 py-3 text-ink-500">{{ $printer->connection_type->label() }}</td>
                        <td class="px-5 py-3">
                            <a href="{{ route('admin.restaurants.show', $printer->branch->restaurant) }}" class="text-brand-600 hover:underline">
                                {{ $printer->branch->restaurant->name }}
                            </a>
                        </td>
                        <td class="px-5 py-3 text-ink-500">{{ $printer->branch->name }}</td>
                        <td class="px-5 py-3 text-ink-500">{{ $printer->is_default ? 'Yes' : '' }}</td>
                        <td class="px-5 py-3">
                            <span class="px-2 py-0.5 rounded-full text-xs {{ $printer->is_approved ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                                {{ $printer->is_approved ? 'Approved' : 'Pending Approval' }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-right">
                            <div class="inline-flex gap-2 items-center">
                                @unless ($printer->is_approved)
                                    <form method="POST" action="{{ route('admin.branches.printers.approve', [$printer->branch->restaurant, $printer->branch, $printer]) }}">
                                        @csrf @method('PATCH')
                                        <button class="text-green-600 hover:underline font-medium text-xs">Approve</button>
                                    </form>
                                @endunless
                                <button type="button"
                                        @click="openEdit(@js([
                                            'id' => $printer->id,
                                            'name' => $printer->name,
                                            'connection_type' => $printer->connection_type->value,
                                            'ip_address' => $printer->ip_address,
                                            'port' => $printer->port,
                                            'is_default' => $printer->is_default,
                                            'restaurant_id' => $printer->branch->restaurant_id,
                                            'branch_id' => $printer->branch_id,
                                        ]))"
                                        class="flex h-7 w-7 items-center justify-center rounded-lg border border-ink-200 text-ink-500 hover:bg-ink-50">
                                    <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 20h9"/>
                                        <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/>
                                    </svg>
                                </button>
                                <form method="POST" action="{{ route('admin.branches.printers.destroy', [$printer->branch->restaurant, $printer->branch, $printer]) }}" onsubmit="return confirm('Remove this printer?')">
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
                    <tr><td colspan="7" class="px-5 py-10 text-center text-ink-400">No printers match these filters.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <x-pagination :paginator="$printers" />
</div>
@endsection
