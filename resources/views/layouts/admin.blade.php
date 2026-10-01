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
        .sidebar-link { display:flex; align-items:center; gap:0.625rem; padding:0.625rem 0.875rem; border-radius:0.75rem; font-size:0.8125rem; font-weight:600; color:#94a3b8; transition:all 0.2s; }
        .sidebar-link:hover { background:rgba(251,146,60,0.1); color:#fb923c; }
        .sidebar-link.active { background:linear-gradient(135deg,#f97316,#f59e0b); color:#fff; box-shadow:0 4px 14px rgba(249,115,22,0.35); }
        .sidebar-link svg { width:1.1rem; height:1.1rem; flex-shrink:0; }
        .stat-card { background:linear-gradient(135deg,var(--from),var(--to)); border-radius:1.25rem; padding:1.25rem; position:relative; overflow:hidden; }
        .stat-card::after { content:''; position:absolute; inset:0; background:rgba(255,255,255,0.05); border-radius:inherit; }
    </style>
</head>
<body class="flex h-full bg-slate-100" style="font-family:'Plus Jakarta Sans',sans-serif">

    <!-- Mobile sidebar overlay -->
    <div id="sidebar-overlay" onclick="closeSidebar()" class="fixed inset-0 z-30 bg-slate-900/60 backdrop-blur-xs hidden lg:hidden"></div>

    <!-- ===== SIDEBAR ===== -->
    <aside id="sidebar" class="fixed lg:static inset-y-0 left-0 z-40 flex flex-col w-64 bg-slate-900 border-r border-slate-800 shadow-2xl -translate-x-full lg:translate-x-0 transition-transform duration-300">

        <!-- Brand -->
        <div class="flex items-center gap-3 px-5 py-5 border-b border-slate-800">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-orange-500 to-amber-400 flex items-center justify-center shadow-lg shadow-orange-500/30 shrink-0">
                <img src="{{ asset('img/kanic-logo.png') }}?v=2" alt="Logo" class="w-8 h-8 rounded-xl object-cover">
            </div>
            <div>
                <div class="text-sm font-black text-white tracking-tight leading-none">Kantin<span class="text-orange-400">Skanic</span></div>
                <div class="text-[10px] text-slate-400 font-medium mt-0.5">@yield('sidebar-role', 'Panel Dashboard')</div>
            </div>
        </div>

        <!-- Nav Links -->
        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-hidden">
            @hasSection('sidebar-nav')
                @yield('sidebar-nav')
            @elseif(Auth::check() && Auth::user()->isAdmin())
                @include('admin.partials.sidebar_nav')
            @endif
        </nav>

        <!-- User Info at Bottom -->
        @auth
        <div class="px-4 py-4 border-t border-slate-800">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-orange-400 to-rose-500 flex items-center justify-center text-white font-black text-xs shadow shrink-0">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-xs font-bold text-white truncate">{{ Auth::user()->name }}</div>
                    <div class="text-[10px] text-slate-400 truncate">{{ Auth::user()->email }}</div>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-2 py-2 px-3 rounded-xl bg-slate-800 hover:bg-rose-600/20 border border-slate-700 hover:border-rose-500/50 text-slate-400 hover:text-rose-400 text-xs font-semibold transition-all cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                    </svg>
                    Keluar
                </button>
            </form>
        </div>
        @endauth
    </aside>

    <!-- ===== MAIN AREA ===== -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

        <!-- Top bar (mobile + header) -->
        <header class="flex items-center justify-between px-4 sm:px-6 min-h-[4.25rem] sm:min-h-[4.75rem] py-3 bg-white border-b border-slate-200 shadow-xs shrink-0">
            <div class="flex items-center gap-3 sm:gap-4">
                <!-- Mobile hamburger -->
                <button type="button" onclick="openSidebar()" class="lg:hidden p-2 rounded-xl text-slate-500 hover:bg-slate-100 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>
                <div>
                    <h1 class="text-base sm:text-xl font-black text-slate-900 tracking-tight leading-snug">@yield('page-title', 'Dashboard')</h1>
                    @hasSection('page-subtitle')
                    <p class="text-xs sm:text-sm text-slate-400 font-medium mt-0.5">@yield('page-subtitle')</p>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-3">
                @auth
                <div class="flex items-center gap-3">
                    <div class="flex flex-col text-right">
                        <span class="text-xs sm:text-sm font-extrabold text-slate-800 leading-tight">
                            {{ Auth::user()->isAdmin() ? 'Administrator' : Auth::user()->name }}
                        </span>
                        <span class="text-[10px] sm:text-xs font-medium text-slate-400 leading-tight mt-0.5">
                            @if(Auth::user()->isAdmin())
                                Super Admin
                            @elseif(Auth::user()->isPenjual())
                                Vendor Stand
                            @else
                                Siswa
                            @endif
                        </span>
                    </div>
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-orange-500 flex items-center justify-center text-white font-black text-sm sm:text-base shrink-0 shadow-xs">
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
            document.getElementById('sidebar').classList.remove('-translate-x-full');
            document.getElementById('sidebar-overlay').classList.remove('hidden');
        }
        function closeSidebar() {
            document.getElementById('sidebar').classList.add('-translate-x-full');
            document.getElementById('sidebar-overlay').classList.add('hidden');
        }
    </script>
    @stack('scripts')
</body>
</html>
