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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Midtrans Snap Preconnect & Script -->
    <link rel="preconnect" href="https://app.sandbox.midtrans.com">
    <link rel="dns-prefetch" href="https://app.sandbox.midtrans.com">
    <link rel="preconnect" href="https://api.sandbox.midtrans.com">
    <link rel="dns-prefetch" href="https://api.sandbox.midtrans.com">
    <script src="{{ config('midtrans.snap_js_url') }}" data-client-key="{{ config('midtrans.client_key') }}" async></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
        }

        [x-cloak] {
            display: none !important;
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

        /* Hero section: fills viewport below navbar with flexible expansion */
        .hero-fullscreen {
            min-height: calc(100vh - 4rem);
        }

        @media (min-width: 640px) {
            .hero-fullscreen {
                min-height: calc(100vh - 5rem);
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
    <header id="main-navbar" class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-slate-200/80 shadow-xs w-full">
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
                        <a href="{{ route('katalog.index') }}" class="nav-link">Beranda</a>
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
                    <!-- Authenticated User Menu -->
                    <div class="flex items-center gap-1.5 sm:gap-2 shrink-0">
                        @if(Auth::user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-1.5 px-2.5 py-1.5 sm:px-3.5 sm:py-2 rounded-xl text-xs sm:text-sm font-bold text-orange-600 bg-orange-50 hover:bg-orange-100 transition-colors border border-orange-200" title="Panel Admin">
                            <svg class="w-4 h-4 text-orange-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" />
                            </svg>
                            <span class="hidden sm:inline">Panel Admin</span>
                        </a>
                        @elseif(Auth::user()->isPenjual())
                        <a href="{{ route('vendor.dashboard') }}" class="inline-flex items-center gap-1.5 px-2.5 py-1.5 sm:px-3.5 sm:py-2 rounded-xl text-xs sm:text-sm font-bold text-orange-600 bg-orange-50 hover:bg-orange-100 transition-colors border border-orange-200" title="Dashboard Stand">
                            <svg class="w-4 h-4 text-orange-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016 2.993 2.993 0 0 0 2.25-1.016 3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
                            </svg>
                            <span class="hidden sm:inline">Dashboard Stand</span>
                        </a>
                        @else
                        <span class="hidden sm:inline-flex items-center gap-2 px-2.5 py-1.5 rounded-xl text-xs font-bold text-slate-700 bg-slate-50 border border-slate-200 hover:bg-slate-100 transition-colors">
                            {{-- Avatar circle dengan inisial --}}
                            <span class="relative shrink-0">
                                <span style="width:28px;height:28px;border-radius:50%;background:linear-gradient(135deg,#f97316,#f59e0b);display:flex;align-items:center;justify-content:center;color:#fff;font-size:11px;font-weight:800;letter-spacing:-0.5px;">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </span>
                                {{-- Status dot online --}}
                                <span style="position:absolute;bottom:0;right:0;width:8px;height:8px;background:#22c55e;border-radius:50%;border:1.5px solid #fff;"></span>
                            </span>
                            <span>{{ explode(' ', Auth::user()->name)[0] }}</span>
                        </span>
                        @endif

                        <form action="{{ route('logout') }}" method="POST" class="hidden sm:inline" onsubmit="clearAuthStorageOnLogout()">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 sm:px-3 sm:py-2 text-xs font-bold text-rose-600 hover:text-rose-700 bg-rose-50 hover:bg-rose-100 rounded-xl transition-colors border border-rose-100 cursor-pointer" title="Keluar">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                                </svg>
                                <span>Keluar</span>
                            </button>
                        </form>
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
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4 w-full">
        <div class="mb-4 rounded-2xl bg-rose-50 p-4 border border-rose-200 flex items-start gap-3 shadow-xs">
            <svg class="w-5 h-5 text-rose-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
            </svg>
            <div class="text-sm font-semibold text-rose-800">{{ session('error') }}</div>
        </div>
    </div>
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
                <p class="text-slate-500 font-medium">
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
            if (!menu) return;

            const isOpen = menu.classList.contains('mobile-nav-open');
            if (isOpen) {
                menu.classList.remove('mobile-nav-open');
                if (btn) btn.classList.remove('is-open');
            } else {
                menu.classList.add('mobile-nav-open');
                if (btn) btn.classList.add('is-open');
            }
        }

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