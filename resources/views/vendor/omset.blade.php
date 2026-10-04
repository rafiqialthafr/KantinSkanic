@extends('layouts.admin')

@section('title', 'Laporan Omset - ' . $stand->nama_stand)
@section('sidebar-role', 'Stand Penjual')
@section('page-title', $stand->nama_stand)
@section('page-subtitle', 'Laporan Omset & Pendapatan')

@section('sidebar-nav')
<a href="{{ route('vendor.dashboard') }}" class="sidebar-link {{ request()->routeIs('vendor.dashboard') ? 'active' : '' }}">
    <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" /></svg>
    Dashboard Pesanan
</a>

<a href="{{ route('vendor.menus.index') }}" class="sidebar-link {{ request()->routeIs('vendor.menus.*') ? 'active' : '' }}">
    <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 0 1 0 3.75H5.625a1.875 1.875 0 0 1 0-3.75Z" /></svg>
    Menu Saya
</a>

<a href="{{ route('vendor.omset') }}" class="sidebar-link active">
    <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941" /></svg>
    Lihat Omset
</a>

<div class="pt-3 pb-1 px-2">
    <span class="text-[10px] uppercase tracking-widest font-bold text-slate-500">Status Pesanan</span>
</div>

<a href="{{ route('vendor.dashboard', ['status' => 'pending']) }}" class="sidebar-link">
    <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
    Menunggu
    @if(($activeOrdersCount ?? 0) > 0)
    <span class="ml-auto text-[10px] font-black bg-orange-500 text-white px-1.5 py-0.5 rounded-md">{{ $activeOrdersCount }}</span>
    @endif
</a>

<a href="{{ route('vendor.dashboard', ['status' => 'siap_diambil']) }}" class="sidebar-link">
    <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" /></svg>
    Siap Diambil
</a>

<a href="{{ route('vendor.dashboard', ['status' => 'selesai']) }}" class="sidebar-link">
    <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
    Selesai
</a>

<div class="pt-3 pb-1 px-2">
    <span class="text-[10px] uppercase tracking-widest font-bold text-slate-500">Stand</span>
</div>

<a href="{{ route('katalog.index', ['stand' => $stand->id]) }}" target="_blank" class="sidebar-link">
    <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" /></svg>
    Pratinjau Stand
</a>
@endsection

