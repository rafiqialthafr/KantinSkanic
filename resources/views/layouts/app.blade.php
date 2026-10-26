<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50 antialiased scroll-smooth overflow-x-hidden w-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-id" content="{{ Auth::id() }}">
    <title>@yield('title', 'KantinSkanic') - Pre-Order Kantin Sekolah</title>
    <link rel="icon" type="image/png" href="{{ asset('img/kanic-logo.png') }}?v=2">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Midtrans Snap Preconnect & Script -->
    @if(config('midtrans.is_production'))
        <link rel="preconnect" href="https://app.midtrans.com">
        <link rel="dns-prefetch" href="https://app.midtrans.com">
    @else
        <link rel="preconnect" href="https://app.sandbox.midtrans.com">
        <link rel="dns-prefetch" href="https://app.sandbox.midtrans.com">
        <link rel="preconnect" href="https://api.sandbox.midtrans.com">
        <link rel="dns-prefetch" href="https://api.sandbox.midtrans.com">
    @endif
    <script src="{{ config('midtrans.snap_js_url') }}" data-client-key="{{ config('midtrans.client_key') }}"></script>
    @stack('head')

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
        }

        [x-cloak] {
            display: none !important;
        }

        /* Sembunyikan indikator scrollbar bawaan browser */
        .no-scrollbar,
        .no-scrollbar * {
            -ms-overflow-style: none !important;
            scrollbar-width: none !important;
        }
        .no-scrollbar::-webkit-scrollbar,
        .no-scrollbar *::-webkit-scrollbar {
            display: none !important;
            width: 0 !important;
            height: 0 !important;
        }

        /* Animated underline hover — kiri ke kanan */
        .nav-link {
            position: relative;
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 0.75rem;
            font-size: 0.9375rem;
            font-weight: 600;
            color: #475569;
            text-decoration: none;
            cursor: pointer;
            background: none;
            border: none;
            transition: color 0.25s ease;
            white-space: nowrap;
            letter-spacing: 0.01em;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 2px;
            left: 0.75rem;
            width: calc(100% - 1.5rem);
            height: 2.5px;
            background: linear-gradient(90deg, #ea580c, #f97316, #f59e0b);
            border-radius: 9999px;
            transform: scaleX(0);
            transform-origin: left center;
            transition: transform 0.28s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .nav-link:hover {
            color: #ea580c;
        }

        .nav-link:hover::after {
            transform: scaleX(1);
        }

        /* Hero section: fills viewport with flexible expansion */
        .hero-fullscreen {
            min-height: 100vh;
        }

        @media (min-width: 640px) {
            .hero-fullscreen {
                min-height: 100vh;
            }
        }

        /* Mobile nav smooth slide animation (Overlay menutupi hero section) */
        #mobile-nav-menu {
            overflow: hidden;
            max-height: 0;
            opacity: 0;
            transform: translateY(-8px);
            pointer-events: none;
            transition: max-height 0.32s cubic-bezier(0.4, 0, 0.2, 1),
                opacity 0.25s ease,
                transform 0.28s cubic-bezier(0.4, 0, 0.2, 1);
        }

        #mobile-nav-menu.mobile-nav-open {
            max-height: calc(100vh - 4.5rem);
            overflow-y: auto;
            opacity: 1;
            transform: translateY(0);
            pointer-events: auto;
        }

        /* Animated hamburger bars */
        .hamburger-bar {
            display: block;
            width: 18px;
            height: 2px;
            border-radius: 9999px;
            background: currentColor;
            transition: transform 0.28s cubic-bezier(0.4, 0, 0.2, 1),
                opacity 0.2s ease,
                width 0.25s ease;
            transform-origin: center;
        }

        .hamburger-btn.is-open .bar-top {
            transform: translateY(6px) rotate(45deg);
        }

        .hamburger-btn.is-open .bar-mid {
            opacity: 0;
            transform: scaleX(0);
        }

        .hamburger-btn.is-open .bar-bot {
            transform: translateY(-6px) rotate(-45deg);
        }

        /* Micro-bounce animation for cart button & badges */
        @keyframes cartPopAnim {
            0% {
                transform: scale(1);
            }

            30% {
                transform: scale(1.08) translateY(-3px);
            }

            60% {
                transform: scale(0.97);
            }

            100% {
                transform: scale(1);
            }
        }

        .cart-pop-anim {
            animation: cartPopAnim 0.32s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        /* Floating circle cart button fixed position di pojok kanan bawah */
        #floating-cart-wrapper {
            position: fixed !important;
            bottom: 1rem !important;
            right: 1rem !important;
            top: auto !important;
            left: auto !important;
            z-index: 40 !important;
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transform: translateY(16px) scale(0.92);
            transition: opacity 0.35s cubic-bezier(0.16, 1, 0.3, 1),
                transform 0.35s cubic-bezier(0.16, 1, 0.3, 1),
                visibility 0.35s cubic-bezier(0.16, 1, 0.3, 1);
        }

        #floating-cart-wrapper.floating-cart-visible {
            opacity: 1 !important;
            visibility: visible !important;
            pointer-events: auto !important;
            transform: translateY(0) scale(1) !important;
        }

        @media (min-width: 640px) {
            #floating-cart-wrapper {
                bottom: 1.5rem !important;
                right: 1.5rem !important;
            }
        }
    </style>
    @stack('styles')
