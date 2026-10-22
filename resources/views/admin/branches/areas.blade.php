@extends('layouts.app')

@section('title', 'Areas — ' . $branch->name)
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

    <div class="bg-white rounded-2xl border border-ink-100 shadow-card p-4 mb-4">
        <form method="GET" class="flex gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search areas..."
                   class="flex-1 rounded-lg border-ink-200 shadow-sm text-sm">
            <button type="submit" class="rounded-lg border border-ink-200 px-4 py-2 text-sm font-medium text-ink-600 hover:bg-ink-50">Search</button>
            @if (request()->filled('search'))
                <a href="{{ route('admin.branches.areas.index', [$restaurant, $branch]) }}" class="rounded-lg border border-ink-200 px-4 py-2 text-sm font-medium text-ink-600 hover:bg-ink-50">Clear</a>
            @endif
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-4">
            <div class="bg-white rounded-2xl border border-ink-100 shadow-card overflow-hidden">
                @forelse ($areas as $area)
                    <div class="flex items-center gap-4 px-5 py-4 border-b last:border-0">
                        @if ($area->image_path)
                            <img src="{{ asset('storage/' . $area->image_path) }}" class="h-12 w-12 rounded-lg object-cover">
                        @else
                            <span class="flex h-12 w-12 items-center justify-center rounded-lg bg-brand-50 text-sm font-semibold text-brand-600">
                                {{ Str::substr($area->name, 0, 1) }}
                            </span>
                        @endif
                        <div class="flex-1">
                            <div class="font-medium text-sm">{{ $area->name }}</div>
                            <div class="text-xs text-ink-400">Sort order: {{ $area->sort_order }}</div>
                        </div>
                        <form method="POST" action="{{ route('admin.branches.areas.destroy', [$restaurant, $branch, $area]) }}" onsubmit="return confirm('Remove this area?')">
                            @csrf @method('DELETE')
                            <button class="text-red-600 text-sm hover:underline">Remove</button>
                        </form>
                    </div>
                @empty
                    <div class="px-5 py-10 text-center text-ink-400 text-sm">No areas match — add dining zones like Rooftop or Indoor.</div>
                @endforelse
            </div>

            <div>{{ $areas->links() }}</div>
        </div>

        <div class="bg-white rounded-2xl border border-ink-100 shadow-card p-6 h-fit">
            <h3 class="font-medium mb-4">Add Area</h3>
            <form method="POST" action="{{ route('admin.branches.areas.store', [$restaurant, $branch]) }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-ink-700">Area Name</label>
                    <input type="text" name="name" required placeholder="e.g. Rooftop" class="mt-1 block w-full rounded-lg border-ink-200 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink-700">Sort Order</label>
                    <input type="number" name="sort_order" value="0" class="mt-1 block w-full rounded-lg border-ink-200 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink-700">Area Image</label>
                    <input type="file" name="image" accept="image/*" class="mt-1 block w-full text-sm rounded-lg border-ink-200 shadow-sm file:mr-4 file:rounded-lg file:border-0 file:bg-ink-900 file:px-3 file:py-2 file:text-white">
                    <p class="text-xs text-ink-500 mt-1">JPEG, PNG, GIF, SVG, WEBP. Max 2MB.</p>
                </div>
                <button type="submit" class="rounded-lg bg-brand-600 px-4 py-2 text-white font-medium hover:bg-brand-700">Save</button>
            </form>
        </div>
    </div>
@endsection
