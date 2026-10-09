@extends('layouts.app')

@section('title', 'Riwayat Pesanan - KantinSkanic')

@section('content')
<!-- ========================================================
     1. FULL-WIDTH PAGE HEADER (Matching Beranda Hero Colors)
======================================================== -->
<section class="w-full relative overflow-hidden bg-gradient-to-br from-orange-600 via-orange-500 to-amber-400 text-white py-12 sm:py-16 md:py-20 text-center shadow-md">
    <!-- Giant Geometric Circles matching beranda hero -->
    <div class="absolute -right-16 sm:-right-8 lg:right-10 top-1/2 -translate-y-1/2 w-[340px] h-[340px] sm:w-[460px] sm:h-[460px] rounded-full bg-white/[0.08] pointer-events-none"></div>
    <div class="absolute -right-32 sm:-right-24 lg:-right-6 top-1/2 -translate-y-1/2 w-[480px] h-[480px] sm:w-[620px] sm:h-[620px] rounded-full border border-white/[0.08] pointer-events-none"></div>
    <div class="absolute -left-16 -top-16 w-64 h-64 rounded-full bg-white/[0.06] pointer-events-none"></div>

    <div class="relative z-10 max-w-4xl mx-auto px-4 sm:px-6">
        <h1 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-black text-white tracking-tight">
            Riwayat Pesanan
        </h1>

        <p class="mt-2.5 sm:mt-3 text-xs sm:text-sm md:text-base text-white/90 font-medium max-w-2xl mx-auto leading-relaxed">
            Wadah pemantauan status pesanan, rincian makanan &amp; minuman, serta struk digital pesananmu
        </p>
    </div>
</section>

<!-- ========================================================
     2. SHOPEE-STYLE PESANAN SAYA CONTENT AREA
