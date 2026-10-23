@extends('layouts.app')

@section('title', 'Edit ' . $restaurant->name)
@section('header', $restaurant->name)

@section('content')
    @if ($errors->any())
        <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <a href="{{ route('admin.restaurants.show', $restaurant) }}" class="inline-flex items-center gap-1 text-sm text-ink-500 hover:text-brand-600 mb-4">
        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
        Back to {{ $restaurant->name }}
    </a>

    <div class="bg-white rounded-2xl border border-ink-100 shadow-card p-6 max-w-xl">
        <h3 class="font-medium mb-4">Edit Restaurant Details</h3>
        <form method="POST" action="{{ route('admin.restaurants.update', $restaurant) }}" class="space-y-4">
            @csrf @method('PUT')

            <div>
                <label class="block text-sm font-medium text-ink-700">Name</label>
                <input type="text" name="name" value="{{ old('name', $restaurant->name) }}" required
                       class="mt-1 block w-full rounded-lg border-ink-200 shadow-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-ink-700">Cuisine Type</label>
                <div class="mt-1">
                    <x-cuisine-type-select name="cuisine_type"
                        :options="$cuisineTypes->map(fn ($name) => ['value' => $name, 'label' => $name])"
                        :selected="old('cuisine_type', $restaurant->cuisine_type)" />
                </div>
            </div>

            <label class="flex items-center gap-2 text-sm text-ink-700">
                <input type="checkbox" name="is_active" value="1" {{ $restaurant->is_active ? 'checked' : '' }} class="rounded border-ink-200">
                Active
            </label>

            <button type="submit" class="rounded-lg bg-brand-600 px-4 py-2 text-white font-medium hover:bg-brand-700">
                Save Changes
            </button>
        </form>

        <form method="POST" action="{{ route('admin.restaurants.destroy', $restaurant) }}"
              onsubmit="return confirm('Delete {{ $restaurant->name }} and all its branches, menus, and staff? This cannot be undone.')"
              class="mt-4 pt-4 border-t border-ink-100">
            @csrf @method('DELETE')
            <button type="submit" class="flex items-center gap-1.5 text-sm font-medium text-red-600 hover:text-red-700">
                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="3 6 5 6 21 6"/>
                    <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                </svg>
                Delete Restaurant
            </button>
        </form>
    </div>
@endsection
