<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Jagdai Cloud Kitchen')</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('images/logo_jagdai.jpeg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-brand-50 text-ink-900 antialiased">
    <div class="min-h-screen grid lg:grid-cols-[55%_45%]">
        {{-- Brand panel --}}
        <div class="hidden lg:block bg-black bg-cover bg-center" style="background-image: url('{{ asset('images/loginImage.jpeg') }}')"></div>

        {{-- Form panel --}}
        <div class="relative flex items-center justify-center overflow-hidden p-6 py-12">
            {{-- Decorative background --}}
            <div class="pointer-events-none absolute inset-0 overflow-hidden select-none" aria-hidden="true">
                <div class="absolute -top-16 -right-16 h-56 w-56 rounded-full bg-brand-200/40"></div>
                <div class="absolute -bottom-24 -right-10 h-72 w-72 rounded-full bg-brand-200/30"></div>

                <svg viewBox="0 0 24 24" class="absolute left-[8%] top-[12%] h-9 w-9 -rotate-12 text-brand-300/40" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M12 2 2 20h20L12 2Z"/><path d="M7 14h10M8.5 17h7"/>
                </svg>
                <svg viewBox="0 0 24 24" class="absolute left-[14%] bottom-[16%] h-10 w-10 rotate-6 text-brand-300/40" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M4 10c2-5 6-7 8-7s6 2 8 7"/><path d="M3 10h18l-1.5 9a2 2 0 0 1-2 1.7H6.5a2 2 0 0 1-2-1.7L3 10Z"/>
                </svg>
                <svg viewBox="0 0 24 24" class="absolute left-[45%] top-[6%] h-7 w-7 rotate-12 text-brand-300/40" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M3 3c9 1 14 6 18 18"/><path d="M3 3c1 9 6 14 10 10"/>
                </svg>
                <svg viewBox="0 0 24 24" class="absolute right-[10%] top-[20%] h-8 w-8 rotate-6 text-brand-300/40" fill="none" stroke="currentColor" stroke-width="1.5">
                    <rect x="4" y="9" width="16" height="10" rx="2"/><path d="M8 9V6a4 4 0 0 1 8 0v3"/>
                </svg>
                <svg viewBox="0 0 24 24" class="absolute right-[16%] bottom-[12%] h-9 w-9 -rotate-6 text-brand-300/40" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M5 11c0-4 3-8 7-8s7 4 7 8"/><path d="M4 11h16l-1.3 7.5a2 2 0 0 1-2 1.5H7.3a2 2 0 0 1-2-1.5L4 11Z"/>
                </svg>
            </div>

            <div class="relative w-full max-w-sm">
                <div class="rounded-3xl bg-white p-8 shadow-xl shadow-brand-900/10 border border-ink-100/70">
                    {{-- Logo --}}
                    <div class="mb-7 flex flex-col items-center text-center">
                        <span class="mb-2 flex h-14 w-14 items-center justify-center rounded-2xl bg-brand-50 text-brand-500">
                            <svg viewBox="0 0 48 48" class="h-8 w-8" fill="currentColor">
                                <circle cx="16" cy="17" r="7"/>
                                <circle cx="32" cy="17" r="7"/>
                                <circle cx="24" cy="12" r="9"/>
                                <rect x="14" y="21" width="20" height="10" rx="2"/>
                                <rect x="16" y="31" width="16" height="6" rx="1.5"/>
                            </svg>
                        </span>
                        <div class="text-xl font-extrabold tracking-tight">
                            <span class="text-ink-900">Jagdai</span> <span class="text-brand-500">Food</span>
                        </div>
                        <div class="mt-1.5 flex items-center gap-2 text-[11px] font-semibold uppercase tracking-[0.2em] text-ink-500">
                            <span class="h-px w-5 bg-ink-200"></span>
                            Cloud Kitchen
                            <span class="h-px w-5 bg-ink-200"></span>
                        </div>
                    </div>

                    @yield('content')
                </div>
            </div>
        </div>
    </div>
</body>
</html>