======================================================== -->
<div class="bg-[#f5f5f5] min-h-[70vh] py-6 sm:py-8">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Shopee Navigation Tabs Bar -->
        <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 mb-4 overflow-hidden">
            <div class="flex items-center overflow-x-auto no-scrollbar scroll-smooth">
                @php
                    $tabs = [
                        'all' => ['label' => 'Semua', 'count' => $statusCounts['all'] ?? 0],
                        'pending' => ['label' => 'Menunggu', 'count' => $statusCounts['pending'] ?? 0],
                        'diproses' => ['label' => 'Sedang Disiapkan', 'count' => $statusCounts['diproses'] ?? 0],
                        'siap_diambil' => ['label' => 'Siap Diambil', 'count' => $statusCounts['siap_diambil'] ?? 0],
                        'selesai' => ['label' => 'Selesai', 'count' => $statusCounts['selesai'] ?? 0],
                        'dibatalkan' => ['label' => 'Dibatalkan', 'count' => $statusCounts['dibatalkan'] ?? 0],
                    ];
                @endphp

                @foreach($tabs as $key => $tab)
                @php
                    $isActive = ($statusFilter === $key) || ($key === 'all' && empty($statusFilter));
                @endphp
                <a href="{{ route('order.history', ['status' => $key, 'q' => $searchQuery]) }}"
                   class="flex-1 min-w-[110px] sm:min-w-[130px] py-3.5 px-3 text-center text-xs sm:text-sm transition-all whitespace-nowrap border-b-2
                          {{ $isActive
                             ? 'text-orange-600 font-black border-orange-500 bg-orange-50/20'
                             : 'text-slate-600 hover:text-orange-600 font-bold border-transparent hover:bg-slate-50' }}">
                    <span>{{ $tab['label'] }}</span>
                    @if($tab['count'] > 0)
                    <span class="ml-1.5 px-1.5 py-0.5 rounded-full text-[10px] font-extrabold
                          {{ $isActive ? 'bg-orange-500 text-white' : 'bg-slate-100 text-slate-500' }}">
                        {{ $tab['count'] }}
                    </span>
                    @endif
                </a>
                @endforeach
            </div>
        </div>

        <!-- Shopee Search Bar Card -->
        <div class="bg-white rounded-2xl p-2.5 sm:p-3 shadow-xs border border-slate-200/80 mb-5">
            <form method="GET" action="{{ route('order.history') }}" class="flex items-center gap-2">
                <input type="hidden" name="status" value="{{ $statusFilter }}">

                <div class="relative flex-1">
                    <div class="absolute left-3.5 top-3 text-slate-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                    </div>
                    <input type="text" name="q" value="{{ $searchQuery }}"
                           placeholder="Kamu dapat mencari berdasarkan No. Pesanan (PO-XXXX), Nama Stand, atau Menu..."
                           class="w-full pl-10 pr-4 py-2 text-xs sm:text-sm bg-slate-50/80 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all font-medium">
                </div>

                <button type="submit" class="px-5 py-2.5 rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-extrabold text-xs sm:text-sm shadow-xs active:scale-95 transition-all flex items-center gap-1.5 shrink-0 cursor-pointer">
                    <span>Cari</span>
                </button>

                @if(!empty($searchQuery))
                <a href="{{ route('order.history', ['status' => $statusFilter]) }}" class="px-3 py-2 text-xs text-slate-400 hover:text-slate-600 font-bold shrink-0">
                    Reset
                </a>
                @endif
            </form>
        </div>

        <!-- Shopee Orders List Cards -->
        @if($orders->count() > 0)
        <div class="space-y-4">
            @foreach($orders as $order)
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs hover:shadow-md transition-all overflow-hidden">

                <!-- 1. Header Toko / Stand ala Shopee -->
                <div class="p-4 sm:p-5 pb-3 border-b border-slate-100 flex flex-wrap items-center justify-between gap-2.5">
                    <!-- Left: Shop Info + Kode Pesanan -->
                    <div class="flex items-center gap-2 sm:gap-3 min-w-0">
                        <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-orange-50 text-orange-600 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.614A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.015a2.993 2.993 0 0 0 2.25 1.015c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72L4.318 3.44A1.5 1.5 0 0 1 5.378 3h13.244a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72m-13.5 0a3 3 0 0 0 3.75-.614A2.993 2.993 0 0 0 9.75 9.75" />
                            </svg>
                        </div>

                        <span class="font-extrabold text-sm sm:text-base text-slate-900 truncate">
                            {{ $order->stand->nama_stand ?? 'Stand Kantin' }}
                        </span>

                        <span class="text-slate-300 hidden sm:inline">&bull;</span>

                        <!-- Kode PO with copy button -->
                        <div class="inline-flex items-center gap-1 font-mono text-xs font-bold bg-slate-50 text-slate-700 px-2.5 py-1 rounded-lg border border-slate-200/80">
                            <span>#{{ $order->kode_tr }}</span>
                            <button type="button" onclick="copyOrderCode('{{ $order->kode_tr }}')" title="Salin Kode PO" class="text-slate-400 hover:text-orange-600 ml-0.5 cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.888A2.25 2.25 0 0 0 13.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 0 1-.75.75H9a.75.75 0 0 1-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 0 1-2.25 2.25H6.75A2.25 2.25 0 0 1 4.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 0 1 1.927-.184" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Right: Shopee Status Label -->
                    <div>
                        @if($order->status === 'siap_diambil')
                        <span class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-black text-emerald-600 uppercase tracking-wide">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                            <span>SIAP DIAMBIL</span>
                        </span>
                        @elseif($order->status === 'diproses')
                        <span class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-black text-amber-600 uppercase tracking-wide">
                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                            <span>SEDANG DISIAPKAN</span>
                        </span>
                        @elseif($order->status === 'pending')
                        <span class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-bold text-orange-600 uppercase tracking-wide">
                            <span class="w-2 h-2 rounded-full bg-orange-500"></span>
                            <span>MENUNGGU KONFIRMASI</span>
                        </span>
                        @elseif($order->status === 'selesai')
                        <span class="inline-flex items-center gap-1 text-xs sm:text-sm font-extrabold text-slate-500 uppercase tracking-wide">
                            <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                            </svg>
                            <span>SELESAI</span>
                        </span>
                        @elseif($order->status === 'dibatalkan')
                        <span class="inline-flex items-center gap-1 text-xs sm:text-sm font-bold text-rose-600 uppercase tracking-wide">
                            <svg class="w-4 h-4 text-rose-500" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                            <span>DIBATALKAN</span>
                        </span>
                        @endif
                    </div>
                </div>

                <!-- 2. Body Daftar Menu ala Shopee -->
                <div class="p-4 sm:p-5 space-y-3.5 divide-y divide-slate-100">
                    @foreach($order->items as $item)
                    <div class="pt-3.5 first:pt-0 flex items-start sm:items-center justify-between gap-3 sm:gap-4">
                        <div class="flex items-start sm:items-center gap-3 min-w-0">
                            <!-- Foto Menu -->
                            @if($item->menu && $item->menu->foto)
                            <img src="{{ asset('storage/' . $item->menu->foto) }}" alt="{{ $item->menu->nama_menu }}"
                                 class="w-16 h-16 sm:w-20 sm:h-20 rounded-xl object-cover bg-slate-100 border border-slate-200/80 shrink-0">
                            @else
                            <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-xl bg-orange-50 border border-orange-100 flex items-center justify-center text-orange-500 shrink-0">
                                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                                </svg>
                            </div>
                            @endif

                            <div class="min-w-0">
                                <h3 class="font-extrabold text-sm sm:text-base text-slate-800 leading-snug">
                                    {{ $item->menu->nama_menu ?? 'Menu' }}
                                </h3>
                                <p class="text-xs text-slate-400 capitalize mt-0.5">
                                    Kategori: {{ $item->menu->kategori ?? 'Umum' }}
                                </p>
                                <span class="inline-block text-xs font-semibold text-slate-600 mt-1">
                                    x{{ $item->jumlah }}
                                </span>
                            </div>
                        </div>

                        <!-- Harga Item -->
                        <div class="text-right shrink-0">
                            <span class="text-xs sm:text-sm font-extrabold text-slate-800 block">
                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                            </span>
                            @if($item->jumlah > 1)
                            <span class="text-[11px] text-slate-400 block mt-0.5">
                                @ Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}
                            </span>
                            @endif
                        </div>
                    </div>
                    @endforeach

                    <!-- Catatan Pembeli jika ada -->
                    @if($order->catatan)
                    <div class="pt-3">
                        <div class="p-2.5 bg-slate-50 rounded-xl border border-slate-100 flex items-start gap-2 text-xs text-slate-500">
                            <svg class="w-4 h-4 text-orange-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v4.018Z" />
                            </svg>
                            <span class="italic">Catatan: &ldquo;{{ $order->catatan }}&rdquo;</span>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- 3. Footer Total Pembayaran ala Shopee -->
                <div class="px-4 sm:px-5 py-3.5 bg-slate-50/70 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
                    <span class="text-xs text-slate-400 font-medium">
                        Dipesan pada {{ $order->created_at->format('d M Y, H:i') }} WIB &bull; {{ $order->created_at->diffForHumans() }}
                    </span>

                    <div class="flex items-baseline gap-2 self-end sm:self-auto">
                        <span class="text-xs text-slate-500">Total Pesanan ({{ $order->items->sum('jumlah') }} Menu):</span>
                        <span class="text-lg sm:text-xl font-black text-orange-600 font-mono">
                            Rp {{ number_format($order->total_harga, 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                <!-- 4. Tombol Aksi ala Shopee -->
                <div class="px-4 sm:px-5 py-3.5 bg-slate-50/70 border-t border-slate-100/80 flex flex-wrap items-center justify-end gap-2.5">
                    <button type="button" onclick="copyOrderCode('{{ $order->kode_tr }}')"
                            class="px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 font-bold text-xs transition-all active:scale-95 cursor-pointer">
                        Salin Kode PO
                    </button>

                    <a href="{{ route('katalog.index', ['stand' => $order->stand_id]) }}#katalog-section"
                       class="px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-700 hover:text-orange-600 hover:border-orange-300 font-bold text-xs transition-all active:scale-95">
                        Pesan di Stand Ini Lagi
                    </a>

                    <a href="{{ route('order.status', $order->kode_tr) }}"
                       class="px-5 py-2 rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-black text-xs shadow-xs active:scale-95 transition-all flex items-center gap-1.5">
                        <span>Lihat Rincian Struk</span>
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>
                    </a>
                </div>

            </div>
            @endforeach
        </div>
        @else
        @guest
        <!-- Guest Not Logged In State -->
        <div class="bg-white rounded-3xl border border-slate-200/90 p-8 sm:p-14 text-center shadow-xs max-w-lg mx-auto">
            <div class="w-16 h-16 rounded-2xl bg-orange-50 border border-orange-100 text-orange-500 mx-auto flex items-center justify-center mb-4">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                </svg>
            </div>
            <h3 class="text-base sm:text-lg font-black text-slate-900 mb-1">Kamu Belum Masuk ke Akun</h3>
            <p class="text-xs sm:text-sm text-slate-500 max-w-sm mx-auto mb-6">
                Silakan masuk ke akunmu untuk melihat riwayat pesanan yang pernah kamu buat.
            </p>

            <div class="flex items-center justify-center gap-3">
                <a href="{{ route('login') }}" class="px-6 py-2.5 rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-extrabold text-xs sm:text-sm shadow-md shadow-orange-500/25 active:scale-95 transition-all">
                    Masuk ke Akun Sekarang
                </a>
            </div>
        </div>
        @else
        <!-- Shopee Empty State -->
        <div class="bg-white rounded-2xl border border-slate-200/90 p-10 sm:p-14 text-center shadow-xs">
            <div class="w-20 h-20 rounded-full bg-orange-50 border border-orange-100 text-orange-500 mx-auto flex items-center justify-center mb-4 shadow-inner">
                <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                </svg>
            </div>
            <h3 class="text-base sm:text-lg font-black text-slate-900 mb-1">Belum Ada Pesanan</h3>
            <p class="text-xs sm:text-sm text-slate-500 max-w-md mx-auto mb-6">
                Yuk pesan makanan atau minuman favoritmu di Kantin Skanic sekarang tanpa antre!
            </p>

            <div class="flex items-center justify-center gap-3">
                <a href="{{ route('katalog.index') }}#katalog-section" class="px-6 py-2.5 rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-extrabold text-xs sm:text-sm shadow-md shadow-orange-500/25 active:scale-95 transition-all">
                    Mulai Pesan Sekarang
                </a>
            </div>
        </div>
        @endguest
        @endif

    </div>
</div>

<!-- Toast Notifikasi Salin Kode PO -->
<div id="copy-toast" class="fixed bottom-6 left-1/2 -translate-x-1/2 bg-slate-900 text-white px-5 py-2.5 rounded-2xl shadow-2xl text-xs font-bold transition-all transform translate-y-20 opacity-0 pointer-events-none z-50 flex items-center gap-2">
    <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
    </svg>
    <span id="copy-toast-text">Kode pesanan berhasil disalin!</span>
</div>

<script>
    function copyOrderCode(code) {
        if (!code) return;
        navigator.clipboard.writeText(code).then(() => {
            showToast('Kode PO ' + code + ' berhasil disalin!');
        }).catch(() => {
            prompt('Salin kode pesanan:', code);
        });
    }

    function showToast(msg) {
        const toast = document.getElementById('copy-toast');
        const text = document.getElementById('copy-toast-text');
        if (toast) {
            if (text && msg) text.innerText = msg;
            toast.classList.remove('translate-y-20', 'opacity-0');
            setTimeout(() => {
                toast.classList.add('translate-y-20', 'opacity-0');
            }, 2500);
        }
    }

    @guest
    // Bersihkan storage riwayat lokal jika user tidak sedang login
    try {
        localStorage.removeItem('kantinskanic_order_history');
    } catch (e) {}
    @endguest
</script>
@endsection
