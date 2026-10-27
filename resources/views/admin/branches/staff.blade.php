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

    <div x-data="{ addOpen: false, openViewFor: null, openEditFor: null }" class="space-y-4">
        <div class="flex justify-end">
            <button type="button" @click="addOpen = true" class="rounded-lg bg-brand-600 px-4 py-2 text-white text-sm font-medium hover:bg-brand-700">
                + Add Staff Account
            </button>
        </div>

        {{-- Add Staff modal --}}
        <div x-show="addOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-ink-900/50 p-4">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden" @click.outside="addOpen = false">
                <div class="flex items-center justify-between px-6 py-4 border-b border-ink-100">
                    <h3 class="text-lg font-bold text-ink-900">Add Staff Account</h3>
                    <button type="button" @click="addOpen = false" class="text-ink-400 hover:text-ink-700">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"/>
                            <line x1="6" y1="6" x2="18" y2="18"/>
                        </svg>
                    </button>
                </div>
                <form method="POST" action="{{ route('admin.branches.staff.store', [$restaurant, $branch]) }}" class="p-6 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-ink-500 mb-1">Name</label>
                        <input type="text" name="name" required class="w-full rounded-lg border-ink-200 px-3 py-2 shadow-sm text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-ink-500 mb-1">Email</label>
                        <input type="email" name="email" required class="w-full rounded-lg border-ink-200 px-3 py-2 shadow-sm text-sm">
                    </div>
                    <div x-data="{ showPassword: false }">
                        <label class="block text-xs font-medium text-ink-500 mb-1">Password</label>
                        <div class="relative">
                            <input :type="showPassword ? 'text' : 'password'" name="password" required minlength="8" class="w-full rounded-lg border-ink-200 px-3 py-2 pr-10 shadow-sm text-sm">
                            <button type="button" @click="showPassword = !showPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-ink-400 hover:text-ink-600">
                                <svg x-show="!showPassword" viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7Z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                                <svg x-show="showPassword" x-cloak viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17.94 17.94A10.94 10.94 0 0 1 12 20c-7 0-11-8-11-8a21.6 21.6 0 0 1 5.06-6.44M9.9 4.24A10.4 10.4 0 0 1 12 4c7 0 11 8 11 8a21.6 21.6 0 0 1-2.16 3.19M14.12 14.12a3 3 0 1 1-4.24-4.24"/>
                                    <line x1="1" y1="1" x2="23" y2="23"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <label class="flex items-center gap-2 text-sm text-ink-700">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" checked class="rounded border-ink-300 text-brand-600">
                        Active
                    </label>
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="addOpen = false" class="rounded-lg border border-ink-200 px-4 py-2 text-sm font-medium text-ink-600 hover:bg-ink-50">Cancel</button>
                        <button type="submit" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700">Create Account</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-ink-100 shadow-card p-4">
            <form method="GET" class="flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-[180px]">
                    <label class="block text-xs font-medium text-ink-500 mb-1">Search</label>
                    <div class="relative">
                        <svg viewBox="0 0 24 24" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-ink-400" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="7"/>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or email..."
                               class="w-full rounded-lg border-ink-200 bg-white pl-10 pr-4 py-2 shadow-sm text-sm">
                    </div>
                </div>
                <x-searchable-select name="status" placeholder="All Status"
                    :options="[['value' => 'active', 'label' => 'Active'], ['value' => 'inactive', 'label' => 'Inactive']]"
                    :selected="request('status')" />
                <button type="submit" class="rounded-lg border border-ink-200 px-4 py-2 text-sm font-medium text-ink-600 hover:bg-ink-50">Search</button>
                @if (request()->anyFilled(['search', 'status']))
                    <a href="{{ route('admin.branches.staff.index', [$restaurant, $branch]) }}" class="rounded-lg border border-ink-200 px-4 py-2 text-sm font-medium text-ink-600 hover:bg-ink-50">Clear</a>
                @endif
            </form>
        </div>

        <div class="bg-white rounded-2xl border border-ink-100 shadow-card overflow-hidden">
            <table class="w-full text-sm">
                <thead class="text-left text-ink-500 border-b bg-ink-50/60 font-bold">
                    <tr>
                        <th class="px-5 py-3">Name</th>
                        <th class="px-5 py-3">Email</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($staff as $member)
                        <tr class="border-b last:border-0">
                            <td class="px-5 py-3 font-medium text-ink-900">{{ $member->name }}</td>
                            <td class="px-5 py-3 text-ink-500">{{ $member->email }}</td>
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center gap-1.5 text-xs font-medium {{ $member->is_active ? 'text-green-700' : 'text-ink-500' }}">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $member->is_active ? 'bg-green-500' : 'bg-ink-300' }}"></span>
                                    {{ $member->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-right">
                                <div class="inline-flex gap-2">
                                    <button type="button" title="View" @click="openViewFor = {{ $member->id }}"
                                            class="flex h-7 w-7 items-center justify-center rounded-lg border border-ink-200 text-ink-500 hover:bg-ink-50">
                                        <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7Z"/>
                                            <circle cx="12" cy="12" r="3"/>
                                        </svg>
                                    </button>
                                    <button type="button" title="Edit" @click="openEditFor = {{ $member->id }}"
                                            class="flex h-7 w-7 items-center justify-center rounded-lg border border-blue-200 bg-blue-50 text-blue-600 hover:bg-blue-100">
                                        <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M12 20h9"/>
                                            <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/>
                                        </svg>
                                    </button>
                                    <form method="POST" action="{{ route('admin.branches.staff.destroy', [$restaurant, $branch, $member]) }}" onsubmit="return confirm('Remove this staff account?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" title="Delete" class="flex h-7 w-7 items-center justify-center rounded-lg border border-red-200 bg-red-50 text-red-600 hover:bg-red-100">
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
                        <tr><td colspan="4" class="px-5 py-6 text-center text-ink-400">No staff accounts match.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-pagination :paginator="$staff" />

        {{-- View modals (one per staff member) --}}
        @foreach ($staff as $member)
            <div x-show="openViewFor === {{ $member->id }}" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-ink-900/50 p-4">
                <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm overflow-hidden" @click.outside="openViewFor = null">
                    <div class="flex items-center justify-between px-6 py-4 border-b border-ink-100">
                        <h3 class="text-lg font-bold text-ink-900">Staff Details</h3>
                        <button type="button" @click="openViewFor = null" class="text-ink-400 hover:text-ink-700">
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="18" y1="6" x2="6" y2="18"/>
                                <line x1="6" y1="6" x2="18" y2="18"/>
                            </svg>
                        </button>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex items-center gap-3">
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-brand-50 text-sm font-semibold text-brand-600">
                                {{ Str::of($member->name)->explode(' ')->map(fn ($p) => Str::substr($p, 0, 1))->take(2)->implode('') }}
                            </span>
                            <div class="min-w-0">
                                <div class="text-base font-bold text-ink-900 truncate">{{ $member->name }}</div>
                                <div class="text-xs font-medium {{ $member->is_active ? 'text-green-600' : 'text-ink-400' }}">{{ $member->is_active ? 'Active' : 'Inactive' }}</div>
                            </div>
                        </div>
                        <dl class="grid grid-cols-2 gap-x-4 gap-y-3 text-sm">
                            <div class="col-span-2">
                                <dt class="text-xs text-ink-400">Email</dt>
                                <dd class="font-medium text-ink-900 truncate">{{ $member->email }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-ink-400">Restaurant</dt>
                                <dd class="font-medium text-ink-900">{{ $restaurant->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-ink-400">Branch</dt>
                                <dd class="font-medium text-ink-900">{{ $branch->name }}</dd>
                            </div>
                        </dl>
                        <div class="flex justify-end pt-2">
                            <button type="button" @click="openViewFor = null" class="rounded-lg border border-ink-200 px-4 py-2 text-sm font-medium text-ink-600 hover:bg-ink-50">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        {{-- Edit modals (one per staff member) --}}
        @foreach ($staff as $member)
            <div x-show="openEditFor === {{ $member->id }}" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-ink-900/50 p-4">
                <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden" @click.outside="openEditFor = null">
                    <div class="flex items-center justify-between px-6 py-4 border-b border-ink-100">
                        <h3 class="text-lg font-bold text-ink-900">Edit Staff Account</h3>
                        <button type="button" @click="openEditFor = null" class="text-ink-400 hover:text-ink-700">
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="18" y1="6" x2="6" y2="18"/>
                                <line x1="6" y1="6" x2="18" y2="18"/>
                            </svg>
                        </button>
                    </div>
                    <form method="POST" action="{{ route('admin.branches.staff.update', [$restaurant, $branch, $member]) }}" class="p-6 space-y-4">
                        @csrf
                        @method('PUT')
                        <div>
                            <label class="block text-xs font-medium text-ink-500 mb-1">Name</label>
                            <input type="text" name="name" value="{{ $member->name }}" required class="w-full rounded-lg border-ink-200 px-3 py-2 shadow-sm text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-ink-500 mb-1">Email</label>
                            <input type="email" name="email" value="{{ $member->email }}" required class="w-full rounded-lg border-ink-200 px-3 py-2 shadow-sm text-sm">
                        </div>
                        <div x-data="{ showPassword: false }">
                            <label class="block text-xs font-medium text-ink-500 mb-1">Password</label>
                            <div class="relative">
                                <input :type="showPassword ? 'text' : 'password'" name="password" minlength="8" placeholder="Leave blank to keep current password" class="w-full rounded-lg border-ink-200 px-3 py-2 pr-10 shadow-sm text-sm">
                                <button type="button" @click="showPassword = !showPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-ink-400 hover:text-ink-600">
                                    <svg x-show="!showPassword" viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7Z"/>
                                        <circle cx="12" cy="12" r="3"/>
                                    </svg>
                                    <svg x-show="showPassword" x-cloak viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M17.94 17.94A10.94 10.94 0 0 1 12 20c-7 0-11-8-11-8a21.6 21.6 0 0 1 5.06-6.44M9.9 4.24A10.4 10.4 0 0 1 12 4c7 0 11 8 11 8a21.6 21.6 0 0 1-2.16 3.19M14.12 14.12a3 3 0 1 1-4.24-4.24"/>
                                        <line x1="1" y1="1" x2="23" y2="23"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <label class="flex items-center gap-2 text-sm text-ink-700">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" {{ $member->is_active ? 'checked' : '' }} class="rounded border-ink-300 text-brand-600">
                            Active
                        </label>
                        <div class="flex justify-end gap-2 pt-2">
                            <button type="button" @click="openEditFor = null" class="rounded-lg border border-ink-200 px-4 py-2 text-sm font-medium text-ink-600 hover:bg-ink-50">Cancel</button>
                            <button type="submit" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
@endsection
