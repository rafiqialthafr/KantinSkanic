@extends('layouts.admin')

@section('title', 'Dashboard - ' . $stand->nama_stand)
@section('sidebar-role', 'Stand Penjual')
@section('page-title', $stand->nama_stand)
@section('page-subtitle', \Carbon\Carbon::now('Asia/Jakarta')->locale('id')->isoFormat('dddd') . ', ' . \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('d F Y'))

@section('sidebar-nav')
<a href="{{ route('vendor.dashboard') }}" class="sidebar-link {{ request()->routeIs('vendor.dashboard') && !request()->has('status') ? 'active' : '' }}">
    <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" /></svg>
    Dashboard Pesanan
</a>

<a href="{{ route('vendor.menus.index') }}" class="sidebar-link {{ request()->routeIs('vendor.menus.*') ? 'active' : '' }}">
    <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 0 1 0 3.75H5.625a1.875 1.875 0 0 1 0-3.75Z" /></svg>
    Menu Saya
</a>

<a href="{{ route('vendor.omset') }}" class="sidebar-link {{ request()->routeIs('vendor.omset') ? 'active' : '' }}">
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

<a href="{{ route('vendor.dashboard', array_merge(request()->query(), ['status' => 'pending'])) }}" class="sidebar-link {{ ($activeStatus ?? '') === 'pending' ? 'active' : '' }}">
    <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
    Menunggu
    @if(($pendingOrdersCount ?? 0) > 0)
    <span class="ml-auto text-[10px] font-black bg-orange-500 text-white px-1.5 py-0.5 rounded-md">{{ $pendingOrdersCount }}</span>
    @endif
</a>

<a href="{{ route('vendor.dashboard', array_merge(request()->query(), ['status' => 'siap_diambil'])) }}" class="sidebar-link {{ ($activeStatus ?? '') === 'siap_diambil' ? 'active' : '' }}">
    <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" /></svg>
    Siap Diambil
    @if(($siapDiambilOrdersCount ?? 0) > 0)
    <span class="ml-auto text-[10px] font-black bg-emerald-500 text-white px-1.5 py-0.5 rounded-md">{{ $siapDiambilOrdersCount }}</span>
    @endif
</a>

