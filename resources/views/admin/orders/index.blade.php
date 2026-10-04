@extends('layouts.admin')

@section('title', 'Riwayat Transaksi - Super Admin')
@section('sidebar-role', 'Super Administrator')
@section('page-title', 'Riwayat Transaksi')
@section('page-subtitle', \Carbon\Carbon::now('Asia/Jakarta')->locale('id')->isoFormat('dddd') . ', ' . \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('d F Y'))

@section('sidebar-nav')
@include('admin.partials.sidebar_nav')
@endsection

@section('content')
<div class="p-3.5 sm:p-6 space-y-4 sm:space-y-6">

    {{-- STAT CARDS (REUSABLE COMPONENT: MOBILE 2 COLS / TABLET-DESKTOP 4 COLS) --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">
        {{-- Total Pesanan --}}
        <x-stat-card
            title="Total Pesanan"
            :value="number_format($totalOrdersCount, 0, ',', '.')"
            :sublabel="'Pesanan ' . $periodeLabel"
            :isTimeSensitive="true"
            color="orange">
            <x-slot:icon>
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" /></svg>
            </x-slot:icon>
        </x-stat-card>

        {{-- Pesanan Menunggu --}}
        <x-stat-card
            title="Pesanan Menunggu"
            :value="number_format($pendingOrdersCount, 0, ',', '.')"
            :sublabel="'Menunggu ' . $periodeLabel"
            :isTimeSensitive="true"
            color="amber">
            <x-slot:icon>
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
            </x-slot:icon>
        </x-stat-card>

        {{-- Pesanan Selesai --}}
        <x-stat-card
            title="Pesanan Selesai"
            :value="number_format($selesaiOrdersCount, 0, ',', '.')"
            :sublabel="'Selesai ' . $periodeLabel"
            :isTimeSensitive="true"
            color="emerald">
            <x-slot:icon>
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
            </x-slot:icon>
        </x-stat-card>

        {{-- Total Omset --}}
        <x-stat-card
            title="Total Omset Selesai"
            :value="'Rp ' . number_format($totalRevenue, 0, ',', '.')"
            :sublabel="'Omset ' . $periodeLabel"
            :isTimeSensitive="true"
            color="sky">
            <x-slot:icon>
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
            </x-slot:icon>
        </x-stat-card>
    </div>

    {{-- FILTER BOX --}}
    <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/90 shadow-xs p-4 sm:p-5">
        <form method="GET" action="{{ route('admin.orders.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            {{-- Preserve current period filter --}}
            <input type="hidden" name="periode" value="{{ $periode }}">

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

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 min-h-[40px] py-2 px-4 rounded-xl bg-orange-500 hover:bg-orange-600 active:scale-95 text-white font-bold text-xs shadow-sm transition-all cursor-pointer">
                    Filter
                </button>
                @if(request()->anyFilled(['search', 'stand_id', 'status']))
                <a href="{{ route('admin.orders.index', ['periode' => $periode]) }}" class="min-h-[40px] flex items-center py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs transition-colors">
                    Reset
                </a>
                @endif
            </div>
        </form>
    </div>

    {{-- ORDERS TABLE / CARD --}}
    <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/90 shadow-xs overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-slate-100">
            <h2 class="text-base sm:text-lg font-extrabold text-slate-900">Daftar Seluruh Riwayat Pesanan</h2>
            <p class="text-xs text-slate-500 mt-0.5">Menampilkan {{ $orders->firstItem() ?? 0 }} - {{ $orders->lastItem() ?? 0 }} dari {{ $orders->total() }} transaksi</p>
        </div>

        {{-- MOBILE CARD VIEW (< md) --}}
        <div class="block md:hidden divide-y divide-slate-100 p-3 sm:p-4 space-y-3">
            @forelse($orders as $ro)
            <div class="p-3.5 rounded-2xl bg-slate-50/70 border border-slate-200/80 space-y-2.5">
                <div class="flex items-center justify-between gap-2">
                    <div>
                        <span class="font-black text-slate-900 font-mono text-xs block">#{{ $ro->kode_tr }}</span>
                        <span class="text-[10px] text-slate-400">{{ $ro->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <span class="px-2.5 py-1 text-[10px] font-extrabold rounded-full uppercase shrink-0
                        @if($ro->status === 'pending') bg-orange-100 text-orange-700
                        @elseif($ro->status === 'diproses') bg-amber-100 text-amber-800
                        @elseif($ro->status === 'siap_diambil') bg-emerald-100 text-emerald-800
                        @elseif($ro->status === 'selesai') bg-slate-100 text-slate-600
                        @else bg-rose-100 text-rose-700 @endif">
                        {{ str_replace('_', ' ', $ro->status) }}
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-2 text-xs pt-1.5 border-t border-slate-200/60">
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Stand</span>
                        <span class="font-bold text-slate-800 block truncate mt-0.5">{{ $ro->stand->nama_stand ?? '-' }}</span>
                        <span class="text-[10px] text-orange-600 font-semibold">{{ $ro->stand->nomor_stand ?? '' }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Pemesan</span>
                        <span class="font-bold text-slate-800 block truncate mt-0.5">{{ $ro->nama_pemesan }}</span>
                        <span class="text-[10px] text-slate-400 block">{{ $ro->kelas }}</span>
                    </div>
                </div>

                <div class="pt-1.5 border-t border-slate-200/60 space-y-1">
                    @foreach($ro->items as $item)
                    <div class="text-[11px] text-slate-700 truncate">
                        <span class="font-bold text-orange-600">{{ $item->qty }}x</span>
                        <span>{{ $item->menu->nama_menu ?? 'Menu' }}</span>
                    </div>
                    @endforeach
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-200/60">
                    <span class="font-black text-slate-900 text-sm">
                        Rp {{ number_format($ro->total_harga, 0, ',', '.') }}
                    </span>
                </div>
            </div>
            @empty
            <div class="text-center py-10 text-slate-400 text-xs">Tidak ada data transaksi yang sesuai filter.</div>
            @endforelse
        </div>

        {{-- DESKTOP / TABLET TABLE VIEW (md+) --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left text-xs min-w-[700px]">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/70 text-[10px] font-extrabold uppercase tracking-wider text-slate-400">
                        <th class="py-3 px-5">Kode & Waktu</th>
                        <th class="py-3 px-4">Stand</th>
                        <th class="py-3 px-4">Pemesan</th>
                        <th class="py-3 px-4">Detail Menu</th>
                        <th class="py-3 px-4">Total</th>
                        <th class="py-3 px-5 text-center">Status</th>
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
                        <td class="py-3.5 px-4 font-black text-slate-900">
                            Rp {{ number_format($ro->total_harga, 0, ',', '.') }}
                        </td>
                        <td class="py-3.5 px-5 text-center">
                            <span class="inline-block px-2.5 py-1 text-[10px] font-extrabold rounded-full uppercase
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
        <div class="px-4 sm:px-5 py-4 border-t border-slate-100 bg-slate-50/50">
            {{ $orders->links() }}
        </div>
        @endif
    </div>

</div>
@endsection