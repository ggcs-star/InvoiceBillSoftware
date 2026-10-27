@extends('layouts.app')

@section('title', 'Bulk Upload Menu Items')
@section('header', 'Bulk Upload Menu Items')

@section('content')
<div x-data="{
        restaurantId: '',
        branchId: '',
        branches: @js($branches->map(fn ($b) => ['id' => $b->id, 'name' => $b->name, 'restaurant_id' => $b->restaurant_id])),
        get branchOptions() {
            return this.branches.filter((b) => String(b.restaurant_id) === String(this.restaurantId));
        },
        fileName: '',
     }">
    <div class="flex items-center justify-between mb-4">
        <a href="{{ route('admin.menu-items.index') }}" class="inline-flex items-center gap-1 text-sm text-ink-500 hover:text-brand-600">
            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
            Back to Menu Items
        </a>
        <a href="{{ route('admin.menu-items.bulk-import.sample') }}" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700">
            Download Sample File
        </a>
    </div>

    @if (session('status'))
        <div class="mb-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <div class="font-medium mb-1">Import failed:</div>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl border border-ink-100 shadow-card p-6">
            <h3 class="font-semibold text-ink-900 mb-4">Import Instructions</h3>

            <div class="text-xs font-semibold uppercase tracking-wide text-ink-400 mb-2">Required Columns</div>
            <div class="space-y-2 mb-4">
                <div class="flex items-center gap-2 bg-ink-50/60 rounded-lg px-3 py-2 text-sm">
                    <code class="text-brand-700 font-medium">item_name</code>
                    <span class="text-ink-500">Item Name</span>
                </div>
                <div class="flex items-center gap-2 bg-ink-50/60 rounded-lg px-3 py-2 text-sm">
                    <code class="text-brand-700 font-medium">category_name</code>
                    <span class="text-ink-500">Item Category <span class="text-ink-400">(auto-created)</span></span>
                </div>
                <div class="flex items-center gap-2 bg-ink-50/60 rounded-lg px-3 py-2 text-sm">
                    <code class="text-brand-700 font-medium">price</code>
                    <span class="text-ink-500">Price</span>
                </div>
            </div>

            <div class="text-xs font-semibold uppercase tracking-wide text-ink-400 mb-2">Optional Columns</div>
            <div class="space-y-2">
                <div class="flex items-center gap-2 bg-ink-50/60 rounded-lg px-3 py-2 text-sm">
                    <code class="text-ink-600 font-medium">description</code>
                    <span class="text-ink-500">Item Description</span>
                </div>
                <div class="flex items-center gap-2 bg-ink-50/60 rounded-lg px-3 py-2 text-sm">
                    <code class="text-ink-600 font-medium">type</code>
                    <span class="text-ink-500">veg / non_veg / egg</span>
                </div>
                <div class="flex items-center gap-2 bg-ink-50/60 rounded-lg px-3 py-2 text-sm">
                    <code class="text-ink-600 font-medium">gst_percentage</code>
                    <span class="text-ink-500">Defaults to 5</span>
                </div>
                <div class="flex items-center gap-2 bg-ink-50/60 rounded-lg px-3 py-2 text-sm">
                    <code class="text-ink-600 font-medium">is_available</code>
                    <span class="text-ink-500">yes / no</span>
                </div>
                <div class="flex items-center gap-2 bg-ink-50/60 rounded-lg px-3 py-2 text-sm">
                    <code class="text-ink-600 font-medium">show_on_customer_site</code>
                    <span class="text-ink-500">yes / no</span>
                </div>
            </div>

            <div class="mt-4 rounded-lg bg-brand-50 text-brand-700 text-xs px-3 py-2">
                Re-uploading a file with the same item names updates those items instead of duplicating them.
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-ink-100 shadow-card p-6">
            <h3 class="font-semibold text-ink-900 mb-4">Upload File</h3>

            <form method="POST" action="{{ route('admin.menu-items.bulk-import.store') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-ink-500 mb-1">Restaurant</label>
                        <select x-model="restaurantId" @change="branchId = ''" required class="w-full rounded-lg border-ink-200 px-3 py-2 shadow-sm text-sm">
                            <option value="" disabled>Select a restaurant</option>
                            @foreach ($restaurants as $restaurant)
                                <option value="{{ $restaurant->id }}">{{ $restaurant->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-ink-500 mb-1">Branch</label>
                        <select name="branch_id" x-model="branchId" required :disabled="!restaurantId" class="w-full rounded-lg border-ink-200 px-3 py-2 shadow-sm text-sm disabled:bg-ink-50">
                            <option value="" disabled>Select a branch</option>
                            <template x-for="branch in branchOptions" :key="branch.id">
                                <option :value="branch.id" x-text="branch.name"></option>
                            </template>
                        </select>
                    </div>
                </div>
                <input type="hidden" name="restaurant_id" :value="restaurantId">

                <label class="block border-2 border-dashed border-ink-200 rounded-xl p-8 text-center cursor-pointer hover:border-brand-300 hover:bg-brand-50/30 transition">
                    <input type="file" name="file" accept=".csv,text/csv" required class="hidden" @change="fileName = $event.target.files[0]?.name || ''">
                    <svg viewBox="0 0 24 24" class="mx-auto h-9 w-9 text-brand-400 mb-2" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 16V4"/>
                        <path d="M7 9l5-5 5 5"/>
                        <path d="M4 16v3a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-3"/>
                    </svg>
                    <div class="font-medium text-ink-900">Upload File</div>
                    <div class="text-sm text-ink-500">Drag and drop your CSV file here, or click to browse</div>
                    <div x-show="fileName" x-text="fileName" class="mt-2 text-sm font-medium text-brand-600"></div>
                </label>

                <button type="submit" class="w-full rounded-lg bg-brand-600 px-4 py-2.5 text-white text-sm font-medium hover:bg-brand-700">
                    Start Import
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
