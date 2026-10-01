@extends('layouts.admin')

@section('title', 'Riwayat Transaksi - Super Admin')
@section('sidebar-role', 'Super Administrator')
@section('page-title', 'Riwayat Transaksi')
@section('page-subtitle', 'Monitoring seluruh pesanan pre-order masuk di seluruh stand kantin')

@section('sidebar-nav')
    @include('admin.partials.sidebar_nav')
@endsection

@section('content')
<div class="p-4 sm:p-6 space-y-6">

    {{-- STAT CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="rounded-2xl p-5 bg-white border border-slate-200/90 shadow-xs">
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Semua Pesanan</p>
            <p class="text-3xl font-black text-slate-900 mt-1">{{ number_format($totalOrdersCount, 0, ',', '.') }}</p>
            <p class="text-xs text-slate-500 mt-1 font-medium">Akumulasi seluruh stand</p>
        </div>

        <div class="rounded-2xl p-5 bg-white border border-slate-200/90 shadow-xs">
            <p class="text-[11px] font-bold text-amber-500 uppercase tracking-wider">Pesanan Menunggu</p>
            <p class="text-3xl font-black text-amber-600 mt-1">{{ number_format($pendingOrdersCount, 0, ',', '.') }}</p>
            <p class="text-xs text-amber-600/80 mt-1 font-medium">Perlu dikonfirmasi vendor</p>
        </div>

        <div class="rounded-2xl p-5 bg-white border border-slate-200/90 shadow-xs">
            <p class="text-[11px] font-bold text-emerald-500 uppercase tracking-wider">Pesanan Selesai</p>
            <p class="text-3xl font-black text-emerald-600 mt-1">{{ number_format($selesaiOrdersCount, 0, ',', '.') }}</p>
            <p class="text-xs text-emerald-600/80 mt-1 font-medium">Transaksi lunas terselesaikan</p>
        </div>

        <div class="rounded-2xl p-5 bg-white border border-slate-200/90 shadow-xs">
            <p class="text-[11px] font-bold text-orange-500 uppercase tracking-wider">Total Omset Selesai</p>
            <p class="text-2xl font-black text-orange-600 mt-1">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
            <p class="text-xs text-orange-600/80 mt-1 font-medium">Pendapatan seluruh stand</p>
        </div>
    </div>

    {{-- FILTER BOX --}}
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs p-5">
        <form method="GET" action="{{ route('admin.orders.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div>
                <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1">Cari Kode / Pemesan</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Kode TR / Nama / Kelas..."
                       class="w-full text-xs rounded-xl border border-slate-200 px-3 py-2 text-slate-800 placeholder-slate-400 focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20">
            </div>

            <div>
                <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1">Filter Stand</label>
                <select name="stand_id" class="w-full text-xs rounded-xl border border-slate-200 px-3 py-2 text-slate-800 focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20">
                    <option value="">Semua Stand</option>
                    @foreach($stands as $st)
                        <option value="{{ $st->id }}" {{ request('stand_id') == $st->id ? 'selected' : '' }}>
                            {{ $st->nomor_stand }} - {{ $st->nama_stand }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1">Status Pesanan</label>
                <select name="status" class="w-full text-xs rounded-xl border border-slate-200 px-3 py-2 text-slate-800 focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu (Pending)</option>
                    <option value="diproses" {{ request('status') === 'diproses' ? 'selected' : '' }}>Diproses</option>
                    <option value="siap_diambil" {{ request('status') === 'siap_diambil' ? 'selected' : '' }}>Siap Diambil</option>
                    <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="dibatalkan" {{ request('status') === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1">Jam Ambil</label>
                <select name="jam_pengambilan" class="w-full text-xs rounded-xl border border-slate-200 px-3 py-2 text-slate-800 focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20">
                    <option value="">Semua Jam</option>
                    <option value="Istirahat 1" {{ str_contains(request('jam_pengambilan', ''), '1') ? 'selected' : '' }}>Istirahat 1 (09:45 - 10:15)</option>
                    <option value="Istirahat 2" {{ str_contains(request('jam_pengambilan', ''), '2') ? 'selected' : '' }}>Istirahat 2 (12:00 - 12:45)</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 px-4 rounded-xl bg-orange-500 hover:bg-orange-600 active:scale-95 text-white font-bold text-xs shadow-sm transition-all cursor-pointer">
                    Filter
                </button>
                @if(request()->anyFilled(['search', 'stand_id', 'status', 'jam_pengambilan']))
                <a href="{{ route('admin.orders.index') }}" class="py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs transition-colors">
                    Reset
                </a>
                @endif
            </div>
        </form>
    </div>

    {{-- ORDERS TABLE --}}
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="text-base font-extrabold text-slate-900">Daftar Seluruh Riwayat Pesanan</h2>
                <p class="text-xs text-slate-500 mt-0.5">Menampilkan {{ $orders->firstItem() ?? 0 }} - {{ $orders->lastItem() ?? 0 }} dari {{ $orders->total() }} transaksi</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/70 text-[10px] font-extrabold uppercase tracking-wider text-slate-400">
                        <th class="py-3 px-5">Kode & Waktu</th>
                        <th class="py-3 px-4">Stand</th>
                        <th class="py-3 px-4">Pemesan</th>
                        <th class="py-3 px-4">Detail Menu</th>
                        <th class="py-3 px-4">Jam Ambil</th>
                        <th class="py-3 px-4">Total</th>
                        <th class="py-3 px-5 text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($orders as $ro)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="py-3.5 px-5">
                            <span class="font-black text-slate-900 block font-mono text-xs">#{{ $ro->kode_tr }}</span>
                            <span class="text-[10px] text-slate-400">{{ $ro->created_at->format('d/m/Y H:i') }}</span>
                        </td>
                        <td class="py-3.5 px-4 font-bold text-slate-800">
                            <div>{{ $ro->stand->nama_stand ?? '-' }}</div>
                            <span class="text-[10px] text-orange-600 font-semibold">{{ $ro->stand->nomor_stand ?? '' }}</span>
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="font-extrabold text-slate-800 block">{{ $ro->nama_pemesan }}</span>
                            <span class="text-[10px] text-slate-400">{{ $ro->kelas }}</span>
                        </td>
                        <td class="py-3.5 px-4">
                            <div class="space-y-0.5 max-w-[220px]">
                                @foreach($ro->items as $item)
                                <div class="text-[11px] text-slate-700 truncate">
                                    <span class="font-bold text-orange-600">{{ $item->qty }}x</span>
                                    <span>{{ $item->menu->nama_menu ?? 'Menu' }}</span>
                                </div>
                                @endforeach
                            </div>
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="px-2 py-0.5 rounded-md bg-orange-50 text-orange-700 font-bold text-[10px] border border-orange-100">{{ $ro->jam_pengambilan }}</span>
                        </td>
                        <td class="py-3.5 px-4 font-black text-slate-900">
                            Rp {{ number_format($ro->total_harga, 0, ',', '.') }}
                        </td>
                        <td class="py-3.5 px-5 text-right">
                            <span class="px-2.5 py-1 text-[10px] font-extrabold rounded-full uppercase
                                @if($ro->status === 'pending') bg-orange-100 text-orange-700
                                @elseif($ro->status === 'diproses') bg-amber-100 text-amber-800
                                @elseif($ro->status === 'siap_diambil') bg-emerald-100 text-emerald-800
                                @elseif($ro->status === 'selesai') bg-slate-100 text-slate-600
                                @else bg-rose-100 text-rose-700 @endif">
                                {{ str_replace('_', ' ', $ro->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-12 text-slate-400 font-medium">
                            Tidak ada data transaksi yang sesuai filter.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
        <div class="px-5 py-4 border-t border-slate-100 bg-slate-50/50">
            {{ $orders->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
