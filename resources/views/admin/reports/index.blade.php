@extends('layouts.admin')

@section('title', 'Laporan Omset & Penjualan - Super Admin')
@section('sidebar-role', 'Super Administrator')
@section('page-title', 'Laporan & Omset')
@section('page-subtitle', 'Rekapitulasi penjualan dan performa finansial per stand kantin')

@section('sidebar-nav')
    @include('admin.partials.sidebar_nav')
@endsection

@section('content')
<div class="p-4 sm:p-6 space-y-6">

    {{-- STAT CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="rounded-2xl p-5 bg-white border border-slate-200/90 shadow-xs">
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Omset Sukses</p>
            <p class="text-3xl font-black text-emerald-600 mt-1">Rp {{ number_format($overallTotalRevenue, 0, ',', '.') }}</p>
            <p class="text-xs text-slate-500 mt-1 font-medium">Akumulasi pesanan berstatus selesai</p>
        </div>

        <div class="rounded-2xl p-5 bg-white border border-slate-200/90 shadow-xs">
            <p class="text-[11px] font-bold text-orange-500 uppercase tracking-wider">Pesanan Terselesaikan</p>
            <p class="text-3xl font-black text-slate-900 mt-1">{{ number_format($overallCompletedOrders, 0, ',', '.') }}</p>
            <p class="text-xs text-slate-500 mt-1 font-medium">Dari {{ $overallTotalOrders }} total pesanan masuk</p>
        </div>

        <div class="rounded-2xl p-5 bg-white border border-slate-200/90 shadow-xs">
            <p class="text-[11px] font-bold text-blue-500 uppercase tracking-wider">Rata-Rata per Transaksi</p>
            @php $avgOrder = $overallCompletedOrders > 0 ? $overallTotalRevenue / $overallCompletedOrders : 0; @endphp
            <p class="text-3xl font-black text-slate-900 mt-1">Rp {{ number_format($avgOrder, 0, ',', '.') }}</p>
            <p class="text-xs text-slate-500 mt-1 font-medium">Estimasi nilai keranjang belanja</p>
        </div>
    </div>

    {{-- STAND PERFORMANCE TABLE --}}
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="text-base font-extrabold text-slate-900">Performa Penjualan Tiap Stand Kantin</h2>
                <p class="text-xs text-slate-500 mt-0.5">Peringkat stand berdasarkan akumulasi omset pesanan selesai</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
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