<a href="{{ route('vendor.dashboard', array_merge(request()->query(), ['status' => 'selesai'])) }}" class="sidebar-link {{ ($activeStatus ?? '') === 'selesai' ? 'active' : '' }}">
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

    @if(!$stand->nomor_rekening)
    <div class="p-4 rounded-2xl bg-gradient-to-r from-rose-50 to-orange-50 border border-rose-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-2xs">
        <div class="flex items-start gap-3">
            <div class="w-9 h-9 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0 mt-0.5">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                </svg>
            </div>
            <div>
                <h4 class="text-xs font-black text-slate-800">Rekening Pencairan Saldo QRIS Belum Diatur</h4>
                <p class="text-[11px] text-slate-500 mt-0.5">Agar Admin Kantin dapat mentransfer pencairan dana penjualan QRIS pesanan Anda, silakan daftarkan rekening Bank atau E-Wallet Anda sekarang.</p>
            </div>
        </div>
        <a href="{{ route('vendor.rekening.index') }}" class="px-4 py-2 rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-bold text-xs shrink-0 inline-flex items-center gap-1.5 shadow-xs transition-all">
            <span>Atur Rekening Sekarang</span>
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
            </svg>
        </a>
    </div>
    @endif


    {{-- PERIODE DROPDOWN + STAT CARDS --}}
    <div class="flex items-center justify-between gap-4 flex-wrap">
        <h2 class="text-base font-black text-slate-800 flex items-center gap-2">
            <span>Statistik</span>
            <span class="text-xs font-bold text-orange-600 bg-orange-50 px-2.5 py-0.5 rounded-full border border-orange-200/80">{{ $periodeLabel }}</span>
        </h2>
        {{-- Periode Dropdown --}}
        <div class="flex items-center gap-2">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Periode:</span>
            <div class="flex items-center gap-1.5 flex-wrap">
                @foreach(['hari_ini' => 'Hari Ini', 'minggu_ini' => 'Minggu Ini', 'bulan_ini' => 'Bulan Ini', 'semua' => 'Keseluruhan'] as $key => $label)
                <a href="{{ route('vendor.dashboard', array_merge(request()->query(), ['periode' => $key])) }}"
                   class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all shrink-0 {{ $periode === $key ? 'bg-orange-500 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
                    {{ $label }}
                </a>
                @endforeach
            </div>
        </div>
    </div>

    {{-- STAT CARDS (MODERN COMPONENT) --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-4">
        {{-- Omset (terpengaruh periode) --}}
        <x-stat-card
            class="col-span-2 sm:col-span-1"
            title="Omset {{ $periodeLabel }}"
            :value="'Rp ' . number_format($revenueToday, 0, ',', '.')"
            sublabel="Dari pesanan selesai lunas"
            color="orange"
            :isTimeSensitive="true">
            <x-slot:icon>
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941"/>
                </svg>
            </x-slot:icon>
        </x-stat-card>

        {{-- Pesanan Aktif (real-time, tidak terikat periode) --}}
        <x-stat-card
            title="Pesanan Aktif"
            :value="$activeOrdersCount"
            sublabel="Perlu disiapkan sekarang"
            :badge="$activeOrdersCount > 0"
            color="amber">
            <x-slot:icon>
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                </svg>
            </x-slot:icon>
        </x-stat-card>

        {{-- Pesanan Selesai (terpengaruh periode) --}}
        <x-stat-card
            title="Pesanan Selesai"
            :value="$completedOrdersCount"
            sublabel="Sudah diambil siswa"
            color="emerald"
            :isTimeSensitive="true">
            <x-slot:icon>
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                </svg>
            </x-slot:icon>
        </x-stat-card>

        {{-- Total Pesanan (terpengaruh periode) --}}
        <x-stat-card
            title="Total Pesanan"
            :value="$totalOrdersToday"
            :sublabel="'Semua status ' . strtolower($periodeLabel)"
            color="sky"
            :isTimeSensitive="true">
            <x-slot:icon>
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007Z"/>
                </svg>
            </x-slot:icon>
        </x-stat-card>
    </div>


    <div>
        <!-- Status Filter Tabs -->
        <div class="flex items-center gap-2 overflow-x-auto pb-1">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider shrink-0 mr-1">Status:</span>
            <a href="{{ route('vendor.dashboard', array_merge(request()->query(), ['status' => 'all'])) }}"
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all shrink-0 {{ ($activeStatus ?? 'all') === 'all' ? 'bg-slate-900 text-white' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
                Semua Status
            </a>
            <a href="{{ route('vendor.dashboard', array_merge(request()->query(), ['status' => 'pending'])) }}"
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all shrink-0 {{ ($activeStatus ?? '') === 'pending' ? 'bg-orange-500 text-white' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
                Menunggu
            </a>
            <a href="{{ route('vendor.dashboard', array_merge(request()->query(), ['status' => 'siap_diambil'])) }}"
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all shrink-0 {{ ($activeStatus ?? '') === 'siap_diambil' ? 'bg-emerald-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
                Siap Diambil
            </a>
            <a href="{{ route('vendor.dashboard', array_merge(request()->query(), ['status' => 'selesai'])) }}"
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all shrink-0 {{ ($activeStatus ?? '') === 'selesai' ? 'bg-slate-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
                Selesai
            </a>
        </div>
    </div>

    {{-- ORDERS CARDS / LIST --}}
    <div class="space-y-4">
        @forelse($orders as $order)
        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs p-5 sm:p-6 transition-all hover:border-orange-200">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4">
                <div class="flex items-center gap-3">
                    <span class="text-sm font-black text-slate-900 font-mono bg-slate-100 px-3 py-1 rounded-xl">#{{ $order->kode_tr }}</span>
                    <div>
                        <div class="flex items-center gap-2">
                            <h4 class="text-sm font-extrabold text-slate-900">{{ $order->nama_pemesan }}</h4>
                            <span class="text-[11px] font-bold text-slate-500">Kelas {{ $order->kelas }}</span>
                        </div>
                        <span class="text-[10px] text-slate-400 mt-0.5 block">{{ $order->created_at->format('d M Y, H:i') }} WIB</span>
                    </div>
                </div>

                <div class="flex items-center gap-2 flex-wrap">
                    <!-- Status Pembayaran Badge -->
                    @if($order->isQris())
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800 border border-emerald-200">
                        LUNAS (QRIS)
                    </span>
                    @elseif($order->isLunas())
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800 border border-emerald-200">
                        LUNAS (TUNAI)
                    </span>
                    @else
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-amber-100 text-amber-900 border border-amber-200">
                        BELUM LUNAS (COD)
                    </span>
                    @endif

                    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider
                        @if($order->status === 'pending') bg-orange-100 text-orange-700
                        @elseif($order->status === 'siap_diambil') bg-emerald-100 text-emerald-800
                        @elseif($order->status === 'selesai') bg-slate-100 text-slate-600
                        @else bg-rose-100 text-rose-700 @endif">
                        {{ str_replace('_', ' ', $order->status) }}
                    </span>
                </div>
            </div>

            <!-- Items ordered -->
            <div class="py-4 space-y-2">
                @foreach($order->items as $item)
                <div class="flex items-center justify-between text-xs">
                    <div class="flex items-center gap-2">
                        <span class="w-5 h-5 rounded-lg bg-orange-50 text-orange-600 font-black text-[10px] flex items-center justify-center">{{ $item->jumlah }}x</span>
                        <span class="font-bold text-slate-800">{{ $item->menu->nama_menu ?? 'Menu' }}</span>
                    </div>
                    <span class="font-semibold text-slate-600">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                </div>
                @endforeach

                @if($order->catatan)
                <div class="mt-2 p-2.5 rounded-xl bg-slate-50 border border-slate-100 text-xs text-slate-600 flex items-start gap-1.5">
                    <span class="font-bold text-slate-700 shrink-0">Catatan:</span>
                    <span class="italic">{{ $order->catatan }}</span>
                </div>
                @endif
            </div>

            <!-- Card Bottom Bar: Total & Status Flow Button -->
            <div class="pt-3 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="text-xs text-slate-400">Total:</span>
                    <span class="text-base font-black text-orange-600">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</span>
                    @if($order->isQris())
                    <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200">
                        Sudah Lunas via QRIS Admin
                    </span>
                    @elseif($order->isLunas())
                    <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200">
                        Lunas Tunai di Stand
                    </span>
                    @else
                    <span class="text-[10px] font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-md border border-amber-200">
                        Bayar Tunai di Stand
                    </span>
                    @endif
                </div>

                <!-- Sequential Status Progression & Actions -->
                <div class="flex items-center gap-2 flex-wrap">
                    @if($order->isTunai() && $order->isBelumLunas() && $order->status !== 'dibatalkan')
                    <form action="{{ route('vendor.orders.markPaid', $order) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-3 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 active:scale-95 text-white font-extrabold text-xs transition-all shadow-xs cursor-pointer flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                            <span>Tandai Lunas Tunai</span>
                        </button>
                    </form>
                    @endif

                    @if($order->status === 'pending')
                    <form action="{{ route('vendor.orders.updateStatus', $order) }}" method="POST" class="inline">
                        @csrf
                        <input type="hidden" name="status" value="siap_diambil">
                        <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white font-extrabold text-xs transition-all shadow-xs cursor-pointer">
                            Tandai Siap Diambil &rarr;
                        </button>
                    </form>
                    @elseif($order->status === 'siap_diambil')
                    <form action="{{ route('vendor.orders.updateStatus', $order) }}" method="POST" class="inline">
                        @csrf
                        <input type="hidden" name="status" value="selesai">
                        <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-black active:scale-95 text-white font-extrabold text-xs transition-all shadow-xs cursor-pointer">
                            Selesaikan (Makanan Diambil) &check;
                        </button>
                    </form>
                    @endif

                    @if(!in_array($order->status, ['selesai', 'dibatalkan']))
                    <form action="{{ route('vendor.orders.updateStatus', $order) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini?')">
                        @csrf
                        <input type="hidden" name="status" value="dibatalkan">
                        <button type="submit" class="px-3 py-2 rounded-xl text-rose-600 hover:bg-rose-50 font-bold text-xs transition-colors cursor-pointer">
                            Batalkan
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-16 bg-white rounded-3xl border border-dashed border-slate-300 p-8">
            <div class="w-14 h-14 mx-auto mb-3 rounded-2xl bg-orange-50 text-orange-400 flex items-center justify-center">
                <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
            </div>
            <h3 class="text-sm font-bold text-slate-800">Tidak Ada Pesanan</h3>
            <p class="text-xs text-slate-400 mt-1">Belum ada pesanan masuk untuk periode {{ strtolower($periodeLabel) }} dengan filter yang dipilih saat ini.</p>
        </div>
        @endforelse
    </div>

</div>
@endsection