@section('content')
<div class="p-4 sm:p-6 space-y-6">

    {{-- HEADER + PERIODE FILTER --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-black text-slate-900">Laporan Omset</h1>
            <p class="text-sm text-slate-500 mt-0.5">{{ $stand->nama_stand }} &mdash; Periode: <span class="font-bold text-orange-500">{{ $periodeLabel }}</span></p>
        </div>
        <div class="flex items-center gap-1.5 flex-wrap">
            @foreach(['hari_ini' => 'Hari Ini', 'minggu_ini' => 'Minggu Ini', 'bulan_ini' => 'Bulan Ini', 'semua' => 'Keseluruhan'] as $key => $label)
            <a href="{{ route('vendor.omset', ['periode' => $key]) }}"
               class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all {{ $periode === $key ? 'bg-orange-500 text-white shadow-sm shadow-orange-500/25' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
                {{ $label }}
            </a>
            @endforeach
        </div>
    </div>

    {{-- STAT CARDS --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

        {{-- Total Omset (terpengaruh periode) --}}
        <div class="col-span-2 sm:col-span-1 rounded-2xl p-5 relative overflow-hidden text-white" style="background:linear-gradient(135deg,#f97316,#ea580c)">
            <div class="absolute -right-4 -top-4 w-24 h-24 rounded-full bg-white/10"></div>
            <div class="flex items-center justify-between mb-2">
                <div class="w-9 h-9 rounded-xl bg-white/15 flex items-center justify-center">
                    {{-- lucide: trending-up --}}
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941"/></svg>
                </div>
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-white/15 text-[9px] sm:text-[10px] font-bold text-white/95 backdrop-blur-xs tracking-tight" title="Data diperbarui sesuai filter periode"><svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg><span>Periode</span></span>
            </div>
            <p class="text-xs font-bold text-orange-100 uppercase tracking-wider">Total Omset</p>
            <p class="text-sm sm:text-base font-black mt-1 leading-snug tracking-tight break-all">Rp {{ number_format($totalOmset, 0, ',', '.') }}</p>
            <p class="text-xs font-semibold text-orange-100 mt-2">Dari pesanan selesai</p>
        </div>

        {{-- Total Pesanan (terpengaruh periode) --}}
        <div class="rounded-2xl p-5 relative overflow-hidden text-white" style="background:linear-gradient(135deg,#f59e0b,#d97706)">
            <div class="absolute -right-3 -top-3 w-20 h-20 rounded-full bg-white/10"></div>
            <div class="flex items-center justify-between mb-2">
                <div class="w-9 h-9 rounded-xl bg-white/15 flex items-center justify-center">
                    {{-- lucide: shopping-bag --}}
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007Z"/></svg>
                </div>
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-white/15 text-[9px] sm:text-[10px] font-bold text-white/95 backdrop-blur-xs tracking-tight" title="Data diperbarui sesuai filter periode"><svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg><span>Periode</span></span>
            </div>
            <p class="text-xs font-bold text-amber-100 uppercase tracking-wider">Total Pesanan</p>
            <p class="text-3xl font-black mt-1">{{ $totalOrders }}</p>
            <p class="text-xs font-semibold text-amber-100 mt-2">Semua status</p>
        </div>

        {{-- Selesai (terpengaruh periode) --}}
        <div class="rounded-2xl p-5 relative overflow-hidden text-white" style="background:linear-gradient(135deg,#059669,#10b981)">
            <div class="absolute -right-3 -top-3 w-20 h-20 rounded-full bg-white/10"></div>
            <div class="flex items-center justify-between mb-2">
                <div class="w-9 h-9 rounded-xl bg-white/15 flex items-center justify-center">
                    {{-- lucide: check-circle --}}
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                </div>
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-white/15 text-[9px] sm:text-[10px] font-bold text-white/95 backdrop-blur-xs tracking-tight" title="Data diperbarui sesuai filter periode"><svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg><span>Periode</span></span>
            </div>
            <p class="text-xs font-bold text-emerald-100 uppercase tracking-wider">Selesai</p>
            <p class="text-3xl font-black mt-1">{{ $selesaiOrders }}</p>
            <p class="text-xs font-semibold text-emerald-100 mt-2">Berhasil terbayar</p>
        </div>

        {{-- Dibatalkan (terpengaruh periode) - biru --}}
        <div class="rounded-2xl p-5 relative overflow-hidden text-white" style="background:linear-gradient(135deg,#3b82f6,#2563eb)">
            <div class="absolute -right-3 -top-3 w-20 h-20 rounded-full bg-white/10"></div>
            <div class="flex items-center justify-between mb-2">
                <div class="w-9 h-9 rounded-xl bg-white/15 flex items-center justify-center">
                    {{-- lucide: x-circle --}}
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                </div>
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-white/15 text-[9px] sm:text-[10px] font-bold text-white/95 backdrop-blur-xs tracking-tight" title="Data diperbarui sesuai filter periode"><svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg><span>Periode</span></span>
            </div>
            <p class="text-xs font-bold text-blue-100 uppercase tracking-wider">Dibatalkan</p>
            <p class="text-3xl font-black mt-1">{{ $batalOrders }}</p>
            <p class="text-xs font-semibold text-blue-100 mt-2">Pesanan batal</p>
        </div>
    </div>

    {{-- TOP MENU + ORDER HISTORY --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Top Selling Menus --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-black text-slate-900 text-sm">Menu Terlaris</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Berdasarkan jumlah terjual &mdash; {{ $periodeLabel }}</p>
                </div>
                <span class="text-xs font-bold text-orange-500 bg-orange-50 px-2.5 py-1 rounded-full border border-orange-200">Top 10</span>
            </div>

            @if($topMenus->isEmpty())
            <div class="px-5 py-10 text-center">
                <svg class="w-10 h-10 text-slate-200 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z"/></svg>
                <p class="text-slate-400 text-sm font-medium">Belum ada data penjualan untuk periode ini.</p>
            </div>
            @else
            <div class="divide-y divide-slate-100">
                @foreach($topMenus as $i => $item)
                <div class="px-5 py-3 flex items-center gap-3">
                    <span class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-black shrink-0
                        {{ $i === 0 ? 'bg-amber-400 text-amber-950' : ($i === 1 ? 'bg-slate-300 text-slate-700' : ($i === 2  ? 'bg-amber-600 text-white' : 'bg-slate-100 text-slate-500')) }}">
                        {{ $i + 1 }}
                    </span>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-slate-800 truncate">{{ $item->menu?->nama_menu ?? 'Menu dihapus' }}</p>
                        <p class="text-xs text-slate-400">{{ $item->total_qty }} porsi terjual</p>
                    </div>
                    <p class="text-sm font-black text-orange-600 shrink-0">Rp {{ number_format($item->total_revenue, 0, ',', '.') }}</p>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Completed Orders History --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h3 class="font-black text-slate-900 text-sm">Riwayat Pesanan Selesai</h3>
                <p class="text-xs text-slate-400 mt-0.5">{{ $orders->count() }} pesanan &mdash; {{ $periodeLabel }}</p>
            </div>

            @if($orders->isEmpty())
            <div class="px-5 py-10 text-center">
                <svg class="w-10 h-10 text-slate-200 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                <p class="text-slate-400 text-sm font-medium">Belum ada pesanan selesai untuk periode ini.</p>
            </div>
            @else
            <div class="divide-y divide-slate-100 max-h-96 overflow-y-auto">
                @foreach($orders as $order)
                <div class="px-5 py-3 flex items-center gap-3">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-black text-slate-800 font-mono">{{ $order->kode_tr }}</span>
                            <span class="text-[10px] font-bold bg-emerald-100 text-emerald-700 px-1.5 py-0.5 rounded-full">Selesai</span>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">{{ $order->nama_pemesan }} &middot; {{ $order->kelas }} &middot; {{ $order->created_at->format('d M, H:i') }}</p>
                    </div>
                    <p class="text-sm font-black text-slate-900 shrink-0">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</p>
                </div>
                @endforeach
            </div>
            @endif
        </div>

    </div>

</div>
@endsection
