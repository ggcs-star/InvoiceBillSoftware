@extends('layouts.app')

@section('title', 'Staff — ' . $branch->name)
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
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or email..."
                       class="flex-1 rounded-lg border-ink-200 shadow-sm text-sm">
                <button type="submit" class="rounded-lg border border-ink-200 px-4 py-2 text-sm font-medium text-ink-600 hover:bg-ink-50">Search</button>
                @if (request()->filled('search'))
                    <a href="{{ route('admin.branches.staff.index', [$restaurant, $branch]) }}" class="rounded-lg border border-ink-200 px-4 py-2 text-sm font-medium text-ink-600 hover:bg-ink-50">Clear</a>
                @endif
            </form>
        </div>

        <div class="bg-white rounded-2xl border border-ink-100 shadow-card overflow-hidden">
            <table class="w-full text-sm">
                <thead class="text-left text-ink-500 border-b">
                    <tr>
                        <th class="px-5 py-3">Name</th>
                        <th class="px-5 py-3">Email</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($staff as $member)
                        <tr class="border-b last:border-0">
                            <td class="px-5 py-3 font-medium">{{ $member->name }}</td>
                            <td class="px-5 py-3">{{ $member->email }}</td>
                            <td class="px-5 py-3">{{ $member->is_active ? 'Active' : 'Inactive' }}</td>
                            <td class="px-5 py-3 text-right">
                                <form method="POST" action="{{ route('admin.branches.staff.destroy', [$restaurant, $branch, $member]) }}" onsubmit="return confirm('Remove this staff account?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:underline">Remove</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-5 py-6 text-center text-ink-400">No staff accounts match.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div>{{ $staff->links() }}</div>

        <div class="bg-white rounded-2xl border border-ink-100 shadow-card p-6 max-w-lg">
            <h3 class="font-medium mb-4">Add Staff Account</h3>
            <form method="POST" action="{{ route('admin.branches.staff.store', [$restaurant, $branch]) }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-ink-700">Name</label>
                    <input type="text" name="name" required class="mt-1 block w-full rounded-lg border-ink-200 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink-700">Email</label>
                    <input type="email" name="email" required class="mt-1 block w-full rounded-lg border-ink-200 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink-700">Password</label>
                    <input type="password" name="password" required class="mt-1 block w-full rounded-lg border-ink-200 shadow-sm">
                </div>
                <button type="submit" class="rounded-lg bg-brand-600 px-4 py-2 text-white font-medium hover:bg-brand-700">Create Account</button>
            </form>
        </div>
    </div>
@endsection
