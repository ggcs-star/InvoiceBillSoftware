<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Receipt')</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('images/logo_jagdai.jpeg') }}">
    @vite(['resources/css/app.css'])
    <style>
        @media print {
            @page { margin: 0; size: 80mm auto; }
            body { margin: 0; }
        }
        body { font-family: 'Courier New', monospace; }
    </style>
</head>
<body class="bg-white text-black">
    <div class="w-[80mm] mx-auto p-3 text-xs">
        @yield('content')
    </div>
    <script>
        window.addEventListener('load', () => window.print());
    </script>
</body>
</html>
