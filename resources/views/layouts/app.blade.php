<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50 antialiased scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'KantinSkanic') - Pre-Order Kantin Sekolah</title>
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
    </style>
    @stack('styles')
</head>

<body class="flex flex-col min-h-full text-slate-800 bg-slate-50">
    <!-- Top Navigation Bar -->
    <header id="main-navbar" class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-slate-200/80 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 sm:h-20">

                <!-- Left: School & App Brand (Spacious & breathable) -->
                <a href="{{ route('katalog.index') }}" class="flex items-center gap-3.5 group py-2">
                    <img src="{{ asset('img/kanic-logo.png') }}?v=2" alt="KantinSkanic" class="w-10 h-10 rounded-2xl shadow-md shadow-orange-500/20 group-hover:scale-105 transition-transform shrink-0 object-cover">
                    <div class="flex flex-col justify-center">
                        <div class="flex items-center gap-2">
                            <span class="text-xl sm:text-lg font-black tracking-tight text-orange-600 leading-none">Kantin<span class="text-slate-900">Skanic</span></span>
                        </div>
                        <p class="text-xs text-slate-500 font-medium hidden sm:block mt-1 leading-tight">Sistem Pre-Order Makanan & Minuman</p>
                    </div>
                </a>

                <!-- Right Nav Items -->
                <div class="flex items-center gap-3">

                    <!-- Desktop Nav Links -->
                    <nav class="hidden md:flex items-center">
                        <a href="{{ route('katalog.index') }}" class="nav-link">Beranda</a>
                        <a href="{{ route('katalog.index') }}#katalog-section" class="nav-link">Daftar Menu</a>
                        <button type="button" onclick="openTrackOrderModal()" class="nav-link">Lacak Pesanan</button>
                    </nav>

                    <!-- Divider -->
                    <div class="hidden md:block w-px h-6 bg-slate-200 mx-1"></div>

                    <!-- Tombol Keranjang with Counter Badge -->
                    <button type="button" onclick="triggerOpenCart()"
                        class="relative inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-orange-50 hover:bg-orange-100 text-orange-700 font-bold text-sm border border-orange-200 transition-all shadow-xs group cursor-pointer"
                        title="Buka Keranjang Belanja">
                        <svg class="w-4.5 h-4.5 text-orange-600 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                        </svg>
                        <span class="hidden sm:inline">Keranjang</span>
                        <span id="nav-cart-badge" class="bg-orange-600 text-white text-[11px] font-black px-2 py-0.5 rounded-full min-w-[20px] text-center">0</span>
                    </button>

                    @auth
                    <!-- Authenticated User Menu -->
                    <div class="flex items-center gap-2">
                        @if(Auth::user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 sm:px-3.5 sm:py-2 rounded-xl text-xs sm:text-sm font-bold text-orange-600 bg-orange-50 hover:bg-orange-100 transition-colors border border-orange-200">
                            <svg class="w-4 h-4 text-orange-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" />
                            </svg>
                            <span>Panel Admin</span>
                        </a>
                        @elseif(Auth::user()->isPenjual())
                        <a href="{{ route('vendor.dashboard') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 sm:px-3.5 sm:py-2 rounded-xl text-xs sm:text-sm font-bold text-orange-600 bg-orange-50 hover:bg-orange-100 transition-colors border border-orange-200">
                            <svg class="w-4 h-4 text-orange-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016 2.993 2.993 0 0 0 2.25-1.016 3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
                            </svg>
                            <span>Dashboard Stand</span>
                        </a>
                        @else
                        <span class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-slate-700 bg-slate-100">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span>{{ explode(' ', Auth::user()->name)[0] }}</span>
                        </span>
                        @endif

                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 sm:px-3 sm:py-2 text-xs font-bold text-rose-600 hover:text-rose-700 bg-rose-50 hover:bg-rose-100 rounded-xl transition-colors border border-rose-100 cursor-pointer" title="Keluar">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                                </svg>
                                <span class="hidden sm:inline">Keluar</span>
                            </button>
                        </form>
                    </div>
                    @else
                    <!-- Tombol Masuk / Login -->
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs sm:text-sm font-bold text-slate-700 hover:text-orange-600 hover:bg-slate-100 border border-slate-200 transition-all">
                        <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>
                        <span>Masuk</span>
                    </a>
                    @endauth

                    <!-- Mobile Menu Toggle Button -->
                    <button type="button" onclick="toggleMobileNav()" class="md:hidden p-2 text-slate-600 hover:text-slate-900 rounded-xl hover:bg-slate-100 transition-colors" aria-label="Buka Menu">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>
                </div>

            </div>

            <!-- Mobile Navigation Dropdown -->
            <div id="mobile-nav-menu" class="hidden md:hidden border-t border-slate-100 py-3 space-y-1">
                <a href="{{ route('katalog.index') }}" class="block px-3 py-2 rounded-lg text-sm font-bold text-slate-700 hover:bg-orange-50 hover:text-orange-600 transition-colors">
                    🏠 Beranda
                </a>
                <a href="{{ route('katalog.index') }}#katalog-section" onclick="toggleMobileNav()" class="block px-3 py-2 rounded-lg text-sm font-bold text-slate-700 hover:bg-orange-50 hover:text-orange-600 transition-colors">
                    🍱 Daftar Menu
                </a>
                <button type="button" onclick="toggleMobileNav(); openTrackOrderModal()" class="w-full text-left px-3 py-2 rounded-lg text-sm font-bold text-slate-700 hover:bg-orange-50 hover:text-orange-600 transition-colors flex items-center justify-between">
                    <span>🔍 Lacak Pesanan</span>
                    <span class="text-[10px] text-orange-600 font-extrabold bg-orange-100 px-2 py-0.5 rounded-full">Cek Kode</span>
                </button>
                @auth
                @if(Auth::user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded-lg text-sm font-bold text-orange-600 bg-orange-50">
                    ⚡ Panel Admin
                </a>
                @elseif(Auth::user()->isPenjual())
                <a href="{{ route('vendor.dashboard') }}" class="block px-3 py-2 rounded-lg text-sm font-bold text-orange-600 bg-orange-50">
                    🏪 Dashboard Stand
                </a>
                @endif
                @else
                <a href="{{ route('login') }}" class="block px-3 py-2 rounded-lg text-sm font-bold text-slate-700 hover:bg-slate-100 transition-colors">
                    🔐 Masuk ke Akun
                </a>
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
    <main class="flex-1 w-full">
        @yield('content')
    </main>

    <!-- Global Modal Lacak Pesanan -->
    <div id="modal-track-order" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-track-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" onclick="closeTrackOrderModal()"></div>
        <div class="flex min-h-full items-center justify-center p-4 text-center">
            <div class="relative transform overflow-hidden rounded-3xl bg-white p-6 sm:p-8 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-slate-100">
                <!-- Close Button -->
                <button type="button" onclick="closeTrackOrderModal()" class="absolute right-4 top-4 text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-100 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>

                <div class="text-center mb-6">
                    <div class="w-14 h-14 mx-auto rounded-2xl bg-gradient-to-tr from-amber-500 to-orange-500 flex items-center justify-center text-white shadow-lg shadow-orange-500/25 mb-3">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                    </div>
                    <h3 id="modal-track-title" class="text-lg sm:text-xl font-extrabold text-slate-900">Lacak Status Pesanan</h3>
                    <p class="text-xs text-slate-500 mt-1">Masukkan kode unik transaksi yang Anda terima saat checkout.</p>
                </div>

                <form onsubmit="submitTrackOrderModal(event)" class="space-y-4">
                    <div>
                        <label for="track-order-input" class="block text-xs font-bold text-slate-700 mb-1.5">Kode Pesanan (PO-XXXX)</label>
                        <div class="relative">
                            <input type="text" id="track-order-input" required placeholder="Contoh: PO-8921 atau 8921"
                                class="w-full uppercase font-mono tracking-wider pl-4 pr-10 py-3 text-sm font-bold bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all">
                            <div class="absolute right-3 top-3 text-slate-400">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    @php $modalRecentOrders = session('recent_orders', []); @endphp
                    @if(!empty($modalRecentOrders))
                    <div class="pt-1">
                        <span class="text-[11px] font-bold text-slate-400 block mb-1.5">Pesanan Terakhir Anda:</span>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($modalRecentOrders as $rc)
                            <a href="{{ route('order.status', $rc) }}" class="px-2.5 py-1 text-xs font-bold text-orange-700 bg-orange-50 hover:bg-orange-100 border border-orange-200 rounded-lg transition-colors font-mono">
                                #{{ $rc }} &rarr;
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <button type="submit" class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 active:scale-98 text-white font-extrabold text-sm shadow-lg shadow-orange-500/25 transition-all flex items-center justify-center gap-2">
                        <span>Cek Status Sekarang</span>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </div>

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
            if (menu) menu.classList.toggle('hidden');
        }

        function openTrackOrderModal() {
            const modal = document.getElementById('modal-track-order');
            if (modal) {
                modal.classList.remove('hidden');
                setTimeout(() => {
                    const input = document.getElementById('track-order-input');
                    if (input) input.focus();
                }, 50);
            }
        }

        function closeTrackOrderModal() {
            const modal = document.getElementById('modal-track-order');
            if (modal) modal.classList.add('hidden');
        }

        function submitTrackOrderModal(e) {
            e.preventDefault();
            const raw = (document.getElementById('track-order-input').value || '').trim().toUpperCase();
            if (!raw) return;
            const code = raw.startsWith('PO-') ? raw : 'PO-' + raw.replace(/^PO-?/i, '');
            window.location.href = '/order/' + code;
        }

        function triggerOpenCart() {
            if (typeof openCartModal === 'function') {
                openCartModal();
            } else {
                window.location.href = '{{ route("katalog.index") }}#katalog-section';
            }
        }

        function syncNavbarCartBadge() {
            try {
                const raw = localStorage.getItem('kantinskanic_cart_v1');
                const items = raw ? JSON.parse(raw) : [];
                const total = items.reduce((sum, i) => sum + (i.jumlah || 1), 0);
                const badge = document.getElementById('nav-cart-badge');
                if (badge) {
                    badge.innerText = total;
                    if (total > 0) {
                        badge.classList.remove('bg-slate-400');
                        badge.classList.add('bg-orange-600');
                    }
                }
            } catch (e) {}
        }

        document.addEventListener('DOMContentLoaded', syncNavbarCartBadge);
        window.addEventListener('storage', syncNavbarCartBadge);
    </script>

    @stack('scripts')
</body>

</html>