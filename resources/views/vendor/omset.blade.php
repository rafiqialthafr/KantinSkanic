@extends('layouts.admin')

@section('title', 'Laporan Omset - ' . $stand->nama_stand)
@section('sidebar-role', 'Stand Penjual')
@section('page-title', $stand->nama_stand)
@section('page-subtitle', 'Laporan Omset & Pendapatan')

@section('sidebar-nav')
@include('vendor.partials.sidebar_nav')
@endsection

@section('content')
<div class="p-3.5 sm:p-6 space-y-4 sm:space-y-6">

    {{-- STAT CARDS (SYMMETRICAL 2x2 ON MOBILE, 4 COLS ON DESKTOP) --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">
        {{-- Total Omset --}}
        <x-stat-card
            title="Total Omset"
            :value="'Rp ' . number_format($totalOmset, 0, ',', '.')"
            sublabel="Pesanan selesai"
            color="orange"
            :isTimeSensitive="true">
            <x-slot:icon>
                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941"/>
                </svg>
            </x-slot:icon>
        </x-stat-card>

        {{-- Total Pesanan --}}
        <x-stat-card
            title="Total Pesanan"
            :value="$totalOrders"
            sublabel="Semua status"
            color="amber"
            :isTimeSensitive="true">
            <x-slot:icon>
                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007Z"/>
                </svg>
            </x-slot:icon>
        </x-stat-card>

        {{-- Selesai --}}
        <x-stat-card
            title="Pesanan Selesai"
            :value="$selesaiOrders"
            sublabel="Berhasil terbayar"
            color="emerald"
            :isTimeSensitive="true">
            <x-slot:icon>
                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                </svg>
            </x-slot:icon>
        </x-stat-card>

        {{-- Dibatalkan --}}
        <x-stat-card
            title="Pesanan Batal"
            :value="$batalOrders"
            sublabel="Batal / ditolak"
            color="rose"
            :isTimeSensitive="true">
            <x-slot:icon>
                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                </svg>
            </x-slot:icon>
        </x-stat-card>
    </div>

    {{-- PAYMENT REVENUE BREAKDOWN (Cash vs QRIS) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">
        {{-- Pendapatan Tunai --}}
        <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/90 shadow-2xs hover:shadow-xs hover:border-emerald-200 p-4 sm:p-5 transition-all duration-200 flex items-center justify-between gap-3 sm:gap-4">
            <div class="min-w-0">
                <span class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider block">Pendapatan Tunai (COD di Stand)</span>
                <span class="text-lg sm:text-2xl font-black text-slate-900 block mt-1 tracking-tight">Rp {{ number_format($cashRevenue ?? 0, 0, ',', '.') }}</span>
                <div class="flex items-center gap-1.5 mt-1.5 sm:mt-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                    <span class="text-[11px] sm:text-xs font-bold text-emerald-700">Uang tunai langsung diterima stand</span>
                </div>
            </div>
            <div class="w-11 h-11 sm:w-13 sm:h-13 rounded-2xl bg-emerald-50 border border-emerald-100 text-emerald-600 flex items-center justify-center shrink-0 shadow-2xs">
                <svg class="w-5 h-5 sm:w-7 sm:h-7" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6H2.25m0 0v9.75c0 .414.336.75.75.75h.75m0-11.25H21a.75.75 0 0 1 .75.75v9.75a.75.75 0 0 1-.75.75h-.75m0-11.25V4.5m0 12v3.75m0 0a60.07 60.07 0 0 0-15.797 2.101M21 16.5H3" />
                </svg>
            </div>
        </div>

        {{-- Pendapatan QRIS --}}
        <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/90 shadow-2xs hover:shadow-xs hover:border-orange-200 p-4 sm:p-5 transition-all duration-200 flex items-center justify-between gap-3 sm:gap-4">
            <div class="min-w-0">
                <span class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider block">Pendapatan QRIS Online (Pusat)</span>
                <span class="text-lg sm:text-2xl font-black text-slate-900 block mt-1 tracking-tight">Rp {{ number_format($qrisRevenue ?? 0, 0, ',', '.') }}</span>
                <div class="flex items-center gap-1.5 mt-1.5 sm:mt-2">
                    <span class="w-2 h-2 rounded-full bg-amber-500 shrink-0"></span>
                    <span class="text-[11px] sm:text-xs font-bold text-amber-700">Menunggu transfer admin: Rp {{ number_format($qrisPendingPayout ?? 0, 0, ',', '.') }}</span>
                </div>
            </div>
            <div class="w-11 h-11 sm:w-13 sm:h-13 rounded-2xl bg-orange-50 border border-orange-100 text-orange-600 flex items-center justify-center shrink-0 shadow-2xs">
                <svg class="w-5 h-5 sm:w-7 sm:h-7" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5ZM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5ZM14.625 3.75c-.621 0-1.125.504-1.125 1.125v4.5c0 .621.504 1.125 1.125 1.125h4.5c.621 0 1.125-.504 1.125-1.125v-4.5c0-.621-.504-1.125-1.125-1.125h-4.5Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.625 14.625h2.25v2.25h-2.25v-2.25ZM19.125 14.625h.375a.75.75 0 0 1 .75.75v.375h-1.125v-1.125ZM14.625 19.125h1.125v1.125h-1.125v-1.125ZM18 18h2.25v2.25H18V18Z" />
                </svg>
            </div>
        </div>
    </div>

    {{-- LOWER SECTION: TOP MENUS & ORDER HISTORY --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">

        {{-- Top Selling Menus --}}
        <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/90 shadow-2xs overflow-hidden">
            <div class="px-4 py-3.5 sm:px-5 sm:py-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-black text-slate-900 text-sm">Menu Terlaris</h3>
                    <p class="text-[11px] sm:text-xs text-slate-400 mt-0.5">Berdasarkan porsi terjual &mdash; {{ $periodeLabel }}</p>
                </div>
                <span class="text-xs font-bold text-orange-500 bg-orange-50 px-2.5 py-1 rounded-full border border-orange-200">Top 10</span>
            </div>

            @if($topMenus->isEmpty())
            <div class="px-5 py-10 text-center">
                <svg class="w-10 h-10 text-slate-200 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z"/></svg>
                <p class="text-slate-400 text-xs sm:text-sm font-medium">Belum ada data penjualan untuk periode ini.</p>
            </div>
            @else
            <div class="divide-y divide-slate-100">
                @foreach($topMenus as $i => $item)
                <div class="px-4 py-3 sm:px-5 sm:py-3.5 flex items-center gap-3">
                    <span class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-black shrink-0
                        {{ $i === 0 ? 'bg-amber-400 text-amber-950' : ($i === 1 ? 'bg-slate-300 text-slate-700' : ($i === 2  ? 'bg-amber-600 text-white' : 'bg-slate-100 text-slate-500')) }}">
                        {{ $i + 1 }}
                    </span>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs sm:text-sm font-bold text-slate-800 truncate">{{ $item->menu?->nama_menu ?? 'Menu dihapus' }}</p>
                        <p class="text-[11px] text-slate-400">{{ $item->total_qty }} porsi terjual</p>
                    </div>
                    <p class="text-xs sm:text-sm font-black text-orange-600 shrink-0">Rp {{ number_format($item->total_revenue, 0, ',', '.') }}</p>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Completed Orders History --}}
        <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/90 shadow-2xs overflow-hidden">
            <div class="px-4 py-3.5 sm:px-5 sm:py-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-black text-slate-900 text-sm">Riwayat Pesanan Selesai</h3>
                    <p class="text-[11px] sm:text-xs text-slate-400 mt-0.5">{{ $orders->count() }} pesanan &mdash; {{ $periodeLabel }}</p>
                </div>
                <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200">
                    {{ $orders->count() }} selesai
                </span>
            </div>

            @if($orders->isEmpty())
            <div class="px-5 py-10 text-center">
                <svg class="w-10 h-10 text-slate-200 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                <p class="text-slate-400 text-xs sm:text-sm font-medium">Belum ada pesanan selesai untuk periode ini.</p>
            </div>
            @else
            <div class="divide-y divide-slate-100 max-h-96 overflow-y-auto">
                @foreach($orders as $order)
                <div class="px-4 py-3 sm:px-5 sm:py-3.5 flex items-center gap-3">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <span class="text-xs font-black text-slate-800 font-mono">#{{ $order->kode_tr }}</span>
                            <span class="text-[9px] font-bold bg-emerald-100 text-emerald-700 px-1.5 py-0.5 rounded-full">Selesai</span>
                            <span class="text-[9px] font-bold {{ $order->metode_pembayaran === 'qris' ? 'bg-sky-50 text-sky-700 border border-sky-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }} px-1.5 py-0.5 rounded-md uppercase">
                                {{ strtoupper($order->metode_pembayaran) }}
                            </span>
                        </div>
                        <p class="text-[11px] sm:text-xs text-slate-500 mt-1 truncate">
                            {{ $order->nama_pemesan }} &middot; {{ $order->kelas }} &middot; {{ $order->created_at->format('d M, H:i') }}
                        </p>
                    </div>
                    <p class="text-xs sm:text-sm font-black text-slate-900 shrink-0">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</p>
                </div>
                @endforeach
            </div>
            @endif
        </div>

    </div>

</div>
@endsection
