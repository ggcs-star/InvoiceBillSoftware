@extends('layouts.guest')

@section('title', 'Login')

@section('content')
    @if ($errors->any())
        <div class="mb-5 flex items-start gap-2 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <svg viewBox="0 0 24 24" class="mt-0.5 h-4 w-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="9"/>
                <line x1="12" y1="8" x2="12" y2="13"/>
                <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <div>
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('login.store') }}" class="space-y-4" x-data="{ showPassword: false }">
        @csrf

        <div class="relative">
            <svg viewBox="0 0 24 24" class="pointer-events-none absolute left-4 top-1/2 h-[18px] w-[18px] -translate-y-1/2 text-ink-400" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="5" width="18" height="14" rx="2"/>
                <path d="m3 7 9 6 9-6"/>
            </svg>
            <label for="email" class="sr-only">Email address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                   placeholder="Email address"
                   class="block w-full rounded-full border-transparent bg-ink-50/80 pl-11 pr-4 py-3 text-sm text-ink-900 placeholder:text-ink-400 focus:border-brand-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-400">
        </div>

        <div class="relative">
            <svg viewBox="0 0 24 24" class="pointer-events-none absolute left-4 top-1/2 h-[18px] w-[18px] -translate-y-1/2 text-ink-400" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <rect x="5" y="11" width="14" height="9" rx="2"/>
                <path d="M8 11V8a4 4 0 0 1 8 0v3"/>
            </svg>
            <label for="password" class="sr-only">Password</label>
            <input :type="showPassword ? 'text' : 'password'" id="password" name="password" required
                   placeholder="Password"
                   class="block w-full rounded-full border-transparent bg-ink-50/80 pl-11 pr-11 py-3 text-sm text-ink-900 placeholder:text-ink-400 focus:border-brand-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-400">
            <button type="button" @click="showPassword = !showPassword"
                    class="absolute right-3.5 top-1/2 -translate-y-1/2 text-ink-400 hover:text-ink-600">
                <svg x-show="!showPassword" viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7Z"/>
                    <circle cx="12" cy="12" r="3"/>
                </svg>
                <svg x-show="showPassword" x-cloak viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17.94 17.94A10.94 10.94 0 0 1 12 20c-7 0-11-8-11-8a21.6 21.6 0 0 1 5.06-6.44M9.9 4.24A10.4 10.4 0 0 1 12 4c7 0 11 8 11 8a21.6 21.6 0 0 1-2.16 3.19M14.12 14.12a3 3 0 1 1-4.24-4.24"/>
                    <line x1="1" y1="1" x2="23" y2="23"/>
                </svg>
            </button>
        </div>

        <div class="flex items-center justify-between px-1 pt-0.5">
            <label class="flex items-center gap-2 text-sm text-ink-600">
                <input type="checkbox" name="remember" class="rounded border-ink-300 text-brand-600 focus:ring-brand-400">
                Remember me
            </label>
            <span class="text-xs font-medium text-brand-600" title="Contact your administrator to reset your password">Forgot password?</span>
        </div>

        <button type="submit"
                class="flex w-full items-center justify-center gap-2 rounded-full bg-gradient-to-r from-brand-500 to-brand-600 px-4 py-3 text-sm font-semibold text-white shadow-sm shadow-brand-500/30 transition hover:from-brand-600 hover:to-brand-700">
            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                <polyline points="10 17 15 12 10 7"/>
                <line x1="15" y1="12" x2="3" y2="12"/>
            </svg>
            Sign in
        </button>
    </form>

    @if ($demoAdmin || $demoStaff->isNotEmpty())
        <div class="mt-7 flex items-center gap-3 text-xs text-ink-400">
            <span class="h-px flex-1 bg-ink-100"></span>
            Demo Accounts
            <span class="h-px flex-1 bg-ink-100"></span>
        </div>

        <div class="mt-3 divide-y divide-ink-100 overflow-hidden rounded-xl border border-ink-100">
            @if ($demoAdmin)
                <div class="flex items-center gap-2.5 bg-ink-50/40 px-3 py-2.5">
                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-brand-50 text-brand-600">
                        <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="8" r="4"/>
                            <path d="M4 21c0-4 3.5-7 8-7s8 3 8 7"/>
                        </svg>
                    </span>
                    <div class="min-w-0 flex-1">
                        <div class="text-xs font-semibold text-ink-900">Admin</div>
                        <div class="truncate text-[11px] text-ink-400">{{ $demoAdmin->email }}</div>
                    </div>
                    <span class="shrink-0 rounded-full bg-ink-100 px-2.5 py-1 text-[11px] font-medium text-ink-500">12345678</span>
                </div>
            @endif
            @foreach ($demoStaff as $staff)
                <div class="flex items-center gap-2.5 bg-ink-50/40 px-3 py-2.5">
                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-brand-50 text-brand-600">
                        <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="8" r="4"/>
                            <path d="M4 21c0-4 3.5-7 8-7s8 3 8 7"/>
                        </svg>
                    </span>
                    <div class="min-w-0 flex-1">
                        <div class="text-xs font-semibold text-ink-900">Staff ({{ $staff->branch->name }})</div>
                        <div class="truncate text-[11px] text-ink-400">{{ $staff->email }}</div>
                    </div>
                    <span class="shrink-0 rounded-full bg-ink-100 px-2.5 py-1 text-[11px] font-medium text-ink-500">12345678</span>
                </div>
            @endforeach
        </div>
    @endif
@endsection
