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

<a href="{{ route('vendor.rekening.index') }}" class="sidebar-link {{ request()->routeIs('vendor.rekening.*') ? 'active' : '' }}">
    <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" /></svg>
    Rekening Pencairan
</a>

<div class="pt-3 pb-1 px-2">
    <span class="text-[10px] uppercase tracking-widest font-bold text-slate-500">Status Pesanan</span>
</div>

<a href="{{ route('vendor.dashboard', ['status' => 'pending']) }}" class="sidebar-link">
    <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
    Menunggu
    @if(($pendingOrdersCount ?? 0) > 0)
    <span class="ml-auto text-[10px] font-black bg-orange-500 text-white px-1.5 py-0.5 rounded-md">{{ $pendingOrdersCount }}</span>
    @endif
</a>

<a href="{{ route('vendor.dashboard', ['status' => 'siap_diambil']) }}" class="sidebar-link">
    <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" /></svg>
    Siap Diambil
    @if(($siapDiambilOrdersCount ?? 0) > 0)
    <span class="ml-auto text-[10px] font-black bg-emerald-500 text-white px-1.5 py-0.5 rounded-md">{{ $siapDiambilOrdersCount }}</span>
    @endif
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

    {{-- STAT CARDS (MODERN COMPONENT) --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-4">
        {{-- Total Omset (terpengaruh periode) --}}
        <x-stat-card
            class="col-span-2 sm:col-span-1"
            title="Total Omset"
            :value="'Rp ' . number_format($totalOmset, 0, ',', '.')"
            sublabel="Dari pesanan selesai"
            color="orange"
            :isTimeSensitive="true">
            <x-slot:icon>
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941"/>
                </svg>
            </x-slot:icon>
        </x-stat-card>

        {{-- Total Pesanan (terpengaruh periode) --}}
        <x-stat-card
            title="Total Pesanan"
            :value="$totalOrders"
            sublabel="Semua status pesanan"
            color="amber"
            :isTimeSensitive="true">
            <x-slot:icon>
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007Z"/>
                </svg>
            </x-slot:icon>
        </x-stat-card>

        {{-- Selesai (terpengaruh periode) --}}
        <x-stat-card
            title="Pesanan Selesai"
            :value="$selesaiOrders"
            sublabel="Berhasil terbayar"
            color="emerald"
            :isTimeSensitive="true">
            <x-slot:icon>
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                </svg>
            </x-slot:icon>
        </x-stat-card>

        {{-- Dibatalkan (terpengaruh periode) --}}
        <x-stat-card
            title="Pesanan Dibatalkan"
            :value="$batalOrders"
            sublabel="Pesanan batal/ditolak"
            color="rose"
            :isTimeSensitive="true">
            <x-slot:icon>
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                </svg>
            </x-slot:icon>
        </x-stat-card>
    </div>

    {{-- PAYMENT REVENUE BREAKDOWN (Cash vs QRIS) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        {{-- Pendapatan Tunai --}}
        <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/90 shadow-2xs hover:shadow-xs hover:border-emerald-200 p-5 transition-all duration-200 flex items-center justify-between gap-4">
            <div class="min-w-0">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Pendapatan Tunai di Stand (Fisik)</span>
                <span class="text-xl sm:text-2xl font-black text-slate-900 block mt-1 tracking-tight">Rp {{ number_format($cashRevenue ?? 0, 0, ',', '.') }}</span>
                <div class="flex items-center gap-1.5 mt-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                    <span class="text-xs font-bold text-emerald-700">Uang tunai COD langsung diterima stand</span>
                </div>
            </div>
            <div class="w-12 h-12 sm:w-13 sm:h-13 rounded-2xl bg-emerald-50 border border-emerald-100 text-emerald-600 flex items-center justify-center shrink-0 shadow-2xs">
                <svg class="w-6 h-6 sm:w-7 sm:h-7" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6H2.25m0 0v9.75c0 .414.336.75.75.75h.75m0-11.25H21a.75.75 0 0 1 .75.75v9.75a.75.75 0 0 1-.75.75h-.75m0-11.25V4.5m0 12v3.75m0 0a60.07 60.07 0 0 0-15.797 2.101M21 16.5H3" />
                </svg>
            </div>
        </div>

        {{-- Pendapatan QRIS --}}
        <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/90 shadow-2xs hover:shadow-xs hover:border-orange-200 p-5 transition-all duration-200 flex items-center justify-between gap-4">
            <div class="min-w-0">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Pendapatan QRIS Online (Rekening Admin)</span>
                <span class="text-xl sm:text-2xl font-black text-slate-900 block mt-1 tracking-tight">Rp {{ number_format($qrisRevenue ?? 0, 0, ',', '.') }}</span>
                <div class="flex items-center gap-1.5 mt-2">
                    <span class="w-2 h-2 rounded-full bg-amber-500 shrink-0"></span>
                    <span class="text-xs font-bold text-amber-700">Menunggu transfer admin: Rp {{ number_format($qrisPendingPayout ?? 0, 0, ',', '.') }}</span>
                </div>
            </div>
            <div class="w-12 h-12 sm:w-13 sm:h-13 rounded-2xl bg-orange-50 border border-orange-100 text-orange-600 flex items-center justify-center shrink-0 shadow-2xs">
                <svg class="w-6 h-6 sm:w-7 sm:h-7" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5ZM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5ZM14.625 3.75c-.621 0-1.125.504-1.125 1.125v4.5c0 .621.504 1.125 1.125 1.125h4.5c.621 0 1.125-.504 1.125-1.125v-4.5c0-.621-.504-1.125-1.125-1.125h-4.5Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.625 14.625h2.25v2.25h-2.25v-2.25ZM19.125 14.625h.375a.75.75 0 0 1 .75.75v.375h-1.125v-1.125ZM14.625 19.125h1.125v1.125h-1.125v-1.125ZM18 18h2.25v2.25H18V18Z" />
                </svg>
            </div>
        </div>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Top Selling Menus --}}
        <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/90 shadow-2xs overflow-hidden">
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
        <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/90 shadow-2xs overflow-hidden">
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
