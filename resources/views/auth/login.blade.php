@extends('layouts.guest')

@section('title', 'Login')

@section('content')
    <h2 class="text-2xl font-bold text-ink-900">Welcome back</h2>
    <p class="mt-1.5 text-sm text-ink-500">Sign in to continue to your dashboard.</p>

    @if ($errors->any())
        <div class="mt-6 flex items-start gap-2 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
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

    <form method="POST" action="{{ route('login.store') }}" class="mt-7 space-y-5">
        @csrf

        <div>
            <label for="email" class="block text-sm font-medium text-ink-700">Email address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                   placeholder="you@example.com"
                   class="mt-1.5 block w-full rounded-xl border-ink-100 bg-ink-50/60 px-4 py-2.5 text-sm text-ink-900 placeholder:text-ink-400 focus:border-brand-400 focus:bg-white focus:ring-brand-400">
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-ink-700">Password</label>
            <input id="password" type="password" name="password" required
                   placeholder="••••••••"
                   class="mt-1.5 block w-full rounded-xl border-ink-100 bg-ink-50/60 px-4 py-2.5 text-sm text-ink-900 placeholder:text-ink-400 focus:border-brand-400 focus:bg-white focus:ring-brand-400">
        </div>

        <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 text-sm text-ink-600">
                <input type="checkbox" name="remember" class="rounded border-ink-300 text-brand-600 focus:ring-brand-400">
                Remember me
            </label>
        </div>

        <button type="submit"
                class="w-full rounded-xl bg-brand-500 px-4 py-2.5 text-sm font-semibold text-white shadow-sm shadow-brand-500/30 transition hover:bg-brand-600">
            Sign in
        </button>
    </form>

    @if ($demoAdmin || $demoStaff->isNotEmpty())
        <div class="mt-8 rounded-xl bg-ink-50/60 px-4 py-3 text-xs text-ink-500 space-y-0.5">
            <div class="font-medium text-ink-600 mb-1">Demo accounts</div>
            @if ($demoAdmin)
                <div>Admin — {{ $demoAdmin->email }} / 12345678</div>
            @endif
            @foreach ($demoStaff as $staff)
                <div>Staff ({{ $staff->branch->name }}) — {{ $staff->email }} / 12345678</div>
            @endforeach
        </div>
    @endif
@endsection
