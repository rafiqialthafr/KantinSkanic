<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50 antialiased">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'KantinSkanic')</title>
    <link rel="icon" type="image/png" href="{{ asset('img/kanic-logo.png') }}?v=2">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
        }

        [x-cloak] {
            display: none !important;
        }
    </style>
    @stack('styles')
</head>

<body class="min-h-screen bg-white text-slate-800 flex flex-col antialiased">
    <!-- Global Flash Messages (session error only) -->
    @if(session('error'))
    <div class="fixed top-4 left-1/2 -translate-x-1/2 z-50 max-w-md px-4 w-full">
        <div class="rounded-2xl bg-rose-50 p-4 border border-rose-200 flex items-start gap-3 shadow-lg">
            <svg class="w-5 h-5 text-rose-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
            </svg>
            <div class="text-sm font-semibold text-rose-800">{{ session('error') }}</div>
        </div>
    </div>
    @endif

    <main class="w-full flex-1 flex flex-col min-h-screen">
        @yield('content')
    </main>

    @stack('scripts')
</body>

</html>