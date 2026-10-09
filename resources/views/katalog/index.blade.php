@extends('layouts.app')

@section('title', 'Daftar Menu & Pesan Makanan')

@section('content')
<div class="relative pb-24 w-full overflow-x-hidden">

    <!-- ===================================================
         1. HERO SECTION (Full-Bleed Edge-to-Edge matching reference)
    =================================================== -->
    <section class="hero-fullscreen relative w-full overflow-hidden bg-gradient-to-br from-orange-600 via-orange-500 to-amber-400 text-white flex items-center">

        <!-- Giant Geometric Circles on Right (proportional to reference image) -->
        <div class="absolute -right-16 sm:-right-8 lg:right-10 top-1/2 -translate-y-1/2 w-[340px] h-[340px] sm:w-[460px] sm:h-[460px] lg:w-[580px] lg:h-[580px] rounded-full bg-white/[0.08] pointer-events-none"></div>
        <div class="absolute -right-32 sm:-right-24 lg:-right-6 top-1/2 -translate-y-1/2 w-[480px] h-[480px] sm:w-[620px] sm:h-[620px] lg:w-[780px] lg:h-[780px] rounded-full border border-white/[0.08] pointer-events-none"></div>

        <!-- Content Container -->
        <div class="relative z-10 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10 lg:py-12">
            <div class="max-w-2xl lg:max-w-3xl">

                <!-- Parallelogram (Jajar Genjang) Badge: PRE-ORDER KANTIN SKANIC -->
                <div class="inline-flex items-center px-3.5 py-1 sm:px-4.5 sm:py-1.5 rounded-xs bg-white/15 border border-white/25 text-white text-[11px] sm:text-[13px] font-semibold mb-3 sm:mb-4 backdrop-blur-xs tracking-wider uppercase shadow-2xs -skew-x-12 origin-center" style="transform: skewX(-12deg);">
                    <span class="skew-x-12 truncate block" style="transform: skewX(12deg);">Pre-Order Kantin Skanic</span>
                </div>

                <!-- Balanced Large Heading (matching reference image) -->
                <h1 class="text-2xl sm:text-4xl lg:text-5xl xl:text-[54px] font-black text-white leading-tight sm:leading-[1.15] tracking-tight break-words">
                    Pesan Makanan <span class="text-amber-200">Tanpa Antre</span><br>
                    Kantin Skanic<br>
                    SMKN 1 Ciomas
                </h1>

                <!-- Subtitle paragraph -->
                <p class="mt-3 sm:mt-4 text-xs sm:text-base lg:text-base xl:text-lg text-white/90 max-w-2xl leading-relaxed font-medium">
                    Wadah digital resmi untuk memesan seluruh menu makanan dan minuman siswa di Kantin SMK Negeri 1 Ciomas. Pilih menu stand favoritmu dan langsung ambil pesananmu tanpa perlu berdesakan.
                </p>

                <!-- Dual CTA Buttons (Solid with arrow + Ghost outline) -->
                <div class="mt-5 sm:mt-6 flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 sm:gap-3.5 w-full sm:w-auto">
                    <!-- CTA 1: Solid button with arrow -->
                    <a href="#katalog-section"
                        class="group w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-3 sm:px-6 sm:py-3.5 rounded-2xl bg-white text-orange-600 hover:bg-orange-50 font-black text-xs sm:text-base shadow-lg hover:shadow-xl active:scale-95 transition-all text-center">
                        <span>Pesan Sekarang</span>
                        <svg class="w-4 h-4 text-orange-600 shrink-0 transform group-hover:translate-x-1.5 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>
                    </a>

                    <!-- CTA 2: Ghost outline button -->
                    <a href="{{ route('order.history') }}"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-3 sm:px-6 sm:py-3.5 rounded-2xl border-2 border-white text-white hover:bg-white/15 font-black text-xs sm:text-base backdrop-blur-xs active:scale-95 transition-all text-center"> 
                        <span>Riwayat Pesanan</span>
                    </a>
                </div>

            </div>
        </div>
    </section>

    <!-- Content Wrapper for Catalog and Below -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-10 sm:pt-14">

        <!-- ===================================================
             2. FILTER & SEARCH SECTION
        =================================================== -->
        <div id="katalog-section" class="scroll-mt-28 space-y-5 mb-8">

            <!-- Section Header Title -->
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-2 border-b border-slate-200/80 pb-3">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-orange-600 block">Pilihan Menu Siswa</span>
                    <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Daftar Makanan & Minuman</h2>
                </div>
                <span class="text-xs font-medium text-slate-500">
                    Menampilkan <strong class="text-slate-800">{{ $menus->count() }}</strong> menu siap dipesan
                </span>
            </div>

            <!-- Stand Filter Tabs (Horizontal Scroll) -->
            <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none">
                <a href="{{ route('katalog.index', array_merge(request()->query(), ['stand' => 'all'])) }}#katalog-section"
                    class="shrink-0 px-4 py-2.5 rounded-2xl text-xs sm:text-sm font-bold transition-all duration-200 flex items-center gap-1.5 {{ $activeStand === 'all' ? 'bg-orange-500 text-white shadow-md shadow-orange-500/25 scale-[1.02]' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200/90 shadow-xs' }}">
                    <span>Semua Stand</span>
                    <span class="ml-1 px-1.5 py-0.5 rounded-full text-[10px] {{ $activeStand === 'all' ? 'bg-white/25 text-white' : 'bg-slate-100 text-slate-600' }}">
                        {{ $stands->sum('menus_count') }}
                    </span>
                </a>
                @foreach($stands as $stand)
                <a href="{{ route('katalog.index', array_merge(request()->query(), ['stand' => $stand->id])) }}#katalog-section"
                    class="shrink-0 px-4 py-2.5 rounded-2xl text-xs sm:text-sm font-bold transition-all duration-200 flex items-center gap-1.5 {{ (string)$activeStand === (string)$stand->id ? 'bg-orange-500 text-white shadow-md shadow-orange-500/25 scale-[1.02]' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200/90 shadow-xs' }}">
                    <span>{{ $stand->nama_stand }}</span>
                    <span class="ml-1 px-1.5 py-0.5 rounded-full text-[10px] {{ (string)$activeStand === (string)$stand->id ? 'bg-white/25 text-white' : 'bg-slate-100 text-slate-600' }}">
                        {{ $stand->menus_count }}
                    </span>
                </a>
                @endforeach
            </div>

            <!-- Search Bar & Category Pills Row -->
            <div class="flex flex-col md:flex-row gap-3 md:items-center md:justify-between bg-white p-3 rounded-2xl border border-slate-200/80 shadow-xs">

                <!-- Category Pills Tabs -->
                <div class="flex items-center gap-1.5 overflow-x-auto pb-1 md:pb-0 scrollbar-none">
                    <a href="{{ route('katalog.index', array_merge(request()->query(), ['kategori' => 'all'])) }}#katalog-section"
                        class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all {{ $activeKategori === 'all' ? 'bg-slate-900 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Semua Kategori
                    </a>
                    <a href="{{ route('katalog.index', array_merge(request()->query(), ['kategori' => 'makanan'])) }}#katalog-section"
                        class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all {{ $activeKategori === 'makanan' ? 'bg-amber-600 text-white shadow-xs' : 'bg-amber-50 text-amber-800 hover:bg-amber-100 border border-amber-200/60' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="inline-block shrink-0 align-middle -mt-0.5">
                            <path d="m16 2-2.3 2.3a3 3 0 0 0 0 4.2l1.8 1.8a3 3 0 0 0 4.2 0L22 8" />
                            <path d="M15 15 3.3 3.3a4.2 4.2 0 0 0 0 6l7.3 7.3c.7.7 2 .7 2.8 0L15 15Zm0 0 7 7" />
                            <path d="m2.1 21.8 6.4-6.3" />
                            <path d="m19 5-7 7" />
                        </svg> Makanan
                    </a>
                    <a href="{{ route('katalog.index', array_merge(request()->query(), ['kategori' => 'minuman'])) }}#katalog-section"
                        class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all {{ $activeKategori === 'minuman' ? 'bg-sky-600 text-white shadow-xs' : 'bg-sky-50 text-sky-800 hover:bg-sky-100 border border-sky-200/60' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="inline-block shrink-0 align-middle -mt-0.5">
                            <path d="M15.2 22H8.8a2 2 0 0 1-2-1.79L5 3h14l-1.81 17.21A2 2 0 0 1 15.2 22Z" />
                            <path d="M6 12a5 5 0 0 1 6 0 5 5 0 0 0 6 0" />
                        </svg> Minuman
                    </a>
                    <a href="{{ route('katalog.index', array_merge(request()->query(), ['kategori' => 'snack'])) }}#katalog-section"
                        class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all {{ $activeKategori === 'snack' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-emerald-50 text-emerald-800 hover:bg-emerald-100 border border-emerald-200/60' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="inline-block shrink-0 align-middle -mt-0.5">
                            <path d="M12 2a5 5 0 1 1 5 5H7a5 5 0 0 1 0-10 5 5 0 0 1 5 5" />
                            <path d="M12 7v5" />
                            <path d="M8 12H5a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-5a2 2 0 0 0-2-2h-3" />
                        </svg> Snack
                    </a>
                </div>

                <!-- Search Bar (Real-time Filter & Form Submit) -->
                <form action="{{ route('katalog.index') }}#katalog-section" method="GET" class="relative w-full md:w-72">
                    @if(request('stand')) <input type="hidden" name="stand" value="{{ request('stand') }}"> @endif
                    @if(request('kategori')) <input type="hidden" name="kategori" value="{{ request('kategori') }}"> @endif
                    <div class="relative">
                        <input type="text" id="menu-search-input" name="q" value="{{ $search }}"
                            oninput="filterMenuCardsRealtime(this.value)"
                            placeholder="Cari soto, nasi goreng, es teh..."
                            class="w-full pl-9 pr-9 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                        @if($search)
                        <a href="{{ route('katalog.index', array_merge(request()->query(), ['q' => ''])) }}#katalog-section" class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-600 p-0.5">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                        </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- ===================================================
         3. MENUS GRID (Photo Ratio 4:3 Responsive Cards)
    =================================================== -->
        @if($menus->isEmpty())
        <div class="text-center py-16 bg-white rounded-3xl border border-dashed border-slate-300 p-8 shadow-xs">
            <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-orange-50 flex items-center justify-center text-orange-400">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                </svg>
            </div>
            <h3 class="text-base font-bold text-slate-800">Menu Tidak Ditemukan</h3>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Coba ganti filter stand atau kata kunci pencarian Anda.</p>
            <a href="{{ route('katalog.index') }}#katalog-section" class="inline-block mt-4 px-4 py-2 text-xs font-bold text-orange-600 bg-orange-50 hover:bg-orange-100 rounded-xl transition-colors">
                Reset Filter
            </a>
        </div>
        @else
        <div id="menus-grid-container" class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-6">
            @foreach($menus as $menu)
            @php
            $defaultPhotos = [
                'nasi goreng' => 'https://images.unsplash.com/photo-1603133872878-684f208fb84b?auto=format&fit=crop&w=600&q=80',
                'mie ayam' => 'https://images.unsplash.com/photo-1612927601601-6638404737ce?auto=format&fit=crop&w=600&q=80',
                'bakso' => 'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?auto=format&fit=crop&w=600&q=80',
                'tahu bakso' => 'https://images.unsplash.com/photo-1541544741938-0af808871cc0?auto=format&fit=crop&w=600&q=80',
                'nasi kuning' => 'https://images.unsplash.com/photo-1512058564366-18510be2db19?auto=format&fit=crop&w=600&q=80',
                'nasi uduk' => 'https://images.unsplash.com/photo-1512058564366-18510be2db19?auto=format&fit=crop&w=600&q=80',
                'siomay' => 'https://images.unsplash.com/photo-1496116218417-1a781b1c416c?auto=format&fit=crop&w=600&q=80',
                'batagor' => 'https://images.unsplash.com/photo-1541544741938-0af808871cc0?auto=format&fit=crop&w=600&q=80',
                'risol' => 'https://images.unsplash.com/photo-1608039829572-78524f79c4c7?auto=format&fit=crop&w=600&q=80',
                'es teh' => 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?auto=format&fit=crop&w=600&q=80',
                'es jeruk' => 'https://images.unsplash.com/photo-1613478223719-2ab802602423?auto=format&fit=crop&w=600&q=80',
                'kopi susu' => 'https://images.unsplash.com/photo-1517701550927-30cf4ba1dba5?auto=format&fit=crop&w=600&q=80',
                'jus alpukat' => 'https://images.unsplash.com/photo-1546039907-7fa05f864c02?auto=format&fit=crop&w=600&q=80',
                'es cincau' => 'https://images.unsplash.com/photo-1556881286-fc6915169721?auto=format&fit=crop&w=600&q=80',
                'air mineral' => 'https://images.unsplash.com/photo-1548839140-29a749e1bc4e?auto=format&fit=crop&w=600&q=80',
            ];
            $lowerName = strtolower($menu->nama_menu);
            $imageUrl = null;
            if ($menu->foto) {
            $imageUrl = asset('storage/' . $menu->foto);
            } else {
            foreach ($defaultPhotos as $key => $url) {
            if (str_contains($lowerName, $key)) {
            $imageUrl = $url;
            break;
            }
            }
            if (!$imageUrl) {
            if ($menu->kategori === 'minuman') {
            $imageUrl = 'https://images.unsplash.com/photo-1551024709-8f23befc6f87?auto=format&fit=crop&w=600&q=80';
            } elseif ($menu->kategori === 'snack') {
            $imageUrl = 'https://images.unsplash.com/photo-1541544741938-0af808871cc0?auto=format&fit=crop&w=600&q=80';
            } else {
            $imageUrl = 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=600&q=80';
            }
            }
            }
            $sellerName = $menu->stand->pemilik ?: trim(preg_replace('/^kantin\s+/i', '', $menu->stand->nama_stand));
            @endphp

            <!-- Menu Card with 4:3 Ratio Photo (2 cols on mobile) -->
            <div class="menu-card-item bg-white rounded-2xl sm:rounded-3xl border border-slate-200/90 shadow-xs sm:shadow-sm hover:shadow-xl hover:border-orange-200 transition-all duration-300 flex flex-col justify-between overflow-hidden group"
                data-menu-id="{{ $menu->id }}"
                data-stok="{{ $menu->stok }}"
                data-nama-raw="{{ e($menu->nama_menu) }}"
                data-harga-raw="{{ $menu->harga }}"
                data-stand-raw="{{ e($menu->stand->nama_stand) }}"
                data-nama="{{ strtolower($menu->nama_menu) }}"
                data-stand="{{ strtolower($menu->stand->nama_stand) }}">

                <div>
                    <!-- Ratio Foto 4:3 -->
                    <div class="relative aspect-[4/3] w-full overflow-hidden bg-slate-100">
                        <img src="{{ $imageUrl }}"
                            alt="{{ $menu->nama_menu }}"
                            loading="lazy"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                            onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=600&q=80';">

                        <!-- Category Badge (Pastel Badge matching reference & filter tabs) -->
                        <div class="absolute right-1.5 top-1.5 sm:right-3 sm:top-3 pointer-events-none">
                            @if($menu->kategori === 'makanan')
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 sm:px-2.5 sm:py-1 text-[9px] sm:text-xs font-bold rounded-full bg-amber-50/95 text-amber-900 border border-amber-200/90 shadow-xs backdrop-blur-xs">
                                <svg class="w-3 h-3 shrink-0 text-amber-800" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m16 2-2.3 2.3a3 3 0 0 0 0 4.2l1.8 1.8a3 3 0 0 0 4.2 0L22 8" />
                                    <path d="M15 15 3.3 3.3a4.2 4.2 0 0 0 0 6l7.3 7.3c.7.7 2 .7 2.8 0L15 15Zm0 0 7 7" />
                                    <path d="m2.1 21.8 6.4-6.3" />
                                    <path d="m19 5-7 7" />
                                </svg>
                                <span>Makanan</span>
                            </span>
                            @elseif($menu->kategori === 'minuman')
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 sm:px-2.5 sm:py-1 text-[9px] sm:text-xs font-bold rounded-full bg-sky-50/95 text-sky-900 border border-sky-200/90 shadow-xs backdrop-blur-xs">
                                <svg class="w-3 h-3 shrink-0 text-sky-800" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M15.2 22H8.8a2 2 0 0 1-2-1.79L5 3h14l-1.81 17.21A2 2 0 0 1 15.2 22Z" />
                                    <path d="M6 12a5 5 0 0 1 6 0 5 5 0 0 0 6 0" />
                                </svg>
                                <span>Minuman</span>
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 sm:px-2.5 sm:py-1 text-[9px] sm:text-xs font-bold rounded-full bg-emerald-50/95 text-emerald-900 border border-emerald-200/90 shadow-xs backdrop-blur-xs">
                                <svg class="w-3 h-3 shrink-0 text-emerald-800" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 2a5 5 0 1 1 5 5H7a5 5 0 0 1 0-10 5 5 0 0 1 5 5" />
                                    <path d="M12 7v5" />
                                    <path d="M8 12H5a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-5a2 2 0 0 0-2-2h-3" />
                                </svg>
                                <span>Snack</span>
                            </span>
                            @endif
                        </div>

                        <!-- Bottom Subtle Gradient -->
                        <div class="absolute inset-x-0 bottom-0 h-8 sm:h-10 bg-gradient-to-t from-black/25 to-transparent pointer-events-none"></div>
                    </div>

                    <!-- Card Body: Name, Seller, Stock -->
                    <div class="p-2.5 sm:p-4">
                        <h3 class="font-bold text-slate-900 text-xs sm:text-base group-hover:text-orange-600 transition-colors line-clamp-2 leading-tight sm:leading-snug min-h-[2.25rem] sm:min-h-[2.5rem]">
                            {{ $menu->nama_menu }}
                        </h3>

                        <div class="mt-1.5 flex items-center justify-between gap-1 text-[11px] sm:text-xs">
                            <span class="text-slate-500 font-medium truncate shrink min-w-0">{{ $sellerName }}</span>
                            <div class="menu-stock-wrapper shrink-0">
                                @if($menu->stok > 0)
                                <span class="inline-flex items-center gap-1 font-bold text-emerald-700 bg-emerald-50 px-1.5 sm:px-2 py-0.5 rounded-full text-[9px] sm:text-xs whitespace-nowrap">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    <span>Stok {{ $menu->stok }}</span>
                                </span>
                                @else
                                <span class="inline-flex items-center gap-1 font-bold text-rose-700 bg-rose-50 px-1.5 sm:px-2 py-0.5 rounded-full text-[9px] sm:text-xs whitespace-nowrap">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                    <span>Habis</span>
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card Footer: Price & "+ Tambah" Button -->
                <div class="px-2.5 py-2 sm:px-4 sm:py-3 border-t border-slate-100 flex items-center justify-between gap-1.5">
                    <span class="text-xs sm:text-base font-extrabold text-orange-600 leading-none whitespace-nowrap tracking-tight">
                        Rp {{ number_format($menu->harga, 0, ',', '.') }}
                    </span>

                    <div class="menu-btn-wrapper shrink-0">
                        @if($menu->stok > 0)
                        <button type="button"
                            data-id="{{ $menu->id }}"
                            data-nama="{{ e($menu->nama_menu) }}"
                            data-harga="{{ $menu->harga }}"
                            data-stand="{{ e($menu->stand->nama_stand) }}"
                            data-stok="{{ $menu->stok }}"
                            onclick="cartStore.addItemFromBtn(this)"
                            class="w-8 h-8 sm:w-auto sm:h-9 sm:px-3 sm:py-2 bg-orange-500 hover:bg-orange-600 active:scale-90 text-white font-bold rounded-lg sm:rounded-xl shadow-xs transition-all duration-150 inline-flex items-center justify-center gap-1 cursor-pointer">
                            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            <span class="hidden sm:inline text-xs font-bold">Tambah</span>
                        </button>
                        @else
                        <button type="button" disabled
                            class="px-2 py-1 sm:px-3 sm:py-2 text-[10px] sm:text-xs font-bold text-slate-400 bg-slate-100 rounded-lg sm:rounded-xl cursor-not-allowed whitespace-nowrap">
                            <span>Habis</span>
                        </button>
                        @endif
                    </div>
                </div>

            </div>
            @endforeach
        </div>
        @endif

    </div> <!-- Close max-w-7xl catalog wrapper -->

    <!-- ===================================================
         TOAST VALIDASI ALA SHOPEE: "Ditambahkan ke keranjang"
    =================================================== -->
    <div id="shopee-toast" style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%) scale(0.85); background: rgba(0, 0, 0, 0.82); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); color: #ffffff; padding: 18px 24px; border-radius: 16px; box-shadow: 0 20px 40px rgba(0,0,0,0.35); display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 10px; z-index: 99999; pointer-events: none; opacity: 0; visibility: hidden; transition: opacity 0.22s cubic-bezier(0.16, 1, 0.3, 1), transform 0.22s cubic-bezier(0.16, 1, 0.3, 1);">
        <div style="width: 44px; height: 44px; border-radius: 50%; background: rgba(255,255,255,0.18); display: flex; align-items: center; justify-content: center;">
            <svg style="width: 26px; height: 26px; color: #ffffff;" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
            </svg>
        </div>
        <span style="font-size: 13.5px; font-weight: 600; color: #ffffff; text-align: center; letter-spacing: 0.2px; white-space: nowrap;">
            Ditambahkan ke keranjang
        </span>
    </div>

    <!-- ===================================================
         5. DRAWER / MODAL KERANJANG & CHECKOUT
    =================================================== -->
    <div id="cart-modal" class="fixed inset-0 z-50 overflow-hidden hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <!-- Backdrop -->
        <div id="cart-backdrop" onclick="closeCartModal()" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity opacity-0"></div>

        <div class="fixed inset-y-0 right-0 max-w-full flex pl-10">
            <div id="cart-panel" class="w-screen max-w-md bg-white shadow-2xl flex flex-col transform translate-x-full transition-transform duration-300 ease-in-out">

                <!-- Header Drawer -->
                <div class="p-5 border-b border-slate-200 flex items-center justify-between bg-slate-50/90">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center font-bold">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-base font-extrabold text-slate-800">Keranjang Pre-Order</h2>
                            <p class="text-[11px] text-slate-400">Pemesanan makanan siswa tanpa antre</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeCartModal()" class="p-2 text-slate-400 hover:text-slate-600 rounded-xl hover:bg-slate-200 transition-colors" aria-label="Tutup">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Body: Item List & Checkout Form -->
                <div class="flex-1 overflow-y-auto p-5 space-y-6">

                    <!-- Item List Section -->
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Rincian Menu</span>
                            <button type="button" onclick="cartStore.clearCart()" class="text-xs font-bold text-rose-600 hover:underline">Kosongkan</button>
                        </div>

                        <div id="cart-items-container" class="space-y-2.5">
                            <!-- Injected dynamically by JS -->
                        </div>

                        <!-- Subtotal Display inside cart -->
                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-500">Subtotal</span>
                            <span id="drawer-subtotal" class="text-base font-black text-slate-900">Rp 0</span>
                        </div>
                    </div>

                    @guest
                    <!-- Guest Checkout Prompt -->
                    <div class="border-t-2 border-slate-100 pt-5 space-y-3.5">
                        <div class="rounded-2xl bg-orange-50 border border-orange-200/80 p-4 text-center">
                            <div class="w-10 h-10 rounded-xl bg-orange-500 text-white flex items-center justify-center mx-auto mb-2 shadow-sm">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                                </svg>
                            </div>
                            <h4 class="text-sm font-extrabold text-slate-900">Login untuk Menyelesaikan Pesanan</h4>
                            <p class="text-[11px] text-slate-600 mt-1 leading-relaxed">
                                Menu di keranjang belanja Anda akan tetap tersimpan dan tidak akan hilang saat login atau mendaftar.
                            </p>
                            <div class="mt-3.5 flex flex-col sm:flex-row items-center justify-center gap-2">
                                <a href="{{ route('checkout.show') }}" class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white font-extrabold text-xs shadow-md shadow-orange-500/20 transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                                    <span>Masuk & Checkout</span>
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                                    </svg>
                                </a>
                                <a href="{{ route('register') }}" class="w-full sm:w-auto px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 font-bold text-xs transition-colors">
                                    Daftar Akun Baru
                                </a>
                            </div>
                        </div>
                    </div>
                    @else
                    <!-- Authenticated Siswa Checkout Form Section -->
                    <form id="checkout-form" onsubmit="handleCheckout(event)" class="border-t-2 border-slate-100 pt-5 space-y-4">
                        @csrf
                        <div>
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Data Pemesan</span>
                        </div>

                        <!-- Nama Lengkap Siswa -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nama Pemesan *</label>
                            <input type="text" id="input-nama" required value="{{ auth()->user()->name }}"
                                class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all font-medium">
                        </div>

                        <!-- Kelas -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Kelas Siswa *</label>
                            <input type="text" id="input-kelas" required placeholder="Contoh: XI PPLG 1 / XII AKL 2" value="{{ session('siswa_kelas', '') }}"
                                class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all font-medium">
                        </div>

                        <!-- Catatan Pesanan -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Tambahan (Opsional)</label>
                            <textarea id="input-catatan" rows="2" placeholder="Contoh: Pedas ya bu, kuah dipisah, es sedikit..."
                                class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all font-medium"></textarea>
                        </div>

                        <!-- COD Payment Notice -->
                        <div class="rounded-2xl bg-amber-50 p-3.5 border border-amber-200 flex items-start gap-2.5">
                            <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            <div class="text-xs text-amber-900 leading-relaxed font-medium">
                                <strong class="font-bold">Pembayaran Tunai (COD):</strong> Anda membayar langsung secara tunai saat mengambil pesanan di stand yang bersangkutan.
                            </div>
                        </div>

                        <!-- Submit Order Button -->
                        <div class="pt-2">
                            <button type="submit" id="btn-submit-order"
                                class="w-full py-3.5 px-4 rounded-2xl bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 active:scale-98 text-white font-extrabold text-sm shadow-lg shadow-orange-500/25 transition-all flex items-center justify-center gap-2 cursor-pointer">
                                <span>Kirim Pesanan Sekarang</span>
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                                </svg>
                            </button>
                        </div>
                    </form>
                    @endguest
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // ==========================================
    // LocalStorage Shopping Cart Management
    // ==========================================
    const cartStore = {
        getKey() {
            return typeof getCartStorageKey === 'function' ? getCartStorageKey() : (window.AUTH_USER_ID ? ('kantinskanic_cart_user_' + window.AUTH_USER_ID) : 'kantinskanic_cart_guest');
        },
        getItems() {
            try {
                return JSON.parse(localStorage.getItem(this.getKey())) || [];
            } catch (e) {
                return [];
            }
        },
        save(items) {
            localStorage.setItem(this.getKey(), JSON.stringify(items));
            this.updateUI();
            if (typeof syncNavbarCartBadge === 'function') {
                syncNavbarCartBadge();
            }
        },
        addItemFromBtn(btn) {
            const id = parseInt(btn.getAttribute('data-id'));
            const nama = btn.getAttribute('data-nama');
            const harga = parseInt(btn.getAttribute('data-harga'));
            const stand = btn.getAttribute('data-stand');
            const stok = parseInt(btn.getAttribute('data-stok'));
            this.addItem(id, nama, harga, stand, stok);
        },
        addItem(menuId, name, price, standName, maxStock) {
            if (maxStock <= 0) {
                alert('Maaf, stok menu ini sudah habis.');
                return;
            }
            let items = this.getItems();
            let existing = items.find(i => i.menu_id === menuId);
            if (existing) {
                if (existing.jumlah < maxStock) {
                    existing.jumlah++;
                } else {
                    alert('Maksimal stok yang tersedia adalah ' + maxStock);
                    return;
                }
            } else {
                items.push({
                    menu_id: menuId,
                    nama: name,
                    harga: price,
                    stand: standName,
                    jumlah: 1,
                    maxStock: maxStock
                });
            }
            this.save(items);
            showShopeeToast();
        },
        updateQty(menuId, delta) {
            let items = this.getItems();
            let index = items.findIndex(i => i.menu_id === menuId);
            if (index !== -1) {
                items[index].jumlah += delta;
                if (items[index].jumlah <= 0) {
                    items.splice(index, 1);
                } else if (items[index].jumlah > items[index].maxStock) {
                    items[index].jumlah = items[index].maxStock;
                    alert('Maksimal stok tercapai: ' + items[index].maxStock);
                }
                this.save(items);
            }
        },
        removeItem(menuId) {
            let items = this.getItems().filter(i => i.menu_id !== menuId);
            this.save(items);
        },
        clearCart() {
            localStorage.removeItem(this.getKey());
            this.updateUI();
            if (typeof syncNavbarCartBadge === 'function') {
                syncNavbarCartBadge();
            }
        },
        getTotalPrice() {
            return this.getItems().reduce((sum, item) => sum + (item.harga * item.jumlah), 0);
        },
        getTotalCount() {
            return this.getItems().reduce((sum, item) => sum + item.jumlah, 0);
        },
        updateUI() {
            const items = this.getItems();
            const totalPrice = this.getTotalPrice();

            const container = document.getElementById('cart-items-container');
            const drawerSubtotal = document.getElementById('drawer-subtotal');

            if (drawerSubtotal) drawerSubtotal.innerText = 'Rp ' + totalPrice.toLocaleString('id-ID');

            // Render items in drawer
            if (container) {
                if (items.length === 0) {
                    container.innerHTML = `
                        <div class="text-center py-10 text-slate-400">
                            <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-2 text-slate-400">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                            </div>
                            <p class="text-xs font-semibold">Keranjang Anda masih kosong.</p>
                            <p class="text-[11px] text-slate-400 mt-0.5">Pilih menu favorit Anda dari daftar menu.</p>
                        </div>
                    `;
                } else {
                    container.innerHTML = items.map(item => `
                        <div class="flex items-center justify-between p-3 rounded-2xl bg-slate-50 border border-slate-200">
                            <div class="flex-1 pr-2">
                                <h4 class="text-xs font-bold text-slate-800 line-clamp-1">${item.nama}</h4>
                                <p class="text-[10px] text-slate-500 font-medium">${item.stand}</p>
                                <p class="text-xs font-black text-orange-600 mt-0.5">Rp ${(item.harga * item.jumlah).toLocaleString('id-ID')}</p>
                            </div>
                            <div class="flex items-center gap-1.5 bg-white rounded-xl border border-slate-200 p-1 shadow-2xs">
                                <button type="button" onclick="cartStore.updateQty(${item.menu_id}, -1)" class="w-6 h-6 flex items-center justify-center text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-lg">&minus;</button>
                                <span class="text-xs font-black px-1.5">${item.jumlah}</span>
                                <button type="button" onclick="cartStore.updateQty(${item.menu_id}, 1)" class="w-6 h-6 flex items-center justify-center text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-lg">&plus;</button>
                            </div>
                        </div>
                    `).join('');
                }
            }
        }
    };

    let toastTimeout = null;

    function showShopeeToast() {
        const toast = document.getElementById('shopee-toast');
        if (!toast) return;

        if (toastTimeout) {
            clearTimeout(toastTimeout);
        }

        toast.style.visibility = 'visible';
        toast.style.opacity = '1';
        toast.style.transform = 'translate(-50%, -50%) scale(1)';

        // Efek pop badge keranjang di navbar ala Shopee
        const navBadge = document.getElementById('nav-cart-badge');
        if (navBadge) {
            navBadge.style.transition = 'transform 0.15s cubic-bezier(0.175, 0.885, 0.32, 1.275)';
            navBadge.style.transform = 'scale(1.4)';
            setTimeout(() => {
                navBadge.style.transform = 'scale(1)';
            }, 180);
        }

        toastTimeout = setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translate(-50%, -50%) scale(0.85)';
            setTimeout(() => {
                if (toast.style.opacity === '0') {
                    toast.style.visibility = 'hidden';
                }
            }, 220);
        }, 1200);
    }

    function openCartModal() {
        const modal = document.getElementById('cart-modal');
        const backdrop = document.getElementById('cart-backdrop');
        const panel = document.getElementById('cart-panel');

        cartStore.updateUI();
        if (modal) {
            modal.classList.remove('hidden');
            setTimeout(() => {
                if (backdrop) backdrop.classList.remove('opacity-0');
                if (panel) panel.classList.remove('translate-x-full');
            }, 10);
        }
    }

    function closeCartModal() {
        const modal = document.getElementById('cart-modal');
        const backdrop = document.getElementById('cart-backdrop');
        const panel = document.getElementById('cart-panel');

        if (backdrop) backdrop.classList.add('opacity-0');
        if (panel) panel.classList.add('translate-x-full');
        setTimeout(() => {
            if (modal) modal.classList.add('hidden');
        }, 300);
    }

    async function handleCheckout(event) {
        event.preventDefault();
        const items = cartStore.getItems();
        if (items.length === 0) {
            alert('Keranjang belanja Anda masih kosong! Silakan pilih menu terlebih dahulu.');
            return;
        }

        const nama = document.getElementById('input-nama').value.trim();
        const kelas = document.getElementById('input-kelas').value.trim();
        const catatan = document.getElementById('input-catatan').value.trim();

        const submitBtn = document.getElementById('btn-submit-order');
        submitBtn.disabled = true;
        submitBtn.innerHTML = `
            <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>Mengirim Pesanan...</span>
        `;

        try {
            const payload = {
                nama_pemesan: nama,
                kelas: kelas,
                catatan: catatan,
                items: items.map(i => ({
                    menu_id: i.menu_id,
                    jumlah: i.jumlah
                }))
            };

            const response = await fetch('{{ route("checkout") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(payload)
            });

            const data = await response.json();

            if (response.ok && data.success) {
                if (typeof saveOrderToHistoryStorage === 'function' && data.all_codes) {
                    saveOrderToHistoryStorage(data.all_codes);
                }
                cartStore.clearCart();
                window.location.href = data.redirect_url;
            } else {
                alert(data.message || 'Gagal membuat pesanan. Silakan periksa kembali data Anda.');
                pollRealtimeStocks();
                submitBtn.disabled = false;
                submitBtn.innerHTML = `
                    <span>Kirim Pesanan Sekarang</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                    </svg>
                `;
            }
        } catch (err) {
            console.error(err);
            alert('Terjadi kesalahan saat memproses pesanan. Periksa koneksi internet Anda.');
            submitBtn.disabled = false;
            submitBtn.innerHTML = `
                <span>Kirim Pesanan Sekarang</span>
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                </svg>
            `;
        }
    }

    // Real-time menu search filter
    function filterMenuCardsRealtime(keyword) {
        const q = keyword.trim().toLowerCase();
        const cards = document.querySelectorAll('.menu-card-item');
        cards.forEach(card => {
            const nama = card.getAttribute('data-nama') || '';
            const stand = card.getAttribute('data-stand') || '';
            if (!q || nama.includes(q) || stand.includes(q)) {
                card.classList.remove('hidden');
            } else {
                card.classList.add('hidden');
            }
        });
    }

    // ==========================================
    // Real-Time Menu Stock Sync Engine
    // ==========================================
    function updateMenuCardStock(menuId, newStock, isAvailable) {
        const card = document.querySelector(`.menu-card-item[data-menu-id="${menuId}"]`);
        if (!card) return;

        const currentStock = parseInt(card.getAttribute('data-stok') || '0');
        const available = isAvailable && newStock > 0;

        // Jangan manipulasi DOM jika stok dan statusnya belum berubah
        if (currentStock === newStock && card.getAttribute('data-synced') === (available ? '1' : '0')) {
            return;
        }

        card.setAttribute('data-stok', newStock);
        card.setAttribute('data-synced', available ? '1' : '0');

        const stockWrapper = card.querySelector('.menu-stock-wrapper');
        const btnWrapper = card.querySelector('.menu-btn-wrapper');

        const rawNama = card.getAttribute('data-nama-raw') || '';
        const rawHarga = card.getAttribute('data-harga-raw') || '0';
        const rawStand = card.getAttribute('data-stand-raw') || '';

        if (available) {
            if (stockWrapper) {
                stockWrapper.innerHTML = `
                    <span class="inline-flex items-center gap-1 font-bold text-emerald-700 bg-emerald-50 px-1.5 sm:px-2 py-0.5 rounded-full text-[9px] sm:text-xs whitespace-nowrap">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        <span>Stok ${newStock}</span>
                    </span>
                `;
            }

            if (btnWrapper) {
                btnWrapper.innerHTML = `
                    <button type="button"
                        data-id="${menuId}"
                        data-nama="${rawNama}"
                        data-harga="${rawHarga}"
                        data-stand="${rawStand}"
                        data-stok="${newStock}"
                        onclick="cartStore.addItemFromBtn(this)"
                        class="w-8 h-8 sm:w-auto sm:h-9 sm:px-3 sm:py-2 bg-orange-500 hover:bg-orange-600 active:scale-90 text-white font-bold rounded-lg sm:rounded-xl shadow-xs transition-all duration-150 inline-flex items-center justify-center gap-1 cursor-pointer">
                        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        <span class="hidden sm:inline text-xs font-bold">Tambah</span>
                    </button>
                `;
            }
        } else {
            // Cukup badge habis di samping nama kantin & tombol disabled
            if (stockWrapper) {
                stockWrapper.innerHTML = `
                    <span class="inline-flex items-center gap-1 font-bold text-rose-700 bg-rose-50 px-1.5 sm:px-2 py-0.5 rounded-full text-[9px] sm:text-xs whitespace-nowrap">
                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                        <span>Habis</span>
                    </span>
                `;
            }

            if (btnWrapper) {
                btnWrapper.innerHTML = `
                    <button type="button" disabled
                        class="px-2 py-1 sm:px-3 sm:py-2 text-[10px] sm:text-xs font-bold text-slate-400 bg-slate-100 rounded-lg sm:rounded-xl cursor-not-allowed whitespace-nowrap">
                        <span>Habis</span>
                    </button>
                `;
            }
        }
    }

    let stockPollTimer = null;

    async function pollRealtimeStocks() {
        if (document.hidden) return; // Hemat resource saat tab browser tidak aktif

        try {
            const res = await fetch('{{ route("katalog.stocks") }}');
            if (!res.ok) return;
            const json = await res.json();
            if (json.success && Array.isArray(json.data)) {
                json.data.forEach(item => {
                    updateMenuCardStock(item.id, item.stok, item.is_available);
                });
            }
        } catch (e) {
            // Silently ignore network errors during background sync
        }
    }

    function initRealtimeStockSync() {
        // Polling setiap 3 detik agar pembeli melihat perubahan stok langsung
        if (stockPollTimer) clearInterval(stockPollTimer);
        stockPollTimer = setInterval(pollRealtimeStocks, 3000);

        // Polling instan saat user membuka kembali tab browser
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) {
                pollRealtimeStocks();
            }
        });
        window.addEventListener('focus', pollRealtimeStocks);
    }

    document.addEventListener('DOMContentLoaded', () => {
        cartStore.updateUI();
        initRealtimeStockSync();
        if (new URLSearchParams(window.location.search).get('cart') === 'open') {
            openCartModal();
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeCartModal();
        }
    });
</script>
@endpush