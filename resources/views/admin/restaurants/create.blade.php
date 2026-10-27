@extends('layouts.app')

@section('title', 'Add Restaurant')
@section('header', 'Add Restaurant')

@section('content')
    <div class="bg-white rounded-2xl border border-ink-100 shadow-card p-6 max-w-xl">
        @if ($errors->any())
            <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('admin.restaurants.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-ink-700">Restaurant / Brand Name</label>
                <input type="text" name="name" value="{{ old('name') }}" required autofocus
                       placeholder="e.g. Jagdai Cloud Kitchen"
                       class="mt-1 block w-full rounded-lg border-ink-200 px-3 py-2 shadow-sm">
                <p class="text-xs text-ink-500 mt-1">This is the brand. You'll add individual branches (locations) next, each with its own address, printer, and menu pricing.</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-ink-700">Cuisine Type</label>
                <div class="mt-1">
                    <x-cuisine-type-select name="cuisine_type"
                        :options="$cuisineTypes->map(fn ($name) => ['value' => $name, 'label' => $name])"
                        :selected="old('cuisine_type')" />
                </div>
            </div>

            <button type="submit" class="rounded-lg bg-brand-600 px-4 py-2 text-white font-medium hover:bg-brand-700">
                Create Restaurant
            </button>
        </form>
    </div>
@endsection
