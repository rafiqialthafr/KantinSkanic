<!DOCTYPE html>
<html lang="id" class="h-full antialiased scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — KantinSkanic</title>
    <link rel="icon" type="image/png" href="{{ asset('img/kanic-logo.png') }}?v=2">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif; }
        [x-cloak] { display: none !important; }
        .sidebar-link { display:flex; align-items:center; gap:0.625rem; padding:0.75rem 0.875rem; min-height:44px; border-radius:0.75rem; font-size:0.8125rem; font-weight:600; color:#94a3b8; transition:all 0.2s; }
        .sidebar-link:hover { background:rgba(251,146,60,0.1); color:#fb923c; }
        .sidebar-link.active { background:linear-gradient(135deg,#f97316,#f59e0b); color:#fff; box-shadow:0 4px 14px rgba(249,115,22,0.35); }
        .sidebar-link svg { width:1.15rem; height:1.15rem; flex-shrink:0; }
        .sidebar-link-logout { color:#94a3b8; }
        .sidebar-link-logout:hover { background:rgba(239,68,68,0.12) !important; color:#ef4444 !important; }
        .sidebar-link-logout:hover svg { color:#ef4444 !important; }
        .stat-card { background:linear-gradient(135deg,var(--from),var(--to)); border-radius:1.25rem; padding:1.25rem; position:relative; overflow:hidden; }
        .stat-card::after { content:''; position:absolute; inset:0; background:rgba(255,255,255,0.05); border-radius:inherit; }

        /* Sembunyikan indikator scrollbar di seluruh sidebar */
        #sidebar,
        #sidebar *,
        .no-scrollbar,
        .no-scrollbar * {
            -ms-overflow-style: none !important;
            scrollbar-width: none !important;
        }
        #sidebar::-webkit-scrollbar,
        #sidebar *::-webkit-scrollbar,
        .no-scrollbar::-webkit-scrollbar,
        .no-scrollbar *::-webkit-scrollbar {
            display: none !important;
            width: 0 !important;
            height: 0 !important;
        }
    </style>
</head>
<body class="flex h-full bg-slate-100 antialiased overflow-x-hidden" style="font-family:'Plus Jakarta Sans',sans-serif">

    <!-- Mobile sidebar overlay -->
    <div id="sidebar-overlay" onclick="closeSidebar()" class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-xs hidden transition-opacity duration-300 lg:hidden"></div>

    <!-- ===== SIDEBAR ===== -->
    <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 flex flex-col w-72 max-w-[85vw] lg:w-64 bg-slate-900 border-r border-slate-800 shadow-2xl -translate-x-full lg:static lg:translate-x-0 transition-transform duration-300 ease-in-out shrink-0 no-scrollbar" style="scrollbar-width: none; -ms-overflow-style: none;">

        <!-- Brand & Mobile Close Button -->
        <div class="flex items-center justify-between px-5 py-4 sm:py-5 border-b border-slate-800">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-orange-500 to-amber-400 flex items-center justify-center shadow-lg shadow-orange-500/30 shrink-0">
                    <img src="{{ asset('img/kanic-logo.png') }}?v=2" alt="Logo" class="w-8 h-8 rounded-xl object-cover">
                </div>
                <div>
                    <div class="text-sm font-black text-white tracking-tight leading-none">Kantin<span class="text-orange-400">Skanic</span></div>
                    <div class="text-[10px] text-slate-400 font-medium mt-0.5">@yield('sidebar-role', 'Panel Dashboard')</div>
                </div>
            </div>
            <!-- Close Button for Mobile (min 44x44px touch target) -->
            <button type="button" onclick="closeSidebar()" aria-label="Tutup Menu"
                class="lg:hidden w-11 h-11 flex items-center justify-center rounded-xl text-slate-400 hover:text-white hover:bg-slate-800 active:scale-95 transition-all cursor-pointer">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Nav Links -->
        <nav class="flex-1 px-3 py-4 flex flex-col justify-between overflow-y-auto no-scrollbar" style="scrollbar-width: none; -ms-overflow-style: none;">
            <div class="space-y-1">
                @hasSection('sidebar-nav')
                    @yield('sidebar-nav')
                @elseif(Auth::check() && Auth::user()->isAdmin())
                    @include('admin.partials.sidebar_nav')
                @endif
            </div>

            @auth
            <div class="pt-4 mt-auto">
                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="w-full sidebar-link sidebar-link-logout cursor-pointer text-left">
                        <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                        </svg>
                        <span>Keluar</span>
                    </button>
                </form>
            </div>
            @endauth
        </nav>
    </aside>

    <!-- ===== MAIN AREA ===== -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

        <!-- Top bar (mobile + header) -->
        <header class="flex items-center justify-between px-3 sm:px-6 min-h-[3.75rem] sm:min-h-[4.5rem] py-2 sm:py-2.5 bg-white border-b border-slate-200 shadow-xs shrink-0 sticky top-0 z-20">
            <div class="flex items-center gap-1.5 sm:gap-4 min-w-0">
                <!-- Mobile hamburger -->
                <button type="button" onclick="openSidebar()" aria-label="Buka Menu Navigasi"
                    class="lg:hidden w-9 h-9 sm:w-10 sm:h-10 flex items-center justify-center rounded-xl text-slate-700 bg-slate-50 hover:bg-orange-50 hover:text-orange-600 active:scale-95 border border-slate-200/80 transition-all cursor-pointer shrink-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>

                <!-- Mobile Logo + Brand -->
                <div class="flex lg:hidden items-center gap-1.5 sm:gap-2 shrink-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl bg-gradient-to-br from-orange-500 to-amber-400 flex items-center justify-center shadow-xs shadow-orange-500/20 shrink-0">
                        <img src="{{ asset('img/kanic-logo.png') }}?v=2" alt="Logo" class="w-6 h-6 sm:w-7 sm:h-7 rounded-lg sm:rounded-xl object-cover">
                    </div>
                    <span class="text-xs sm:text-sm font-black text-slate-900 tracking-tight leading-none">Kantin<span class="text-orange-500">Skanic</span></span>
                </div>

                <!-- Desktop Page Title & Subtitle -->
                <div class="min-w-0 hidden lg:block">
                    <h1 class="text-lg sm:text-xl font-black text-slate-900 tracking-tight leading-snug truncate">@yield('page-title', 'Dashboard')</h1>
                    @hasSection('page-subtitle')
                    <p class="text-xs sm:text-sm text-slate-400 font-medium mt-0.5 truncate">@yield('page-subtitle')</p>
                    @endif
                </div>
            </div>

            <!-- Right Actions: Period Filter Dropdown + Profile Avatar & Info -->
            <div class="flex items-center gap-1.5 sm:gap-3 shrink-0">
                @if(!request()->routeIs('admin.menus.*') && (request()->routeIs('admin.*') || isset($periode)))
                    <div class="shrink-0">
                        <x-period-filter :periode="$periode ?? request('periode', 'hari_ini')" />
                    </div>
                @endif

                @auth
                <div class="flex items-center gap-2 sm:gap-3">
                    <div class="hidden sm:flex flex-col text-right">
                        <span class="text-xs sm:text-sm font-extrabold text-slate-800 leading-tight">
                            {{ Auth::user()->isAdmin() ? 'Administrator' : Auth::user()->name }}
                        </span>
                        <span class="text-[10px] sm:text-xs font-semibold text-slate-400 leading-tight mt-0.5">
                            @if(Auth::user()->isAdmin())
                                Super Admin
                            @elseif(Auth::user()->isPenjual())
                                Vendor Stand
                            @else
                                Siswa
                            @endif
                        </span>
                    </div>
                    <!-- Profile Avatar -->
                    <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-gradient-to-br from-orange-500 to-amber-500 flex items-center justify-center text-white font-black text-xs sm:text-sm shrink-0 shadow-sm border-2 border-white ring-2 ring-orange-500/20" title="{{ Auth::user()->name }}">
                        {{ strtoupper(substr(Auth::user()->isAdmin() ? 'Administrator' : Auth::user()->name, 0, 1)) }}
                    </div>
                </div>
                @endauth
            </div>
        </header>

        <!-- Flash Messages -->
        @php
            $hasValidSuccess = session('success') && !str_contains(strtolower(session('success')), 'selamat datang');
        @endphp
        @if($hasValidSuccess || session('error') || $errors->any())
        <div id="flash-alerts" class="px-4 sm:px-6 pt-4 transition-all duration-500">
            @if($hasValidSuccess)
            <div class="mb-3 rounded-xl bg-emerald-50 p-3.5 border border-emerald-200 flex items-center gap-3 shadow-xs">
                <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                <div class="text-sm font-semibold text-emerald-800">{{ session('success') }}</div>
            </div>
            @endif
            @if(session('error'))
            <div class="mb-3 rounded-xl bg-rose-50 p-3.5 border border-rose-200 flex items-center gap-3 shadow-xs">
                <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                </svg>
                <div class="text-sm font-semibold text-rose-800">{{ session('error') }}</div>
            </div>
            @endif
            @if($errors->any())
            <div class="mb-3 rounded-xl bg-rose-50 p-3.5 border border-rose-200 shadow-xs">
                <ul class="list-disc list-inside text-xs text-rose-700 font-medium space-y-0.5">
                    @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
                </ul>
            </div>
            @endif
        </div>
        <script>
            setTimeout(function () {
                var el = document.getElementById('flash-alerts');
                if (el) {
                    el.style.opacity = '0';
                    el.style.transform = 'translateY(-6px)';
                    setTimeout(function () { el.style.display = 'none'; }, 500);
                }
            }, 3000);
        </script>
        @endif

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto">
            @yield('content')
        </main>

    </div>

    <script>
        function openSidebar() {
            var sidebar = document.getElementById('sidebar');
            var overlay = document.getElementById('sidebar-overlay');
            if (sidebar) sidebar.classList.remove('-translate-x-full');
            if (overlay) overlay.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }
        function closeSidebar() {
            var sidebar = document.getElementById('sidebar');
            var overlay = document.getElementById('sidebar-overlay');
            if (sidebar) sidebar.classList.add('-translate-x-full');
            if (overlay) overlay.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeSidebar();
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
