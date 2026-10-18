@extends('layouts.admin')

@section('title', 'Laporan Omset & Penjualan - Super Admin')
@section('sidebar-role', 'Super Administrator')
@section('page-title', 'Laporan & Omset')
@section('page-subtitle', \Carbon\Carbon::now('Asia/Jakarta')->locale('id')->isoFormat('dddd') . ', ' . \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('d F Y'))

@section('sidebar-nav')
@include('admin.partials.sidebar_nav')
@endsection

@section('content')
<div class="p-3.5 sm:p-6 space-y-4 sm:space-y-6">

    {{-- STAT CARDS (4 CARDS: TOTAL OMSET, OMSET QRIS, OMSET COD, PESANAN SELESAI) --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">
        {{-- Total Omset Sukses --}}
        <x-stat-card
            title="Total Omset Sukses"
            :value="'Rp ' . number_format($overallTotalRevenue, 0, ',', '.')"
            :sublabel="'Omset selesai ' . $periodeLabel"
            :isTimeSensitive="true"
            color="emerald">
            <x-slot:icon>
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
            </x-slot:icon>
        </x-stat-card>

        {{-- Omset QRIS (Non-Tunai) --}}
        <x-stat-card
            title="Omset QRIS"
            :value="'Rp ' . number_format($overallQrisRevenue, 0, ',', '.')"
            :sublabel="'Non-tunai ' . $periodeLabel"
            :isTimeSensitive="true"
            color="orange">
            <x-slot:icon>
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5ZM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5ZM14.625 3.75c-.621 0-1.125.504-1.125 1.125v4.5c0 .621.504 1.125 1.125 1.125h4.5c.621 0 1.125-.504 1.125-1.125v-4.5c0-.621-.504-1.125-1.125-1.125h-4.5Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.625 14.625h2.25v2.25h-2.25v-2.25ZM19.125 14.625h.375a.75.75 0 0 1 .75.75v.375h-1.125v-1.125ZM14.625 19.125h1.125v1.125h-1.125v-1.125ZM18 18h2.25v2.25H18V18Z" />
                </svg>
            </x-slot:icon>
        </x-stat-card>

        {{-- Omset Tunai (COD) --}}
        <x-stat-card
            title="Omset Tunai (COD)"
            :value="'Rp ' . number_format($overallCashRevenue, 0, ',', '.')"
            :sublabel="'Fisik stand ' . $periodeLabel"
            :isTimeSensitive="true"
            color="amber">
            <x-slot:icon>
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6H2.25m0 0v9.75c0 .414.336.75.75.75h.75m0-11.25H21a.75.75 0 0 1 .75.75v9.75a.75.75 0 0 1-.75.75h-.75m0-11.25V4.5m0 12v3.75m0 0a60.07 60.07 0 0 0-15.797 2.101M21 16.5H3" />
                </svg>
            </x-slot:icon>
        </x-stat-card>

        {{-- Pesanan Terselesaikan --}}
        <x-stat-card
            title="Pesanan Selesai"
            :value="number_format($overallCompletedOrders, 0, ',', '.')"
            :sublabel="'Dari ' . $overallTotalOrders . ' pesanan (' . $periodeLabel . ')'"
            :isTimeSensitive="true"
            color="sky">
            <x-slot:icon>
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
            </x-slot:icon>
        </x-stat-card>
    </div>

    {{-- STAND PERFORMANCE TABLE / CARD --}}
    <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/90 shadow-xs overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-slate-100">
            <h2 class="text-base sm:text-lg font-extrabold text-slate-900">Performa Penjualan Tiap Stand Kantin</h2>
            <p class="text-xs text-slate-500 mt-0.5">Peringkat stand berdasarkan akumulasi omset pesanan selesai</p>
        </div>

        {{-- MOBILE CARD VIEW (< md) --}}
        <div class="block md:hidden divide-y divide-slate-100 p-3 sm:p-4 space-y-3">
            @forelse($stands as $index => $st)
            <div class="p-3.5 rounded-2xl bg-slate-50/70 border border-slate-200/80 space-y-2.5">
                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 rounded-full flex items-center justify-center font-black text-xs shrink-0 {{ $index === 0 ? 'bg-amber-100 text-amber-800 border border-amber-200' : ($index === 1 ? 'bg-slate-200 text-slate-700' : ($index === 2 ? 'bg-orange-100 text-orange-800' : 'bg-slate-100 text-slate-400')) }}">
                        {{ $index + 1 }}
                    </span>
                    <div class="min-w-0">
                        <span class="font-extrabold text-slate-900 text-sm block truncate">{{ $st->nama_stand }}</span>
                        <span class="text-[10px] font-bold bg-orange-100 text-orange-700 px-1.5 py-0.5 rounded-md inline-block mt-0.5">{{ $st->nomor_stand }}</span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-200/60 text-xs">
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Pemilik</span>
                        <span class="font-bold text-slate-800 block mt-0.5">{{ $st->pemilik }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Statistik</span>
                        <span class="font-bold text-slate-700 block mt-0.5">{{ $st->menus_count }} Menu · {{ $st->orders_count }} Pesanan</span>
                    </div>
                </div>

                <div class="pt-2 border-t border-slate-200/60">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Total Omset Selesai</span>
                    <span class="text-base font-black text-emerald-600 block mt-0.5">Rp {{ number_format($st->total_omset ?? 0, 0, ',', '.') }}</span>
                </div>
            </div>
            @empty
            <div class="text-center py-10 text-slate-400 text-xs">Belum ada stand yang terdaftar.</div>
            @endforelse
        </div>

        {{-- DESKTOP / TABLET TABLE VIEW (md+) --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left text-xs min-w-[600px]">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/70 text-[10px] font-extrabold uppercase tracking-wider text-slate-400">
                        <th class="py-3 px-5">Peringkat & Stand</th>
                        <th class="py-3 px-4">Pemilik</th>
                        <th class="py-3 px-4">Menu Aktif</th>
                        <th class="py-3 px-4">Total Pesanan</th>
                        <th class="py-3 px-5 text-right">Total Omset Selesai</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($stands as $index => $st)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="py-4 px-5">
                            <div class="flex items-center gap-3">
                                <span class="w-6 h-6 rounded-full flex items-center justify-center font-black text-xs {{ $index === 0 ? 'bg-amber-100 text-amber-800 border border-amber-200' : ($index === 1 ? 'bg-slate-200 text-slate-700' : ($index === 2 ? 'bg-orange-100 text-orange-800' : 'text-slate-400')) }}">
                                    {{ $index + 1 }}
                                </span>
                                <div>
                                    <span class="font-extrabold text-slate-900 text-sm block">{{ $st->nama_stand }}</span>
                                    <span class="text-[10px] font-bold bg-orange-100 text-orange-700 px-1.5 py-0.5 rounded-md inline-block mt-0.5">{{ $st->nomor_stand }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-4 font-bold text-slate-800">
                            {{ $st->pemilik }}
                        </td>
                        <td class="py-4 px-4 font-bold text-slate-700">
                            {{ $st->menus_count }} Menu
                        </td>
                        <td class="py-4 px-4 font-bold text-slate-700">
                            {{ $st->orders_count }} Pesanan
                        </td>
                        <td class="py-4 px-5 text-right">
                            <span class="text-sm font-black text-emerald-600 block">
                                Rp {{ number_format($st->total_omset ?? 0, 0, ',', '.') }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-10 text-slate-400 font-medium">Belum ada stand yang terdaftar.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection