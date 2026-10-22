@extends('layouts.app')

@section('title', 'Printers — ' . $branch->name)
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

    <div class="space-y-4">
        <div class="bg-white rounded-2xl border border-ink-100 shadow-card p-4">
            <form method="GET" class="flex gap-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search printers..."
                       class="flex-1 rounded-lg border-ink-200 shadow-sm text-sm">
                <button type="submit" class="rounded-lg border border-ink-200 px-4 py-2 text-sm font-medium text-ink-600 hover:bg-ink-50">Search</button>
                @if (request()->filled('search'))
                    <a href="{{ route('admin.branches.printers.index', [$restaurant, $branch]) }}" class="rounded-lg border border-ink-200 px-4 py-2 text-sm font-medium text-ink-600 hover:bg-ink-50">Clear</a>
                @endif
            </form>
        </div>

        <div class="bg-white rounded-2xl border border-ink-100 shadow-card overflow-hidden">
            <table class="w-full text-sm">
                <thead class="text-left text-ink-500 border-b">
                    <tr>
                        <th class="px-5 py-3">Name</th>
                        <th class="px-5 py-3">Type</th>
                        <th class="px-5 py-3">Address</th>
                        <th class="px-5 py-3">Default</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($printers as $printer)
                        <tr class="border-b last:border-0">
                            <td class="px-5 py-3 font-medium">{{ $printer->name }}</td>
                            <td class="px-5 py-3">{{ $printer->connection_type->label() }}</td>
                            <td class="px-5 py-3">{{ $printer->ip_address }}@if($printer->port):{{ $printer->port }}@endif</td>
                            <td class="px-5 py-3">{{ $printer->is_default ? 'Yes' : '' }}</td>
                            <td class="px-5 py-3 text-right">
                                <form method="POST" action="{{ route('admin.branches.printers.destroy', [$restaurant, $branch, $printer]) }}" onsubmit="return confirm('Remove this printer?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:underline">Remove</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-6 text-center text-ink-400">No printers match.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div>{{ $printers->links() }}</div>

        <div class="bg-white rounded-2xl border border-ink-100 shadow-card p-6 max-w-lg">
            <h3 class="font-medium mb-4">Add Printer</h3>
            <form method="POST" action="{{ route('admin.branches.printers.store', [$restaurant, $branch]) }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-ink-700">Name</label>
                    <input type="text" name="name" required placeholder="e.g. Counter Printer" class="mt-1 block w-full rounded-lg border-ink-200 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink-700">Connection Type</label>
                    <select name="connection_type" class="mt-1 block w-full rounded-lg border-ink-200 shadow-sm">
                        <option value="network">Network / WiFi</option>
                        <option value="usb">USB</option>
                        <option value="bluetooth">Bluetooth</option>
                    </select>
                    <p class="text-xs text-ink-500 mt-1">Only Network/WiFi printers can be printed to directly from the server. USB/Bluetooth printers use the browser print fallback.</p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-ink-700">IP Address</label>
                        <input type="text" name="ip_address" placeholder="192.168.1.50" class="mt-1 block w-full rounded-lg border-ink-200 shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-ink-700">Port</label>
                        <input type="number" name="port" placeholder="9100" class="mt-1 block w-full rounded-lg border-ink-200 shadow-sm">
                    </div>
                </div>
                <label class="flex items-center gap-2 text-sm text-ink-700">
                    <input type="checkbox" name="is_default" value="1" class="rounded border-ink-200">
                    Default printer for this branch
                </label>
                <button type="submit" class="rounded-lg bg-brand-600 px-4 py-2 text-white font-medium hover:bg-brand-700">Add Printer</button>
            </form>
        </div>
    </div>
@endsection
