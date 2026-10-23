<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Jagdai Cloud Kitchen')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white text-ink-900 antialiased">
    <div class="min-h-screen grid lg:grid-cols-2">
        {{-- Brand panel --}}
        <div class="hidden lg:block bg-black bg-contain bg-center bg-no-repeat" style="background-image: url('{{ asset('images/loginImage.jpeg') }}')"></div>

        {{-- Form panel --}}
        <div class="flex items-center justify-center p-8">
            <div class="w-full max-w-sm">
                <div class="mb-8 flex items-center gap-2.5 lg:hidden">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-brand-500 text-white">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2c-3.5 0-6 2.8-6 6.2 0 4.4 6 13.3 6 13.3s6-8.9 6-13.3C18 4.8 15.5 2 12 2Z"/>
                            <circle cx="12" cy="8.2" r="2.2"/>
                        </svg>
                    </span>
                    <span class="text-lg font-bold tracking-tight text-ink-900">Jagdai Cloud Kitchen</span>
                </div>

                @yield('content')
            </div>
        </div>
    </div>
</body>
</html>
