@extends('layouts.app')

@section('title', 'Riwayat Pesanan - KantinSkanic')

@section('content')
<style>
/* Custom smooth scrollbar for digital receipt */
#receipt-printable-body {
    scrollbar-width: thin;
    scrollbar-color: #cbd5e1 transparent;
    -webkit-overflow-scrolling: touch;
}
#receipt-printable-body::-webkit-scrollbar {
    width: 6px;
}
#receipt-printable-body::-webkit-scrollbar-track {
    background: transparent;
}
#receipt-printable-body::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 9999px;
}
#receipt-printable-body::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Sembunyikan scrollbar slider bawaan abu-abu */
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

@media print {
    body * {
        visibility: hidden !important;
    }
    #digital-receipt-modal,
    #digital-receipt-modal * {
        visibility: visible !important;
    }
    #digital-receipt-modal {
        position: absolute !important;
        left: 0 !important;
        top: 0 !important;
        width: 100% !important;
        height: auto !important;
        background: white !important;
        padding: 0 !important;
        display: block !important;
        opacity: 1 !important;
        pointer-events: auto !important;
        z-index: 99999 !important;
    }
    #digital-receipt-card {
        max-width: 100% !important;
        max-height: none !important;
        border: none !important;
        box-shadow: none !important;
        transform: none !important;
        overflow: visible !important;
    }
    #digital-receipt-card .receipt-no-print,
    #digital-receipt-modal button {
        display: none !important;
    }
}
</style>
<!-- ========================================================
     1. FULL-WIDTH PAGE HEADER (Matching Beranda Hero Colors)