</head>

<body class="flex flex-col min-h-full text-slate-800 bg-slate-50 overflow-x-hidden w-full">
    <!-- Top Navigation Bar -->
    <header id="main-navbar" class="{{ request()->routeIs('katalog.index') ? 'absolute top-0 left-0 right-0 z-40 bg-transparent border-b border-transparent shadow-none w-full' : 'relative z-40 bg-white border-b border-slate-200/80 shadow-xs w-full' }}">
        <div class="max-w-7xl mx-auto px-3.5 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 sm:h-20 gap-2">

                <!-- Left: School & App Brand (Spacious & breathable) -->
                <a href="{{ route('katalog.index') }}" class="flex items-center gap-2.5 sm:gap-3.5 group py-2 shrink-0 min-w-0">
                    <img src="{{ asset('img/kanic-logo.png') }}?v=2" alt="KantinSkanic" class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl shadow-md shadow-orange-500/20 group-hover:scale-105 transition-transform shrink-0 object-cover">
                    <div class="flex flex-col justify-center min-w-0">
                        <div class="flex items-center gap-1.5 sm:gap-2">
                            <span class="text-base sm:text-lg font-black tracking-tight text-orange-600 leading-none">Kantin<span class="text-slate-900">Skanic</span></span>
                        </div>
                        <p class="text-xs text-slate-500 font-medium hidden sm:block mt-1 leading-tight truncate">Sistem Pre-Order Makanan & Minuman</p>
                    </div>
                </a>

                <!-- Right Nav Items -->
                <div class="flex items-center gap-1.5 sm:gap-3 shrink-0">

                    <!-- Desktop Nav Links -->
                    <nav class="hidden md:flex items-center">
                        <a href="{{ route('katalog.index') }}" class="nav-link {{ request()->routeIs('katalog.index') ? 'active' : '' }}">Beranda</a>
                        <a href="{{ route('katalog.index') }}#katalog-section" class="nav-link">Daftar Menu</a>
                        <a href="{{ route('order.history') }}" class="nav-link {{ request()->routeIs('order.history') ? 'active' : '' }}">Riwayat Pesanan</a>
                    </nav>

                    <!-- Divider -->
                    <div class="hidden md:block w-px h-6 bg-slate-200 mx-1"></div>

                    <!-- Keranjang: icon-only + badge ala Shopee -->
                    <button type="button" id="nav-cart-button" onclick="triggerOpenCart()"
                        class="p-2 text-slate-600 hover:text-orange-600 transition-colors duration-200 cursor-pointer group shrink-0"
                        title="Buka Keranjang Belanja">
                        <span style="position:relative; display:inline-block;">
                            <svg style="width:22px;height:22px;display:block;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                            </svg>
                            <span id="nav-cart-badge"
                                style="position:absolute; top:-6px; right:-7px; min-width:16px; height:16px; background:#f97316; color:#fff; font-size:10px; font-weight:900; border-radius:9999px; display:none; align-items:center; justify-content:center; padding:0 4px; line-height:1; box-shadow:0 0 0 2px #fff;">
                                0
                            </span>
                        </span>
                    </button>

                    @auth
                    <!-- Authenticated User Menu: Like Admin Profile (Nama & Kelas + Avatar Circle) with Dropdown -->
                    <div class="relative shrink-0 flex items-center" id="user-profile-menu-container">
                        <!-- Profile Trigger Button -->
                        <button type="button"
                            id="user-profile-btn"
                            onclick="toggleUserProfileDropdown(event)"
                            class="flex items-center gap-2 sm:gap-3 p-1 rounded-2xl hover:bg-slate-100/70 active:scale-98 transition-all cursor-pointer focus:outline-hidden group text-left"
                            aria-expanded="false"
                            aria-haspopup="true"
                            title="{{ Auth::user()->name }}">
                            <div class="hidden sm:flex flex-col text-right">
                                <span class="text-xs sm:text-sm font-extrabold text-slate-800 leading-tight group-hover:text-orange-600 transition-colors max-w-[140px] sm:max-w-[180px] truncate">
                                    {{ Auth::user()->name }}
                                </span>
                                <span class="text-[10px] sm:text-xs font-semibold text-slate-400 leading-tight mt-0.5 truncate">
                                    @if(Auth::user()->isSiswa())
                                        {{ Auth::user()->kelas ?? 'Siswa' }}
                                    @elseif(Auth::user()->isAdmin())
                                        Super Admin
                                    @elseif(Auth::user()->isPenjual())
                                        Vendor Stand
                                    @else
                                        {{ Auth::user()->kelas ?? 'Siswa' }}
                                    @endif
                                </span>
                            </div>
                            <!-- Profile Avatar -->
                            <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-gradient-to-br from-orange-500 to-amber-500 flex items-center justify-center text-white font-black text-xs sm:text-sm shrink-0 shadow-sm border-2 border-white ring-2 ring-orange-500/20 group-hover:ring-orange-500/40 transition-all">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                        </button>

                        <!-- Dropdown Popup Card (Kembali ke layout lengkap: Nama, Kelas, Email, Logout — tanpa menu Riwayat) -->
                        <div id="user-profile-dropdown"
                            class="hidden absolute right-0 top-full mt-2 w-64 sm:w-72 bg-white rounded-2xl shadow-xl shadow-slate-900/10 border border-slate-200/90 p-3.5 z-50 transition-all origin-top-right">
                            
                            <!-- Header: Profil Avatar, Nama, dan Kelas / Role -->
                            <div class="flex items-center gap-3 p-1">
                                <div class="shrink-0 aspect-square w-11 h-11 rounded-full bg-gradient-to-tr from-orange-500 to-amber-500 text-white font-black text-base flex items-center justify-center">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="font-extrabold text-sm text-slate-800 leading-tight truncate">
                                        {{ Auth::user()->name }}
                                    </div>
                                    @if(Auth::user()->isSiswa() && Auth::user()->kelas)
                                    <div class="mt-1 inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-orange-50 text-orange-700 text-[11px] font-bold border border-orange-200/60">
                                        <svg class="w-3 h-3 text-orange-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
                                        </svg>
                                        <span>Kelas {{ Auth::user()->kelas }}</span>
                                    </div>
                                    @elseif(Auth::user()->isAdmin())
                                    <div class="mt-1 inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-purple-50 text-purple-700 text-[11px] font-bold border border-purple-200/60">
                                        <span>Administrator</span>
                                    </div>
                                    @elseif(Auth::user()->isPenjual())
                                    <div class="mt-1 inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-amber-50 text-amber-700 text-[11px] font-bold border border-amber-200/60">
                                        <span>{{ Auth::user()->stand->nama_stand ?? 'Penjual Stand' }}</span>
                                    </div>
                                    @else
                                    <div class="mt-1 inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 text-[11px] font-bold">
                                        <span>Siswa</span>
                                    </div>
                                    @endif
                                    <span class="text-[11px] text-slate-400 mt-0.5 block truncate">
                                        {{ Auth::user()->email }}
                                    </span>
                                </div>
                            </div>

                            @if(Auth::user()->isAdmin())
                            <div class="mt-3 pt-2.5 border-t border-slate-100">
                                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 px-2.5 py-2 rounded-xl text-xs font-bold text-orange-700 hover:bg-orange-50 transition-colors">
                                    <div class="w-6 h-6 rounded-lg bg-orange-100 text-orange-600 flex items-center justify-center shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" />
                                        </svg>
                                    </div>
                                    <span>Panel Admin</span>
                                </a>
                            </div>
                            @elseif(Auth::user()->isPenjual())
                            <div class="mt-3 pt-2.5 border-t border-slate-100">
                                <a href="{{ route('vendor.dashboard') }}" class="flex items-center gap-2.5 px-2.5 py-2 rounded-xl text-xs font-bold text-orange-700 hover:bg-orange-50 transition-colors">
                                    <div class="w-6 h-6 rounded-lg bg-orange-100 text-orange-600 flex items-center justify-center shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016 2.993 2.993 0 0 0 2.25-1.016 3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
                                        </svg>
                                    </div>
                                    <span>Dashboard Stand</span>
                                </a>
                            </div>
                            @endif

                            <!-- Tombol Logout -->
                            <div class="mt-3 pt-2.5 border-t border-slate-100">
                                <form action="{{ route('logout') }}" method="POST" onsubmit="clearAuthStorageOnLogout()">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-2.5 px-2.5 py-2 rounded-xl text-xs sm:text-sm font-bold text-rose-600 hover:bg-rose-50 transition-colors cursor-pointer group">
                                        <div class="w-6 h-6 rounded-lg bg-rose-50 group-hover:bg-rose-100 text-rose-600 flex items-center justify-center transition-colors shrink-0">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                                            </svg>
                                        </div>
                                        <span>Keluar Akun</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @else
                    <!-- Tombol Masuk / Login (Corporate & Modern Clean Pill) -->
                    <a href="{{ route('login') }}" class="group inline-flex items-center gap-2 pl-1.5 pr-3.5 py-1 sm:pl-2 sm:pr-4 sm:py-1.5 rounded-full text-xs sm:text-sm font-bold text-slate-700 hover:text-orange-600 bg-white hover:bg-orange-50/60 border border-slate-200/90 hover:border-orange-300 shadow-xs hover:shadow-md hover:shadow-orange-500/10 hover:-translate-y-0.5 active:translate-y-0 active:scale-95 transition-all duration-200 shrink-0">
                        <span class="w-6 h-6 sm:w-7 sm:h-7 rounded-full bg-orange-100/80 group-hover:bg-gradient-to-tr group-hover:from-orange-500 group-hover:to-amber-500 text-orange-600 group-hover:text-white border border-orange-200/60 group-hover:border-transparent flex items-center justify-center transition-all duration-200 shrink-0 shadow-2xs">
                            <svg class="w-3.5 h-3.5 transition-transform duration-200 group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke-width="2.3" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15M12 9l3 3m0 0-3 3m3-3H2.25" />
                            </svg>
                        </span>
                        <span>Masuk</span>
                    </a>
                    @endauth

                    <!-- Mobile Menu Toggle Button (Animated 3-bar hamburger) -->
                    <button type="button" id="mobile-menu-btn" onclick="toggleMobileNav()"
                        class="hamburger-btn md:hidden w-9 h-9 flex flex-col items-center justify-center gap-[5px] text-slate-600 hover:text-orange-600 hover:bg-orange-50/80 active:scale-95 rounded-xl border border-slate-200/90 transition-all shrink-0 cursor-pointer"
                        aria-label="Buka Menu">
                        <span class="hamburger-bar bar-top"></span>
                        <span class="hamburger-bar bar-mid" style="width:12px;"></span>
                        <span class="hamburger-bar bar-bot"></span>
                    </button>
                </div>

            </div>
        </div>

        <!-- Mobile Navigation Dropdown (Absolute overlay - menutupi hero section tanpa mendorong ke bawah) -->
        <div id="mobile-nav-menu" class="md:hidden absolute top-full left-0 right-0 w-full bg-white border-b border-slate-200 shadow-2xl z-50">
            <div class="max-w-7xl mx-auto px-3.5 sm:px-6 lg:px-8 py-3.5 space-y-1.5">
                @auth
                <!-- User Profile Header Card -->
                <div class="px-3.5 py-3 mb-2.5 rounded-2xl bg-gradient-to-r from-orange-50/80 via-amber-50/40 to-slate-50 border border-orange-200/70 flex items-center justify-between">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-orange-500 to-amber-500 flex items-center justify-center text-white font-black text-xs shadow-xs shrink-0">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <span class="text-xs font-black text-slate-900 block truncate leading-tight">{{ Auth::user()->name }}</span>
                            <span class="text-[10px] font-bold text-orange-600 block mt-0.5 uppercase tracking-wide">
                                @if(Auth::user()->isAdmin())
                                Super Administrator
                                @elseif(Auth::user()->isPenjual())
                                Stand Penjual
                                @elseif(Auth::user()->kelas)
                                Kelas {{ Auth::user()->kelas }}
                                @else
                                Siswa
                                @endif
                            </span>
                        </div>
                    </div>

                    @if(Auth::user()->isPenjual())
                    <a href="{{ route('vendor.dashboard') }}" class="px-2.5 py-1.5 rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-bold text-[10px] shadow-xs active:scale-95 transition-all flex items-center gap-1 shrink-0">
                        <span>Panel</span>
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>
                    </a>
                    @elseif(Auth::user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="px-2.5 py-1.5 rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-bold text-[10px] shadow-xs active:scale-95 transition-all flex items-center gap-1 shrink-0">
                        <span>Panel</span>
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>
                    </a>
                    @endif
                </div>
                @endauth

                <!-- Nav Links with Sleek SVG Heroicons (Icon box dipertahankan, panah chevron > dihapus) -->
                <a href="{{ route('katalog.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold text-slate-700 hover:text-orange-600 hover:bg-orange-50/70 transition-colors group">
                    <div class="w-8 h-8 rounded-lg bg-slate-100 group-hover:bg-orange-100 group-hover:text-orange-600 text-slate-500 flex items-center justify-center transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                        </svg>
                    </div>
                    <span>Beranda</span>
                </a>

                <a href="{{ route('katalog.index') }}#katalog-section" onclick="toggleMobileNav()" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold text-slate-700 hover:text-orange-600 hover:bg-orange-50/70 transition-colors group">
                    <div class="w-8 h-8 rounded-lg bg-slate-100 group-hover:bg-orange-100 group-hover:text-orange-600 text-slate-500 flex items-center justify-center transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 0 1 0 3.75H5.625a1.875 1.875 0 0 1 0-3.75Z" />
                        </svg>
                    </div>
                    <span>Daftar Menu</span>
                </a>

                <a href="{{ route('order.history') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold text-slate-700 hover:text-orange-600 hover:bg-orange-50/70 transition-colors group">
                    <div class="w-8 h-8 rounded-lg bg-slate-100 group-hover:bg-orange-100 group-hover:text-orange-600 text-slate-500 flex items-center justify-center transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    </div>
                    <span>Riwayat Pesanan</span>
                </a>

                @auth
                @if(Auth::user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold text-orange-700 bg-orange-50/80 border border-orange-200/70 hover:bg-orange-100 transition-colors group">
                    <div class="w-8 h-8 rounded-lg bg-orange-500 text-white flex items-center justify-center shadow-xs">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" />
                        </svg>
                    </div>
                    <span>Panel Admin</span>
                </a>
                @elseif(Auth::user()->isPenjual())
                <a href="{{ route('vendor.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold text-orange-700 bg-orange-50/80 border border-orange-200/70 hover:bg-orange-100 transition-colors group">
                    <div class="w-8 h-8 rounded-lg bg-orange-500 text-white flex items-center justify-center shadow-xs">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016 2.993 2.993 0 0 0 2.25-1.016 3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
                        </svg>
                    </div>
                    <span>Dashboard Stand</span>
                </a>
                @endif

                <div class="pt-2 border-t border-slate-100">
                    <form action="{{ route('logout') }}" method="POST" onsubmit="clearAuthStorageOnLogout()">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-bold text-rose-600 hover:bg-rose-50 transition-colors group cursor-pointer">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 group-hover:bg-rose-100 flex items-center justify-center transition-colors">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                                    </svg>
                                </div>
                                <span>Keluar Akun</span>
                            </div>
                            <span class="text-[10px] text-rose-400 font-semibold">Logout</span>
                        </button>
                    </form>
                </div>
                @else
                <div class="pt-2 border-t border-slate-100">
                    <a href="{{ route('login') }}" class="w-full py-2.5 px-4 rounded-xl bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white font-extrabold text-xs shadow-md shadow-orange-500/20 transition-all flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15M12 9l3 3m0 0-3 3m3-3H2.25" />
                        </svg>
                        <span>Masuk ke Akun</span>
                    </a>
                </div>
                @endauth
            </div>
        </div>
    </header>

    <!-- Global Flash Messages (session error only; validation errors are shown inside each form) -->
    @if(session('error'))
    <div id="app-flash-error" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4 w-full transition-all duration-300">
        <div class="mb-4 rounded-2xl bg-rose-50 p-4 border border-rose-200 flex items-center justify-between gap-3 shadow-xs">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-rose-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                </svg>
                <div class="text-sm font-semibold text-rose-800">{{ session('error') }}</div>
            </div>
            <button type="button" onclick="document.getElementById('app-flash-error').style.display='none'" class="text-rose-400 hover:text-rose-600 font-bold text-base cursor-pointer">&times;</button>
        </div>
    </div>
    <script>
        setTimeout(function() {
            var el = document.getElementById('app-flash-error');
            if (el) {
                el.style.opacity = '0';
                el.style.transform = 'translateY(-8px)';
                setTimeout(function() { el.style.display = 'none'; }, 300);
            }
        }, 3500);
    </script>
    @endif

    <!-- Main Content Area (Full width for hero section edge-to-edge) -->
    <main class="flex-1 w-full overflow-x-hidden">
        @yield('content')
    </main>

    <!-- Floating Bottom-Right Circle Cart Button (Akses Cepat Keranjang & Checkout) -->
    @if(!request()->routeIs('checkout.show'))
    <div id="floating-cart-wrapper" class="fixed bottom-4 right-4 sm:bottom-6 sm:right-6 z-40 print:hidden">
        <button type="button" id="floating-cart-btn" onclick="triggerOpenCart()"
            class="group relative flex items-center justify-center w-11 h-11 sm:w-16 sm:h-16 rounded-full bg-gradient-to-tr from-orange-500 via-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white shadow-lg shadow-orange-500/35 hover:shadow-2xl hover:shadow-orange-500/50 hover:scale-105 active:scale-95 transition-all duration-200 cursor-pointer ring-3 sm:ring-4 ring-white/90 select-none">

            <!-- Cart Icon in Center -->
            <svg class="w-5 h-5 sm:w-7 sm:h-7 transition-transform duration-200 group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
            </svg>

            <!-- Pill Badge Counter on Top Right of Circle -->
            <span id="floating-cart-badge"
                class="absolute -top-1 -right-1 sm:-top-1.5 sm:-right-1.5 min-w-[18px] h-[18px] sm:min-w-[22px] sm:h-[22px] px-1 sm:px-1.5 bg-orange-500 text-white text-[10px] sm:text-[11px] font-black rounded-full items-center justify-center shadow-md ring-2 ring-white transition-transform duration-200"
                style="display: none;">
                0
            </span>
        </button>
    </div>
    @endif



    <!-- Footer -->
    <footer class="mt-auto border-t border-slate-200 bg-white py-6 sm:py-7 text-xs text-slate-500">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4 text-center md:text-left">
                <!-- Brand & Logo -->
                <div class="flex items-center gap-2.5">
                    <img src="{{ asset('img/kanic-logo.png') }}?v=2" alt="Logo KantinSkanic" class="w-8 h-8 rounded-xl object-cover shadow-2xs">
                    <span class="font-extrabold text-slate-800 text-sm tracking-tight">Kantin<span class="text-orange-600">Skanic</span></span>
                    <span class="text-slate-400">&bull;</span>
                    <span class="text-slate-500 font-medium">Pre-Order Kantin Sekolah</span>
                </div>

                <!-- Tagline -->
                <p class="hidden md:block text-slate-500 font-medium">
                    Pesan makanan favoritmu di Kantin Skanic. Cepat, praktis, & efisien.
                </p>

                <!-- Copyright -->
                <div class="text-slate-400 text-[11px] font-medium">
                    &copy; {{ date('Y') }} SMK Negeri 1 Ciomas
                </div>
            </div>
        </div>
    </footer>

    <!-- Global Layout Scripts -->
    <script>
        function toggleMobileNav() {
            const menu = document.getElementById('mobile-nav-menu');
            const btn = document.getElementById('mobile-menu-btn');
            const navbar = document.getElementById('main-navbar');
            if (!menu) return;

            const isOpen = menu.classList.contains('mobile-nav-open');
            if (isOpen) {
                menu.classList.remove('mobile-nav-open');
                if (btn) btn.classList.remove('is-open');
                @if(request()->routeIs('katalog.index'))
                if (navbar) {
                    navbar.classList.remove('bg-white', 'shadow-xs', 'border-slate-200/80');
                    navbar.classList.add('bg-transparent', 'border-transparent');
                }
                @endif
            } else {
                menu.classList.add('mobile-nav-open');
                if (btn) btn.classList.add('is-open');
                @if(request()->routeIs('katalog.index'))
                if (navbar) {
                    navbar.classList.add('bg-white', 'shadow-xs', 'border-slate-200/80');
                    navbar.classList.remove('bg-transparent', 'border-transparent');
                }
                @endif
            }
        }


        function toggleUserProfileDropdown(event) {
            if (event) {
                event.stopPropagation();
            }
            const dropdown = document.getElementById('user-profile-dropdown');
            const btn = document.getElementById('user-profile-btn');
            if (!dropdown) return;

            const isHidden = dropdown.classList.contains('hidden');
            if (isHidden) {
                dropdown.classList.remove('hidden');
                if (btn) btn.setAttribute('aria-expanded', 'true');
            } else {
                dropdown.classList.add('hidden');
                if (btn) btn.setAttribute('aria-expanded', 'false');
            }
        }

        // Tutup dropdown profile saat klik di luar container
        document.addEventListener('click', function(e) {
            const container = document.getElementById('user-profile-menu-container');
            const dropdown = document.getElementById('user-profile-dropdown');
            const btn = document.getElementById('user-profile-btn');
            if (container && dropdown && !container.contains(e.target)) {
                dropdown.classList.add('hidden');
                if (btn) btn.setAttribute('aria-expanded', 'false');
            }
        });

        // Tutup dropdown profile saat tombol Escape ditekan
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const dropdown = document.getElementById('user-profile-dropdown');
                const btn = document.getElementById('user-profile-btn');
                if (dropdown && !dropdown.classList.contains('hidden')) {
                    dropdown.classList.add('hidden');
                    if (btn) btn.setAttribute('aria-expanded', 'false');
                }
            }
        });

        function clearAuthStorageOnLogout() {
            try {
                // Saat logout akun, kosongkan guest cart dan riwayat sesi.
                // Keranjang akun user (kantinskanic_cart_user_<id>) TIDAK DIHAPUS agar saat login kembali
                // ke akun yang sama, isi keranjang pengguna tetap tersimpan utuh.
                localStorage.removeItem('kantinskanic_cart_guest');
                localStorage.removeItem('kantinskanic_order_history');
                sessionStorage.clear();
            } catch (e) {}
        }

        function getOrderHistoryStorage() {
            try {
                const raw = localStorage.getItem('kantinskanic_order_history');
                return raw ? JSON.parse(raw) : [];
            } catch (e) {
                return [];
            }
        }

        function saveOrderToHistoryStorage(codes) {
            try {
                const existing = getOrderHistoryStorage();
                const newCodes = Array.isArray(codes) ? codes : [codes];
                const cleanCodes = newCodes.map(c => String(c).trim().toUpperCase()).filter(Boolean);
                const merged = Array.from(new Set([...cleanCodes, ...existing])).filter(Boolean);
                localStorage.setItem('kantinskanic_order_history', JSON.stringify(merged));
            } catch (e) {
                console.error(e);
            }
        }

        function openOrderHistoryModal() {
            window.location.href = '{{ route("order.history") }}';
        }

        function closeOrderHistoryModal() {}

        function openTrackOrderModal() {
            window.location.href = '{{ route("order.history") }}';
        }

        function closeTrackOrderModal() {}

        window.AUTH_USER_ID = document.querySelector('meta[name="user-id"]')?.getAttribute('content') || null;

        function getCartStorageKey() {
            return window.AUTH_USER_ID ? ('kantinskanic_cart_user_' + window.AUTH_USER_ID) : 'kantinskanic_cart_guest';
        }

        function triggerOpenCart() {
            if (typeof openCartModal === 'function') {
                openCartModal();
            } else {
                window.location.href = '{{ route("katalog.index") }}?cart=open#katalog-section';
            }
        }

        function syncNavbarCartBadge() {
            try {
                // Auto-migrate legacy cart key if found
                const legacy = localStorage.getItem('kantinskanic_cart_v1');
                if (legacy) {
                    const activeKey = getCartStorageKey();
                    if (!localStorage.getItem(activeKey)) {
                        localStorage.setItem(activeKey, legacy);
                    }
                    localStorage.removeItem('kantinskanic_cart_v1');
                }

                // If user logged in and has guest items, merge them without duplicating quantities
                if (window.AUTH_USER_ID) {
                    const guestRaw = localStorage.getItem('kantinskanic_cart_guest');
                    if (guestRaw) {
                        try {
                            const guestItems = JSON.parse(guestRaw);
                            if (Array.isArray(guestItems) && guestItems.length > 0) {
                                const userKey = getCartStorageKey();
                                const userRaw = localStorage.getItem(userKey);
                                const userItems = userRaw ? JSON.parse(userRaw) : [];
                                guestItems.forEach(gItem => {
                                    const exist = userItems.find(u => u.menu_id === gItem.menu_id);
                                    if (exist) {
                                        // Jangan gandakan jika sudah ada di keranjang user
                                        exist.jumlah = Math.max(exist.jumlah, gItem.jumlah);
                                    } else {
                                        userItems.push(gItem);
                                    }
                                });
                                localStorage.setItem(userKey, JSON.stringify(userItems));
                            }
                        } catch (e) {}
                        localStorage.removeItem('kantinskanic_cart_guest');
                    }
                }

                if (!window.AUTH_USER_ID) {
                    try {
                        localStorage.removeItem('kantinskanic_order_history');
                    } catch (e) {}
                }

                const raw = localStorage.getItem(getCartStorageKey());
                const items = raw ? JSON.parse(raw) : [];
                const total = items.reduce((sum, i) => sum + (i.jumlah || 1), 0);
                const totalPrice = items.reduce((sum, i) => sum + ((i.harga || 0) * (i.jumlah || 1)), 0);

                const badge = document.getElementById('nav-cart-badge');
                if (badge) {
                    badge.innerText = total;
                    if (total > 0) {
                        badge.style.display = 'flex';
                    } else {
                        badge.style.display = 'none';
                    }
                }
                const mobileBadge = document.getElementById('mobile-nav-cart-badge');
                if (mobileBadge) {
                    mobileBadge.innerText = total;
                    if (total > 0) {
                        mobileBadge.style.display = 'inline-block';
                    } else {
                        mobileBadge.style.display = 'none';
                    }
                }

                // Sync Floating Bottom-Right Circle Cart Button
                const floatBadge = document.getElementById('floating-cart-badge');
                if (floatBadge) {
                    floatBadge.innerText = total;
                    if (total > 0) {
                        floatBadge.style.display = 'flex';
                    } else {
                        floatBadge.style.display = 'none';
                    }
                }
            } catch (e) {}
        }

        function handleFloatingCartScroll() {
            const wrapper = document.getElementById('floating-cart-wrapper');
            if (!wrapper) return;

            const hero = document.querySelector('.hero-fullscreen');
            let shouldShow = false;

            if (hero) {
                // Sembunyikan saat di hero section, muncul setelah scroll 280px
                shouldShow = window.scrollY > 280;
            } else {
                // Di halaman tanpa hero: tampil setelah scroll 150px atau jika halaman pendek
                shouldShow = window.scrollY > 150 || (document.documentElement.scrollHeight <= window.innerHeight + 100);
            }

            if (shouldShow) {
                wrapper.classList.add('floating-cart-visible');
            } else {
                wrapper.classList.remove('floating-cart-visible');
            }
        }

        window.addEventListener('scroll', handleFloatingCartScroll, {
            passive: true
        });
        window.addEventListener('resize', handleFloatingCartScroll, {
            passive: true
        });
        document.addEventListener('DOMContentLoaded', () => {
            syncNavbarCartBadge();
            handleFloatingCartScroll();
        });
        window.addEventListener('storage', syncNavbarCartBadge);
        handleFloatingCartScroll();
    </script>

    @stack('scripts')
</body>

</html>