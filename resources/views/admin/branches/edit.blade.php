@extends('layouts.app')

@section('title', $branch->name)
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

    <div class="bg-white rounded-2xl border border-ink-100 shadow-card p-6 max-w-xl">
        <form method="POST" action="{{ route('admin.branches.update', [$restaurant, $branch]) }}" class="space-y-4">
            @csrf @method('PUT')

            <div>
                <label class="block text-sm font-medium text-ink-700">Branch Name</label>
                <input type="text" name="name" value="{{ old('name', $branch->name) }}" required
                       class="mt-1 block w-full rounded-lg border-ink-200 shadow-sm">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-ink-700">Email</label>
                    <input type="email" name="email" value="{{ old('email', $branch->email) }}"
                           class="mt-1 block w-full rounded-lg border-ink-200 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink-700">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $branch->phone) }}"
                           class="mt-1 block w-full rounded-lg border-ink-200 shadow-sm">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-ink-700">Address</label>
                <textarea name="address" rows="2" class="mt-1 block w-full rounded-lg border-ink-200 shadow-sm">{{ old('address', $branch->address) }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-ink-700">GSTIN</label>
                    <input type="text" name="gstin" value="{{ old('gstin', $branch->gstin) }}"
                           class="mt-1 block w-full rounded-lg border-ink-200 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink-700">Currency</label>
                    <input type="text" name="currency" value="{{ old('currency', $branch->currency) }}" maxlength="3"
                           class="mt-1 block w-full rounded-lg border-ink-200 shadow-sm">
                </div>
            </div>

            <label class="flex items-center gap-2 text-sm text-ink-700">
                <input type="checkbox" name="is_active" value="1" {{ $branch->is_active ? 'checked' : '' }} class="rounded border-ink-200">
                Active
            </label>

            <button type="submit" class="rounded-lg bg-brand-600 px-4 py-2 text-white font-medium hover:bg-brand-700">
                Save Changes
            </button>
        </form>
    </div>
@endsection