======================================================== -->
<section class="w-full relative overflow-hidden bg-gradient-to-br from-orange-600 via-orange-500 to-amber-400 text-white py-12 sm:py-16 md:py-20 text-center shadow-md">
    <!-- Giant Geometric Circles matching beranda hero (hidden on mobile and tablet) -->
    <div class="hidden md:block absolute -right-16 sm:-right-8 lg:right-10 top-1/2 -translate-y-1/2 w-[340px] h-[340px] sm:w-[460px] sm:h-[460px] rounded-full bg-white/[0.08] pointer-events-none"></div>
    <div class="hidden md:block absolute -right-32 sm:-right-24 lg:-right-6 top-1/2 -translate-y-1/2 w-[480px] h-[480px] sm:w-[620px] sm:h-[620px] rounded-full border border-white/[0.08] pointer-events-none"></div>
    <div class="hidden md:block absolute -left-16 -top-16 w-64 h-64 rounded-full bg-white/[0.06] pointer-events-none"></div>

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
<div class="bg-[#f5f5f5] min-h-[70vh] pt-6 sm:pt-8 pb-24 md:pb-8">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

        @if(!empty($searchQuery))
        <div class="mb-4 flex items-center justify-between p-3 sm:p-3.5 bg-orange-50 border border-orange-200/80 rounded-2xl text-xs sm:text-sm shadow-2xs">
            <div class="flex items-center gap-2 text-slate-800 font-bold min-w-0">
                <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                <span class="truncate">Menampilkan pesanan: <span class="font-mono text-orange-700 bg-white px-2 py-0.5 rounded-lg border border-orange-200 shadow-2xs">#{{ $searchQuery }}</span></span>
            </div>
            @auth
            <a href="{{ route('order.history') }}" class="shrink-0 font-extrabold text-orange-600 hover:text-orange-700 hover:underline text-xs ml-3">
                Lihat Semua Pesanan &rarr;
            </a>
            @endauth
        </div>
        @endif

        <!-- Shopee Navigation Tabs Bar -->
        <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 mb-5 overflow-hidden">
            <div id="shopee-tabs-container" class="flex items-center overflow-x-auto no-scrollbar scroll-smooth" style="scrollbar-width: none; -ms-overflow-style: none;">
                @php
                    $tabs = [
                        'all' => ['label' => 'Semua', 'count' => $statusCounts['all'] ?? 0],
                        'belum_bayar' => ['label' => 'Belum Bayar', 'count' => $statusCounts['belum_bayar'] ?? 0],
                        'pending' => ['label' => 'Menunggu', 'count' => $statusCounts['pending'] ?? 0],
                        'siap_diambil' => ['label' => 'Siap Diambil', 'count' => $statusCounts['siap_diambil'] ?? 0],
                        'selesai' => ['label' => 'Selesai', 'count' => $statusCounts['selesai'] ?? 0],
                    ];
                @endphp

                @foreach($tabs as $key => $tab)
                @php
                    $isActive = ($statusFilter === $key) || ($key === 'all' && empty($statusFilter));
                @endphp
                <a href="{{ route('order.history', array_filter(['status' => $key, 'q' => request('q')])) }}"
                   data-tab-key="{{ $key }}"
                   data-active="{{ $isActive ? 'true' : 'false' }}"
                   class="tab-item shrink-0 sm:flex-1 px-4 sm:px-5 py-3 sm:py-3.5 text-center text-xs sm:text-sm whitespace-nowrap select-none transition-colors duration-150 flex items-center justify-center relative
                          {{ $isActive
                             ? 'text-orange-600 font-black'
                             : 'text-slate-600 hover:text-orange-600 font-bold' }}">
                    <span class="inline-flex flex-col items-center relative">
                        <span class="leading-normal flex items-center justify-center gap-1.5">
                            <span>{{ $tab['label'] }}</span>
                            @if($key !== 'all' && $key !== 'selesai' && ($tab['count'] ?? 0) > 0)
                            <span class="px-1.5 py-0.5 rounded-full text-[10px] font-black leading-none transition-colors
                                  {{ $isActive
                                     ? 'bg-orange-500 text-white shadow-2xs'
                                     : 'bg-slate-100 text-slate-600 border border-slate-200/80' }}">
                                {{ $tab['count'] }}
                            </span>
                            @endif
                        </span>
                        <!-- Garis underline aktif persis selebar teks -->
                        <span class="w-full rounded-full transition-all"
                              style="height: 3px; margin-top: 5px; {{ $isActive ? 'background-color: #ea580c;' : 'background-color: transparent;' }}"></span>
                    </span>
                </a>
                @endforeach
            </div>
        </div>

        <!-- Shopee Orders List Cards (Batched for Unpaid Multi-Stand, SubOrder for Paid) -->
        @php
            $itemsToDisplay = isset($displayItems) ? $displayItems : $orders->map(fn($o) => ['type' => 'single_order', 'order' => $o]);
        @endphp
        @if($itemsToDisplay->count() > 0)
        <div class="space-y-3.5 sm:space-y-4">
            @foreach($itemsToDisplay as $item)
            @if($item['type'] === 'batch_unpaid')
                @php
                    $batchCode = $item['batch_code'];
                    $grossAmount = $item['gross_amount'];
                    $subOrders = $item['sub_orders'];
                    $isMultiStand = $subOrders->count() > 1;
                    $orderIdsStr = implode(',', $item['order_ids']);
                    $expiryHours = config('midtrans.expiry_hours', 24);
                    $expiresAt = $item['created_at']->copy()->addHours($expiryHours);
                    $isExpired = now()->greaterThanOrEqualTo($expiresAt);
                @endphp
                <!-- UNIFIED BATCH CARD FOR UNPAID ORDER (MULTI-STAND / SINGLE-STAND) -->
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs hover:shadow-md transition-all overflow-hidden" 
                     id="batch-card-{{ $batchCode }}"
                     data-batch-code="{{ $batchCode }}"
                     data-order-ids="{{ $orderIdsStr }}"
                     data-status="menunggu_pembayaran"
                     data-is-lunas="0"
                     data-is-qris="1">

                    <!-- 1. Header Batch (Multi-Stand vs Single Stand) -->
                    <div class="p-3.5 sm:p-4 bg-slate-50/70 border-b border-slate-100">
                        <div class="flex items-center justify-between gap-2">
                            <div class="flex items-center gap-2 min-w-0">
                                <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center shrink-0 shadow-2xs">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.614A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.015a2.993 2.993 0 0 0 2.25 1.015c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72L4.318 3.44A1.5 1.5 0 0 1 5.378 3h13.244a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72m-13.5 0a3 3 0 0 0 3.75-.614A2.993 2.993 0 0 0 9.75 9.75" />
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <span class="font-extrabold text-sm sm:text-base text-slate-800 truncate block">
                                        @if($isMultiStand)
                                            Pesanan Multi-Stand ({{ $subOrders->count() }} Stand)
                                        @else
                                            {{ $subOrders->first()?->stand->nama_stand ?? 'Stand Kantin' }}
                                        @endif
                                    </span>
                                </div>
                            </div>

                            <!-- Right: Badges -->
                            <div class="flex items-center gap-1.5 shrink-0">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-lg sm:rounded-xl text-[10px] sm:text-xs font-black bg-orange-500 text-white shadow-2xs">
                                    <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25a2.25 2.25 0 0 1 13.5 18v-2.25Z" />
                                    </svg>
                                    <span>QRIS</span>
                                </span>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-lg sm:rounded-xl text-[10px] sm:text-xs font-black {{ $isExpired ? 'bg-slate-100 text-slate-500 border border-slate-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}"
                                      id="batch-status-badge-{{ $batchCode }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $isExpired ? 'bg-slate-400' : 'bg-amber-500 animate-pulse' }}" id="batch-status-dot-{{ $batchCode }}"></span>
                                    <span id="batch-status-text-{{ $batchCode }}">{{ $isExpired ? 'Kedaluwarsa' : 'Belum Bayar' }}</span>
                                </span>
                            </div>
                        </div>

                        <!-- Sub-bar: Kode Batch & Waktu Pesan -->
                        <div class="mt-2.5 pt-2 border-t border-slate-200/50 flex items-center justify-between text-[11px] sm:text-xs text-slate-500">
                            <div class="inline-flex items-center gap-1.5 font-mono font-bold bg-white px-2 py-0.5 rounded-lg border border-slate-200 shadow-2xs">
                                <span class="text-orange-600">#{{ $batchCode }}</span>
                                <button type="button" onclick="copyOrderCode('{{ $batchCode }}')" title="Salin Kode Pesanan" class="text-slate-400 hover:text-orange-600 cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.888A2.25 2.25 0 0 0 13.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 0 1-.75.75H9a.75.75 0 0 1-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 0 1-2.25 2.25H6.75A2.25 2.25 0 0 1 4.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 0 1 1.927-.184" />
                                    </svg>
                                </button>
                            </div>
                            <span class="text-slate-400 font-medium">
                                {{ $item['created_at']->format('d M Y, H:i') }} WIB
                            </span>
                        </div>
                    </div>

                    <!-- 1.5. Countdown Bar Batas Waktu Pembayaran QRIS 24 Jam -->
                    <div class="px-3.5 sm:px-4 py-2 bg-gradient-to-r from-amber-50/90 via-orange-50/70 to-amber-50/90 border-b border-amber-200/70 flex items-center justify-between gap-2 text-xs">
                        <div class="flex items-center gap-1.5 text-slate-700 min-w-0">
                            <svg class="w-4 h-4 text-orange-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            <span class="truncate text-[11px] sm:text-xs text-slate-600 font-medium" id="countdown-desc-{{ $batchCode }}">
                                @if($isExpired)
                                    Batas pembayaran telah berakhir (<span class="font-bold text-slate-800">{{ $expiresAt->format('d M Y, H:i') }} WIB</span>)
                                @else
                                    Bayar sebelum <strong class="font-extrabold text-slate-800">{{ $expiresAt->format('d M Y, H:i') }} WIB</strong>
                                @endif
                            </span>
                        </div>
                        <div class="shrink-0 flex items-center gap-1.5 font-mono text-[11px] sm:text-xs font-black px-2.5 py-1 rounded-lg {{ $isExpired ? 'bg-slate-100 text-slate-500 border border-slate-200' : 'bg-white text-orange-600 border border-orange-200/80 shadow-2xs' }}"
                             id="countdown-container-{{ $batchCode }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $isExpired ? 'bg-slate-400' : 'bg-orange-500 animate-pulse' }}" id="timer-dot-{{ $batchCode }}"></span>
                            <span class="batch-countdown-timer" 
                                  data-expires-timestamp="{{ $expiresAt->timestamp }}"
                                  data-batch-code="{{ $batchCode }}"
                                  data-expires-format="{{ $expiresAt->format('d M Y, H:i') }} WIB"
                                  id="timer-{{ $batchCode }}">
                                {{ $isExpired ? '00:00:00' : 'Memuat...' }}
                            </span>
                        </div>
                    </div>

                    <!-- 2. Body Rincian Menu per Stand -->
                    <div class="p-3.5 sm:p-4 space-y-4">
                        @foreach($subOrders as $subOrder)
                        <div class="{{ !$loop->first ? 'pt-3.5 border-t border-slate-200/60' : '' }}">
                            @if($isMultiStand)
                            <!-- Stand Header Banner -->
                            <div class="flex items-center justify-between gap-2 mb-2.5 pb-1.5 border-b border-slate-100 bg-slate-50/50 p-2 rounded-xl">
                                <div class="flex items-center gap-1.5 min-w-0">
                                    <span class="px-2 py-0.5 rounded-md bg-orange-100 text-orange-700 font-black text-[11px] shrink-0">
                                        {{ $subOrder->stand->nomor_stand ?? 'Stand' }}
                                    </span>
                                    <strong class="text-xs font-extrabold text-slate-800 truncate">
                                        {{ $subOrder->stand->nama_stand ?? 'Stand Kantin' }}
                                    </strong>
                                    <span class="text-[10px] font-mono text-slate-400 font-bold">#{{ $subOrder->ticket_code }}</span>
                                </div>
                                <span class="text-xs font-black text-slate-700 font-mono shrink-0">
                                    Rp {{ number_format($subOrder->total_harga, 0, ',', '.') }}
                                </span>
                            </div>
                            @endif

                            <!-- Items in this stand -->
                            <div class="space-y-3 divide-y divide-slate-100">
                                @foreach($subOrder->items as $subItem)
                                <div class="pt-3 first:pt-0 flex items-start sm:items-center justify-between gap-3">
                                    <div class="flex items-start sm:items-center gap-3 min-w-0 flex-1">
                                        @if($subItem->menu && $subItem->menu->foto)
                                        <img src="{{ asset('storage/' . $subItem->menu->foto) }}" alt="{{ $subItem->menu->nama_menu }}"
                                             class="w-12 h-12 sm:w-14 sm:h-14 rounded-xl object-cover bg-slate-100 border border-slate-200/80 shrink-0">
                                        @else
                                        <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-xl bg-orange-50 border border-orange-100 flex items-center justify-center text-orange-500 shrink-0">
                                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                                            </svg>
                                        </div>
                                        @endif

                                        <div class="min-w-0 flex-1">
                                            <h3 class="font-extrabold text-xs sm:text-sm text-slate-800 leading-snug truncate">
                                                {{ $subItem->menu->nama_menu ?? 'Menu' }}
                                            </h3>
                                            <div class="flex items-center gap-1.5 mt-0.5 text-[11px] sm:text-xs text-slate-400">
                                                <span class="capitalize">{{ $subItem->menu->kategori ?? 'Umum' }}</span>
                                                <span>&bull;</span>
                                                <span class="font-semibold text-slate-600">{{ $subItem->jumlah }}x @ Rp {{ number_format($subItem->harga_satuan, 0, ',', '.') }}</span>
                                            </div>
                                            @if(!empty($subItem->notes))
                                            <p class="text-[10px] sm:text-[11px] text-amber-700 bg-amber-50 px-2 py-0.5 rounded-md inline-block mt-1 font-medium max-w-full truncate">
                                                Catatan: {{ $subItem->notes }}
                                            </p>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="text-right shrink-0 pt-0.5">
                                        <span class="text-xs sm:text-sm font-black text-slate-800 block font-mono">
                                            Rp {{ number_format($subItem->subtotal, 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach

                        <!-- Global Catatan if any -->
                        @if($item['catatan'])
                        <div class="pt-2">
                            <div class="p-2.5 bg-slate-50 rounded-xl border border-slate-100 flex items-start gap-2 text-[11px] text-slate-500">
                                <svg class="w-3.5 h-3.5 text-orange-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v4.018Z" />
                                </svg>
                                <span class="italic">Catatan: &ldquo;{{ $item['catatan'] }}&rdquo;</span>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- 3. Footer Total Tagihan & 1 Tombol Bayar Sekarang -->
                    <div class="p-3.5 sm:p-4 bg-slate-50/80 border-t border-slate-100">
                        <div class="flex items-center justify-between gap-2 pb-2.5 border-b border-slate-200/60">
                            <span class="text-[11px] sm:text-xs text-slate-500 truncate">
                                Pemesan: <strong class="text-slate-800">{{ $item['nama_pemesan'] }}</strong> ({{ $item['kelas'] }})
                            </span>
                            <div class="flex items-baseline gap-1.5 shrink-0">
                                <span class="text-[11px] text-slate-500 font-bold">Total Tagihan:</span>
                                <span class="text-base sm:text-lg font-black text-orange-600 font-mono">
                                    Rp {{ number_format($grossAmount, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        <!-- 1 Single Payment Button for Entire Multi-Stand Order -->
                        <div class="flex items-center gap-2.5 pt-2.5 w-full">
                            @if($isExpired)
                            <button type="button" disabled
                                    id="btn-pay-{{ $batchCode }}"
                                    class="flex-1 w-full py-2.5 sm:py-3 px-4 rounded-xl bg-slate-200 text-slate-400 font-black text-xs sm:text-sm cursor-not-allowed flex items-center justify-center gap-2">
                                <span>Waktu Pembayaran Habis</span>
                            </button>
                            @else
                            <button type="button" onclick="payWithSnap('{{ $batchCode }}', this)"
                                    id="btn-pay-{{ $batchCode }}"
                                    class="flex-1 w-full py-2.5 sm:py-3 px-4 rounded-xl bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white font-black text-xs sm:text-sm shadow-md shadow-orange-500/20 active:scale-[0.99] transition-all flex items-center justify-center cursor-pointer gap-2">
                                <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25a2.25 2.25 0 0 1 13.5 18v-2.25Z" />
                                </svg>
                                <span>Bayar Sekarang (Rp {{ number_format($grossAmount, 0, ',', '.') }})</span>
                            </button>
                            @endif

                            <button type="button" onclick="cancelOrder('{{ $batchCode }}', this)"
                                    class="py-2.5 sm:py-3 px-3.5 sm:px-4 rounded-xl bg-white hover:bg-rose-50 text-slate-600 hover:text-rose-600 border border-slate-200/90 font-extrabold text-xs sm:text-sm shadow-2xs active:scale-[0.99] transition-all flex items-center justify-center gap-1.5 cursor-pointer shrink-0"
                                    title="Batalkan Pesanan">
                                <svg class="w-3.5 h-3.5 text-slate-400 hover:text-rose-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                </svg>
                                <span>Batalkan</span>
                            </button>
                        </div>
                    </div>

                </div>
            @else
                @php
                    $order = $item['order'];
                    $isQris = $order->isQris();
                    $isLunas = $order->isLunas();
                    $isMenungguBayar = $isQris && ! $isLunas && $order->status !== 'dibatalkan';
                    $ticketCode = $order->ticket_code;
                    $batchCode = $order->batch_kode ?: ($order->parentOrder?->order_id ?: $order->kode_tr);
                @endphp
                <!-- SINGLE STAND / PAID ORDER CARD -->
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs hover:shadow-md transition-all overflow-hidden" 
                     id="order-card-{{ $order->id }}"
                     data-order-id="{{ $order->id }}"
                     data-status="{{ $order->status }}"
                     data-is-lunas="{{ $isLunas ? '1' : '0' }}"
                     data-is-qris="{{ $isQris ? '1' : '0' }}"
                     data-batch-code="{{ $batchCode }}">

                    <!-- 1. Header Stand & Tiket SubOrder -->
                    <div class="p-3.5 sm:p-4 bg-slate-50/70 border-b border-slate-100">
                        <div class="flex items-center justify-between gap-2">
                            <div class="flex items-center gap-2 min-w-0">
                                <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center shrink-0 shadow-2xs">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.614A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.015a2.993 2.993 0 0 0 2.25 1.015c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72L4.318 3.44A1.5 1.5 0 0 1 5.378 3h13.244a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72m-13.5 0a3 3 0 0 0 3.75-.614A2.993 2.993 0 0 0 9.75 9.75" />
                                    </svg>
                                </div>
                                <span class="font-extrabold text-sm sm:text-base text-slate-800 truncate">
                                    {{ $order->stand->nama_stand ?? 'Stand Kantin' }}
                                </span>
                            </div>

                            <!-- Right: Payment & Cooking Badges -->
                            <div class="flex items-center gap-1.5 shrink-0" id="order-badges-{{ $order->id }}">
                                @if($isLunas)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-lg sm:rounded-xl text-[10px] sm:text-xs font-black bg-emerald-500 text-white shadow-2xs">
                                    <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25a2.25 2.25 0 0 1 13.5 18v-2.25Z" />
                                    </svg>
                                    <span>QRIS</span>
                                </span>
                                @elseif(! $isQris)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-lg sm:rounded-xl text-[10px] sm:text-xs font-black bg-amber-500 text-white shadow-2xs">
                                    <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>
                                    <span>COD</span>
                                </span>
                                @endif

                                <!-- Status Pengerjaan Makanan -->
                                <span id="order-status-badge-{{ $order->id }}">
                                @if($order->status === 'siap_diambil')
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-lg sm:rounded-xl text-[10px] sm:text-xs font-black bg-emerald-50 text-emerald-700 border border-emerald-300 shadow-2xs">
                                    <span class="relative flex h-2 w-2 shrink-0">
                                        <span class="absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                                    </span>
                                    <span>Siap Diambil</span>
                                </span>
                                @elseif($order->status === 'diproses')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-lg sm:rounded-xl text-[10px] sm:text-xs font-black bg-orange-50 text-orange-700 border border-orange-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-orange-500 animate-pulse"></span>
                                    <span>Dimasak</span>
                                </span>
                                @elseif($order->status === 'pending' || $order->status === 'menunggu_pembayaran')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-lg sm:rounded-xl text-[10px] sm:text-xs font-black bg-amber-50 text-amber-700 border border-amber-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                    <span>{{ ($order->status === 'menunggu_pembayaran' || $isMenungguBayar) ? 'Belum Bayar' : 'Menunggu' }}</span>
                                </span>
                                @elseif($order->status === 'selesai')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-lg sm:rounded-xl text-[10px] sm:text-xs font-black bg-slate-100 text-slate-700 border border-slate-200">
                                    <svg class="w-3 h-3 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                    </svg>
                                    <span>Selesai</span>
                                </span>
                                @elseif($order->status === 'dibatalkan')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-lg sm:rounded-xl text-[10px] sm:text-xs font-black bg-rose-50 text-rose-700 border border-rose-200">
                                    <span>Dibatalkan</span>
                                </span>
                                @endif
                                </span>
                            </div>
                        </div>

                        <!-- Baris Bawah: Kode Tiket & Waktu Pemesanan -->
                        <div class="mt-2.5 pt-2 border-t border-slate-200/50 flex items-center justify-between text-[11px] sm:text-xs text-slate-500">
                            <div class="inline-flex items-center gap-1.5 font-mono font-bold bg-white px-2 py-0.5 rounded-lg border border-slate-200 shadow-2xs">
                                <span class="text-orange-600">#{{ $ticketCode }}</span>
                                <button type="button" onclick="copyOrderCode('{{ $ticketCode }}')" title="Salin Kode Tiket" class="text-slate-400 hover:text-orange-600 cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.888A2.25 2.25 0 0 0 13.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 0 1-.75.75H9a.75.75 0 0 1-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 0 1-2.25 2.25H6.75A2.25 2.25 0 0 1 4.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 0 1 1.927-.184" />
                                    </svg>
                                </button>
                            </div>
                            <span class="text-slate-400 font-medium">
                                {{ $order->created_at->format('d M Y, H:i') }} WIB
                            </span>
                        </div>
                    </div>

                    @if($isMenungguBayar)
                    @php
                        $singleExpiryHours = config('midtrans.expiry_hours', 24);
                        $singleExpiresAt = $order->created_at->copy()->addHours($singleExpiryHours);
                        $isSingleExpired = now()->greaterThanOrEqualTo($singleExpiresAt);
                    @endphp
                    <!-- 1.5. Countdown Bar Batas Waktu Pembayaran QRIS 24 Jam -->
                    <div class="px-3.5 sm:px-4 py-2 bg-gradient-to-r from-amber-50/90 via-orange-50/70 to-amber-50/90 border-b border-amber-200/70 flex items-center justify-between gap-2 text-xs">
                        <div class="flex items-center gap-1.5 text-slate-700 min-w-0">
                            <svg class="w-4 h-4 text-orange-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            <span class="truncate text-[11px] sm:text-xs text-slate-600 font-medium" id="countdown-desc-order-{{ $order->id }}">
                                @if($isSingleExpired)
                                    Batas pembayaran telah berakhir (<span class="font-bold text-slate-800">{{ $singleExpiresAt->format('d M Y, H:i') }} WIB</span>)
                                @else
                                    Bayar sebelum <strong class="font-extrabold text-slate-800">{{ $singleExpiresAt->format('d M Y, H:i') }} WIB</strong>
                                @endif
                            </span>
                        </div>
                        <div class="shrink-0 flex items-center gap-1.5 font-mono text-[11px] sm:text-xs font-black px-2.5 py-1 rounded-lg {{ $isSingleExpired ? 'bg-slate-100 text-slate-500 border border-slate-200' : 'bg-white text-orange-600 border border-orange-200/80 shadow-2xs' }}"
                             id="countdown-container-order-{{ $order->id }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $isSingleExpired ? 'bg-slate-400' : 'bg-orange-500 animate-pulse' }}" id="timer-dot-order-{{ $order->id }}"></span>
                            <span class="batch-countdown-timer" 
                                  data-expires-timestamp="{{ $singleExpiresAt->timestamp }}"
                                  data-batch-code="order-{{ $order->id }}"
                                  data-order-id="{{ $order->id }}"
                                  data-expires-format="{{ $singleExpiresAt->format('d M Y, H:i') }} WIB"
                                  id="timer-order-{{ $order->id }}">
                                {{ $isSingleExpired ? '00:00:00' : 'Memuat...' }}
                            </span>
                        </div>
                    </div>
                    @endif

                    <!-- 2. Body Daftar Menu Khusus Stand Tersebut -->
                    <div class="p-3.5 sm:p-4 space-y-3 divide-y divide-slate-100">
                        @foreach($order->items as $itemOrder)
                        <div class="pt-3 first:pt-0 flex items-start sm:items-center justify-between gap-3">
                            <div class="flex items-start sm:items-center gap-3 min-w-0 flex-1">
                                @if($itemOrder->menu && $itemOrder->menu->foto)
                                <img src="{{ asset('storage/' . $itemOrder->menu->foto) }}" alt="{{ $itemOrder->menu->nama_menu }}"
                                     class="w-12 h-12 sm:w-14 sm:h-14 rounded-xl object-cover bg-slate-100 border border-slate-200/80 shrink-0">
                                @else
                                <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-xl bg-orange-50 border border-orange-100 flex items-center justify-center text-orange-500 shrink-0">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                                    </svg>
                                </div>
                                @endif

                                <div class="min-w-0 flex-1">
                                    <h3 class="font-extrabold text-xs sm:text-sm text-slate-800 leading-snug truncate">
                                        {{ $itemOrder->menu->nama_menu ?? 'Menu' }}
                                    </h3>
                                    <div class="flex items-center gap-1.5 mt-0.5 text-[11px] sm:text-xs text-slate-400">
                                        <span class="capitalize">{{ $itemOrder->menu->kategori ?? 'Umum' }}</span>
                                        <span>&bull;</span>
                                        <span class="font-semibold text-slate-600">{{ $itemOrder->jumlah }}x @ Rp {{ number_format($itemOrder->harga_satuan, 0, ',', '.') }}</span>
                                    </div>
                                    @if(!empty($itemOrder->notes))
                                    <p class="text-[10px] sm:text-[11px] text-amber-700 bg-amber-50 px-2 py-0.5 rounded-md inline-block mt-1 font-medium max-w-full truncate">
                                        Catatan: {{ $itemOrder->notes }}
                                    </p>
                                    @endif
                                </div>
                            </div>

                            <div class="text-right shrink-0 pt-0.5">
                                <span class="text-xs sm:text-sm font-black text-slate-800 block">
                                    Rp {{ number_format($itemOrder->subtotal, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                        @endforeach

                        @if($order->catatan)
                        <div class="pt-2.5">
                            <div class="p-2.5 bg-slate-50 rounded-xl border border-slate-100 flex items-start gap-2 text-[11px] text-slate-500">
                                <svg class="w-3.5 h-3.5 text-orange-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v4.018Z" />
                                </svg>
                                <span class="italic">Catatan: &ldquo;{{ $order->catatan }}&rdquo;</span>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- 3. Footer Total Stand & Tombol Aksi -->
                    <div class="p-3.5 sm:p-4 bg-slate-50/80 border-t border-slate-100">
                        <div class="flex items-center justify-between gap-2 pb-2.5 border-b border-slate-200/60">
                            <span class="text-[11px] sm:text-xs text-slate-500 truncate">
                                Pemesan: <strong class="text-slate-800">{{ $order->nama_pemesan }}</strong> ({{ $order->kelas }})
                            </span>
                            <div class="flex items-baseline gap-1.5 shrink-0">
                                <span class="text-[11px] text-slate-500 font-bold">Total:</span>
                                <span class="text-base sm:text-lg font-black text-orange-600 font-mono">
                                    Rp {{ number_format($order->total_harga, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        <div class="flex items-center gap-2.5 pt-2.5 w-full" id="order-actions-{{ $order->id }}">
                            @if($isQris && ! $isLunas && $order->status !== 'dibatalkan')
                                @if(isset($isSingleExpired) && $isSingleExpired)
                                <button type="button" disabled
                                        id="btn-pay-{{ $order->id }}"
                                        class="flex-1 w-full py-2.5 sm:py-3 px-4 rounded-xl bg-slate-200 text-slate-400 font-black text-xs sm:text-sm cursor-not-allowed flex items-center justify-center gap-2">
                                    <span>Waktu Pembayaran Habis</span>
                                </button>
                                @else
                                <button type="button" onclick="payWithSnap('{{ $batchCode }}', this)"
                                        id="btn-pay-{{ $order->id }}"
                                        class="flex-1 w-full py-2.5 sm:py-3 px-4 rounded-xl bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white font-black text-xs sm:text-sm shadow-md shadow-orange-500/20 active:scale-[0.99] transition-all flex items-center justify-center cursor-pointer gap-2">
                                    <span>Bayar Sekarang</span>
                                </button>
                                @endif
                            @endif

                            @php
                                $receiptPayload = [
                                    'id' => $order->id,
                                    'kode_tr' => $order->kode_tr,
                                    'ticket_code' => $ticketCode,
                                    'batch_code' => $batchCode,
                                    'stand_name' => $order->stand->nama_stand ?? 'Stand Kantin',
                                    'stand_num' => $order->stand->nomor_stand ?? '-',
                                    'nama_pemesan' => $order->nama_pemesan,
                                    'kelas' => $order->kelas,
                                    'created_at' => $order->created_at->format('d M Y, H:i') . ' WIB',
                                    'is_qris' => $isQris,
                                    'is_lunas' => $isLunas,
                                    'status' => $order->status,
                                    'total_harga' => $order->total_harga,
                                    'items' => $order->items->map(fn($it) => [
                                        'name' => $it->menu->nama_menu ?? 'Menu',
                                        'qty' => $it->jumlah,
                                        'price' => $it->harga_satuan,
                                        'subtotal' => $it->subtotal,
                                        'notes' => $it->notes,
                                    ]),
                                    'catatan' => $order->catatan,
                                    'detail_url' => route('order.status', $order->kode_tr)
                                ];
                            @endphp
                            <button type="button"
                                    data-receipt="{{ json_encode($receiptPayload) }}"
                                    onclick="openDigitalReceiptModal(JSON.parse(this.dataset.receipt))"
                                    class="flex-1 w-full py-2.5 sm:py-3 px-4 rounded-xl bg-white hover:bg-orange-50 text-slate-700 hover:text-orange-600 border border-slate-200/90 font-extrabold text-xs sm:text-sm shadow-2xs active:scale-[0.99] transition-all flex items-center justify-center gap-2 cursor-pointer">
                                <svg class="w-4 h-4 text-orange-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                </svg>
                                <span>Lihat Struk</span>
                            </button>
                        </div>
                    </div>

                </div>
            @endif
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
        <div class="bg-white rounded-2xl border border-slate-200/90 py-14 sm:py-20 px-6 text-center shadow-xs flex flex-col items-center justify-center"
             style="min-height: 420px; display: flex; flex-direction: column; align-items: center; justify-content: center;">
            <div class="w-20 h-20 rounded-full bg-orange-50 border border-orange-100 text-orange-500 flex items-center justify-center mb-4 shadow-inner">
                <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                </svg>
            </div>
            @if($statusFilter === 'belum_bayar')
            <h3 class="text-base sm:text-lg font-black text-slate-900 mb-1.5">Tidak Ada Tagihan QRIS</h3>
            <p class="text-xs sm:text-sm text-slate-500 max-w-md mx-auto mb-6 leading-relaxed">
                Semua pesanan QRIS kamu sudah dibayar atau kamu belum memiliki pesanan QRIS aktif.
            </p>
            @else
            <h3 class="text-base sm:text-lg font-black text-slate-900 mb-1.5">Belum Ada Pesanan</h3>
            <p class="text-xs sm:text-sm text-slate-500 max-w-md mx-auto mb-6 leading-relaxed">
                Yuk pesan makanan atau minuman favoritmu di Kantin Skanic sekarang tanpa antre!
            </p>
            @endif

            <div class="flex items-center justify-center">
                <a href="{{ route('katalog.index') }}#katalog-section" class="px-6 py-2.5 rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-extrabold text-xs sm:text-sm shadow-md shadow-orange-500/25 active:scale-95 transition-all">
                    Pesan Sekarang
                </a>
            </div>
        </div>
        @endguest
        @endif

    </div>
</div>

<!-- Toast Notifikasi Salin Kode PO -->
<div id="copy-toast" class="fixed bottom-20 md:bottom-6 left-1/2 -translate-x-1/2 bg-slate-900 text-white px-5 py-2.5 rounded-2xl shadow-2xl text-xs font-bold transition-all transform translate-y-20 opacity-0 pointer-events-none z-[60] flex items-center gap-2">
    <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
    </svg>
    <span id="copy-toast-text">Kode pesanan berhasil disalin!</span>
</div>

<!-- Modal Popup Struk Digital Pengambilan Stand (Floating Modal - Tanpa Pindah Halaman & Tanpa QR) -->
<div id="digital-receipt-modal" class="fixed inset-0 z-[60] overflow-y-auto p-3 sm:p-4 bg-slate-900/75 backdrop-blur-xs transition-opacity duration-200 opacity-0 pointer-events-none flex items-center justify-center">
    <div id="digital-receipt-card" class="bg-white rounded-[2rem] max-w-md w-full shadow-2xl border border-slate-200 overflow-hidden transform scale-95 transition-all duration-200 max-h-[88vh] sm:max-h-[90vh] flex flex-col relative my-auto">

        <!-- Scrollable Receipt Body (Simpel, bersih, persis struk floating kemaren tanpa QR) -->
        <div class="overflow-y-auto flex-1 min-h-0 printable-receipt overscroll-contain" id="receipt-printable-body">

            <!-- Top Header Colored Banner (Dynamic Status Gradients) - Langsung di atas kartu seperti struk asli -->
            <div id="receipt-header-banner" class="p-6 text-center text-white relative overflow-hidden bg-gradient-to-br from-orange-500 via-amber-500 to-orange-600 transition-colors">
                <div class="absolute -right-6 -bottom-6 w-28 h-28 rounded-full bg-white/10 blur-xl pointer-events-none"></div>
                <div class="absolute -left-6 -top-6 w-28 h-28 rounded-full bg-black/10 blur-xl pointer-events-none"></div>

                <!-- Tombol Tutup (X) Melayang Cantik di Kanan Atas Header -->
                <button type="button" onclick="closeDigitalReceiptModal()" class="absolute top-3.5 right-3.5 z-20 w-8 h-8 rounded-full bg-black/20 hover:bg-black/35 active:scale-95 text-white flex items-center justify-center transition-all cursor-pointer backdrop-blur-xs shadow-xs receipt-no-print" title="Tutup Struk">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>

                <span class="inline-block text-[10px] font-extrabold uppercase tracking-widest text-white/85 mb-1.5 bg-black/15 px-3.5 py-1 rounded-full backdrop-blur-xs">
                    KODE PESANAN KANTIN
                </span>

                <div class="text-3xl sm:text-4xl font-black tracking-tight flex items-center justify-center gap-2.5 my-1">
                    <span class="font-mono drop-shadow-xs" id="receipt-ticket-code">#SKNC-0000</span>
                    <button type="button" onclick="copyModalTicketCode()" title="Salin Kode Pesanan"
                        class="p-2 rounded-xl bg-white/20 hover:bg-white/30 active:scale-90 transition-all text-white backdrop-blur-sm shadow-xs cursor-pointer">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.888A2.25 2.25 0 0 0 13.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 0 1-.75.75H9a.75.75 0 0 1-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 0 1-2.25 2.25H6.75A2.25 2.25 0 0 1 4.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 0 1 1.927-.184" />
                        </svg>
                    </button>
                </div>

                <!-- High-Contrast Status Pembayaran Badge -->
                <div class="mt-2.5 flex items-center justify-center" id="receipt-payment-badge">
                    <!-- Dynamic payment badge -->
                </div>

                <p class="text-xs text-white/90 mt-2 font-medium">
                    Tunjukkan kode ini kepada penjual saat mengambil pesanan
                </p>
            </div>

            <!-- Perforated Receipt Divider Effect -->
            <div class="relative flex items-center justify-between px-3 -my-3 z-10">
                <div class="w-6 h-6 rounded-full bg-slate-900/75 -ml-6 shadow-inner"></div>
                <div class="flex-1 border-t-2 border-dashed border-slate-200 mx-2"></div>
                <div class="w-6 h-6 rounded-full bg-slate-900/75 -mr-6 shadow-inner"></div>
            </div>

            <!-- Visual Status Stepper Tracker -->
            <div class="p-5 sm:p-6 bg-slate-50/70 border-b border-slate-200/80">
                <h4 class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-4 text-center">
                    Visual Status Pesanan
                </h4>

                <div id="receipt-stepper-container">
                    <!-- Stepper Bar -->
                </div>

                <div id="receipt-alert-container">
                    <!-- Ready or Cooking Alert -->
                </div>
            </div>

            <!-- Order Information Details Grid -->
            <div class="p-5 sm:p-6 space-y-5">
                <div class="grid grid-cols-2 gap-3.5 pb-4 border-b border-slate-100 text-xs">
                    <div>
                        <span class="text-slate-400 block mb-0.5 font-bold uppercase tracking-wider text-[10px]">Nama Pemesan</span>
                        <strong class="font-extrabold text-slate-800 text-sm block" id="receipt-customer-name">-</strong>
                        <span class="text-slate-500 block font-medium mt-0.5" id="receipt-customer-class">Kelas: -</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-0.5 font-bold uppercase tracking-wider text-[10px]">Stand Kantin</span>
                        <strong class="font-extrabold text-slate-800 text-sm block" id="receipt-stand-name">-</strong>
                        <span class="text-orange-600 font-bold block mt-0.5" id="receipt-stand-num">-</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-0.5 font-bold uppercase tracking-wider text-[10px]">Metode Pembayaran</span>
                        <div id="receipt-payment-method-desc">
                            <!-- Dynamic Payment Method text -->
                        </div>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-0.5 font-bold uppercase tracking-wider text-[10px]">Waktu Pesan</span>
                        <span class="text-slate-700 font-bold block" id="receipt-created-at">-</span>
                    </div>
                </div>

                <!-- Optional Catatan Pembeli -->
                <div id="receipt-notes-box" class="hidden p-3 bg-amber-50/80 rounded-2xl border border-amber-200 text-xs">
                    <span class="font-bold text-amber-800 block mb-0.5">Catatan Tambahan:</span>
                    <p class="text-amber-900 italic font-medium" id="receipt-notes-text">-</p>
                </div>

                <!-- Itemized Order Table -->
                <div>
                    <h4 class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-2.5">Rincian Menu Pesanan</h4>
                    <div id="receipt-items-list" class="space-y-1.5 text-xs">
                        <!-- Items populated via JS -->
                    </div>
                </div>

                <!-- Total Pembayaran -->
                <div class="pt-3 border-t-2 border-dashed border-slate-200 flex items-center justify-between">
                    <span class="text-xs font-black text-slate-700 uppercase tracking-wider">Total Pembayaran</span>
                    <span class="text-xl font-black text-orange-600 font-mono tracking-tight" id="receipt-total">Rp 0</span>
                </div>
            </div>

        </div>

        <!-- Sticky Bottom Actions Bar (Selalu terlihat, rapi, dan tombol tidak terpotong) -->
        <div class="p-3.5 sm:p-4 bg-slate-50 border-t border-slate-200 flex items-center justify-between gap-3 shrink-0 receipt-no-print">
            <button type="button" onclick="window.print()" class="px-4 py-2.5 rounded-xl bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 font-bold text-xs shadow-2xs transition-all active:scale-95 cursor-pointer flex items-center gap-1.5">
                <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
                </svg>
                <span>Cetak Struk</span>
            </button>
            <button type="button" onclick="closeDigitalReceiptModal()" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white font-black text-xs shadow-md shadow-orange-500/20 transition-all active:scale-95 cursor-pointer">
                Tutup
            </button>
        </div>
    </div>
</div>

<!-- Modal Validasi Pembayaran QRIS Sukses (Tampilan Persis Seperti di Sidebar Keranjang) -->
<div id="riwayat-qris-success-modal" class="fixed inset-0 z-[60] overflow-y-auto p-3 sm:p-4 bg-slate-900/75 backdrop-blur-xs transition-opacity duration-200 opacity-0 pointer-events-none flex items-center justify-center">
    <div id="riwayat-qris-success-card" class="bg-slate-50/70 rounded-[2rem] max-w-md w-full shadow-2xl border border-slate-200 overflow-hidden transform scale-95 transition-all duration-200 max-h-[90vh] flex flex-col relative my-auto">

        <!-- Header: Clean, Minimal, Cohesive with KantinSkanic -->
        <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between bg-white shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-orange-50 text-orange-600 border border-orange-100 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5 text-orange-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-extrabold text-slate-800">Status Pembayaran</h2>
                    <p class="text-[11px] text-emerald-600 font-bold flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        QRIS Terverifikasi Lunas
                    </p>
                </div>
            </div>
            <button type="button" onclick="closeRiwayatQrisSuccessModal()" class="p-2 text-slate-400 hover:text-slate-600 rounded-xl hover:bg-slate-100 transition-colors cursor-pointer" aria-label="Tutup">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Body Content: Clean Modern E-Receipt & Stand Ticket -->
        <div class="flex-1 overflow-y-auto p-4 sm:p-5 space-y-4 pb-6" style="scrollbar-width: thin; scrollbar-color: #cbd5e1 transparent;">

            <!-- Hero: Refined Minimal Success Checkmark -->
            <div class="text-center pt-1 pb-1">
                <div class="relative w-16 h-16 mx-auto flex items-center justify-center mb-2.5">
                    <div class="absolute inset-0 rounded-full opacity-25" style="background-color: #10b981;"></div>
                    <div class="relative w-14 h-14 rounded-full bg-emerald-500 text-white flex items-center justify-center shadow-lg shadow-emerald-500/25">
                        <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" style="stroke: #ffffff;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                        </svg>
                    </div>
                </div>
                <h3 class="text-xl font-black text-slate-900 tracking-tight">Pembayaran Sukses!</h3>
                <p class="text-xs text-slate-500 mt-0.5">Pesanan otomatis diteruskan ke penjual stand</p>
            </div>

            <!-- Modern E-Receipt Card: Soft Warm KantinSkanic Aesthetic (Clean & Light) -->
            <div class="bg-white rounded-3xl border border-orange-200/80 p-4 sm:p-5 shadow-xs relative overflow-hidden">
                <!-- Subtle Orange Gradient Accent Line -->
                <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-orange-400 via-amber-400 to-orange-500"></div>

                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-black tracking-wider text-slate-400 uppercase">Total Pembayaran</span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200/80 text-[10px] font-black">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        LUNAS
                    </span>
                </div>

                <!-- Total Amount -->
                <div class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight mt-1" id="riwayat-qris-success-amount">
                    Rp 0
                </div>

                <!-- Receipt Metadata (Clean Details Grid) -->
                <div class="mt-3.5 pt-3 border-t border-dashed border-slate-200 space-y-2 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400 font-medium text-[11px]">Nama Pemesan</span>
                        <span class="font-bold text-slate-800 text-[11px] truncate max-w-[200px]" id="riwayat-qris-success-buyer">-</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-slate-400 font-medium text-[11px]">Metode Pembayaran</span>
                        <span class="font-bold text-slate-700 text-[11px] flex items-center gap-1.5">
                            QRIS
                        </span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-slate-400 font-medium text-[11px]">Waktu Transaksi</span>
                        <span class="text-slate-500 font-medium text-[11px]" id="riwayat-qris-success-time">-</span>
                    </div>
                </div>
            </div>

            <!-- Stand Tickets Section -->
            <div id="riwayat-qris-success-tickets-container" class="space-y-2.5">
                <!-- Injected dynamically by JS -->
            </div>

        </div>

        <!-- Sticky Bottom CTA -->
        <div class="p-4 border-t border-slate-100 bg-white space-y-2 shrink-0 shadow-[0_-4px_20px_rgba(0,0,0,0.03)]">
            <a id="btn-riwayat-qris-success-history" href="{{ route('order.history', ['status' => 'pending']) }}"
               onclick="try { sessionStorage.removeItem('riwayat_qris_success_payload'); } catch(e){}"
               class="w-full py-3.5 px-4 rounded-2xl bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 active:scale-[0.99] text-white font-extrabold text-sm shadow-md shadow-orange-500/25 transition-all flex items-center justify-center gap-2 cursor-pointer text-center">
                <span>Lihat Status Pesanan</span>
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                </svg>
            </a>
            <button type="button" onclick="closeRiwayatQrisSuccessModal()"
                    class="w-full py-2.5 px-4 rounded-xl bg-slate-50 hover:bg-slate-100 active:scale-[0.99] text-slate-600 font-bold text-xs transition-colors cursor-pointer text-center">
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
    // Auto-scroll tab container agar tab aktif (misal yang paling pojok) otomatis bergeser ke tengah
    function autoScrollActiveTab(smooth = false) {
        const container = document.getElementById('shopee-tabs-container');
        if (!container) return;
        const activeTab = container.querySelector('.tab-item[data-active="true"]');
        if (!activeTab) return;

        const scrollTarget = activeTab.offsetLeft - (container.clientWidth / 2) + (activeTab.offsetWidth / 2);
        container.scrollTo({
            left: Math.max(0, scrollTarget),
            behavior: smooth ? 'smooth' : 'auto'
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            autoScrollActiveTab(false);
            setTimeout(() => autoScrollActiveTab(true), 80);
            try {
                const savedRiwayatSuccess = sessionStorage.getItem('riwayat_qris_success_payload');
                if (savedRiwayatSuccess) {
                    showRiwayatQrisSuccessModal(JSON.parse(savedRiwayatSuccess));
                }
            } catch (e) {}
        });
    } else {
        autoScrollActiveTab(false);
        setTimeout(() => autoScrollActiveTab(true), 80);
        try {
            const savedRiwayatSuccess = sessionStorage.getItem('riwayat_qris_success_payload');
            if (savedRiwayatSuccess) {
                showRiwayatQrisSuccessModal(JSON.parse(savedRiwayatSuccess));
            }
        } catch (e) {}
    }

    // Saat tab diklik, kontainer langsung bergeser ke tengah tab tersebut
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.tab-item').forEach(tab => {
            tab.addEventListener('click', function() {
                const container = document.getElementById('shopee-tabs-container');
                if (container) {
                    const scrollTarget = this.offsetLeft - (container.clientWidth / 2) + (this.offsetWidth / 2);
                    container.scrollTo({
                        left: Math.max(0, scrollTarget),
                        behavior: 'smooth'
                    });
                }
            });
        });
    });

    let currentModalTicketCode = '';
    let currentModalReceiptData = null;
    let isShowingSuccessModal = false;

    function copyModalTicketCode() {
        if (currentModalTicketCode) {
            copyOrderCode(currentModalTicketCode);
        }
    }

    function copyOrderCode(code) {
        if (!code) return;
        navigator.clipboard.writeText(code).then(() => {
            showToast('Kode Tiket #' + code + ' berhasil disalin!');
        }).catch(() => {
            prompt('Salin kode tiket:', code);
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

    function renderReceiptStepper(status) {
        if (status === 'dibatalkan') {
            return `
                <div class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-center">
                    <span class="text-rose-700 font-extrabold text-xs block">Pesanan Ini Telah Dibatalkan</span>
                    <span class="text-[11px] text-rose-600 mt-0.5 block">Silakan hubungi penjual stand atau buat pesanan baru.</span>
                </div>
            `;
        }

        const steps = [
            { key: 'pending', label: 'Menunggu' },
            { key: 'siap_diambil', label: 'Siap Diambil' },
            { key: 'selesai', label: 'Selesai' }
        ];
        const statusOrder = ['pending', 'siap_diambil', 'selesai'];
        let currentIndex = statusOrder.indexOf(status);
        if (status === 'diproses') currentIndex = 0;

        const progressPercent = currentIndex <= 0 ? 0 : (currentIndex === 1 ? 50 : 100);

        let html = `
            <div class="relative flex items-center justify-between max-w-xs mx-auto">
                <div class="absolute top-3.5 left-3.5 right-3.5 h-1 bg-slate-200 rounded-full -z-0"></div>
                <div class="absolute top-3.5 left-3.5 h-1 bg-gradient-to-r from-orange-500 to-amber-500 rounded-full transition-all duration-500 -z-0" style="width: ${progressPercent}%;"></div>
        `;

        steps.forEach((step, idx) => {
            const isPassed = currentIndex >= idx;
            const isCurrent = currentIndex === idx;
            html += `
                <div class="flex flex-col items-center relative z-10">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center text-[11px] font-black transition-all shadow-xs ${isCurrent ? 'bg-orange-500 text-white ring-4 ring-orange-100 scale-105' : (isPassed ? 'bg-orange-500 text-white' : 'bg-slate-200 text-slate-500')}">
                        ${isPassed && !isCurrent ? '<svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>' : (idx + 1)}
                    </div>
                    <span class="text-[9px] font-extrabold mt-1.5 tracking-tight ${isCurrent ? 'text-orange-600' : (isPassed ? 'text-slate-800' : 'text-slate-400')}">
                        ${step.label}
                    </span>
                </div>
            `;
        });

        html += `</div>`;
        return html;
    }

    function renderReceiptAlert(status, standName, standNum) {
        if (status === 'siap_diambil') {
            return `
                <div class="mt-4 p-3.5 rounded-2xl bg-emerald-500 text-white shadow-md shadow-emerald-500/20 flex items-center gap-3 animate-pulse">
                    <div class="w-8 h-8 rounded-xl bg-white text-emerald-600 flex items-center justify-center shrink-0 font-black text-base shadow-xs">
                        🔔
                    </div>
                    <div>
                        <h5 class="text-xs font-black">Makanan Anda Sudah Siap!</h5>
                        <p class="text-[11px] text-emerald-50 mt-0.5">Silakan langsung ambil di <strong>${standName} (${standNum || '-'})</strong>.</p>
                    </div>
                </div>
            `;
        } else if (status === 'diproses') {
            return `
                <div class="mt-3.5 p-2.5 rounded-xl bg-amber-50 border border-amber-200 flex items-center gap-2 text-xs text-amber-800 font-semibold">
                    <svg class="w-4 h-4 text-amber-600 shrink-0 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>Penjual sedang menyiapkan pesanan Anda. Tunggu status berubah menjadi Siap Diambil.</span>
                </div>
            `;
        }
        return '';
    }

    function updateReceiptModalDynamicContent(data) {
        if (!data) return;

        // Dynamic Header Banner Gradient based on Status
        const banner = document.getElementById('receipt-header-banner');
        if (banner) {
            banner.className = 'p-6 text-center text-white relative overflow-hidden transition-colors ' + (
                data.status === 'siap_diambil' ? 'bg-gradient-to-br from-emerald-500 via-teal-600 to-emerald-700' :
                data.status === 'diproses' ? 'bg-gradient-to-br from-amber-500 via-orange-500 to-amber-600' :
                data.status === 'selesai' ? 'bg-gradient-to-br from-slate-700 via-slate-800 to-slate-900' :
                data.status === 'dibatalkan' ? 'bg-gradient-to-br from-rose-600 via-red-600 to-rose-700' :
                'bg-gradient-to-br from-orange-500 via-amber-500 to-orange-600'
            );
        }

        const ticketElem = document.getElementById('receipt-ticket-code');
        if (ticketElem && (data.ticket_code || data.kode_tr)) {
            ticketElem.textContent = '#' + (data.ticket_code || data.kode_tr);
        }

        // Determine effective display status
        let effectiveStatus = data.status;
        if (data.is_qris && !data.is_lunas && data.status !== 'dibatalkan') {
            effectiveStatus = 'menunggu_pembayaran';
        } else if (data.status === 'pending' || data.status === 'menunggu') {
            effectiveStatus = 'pending';
        }

        const statusBadgeMap = {
            menunggu_pembayaran: { label: 'Belum Bayar', cls: 'bg-amber-500 border-amber-200', dot: true },
            pending: { label: 'Menunggu', cls: 'bg-amber-500 border-amber-200', dot: true },
            diproses: { label: 'Sedang Dimasak', cls: 'bg-orange-500 border-orange-200', dot: true },
            siap_diambil: { label: 'Siap Diambil', cls: 'bg-emerald-500 border-emerald-200', dot: true },
            selesai: { label: 'Selesai', cls: 'bg-slate-600 border-slate-400', dot: false },
            dibatalkan: { label: 'Dibatalkan', cls: 'bg-rose-500 border-rose-200', dot: false }
        };
        const statusInfo = statusBadgeMap[effectiveStatus] || { label: effectiveStatus || '-', cls: 'bg-amber-500 border-amber-200', dot: false };

        const payBadge = document.getElementById('receipt-payment-badge');
        if (payBadge) {
            payBadge.innerHTML = `
                <div class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full ${statusInfo.cls} text-white shadow-md border-2 font-black text-xs sm:text-sm tracking-wide uppercase transition-all duration-300">
                    ${statusInfo.dot ? '<span class="w-2 h-2 rounded-full bg-white animate-pulse"></span>' : ''}
                    <span>${statusInfo.label}</span>
                </div>
            `;
        }

        const payDesc = document.getElementById('receipt-payment-method-desc');
        if (payDesc) {
            payDesc.innerHTML = `<strong class="font-extrabold text-slate-800 text-sm block">${data.is_qris ? 'QRIS' : 'COD'}</strong>`;
        }

        // Stepper & Alert
        const stepperContainer = document.getElementById('receipt-stepper-container');
        if (stepperContainer) {
            stepperContainer.innerHTML = renderReceiptStepper(data.status);
        }
        const alertContainer = document.getElementById('receipt-alert-container');
        if (alertContainer) {
            alertContainer.innerHTML = renderReceiptAlert(data.status, data.stand_name, data.stand_num);
        }
    }

    // Modal Struk Pengambilan Digital (Floating - Tanpa QRIS/QR Code)
    function openDigitalReceiptModal(data) {
        const modal = document.getElementById('digital-receipt-modal');
        const card = document.getElementById('digital-receipt-card');
        if (!modal || !card) return;

        currentModalReceiptData = { ...data };
        currentModalTicketCode = data.ticket_code || data.kode_tr || '';

        // Dynamic Header & Status Badge
        updateReceiptModalDynamicContent(currentModalReceiptData);

        document.getElementById('receipt-stand-name').textContent = data.stand_name || 'Stand Kantin';
        document.getElementById('receipt-stand-num').textContent = data.stand_num && data.stand_num !== '-' ? data.stand_num : '';
        document.getElementById('receipt-customer-name').textContent = data.nama_pemesan || 'Siswa';
        document.getElementById('receipt-customer-class').textContent = 'Kelas: ' + (data.kelas || '-');
        document.getElementById('receipt-created-at').textContent = data.created_at || '-';
        document.getElementById('receipt-total').textContent = 'Rp ' + Number(data.total_harga || 0).toLocaleString('id-ID');

        // Items list
        const itemsList = document.getElementById('receipt-items-list');
        itemsList.innerHTML = '';
        (data.items || []).forEach(item => {
            const row = document.createElement('div');
            row.className = 'flex items-center justify-between text-xs py-2 border-b border-dashed border-slate-100 last:border-none';
            row.innerHTML = `
                <div class="flex-1 pr-2">
                    <span class="font-bold text-slate-800 text-xs sm:text-sm block">${item.name}</span>
                    <span class="text-slate-400 text-[11px]">Rp ${Number(item.price).toLocaleString('id-ID')} &times; ${item.qty} porsi</span>
                    ${item.notes ? `<p class="italic text-amber-700 text-[11px] mt-0.5 font-medium">"${item.notes}"</p>` : ''}
                </div>
                <span class="font-extrabold text-slate-800 text-xs sm:text-sm">
                    Rp ${Number(item.subtotal).toLocaleString('id-ID')}
                </span>
            `;
            itemsList.appendChild(row);
        });

        // Notes box
        const notesBox = document.getElementById('receipt-notes-box');
        const notesText = document.getElementById('receipt-notes-text');
        if (data.catatan) {
            notesText.textContent = data.catatan;
            notesBox.classList.remove('hidden');
        } else {
            notesBox.classList.add('hidden');
        }

        // Reset scroll position to top
        const scrollBody = document.getElementById('receipt-printable-body');
        if (scrollBody) scrollBody.scrollTop = 0;
        modal.scrollTop = 0;

        modal.classList.remove('opacity-0', 'pointer-events-none');
        card.classList.remove('scale-95');
        card.classList.add('scale-100');

        // Immediate poll check
        pollActiveOrderStatuses();
    }

    function closeDigitalReceiptModal() {
        const modal = document.getElementById('digital-receipt-modal');
        const card = document.getElementById('digital-receipt-card');
        if (!modal || !card) return;

        currentModalReceiptData = null;
        currentModalTicketCode = '';

        card.classList.remove('scale-100');
        card.classList.add('scale-95');
        modal.classList.add('opacity-0', 'pointer-events-none');
    }

    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('digital-receipt-modal');
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeDigitalReceiptModal();
                }
            });
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeDigitalReceiptModal();
        }
    });

    // Sinkronisasi status order otomatis ke backend
    async function syncOrderStatus(orderId, result, forceConfirm) {
        try {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            await fetch(`/api/orders/${orderId}/sync-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf || ''
                },
                body: JSON.stringify({ result: result || {}, force_confirm: !!forceConfirm })
            });
        } catch (e) {
            console.error('syncOrderStatus error:', e);
        }
    }

    // Real-time Status Polling for Active Orders
    const activeOrderCards = document.querySelectorAll('[data-order-id], [data-order-ids]');
    const activeOrderIds = [];
    activeOrderCards.forEach(card => {
        const isLunas = card.getAttribute('data-is-lunas') === '1';
        if (card.hasAttribute('data-order-ids')) {
            if (!isLunas) {
                const ids = card.getAttribute('data-order-ids').split(',').map(s => parseInt(s.trim())).filter(Boolean);
                ids.forEach(id => { if (!activeOrderIds.includes(id)) activeOrderIds.push(id); });
            }
        } else if (card.hasAttribute('data-order-id')) {
            const status = card.getAttribute('data-status');
            if (['menunggu_pembayaran', 'pending', 'diproses', 'siap_diambil'].includes(status) || !isLunas) {
                const oId = parseInt(card.getAttribute('data-order-id'));
                if (oId && !activeOrderIds.includes(oId)) activeOrderIds.push(oId);
            }
        }
    });

    let isPolling = false;
    async function pollActiveOrderStatuses() {
        if (isPolling) return;

        const idsToPoll = [...activeOrderIds];
        const codesToPoll = [];

        if (currentModalReceiptData) {
            const mId = parseInt(currentModalReceiptData.id);
            if (mId && !idsToPoll.includes(mId)) {
                idsToPoll.push(mId);
            }
            if (currentModalReceiptData.ticket_code && !codesToPoll.includes(currentModalReceiptData.ticket_code)) {
                codesToPoll.push(currentModalReceiptData.ticket_code);
            }
            if (currentModalReceiptData.kode_tr && !codesToPoll.includes(currentModalReceiptData.kode_tr)) {
                codesToPoll.push(currentModalReceiptData.kode_tr);
            }
        }

        if (idsToPoll.length === 0 && codesToPoll.length === 0) return;

        isPolling = true;
        try {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const res = await fetch('{{ route("api.orders.realtime-statuses") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf || ''
                },
                body: JSON.stringify({ 
                    order_ids: idsToPoll,
                    order_codes: codesToPoll 
                })
            });

            if (res.ok) {
                const data = await res.json();
                if (data.success && Array.isArray(data.orders)) {
                    data.orders.forEach(ord => {
                        // 1. Update Card in DOM if on page
                        const card = document.getElementById(`order-card-${ord.id}`);
                        if (card) {
                            const prevStatus = card.getAttribute('data-status');
                            const prevLunas = card.getAttribute('data-is-lunas') === '1';

                            if (prevStatus !== ord.status || prevLunas !== ord.is_lunas) {
                                card.setAttribute('data-status', ord.status);
                                card.setAttribute('data-is-lunas', ord.is_lunas ? '1' : '0');
                                updateOrderBadgesDOM(ord);

                                const receiptBtn = card.querySelector('button[data-receipt]');
                                if (receiptBtn) {
                                    try {
                                        const rData = JSON.parse(receiptBtn.getAttribute('data-receipt'));
                                        rData.status = ord.status;
                                        rData.is_lunas = ord.is_lunas;
                                        rData.is_qris = ord.is_qris;
                                        receiptBtn.setAttribute('data-receipt', JSON.stringify(rData));
                                    } catch(e) {}
                                }
                            }
                        }

                        // Also check if any batch card contains this paid order
                        if (ord.is_lunas) {
                            document.querySelectorAll('[data-order-ids]').forEach(batchCard => {
                                const bIds = batchCard.getAttribute('data-order-ids').split(',').map(s => parseInt(s.trim())).filter(Boolean);
                                if (bIds.includes(parseInt(ord.id))) {
                                    batchCard.setAttribute('data-is-lunas', '1');
                                }
                            });
                        }

                        // 2. Real-time update open receipt modal
                        if (currentModalReceiptData) {
                            const isMatch = (currentModalReceiptData.id && parseInt(currentModalReceiptData.id) === parseInt(ord.id)) ||
                                (currentModalReceiptData.ticket_code && (currentModalReceiptData.ticket_code === ord.ticket_code || currentModalReceiptData.ticket_code === ord.kode_tr)) ||
                                (currentModalReceiptData.kode_tr && (currentModalReceiptData.kode_tr === ord.kode_tr || currentModalReceiptData.kode_tr === ord.ticket_code));

                            if (isMatch) {
                                if (currentModalReceiptData.status !== ord.status || currentModalReceiptData.is_lunas !== ord.is_lunas) {
                                    currentModalReceiptData.status = ord.status;
                                    currentModalReceiptData.is_lunas = ord.is_lunas;
                                    currentModalReceiptData.is_qris = ord.is_qris;
                                    if (ord.stand_name) currentModalReceiptData.stand_name = ord.stand_name;
                                    if (ord.stand_num) currentModalReceiptData.stand_num = ord.stand_num;
                                    updateReceiptModalDynamicContent(currentModalReceiptData);
                                }
                            }
                        }
                    });
                }
            }
        } catch (e) {
            console.error('Status poll error:', e);
        } finally {
            isPolling = false;
        }
    }

    function updateOrderBadgesDOM(ord) {
        const badgesContainer = document.getElementById(`order-badges-${ord.id}`);
        if (!badgesContainer) return;

        let paymentBadgeHtml = '';
        if (ord.is_lunas) {
            paymentBadgeHtml = `
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-xs font-black bg-emerald-500 text-white shadow-xs">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25a2.25 2.25 0 0 1 13.5 18v-2.25Z" />
                    </svg>
                    <span>QRIS</span>
                </span>
            `;
            // Hapus tombol Bayar di bawah jika pesanan sudah lunas
            document.getElementById(`btn-pay-${ord.id}`)?.remove();
        } else if (!ord.is_qris) {
            paymentBadgeHtml = `
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-xs font-black bg-amber-500 text-white shadow-xs">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    <span>COD</span>
                </span>
            `;
        }

        let statusBadgeHtml = '';
        if (ord.status === 'siap_diambil') {
            statusBadgeHtml = `
                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-lg sm:rounded-xl text-[10px] sm:text-xs font-black bg-emerald-50 text-emerald-700 border border-emerald-300 shadow-2xs">
                    <span class="relative flex h-2 w-2 shrink-0">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    <span>Siap Diambil</span>
                </span>
            `;
        } else if (ord.status === 'diproses') {
            statusBadgeHtml = `
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-xs font-black bg-orange-50 text-orange-700 border border-orange-200">
                    <span class="w-2 h-2 rounded-full bg-orange-500 animate-pulse"></span>
                    <span>Sedang Dimasak</span>
                </span>
            `;
        } else if (ord.status === 'pending' || ord.status === 'menunggu_pembayaran') {
            statusBadgeHtml = `
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-xs font-black bg-amber-50 text-amber-700 border border-amber-200">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    <span>${(ord.status === 'menunggu_pembayaran' || (!ord.is_lunas && ord.is_qris && ord.status !== 'dibatalkan')) ? 'Belum Bayar' : 'Menunggu'}</span>
                </span>
            `;
        } else if (ord.status === 'selesai') {
            statusBadgeHtml = `
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-xs font-black bg-slate-100 text-slate-700 border border-slate-200">
                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                    </svg>
                    <span>Selesai</span>
                </span>
            `;
        } else if (ord.status === 'dibatalkan') {
            statusBadgeHtml = `
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-xs font-black bg-rose-50 text-rose-700 border border-rose-200">
                    <span>Dibatalkan</span>
                </span>
            `;
        }

        badgesContainer.innerHTML = paymentBadgeHtml + `<span id="order-status-badge-${ord.id}">${statusBadgeHtml}</span>`;
    }

    // Auto polling interval (every 2.5s)
    setInterval(pollActiveOrderStatuses, 2500);

    // Cancel checkout order handler
    async function cancelOrder(orderId, btnElement) {
        if (!confirm('Apakah kamu yakin ingin membatalkan pesanan ini? Stok menu akan dikembalikan ke kantin.')) {
            return;
        }
        if (btnElement) {
            btnElement.disabled = true;
            btnElement.innerHTML = `
                <svg class="w-3.5 h-3.5 animate-spin text-slate-500" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Membatalkan...</span>
            `;
        }
        try {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const res = await fetch(`/api/orders/${orderId}/cancel-checkout`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf || ''
                }
            });
            const data = await res.json();
            if (data.success) {
                showToast('✓ Pesanan berhasil dibatalkan.');
                setTimeout(() => {
                    window.location.reload();
                }, 800);
            } else {
                alert(data.message || 'Gagal membatalkan pesanan.');
                if (btnElement) {
                    btnElement.disabled = false;
                    btnElement.innerHTML = `<span>Batalkan</span>`;
                }
            }
        } catch (e) {
            console.error(e);
            alert('Terjadi kesalahan jaringan.');
            if (btnElement) {
                btnElement.disabled = false;
                btnElement.innerHTML = `<span>Batalkan</span>`;
            }
        }
    }

    // Snap Payment Handler for QRIS
    async function payWithSnap(orderId, btnElement) {
        if (!orderId) {
            alert('ID pesanan tidak valid.');
            return;
        }

        let origHtml = '';
        if (btnElement) {
            origHtml = btnElement.innerHTML;
            btnElement.disabled = true;
            btnElement.innerHTML = `
                <svg class="w-3.5 h-3.5 animate-spin text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Memuat QRIS...</span>
            `;
        }

        const resetBtn = () => {
            if (btnElement) {
                btnElement.disabled = false;
                btnElement.innerHTML = origHtml;
            }
        };

        try {
            const res = await fetch(`/api/orders/${orderId}/snap-token`, {
                headers: {
                    'Accept': 'application/json'
                }
            });
            const data = await res.json();
            resetBtn();

            if (!data.success || !data.snap_token) {
                alert(data.message || 'Gagal memuat token pembayaran QRIS.');
                return;
            }

            const orderPayload = {
                orderId: data.order_id,
                grossAmount: data.gross_amount,
                nama: data.nama_pemesan,
                kelas: data.kelas,
                tickets: data.tickets || []
            };

            const launchSnap = () => {
                window.snap.pay(data.snap_token, {
                    onSuccess: async function(result) {
                        isShowingSuccessModal = true;
                        showRiwayatQrisSuccessModal(orderPayload);
                        await syncOrderStatus(orderId, result, true);
                        pollActiveOrderStatuses();
                        showToast('✓ Pembayaran QRIS berhasil!');
                    },
                    onPending: async function(result) {
                        await syncOrderStatus(orderId, result, false);
                        pollActiveOrderStatuses();
                    },
                    onError: function(result) {
                        showToast('Pembayaran gagal atau dibatalkan.');
                    },
                    onClose: function() {
                        fetch(`/api/orders/${orderId}/sync-status`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                            },
                            body: JSON.stringify({})
                        }).then(res => res.json()).then(syncData => {
                            if (syncData && syncData.is_paid) {
                                isShowingSuccessModal = true;
                                showRiwayatQrisSuccessModal(orderPayload);
                                pollActiveOrderStatuses();
                                showToast('✓ Pembayaran QRIS berhasil!');
                            } else {
                                pollActiveOrderStatuses();
                            }
                        }).catch(() => {
                            pollActiveOrderStatuses();
                        });
                    }
                });
            };

            if (typeof window.snap !== 'undefined' && window.snap.pay) {
                launchSnap();
            } else {
                let attempts = 0;
                const checkSnapInterval = setInterval(() => {
                    attempts++;
                    if (typeof window.snap !== 'undefined' && window.snap.pay) {
                        clearInterval(checkSnapInterval);
                        launchSnap();
                    } else if (attempts >= 20) {
                        clearInterval(checkSnapInterval);
                        resetBtn();
                        alert('Modul pembayaran Midtrans gagal dimuat. Silakan muat ulang halaman.');
                    }
                }, 200);
            }
        } catch (err) {
            resetBtn();
            console.error('payWithSnap error:', err);
            alert('Terjadi kesalahan jaringan.');
        }
    }



    // Modal Validasi Pembayaran QRIS Sukses di Riwayat (Tampilan Identik dengan Sidebar Keranjang)
    function showRiwayatQrisSuccessModal(payload) {
        isShowingSuccessModal = true;
        try {
            sessionStorage.setItem('riwayat_qris_success_payload', JSON.stringify(payload));
        } catch (e) {}

        const modal = document.getElementById('riwayat-qris-success-modal');
        const card = document.getElementById('riwayat-qris-success-card');
        if (!modal || !card) return;

        const { orderId, grossAmount, tickets, nama, kelas } = payload || {};

        const amountElem = document.getElementById('riwayat-qris-success-amount');
        if (amountElem) {
            amountElem.textContent = 'Rp ' + Number(grossAmount || 0).toLocaleString('id-ID');
        }

        const now = new Date();
        const formattedDate = now.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) +
            ', ' + now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB';
        const timeElem = document.getElementById('riwayat-qris-success-time');
        if (timeElem) {
            timeElem.textContent = formattedDate;
        }

        const buyerElem = document.getElementById('riwayat-qris-success-buyer');
        if (buyerElem) {
            const customerName = nama || '{{ auth()->user()->name ?? "Siswa" }}';
            const customerClass = kelas || '{{ auth()->user()->kelas ?? "" }}';
            buyerElem.textContent = customerName + (customerClass ? ' (' + customerClass + ')' : '');
        }

        const historyBtn = document.getElementById('btn-riwayat-qris-success-history');
        if (historyBtn) {
            historyBtn.href = "{{ route('order.history', ['status' => 'pending']) }}";
        }

        // Render tickets persis seperti di sidebar
        const ticketsContainer = document.getElementById('riwayat-qris-success-tickets-container');
        if (ticketsContainer) {
            let html = '';
            if (tickets && tickets.length > 0) {
                html = tickets.map(t => `
                    <div class="rounded-2xl border border-slate-200/80 bg-white p-3.5 shadow-xs">
                        <div class="flex items-center justify-between pb-2.5 border-b border-slate-100">
                            <div class="flex items-center gap-2 min-w-0">
                                <span class="w-6 h-6 rounded-lg bg-orange-100/80 text-orange-600 flex items-center justify-center shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.614A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.015a2.993 2.993 0 0 0 2.25 1.015c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72L4.318 3.44A1.5 1.5 0 0 1 5.378 3h13.244a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72m-13.5 0a3 3 0 0 0 3.75-.614A2.993 2.993 0 0 0 9.75 9.75" />
                                    </svg>
                                </span>
                                <span class="text-xs font-black text-slate-800 truncate">${t.stand_name || 'Stand Kantin'}</span>
                            </div>
                            <span class="font-mono text-xs font-black text-orange-600 bg-orange-50 px-2.5 py-0.5 rounded-xl border border-orange-200/80 shrink-0">
                                #${t.ticket_code}
                            </span>
                        </div>
                        ${t.items && t.items.length > 0 ? `
                            <div class="mt-2.5 space-y-1.5">
                                ${t.items.map(it => `
                                    <div class="flex items-center justify-between text-xs text-slate-600">
                                        <span class="truncate pr-2">${it.nama_menu || it.name || 'Menu'} <span class="font-bold text-slate-900">x${it.jumlah || it.qty || 1}</span></span>
                                        <span class="font-bold text-slate-800 shrink-0">Rp ${(it.subtotal || ((it.harga_satuan || it.price || 0) * (it.jumlah || it.qty || 1))).toLocaleString('id-ID')}</span>
                                    </div>
                                `).join('')}
                            </div>
                        ` : ''}
                    </div>
                `).join('');
            } else {
                html = `
                    <div class="rounded-2xl border border-slate-200/80 bg-white p-3.5 shadow-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-black text-slate-800">Kode Pesanan</span>
                            <span class="font-mono text-xs font-black text-orange-600 bg-orange-50 px-2.5 py-0.5 rounded-xl border border-orange-200/80">
                                #${orderId}
                            </span>
                        </div>
                    </div>
                `;
            }
            ticketsContainer.innerHTML = html;
        }

        modal.classList.remove('opacity-0', 'pointer-events-none');
        modal.classList.add('opacity-100', 'pointer-events-auto');
        card.classList.remove('scale-95');
        card.classList.add('scale-100');
        document.body.style.overflow = 'hidden';
    }

    function closeRiwayatQrisSuccessModal() {
        isShowingSuccessModal = false;
        try {
            sessionStorage.removeItem('riwayat_qris_success_payload');
        } catch (e) {}

        const modal = document.getElementById('riwayat-qris-success-modal');
        const card = document.getElementById('riwayat-qris-success-card');
        if (!modal || !card) return;

        modal.classList.remove('opacity-100', 'pointer-events-auto');
        modal.classList.add('opacity-0', 'pointer-events-none');
        card.classList.remove('scale-100');
        card.classList.add('scale-95');
        document.body.style.overflow = '';

        window.location.href = "{{ route('order.history', ['status' => 'pending']) }}";
    }

    // 24-Hour Countdown Timer for Unpaid QRIS Orders (matches Midtrans 24-hour expiry)
    function updateCountdownTimers() {
        const timerElements = document.querySelectorAll('.batch-countdown-timer[data-expires-timestamp]');
        const now = Math.floor(Date.now() / 1000);

        timerElements.forEach(el => {
            const expiresAt = parseInt(el.getAttribute('data-expires-timestamp'), 10);
            const batchCode = el.getAttribute('data-batch-code');
            const orderId = el.getAttribute('data-order-id');
            const expiresFormat = el.getAttribute('data-expires-format') || '';
            if (isNaN(expiresAt)) return;

            const diff = expiresAt - now;

            if (diff <= 0) {
                el.textContent = '00:00:00';

                // Automatically remove expired order card after 24 hours
                const card = document.getElementById(`batch-card-${batchCode}`) ||
                             document.getElementById(`order-card-${batchCode}`) ||
                             document.querySelector(`[data-batch-code="${batchCode}"]`);

                if (card && !card.dataset.isRemoving) {
                    card.dataset.isRemoving = 'true';
                    card.style.transition = 'opacity 0.5s ease-out, transform 0.5s ease-out, max-height 0.5s ease-in-out, margin 0.5s ease-in-out, padding 0.5s ease-in-out';
                    card.style.opacity = '0';
                    card.style.transform = 'scale(0.96)';

                    // Call backend cancel checkout to restore stock and remove records
                    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    fetch(`/api/orders/${encodeURIComponent(batchCode)}/cancel-checkout`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrf || ''
                        }
                    }).catch(e => console.error('Auto-cancel expired order error:', e));

                    setTimeout(() => {
                        card.style.maxHeight = '0px';
                        card.style.marginBottom = '0px';
                        card.style.padding = '0px';
                        card.style.overflow = 'hidden';

                        setTimeout(() => {
                            card.remove();

                            // Decrement badge count on Belum Bayar tab
                            const belumBayarBadge = document.querySelector('a[href*="status=belum_bayar"] span');
                            if (belumBayarBadge) {
                                const current = parseInt(belumBayarBadge.textContent.trim(), 10) || 0;
                                if (current > 1) {
                                    belumBayarBadge.textContent = current - 1;
                                } else {
                                    belumBayarBadge.remove();
                                }
                            }

                            // Reload to show empty state if all unpaid cards are gone
                            const remaining = document.querySelectorAll('[data-batch-code], [data-order-ids]');
                            if (remaining.length === 0) {
                                window.location.reload();
                            }
                        }, 300);
                    }, 400);
                }
            } else {
                const hours = Math.floor(diff / 3600);
                const minutes = Math.floor((diff % 3600) / 60);
                const seconds = diff % 60;
                const pad = (n) => String(n).padStart(2, '0');
                el.textContent = `${pad(hours)}:${pad(minutes)}:${pad(seconds)}`;
            }
        });
    }

    // Run countdown timer immediately and every second
    updateCountdownTimers();
    setInterval(updateCountdownTimers, 1000);

    // Explicitly bind all public functions to window for global access
    window.updateCountdownTimers = updateCountdownTimers;
    window.payWithSnap = payWithSnap;
    window.cancelOrder = cancelOrder;
    window.openDigitalReceiptModal = openDigitalReceiptModal;
    window.closeDigitalReceiptModal = closeDigitalReceiptModal;
    window.copyOrderCode = copyOrderCode;
    window.copyModalTicketCode = copyModalTicketCode;
    window.showToast = showToast;
    window.showRiwayatQrisSuccessModal = showRiwayatQrisSuccessModal;
    window.closeRiwayatQrisSuccessModal = closeRiwayatQrisSuccessModal;
    window.syncOrderStatus = syncOrderStatus;
    window.pollActiveOrderStatuses = pollActiveOrderStatuses;

    // Bersihkan storage riwayat lokal jika user tidak sedang login
    if ({{ auth()->guest() ? 'true' : 'false' }}) {
        try {
            localStorage.removeItem('kantinskanic_order_history');
        } catch (e) {}
    }
</script>
@endsection
