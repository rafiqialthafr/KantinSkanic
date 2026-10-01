@extends('layouts.admin')

@section('title', 'Dashboard - ' . $stand->nama_stand)
@section('sidebar-role', 'Stand Penjual')
@section('page-title', $stand->nama_stand)
@section('page-subtitle', 'Pantau pesanan & kelola stand kamu hari ini')

@section('sidebar-nav')
<a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') && !request()->routeIs('menus.*') ? 'active' : '' }}">
    <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" /></svg>
    Dashboard
</a>

<a href="{{ route('menus.index') }}" class="sidebar-link {{ request()->routeIs('menus.*') ? 'active' : '' }}">
    <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 0 1 0 3.75H5.625a1.875 1.875 0 0 1 0-3.75Z" /></svg>
    Menu Saya
    <span class="ml-auto text-[10px] font-black bg-orange-500/20 text-orange-300 px-1.5 py-0.5 rounded-md">{{ $stand->menus->count() }}</span>
</a>

<div class="pt-3 pb-1 px-2">
    <span class="text-[10px] uppercase tracking-widest font-bold text-slate-600">Status Pesanan</span>
</div>

<a href="{{ route('dashboard', ['status' => 'pending']) }}" class="sidebar-link {{ ($activeStatus ?? '') === 'pending' ? 'active' : '' }}">
    <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
    Menunggu
    @if($activeOrdersCount > 0)
    <span class="ml-auto text-[10px] font-black bg-orange-500 text-white px-1.5 py-0.5 rounded-md">{{ $activeOrdersCount }}</span>
    @endif
</a>

<a href="{{ route('dashboard', ['status' => 'diproses']) }}" class="sidebar-link {{ ($activeStatus ?? '') === 'diproses' ? 'active' : '' }}">
    <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.59 14.37a6 6 0 0 1-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 0 0 6.16-12.12A14.98 14.98 0 0 0 9.631 8.41m5.96 5.96a14.926 14.926 0 0 1-5.841 2.58m-.119-8.54a6 6 0 0 0-7.381 5.84h4.8m2.581-5.84a14.927 14.927 0 0 0-2.58 5.84m2.699 2.7c-.103.021-.207.041-.311.06a15.09 15.09 0 0 1-2.448-2.448 14.9 14.9 0 0 1 .06-.312m-2.24 2.39a4.493 4.493 0 0 0-1.757 4.306 4.493 4.493 0 0 0 4.306-1.758M16.5 9a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z" /></svg>
    Diproses
</a>

<a href="{{ route('dashboard', ['status' => 'siap_diambil']) }}" class="sidebar-link {{ ($activeStatus ?? '') === 'siap_diambil' ? 'active' : '' }}">
    <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" /></svg>
    Siap Diambil
</a>

<a href="{{ route('dashboard', ['status' => 'selesai']) }}" class="sidebar-link {{ ($activeStatus ?? '') === 'selesai' ? 'active' : '' }}">
    <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
    Selesai
</a>

<div class="pt-3 pb-1 px-2">
    <span class="text-[10px] uppercase tracking-widest font-bold text-slate-600">Stand</span>
</div>

<a href="{{ route('katalog.index', ['stand' => $stand->id]) }}" target="_blank" class="sidebar-link">
    <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" /></svg>
    Pratinjau Stand
</a>
@endsection

@section('content')
<div class="p-4 sm:p-6 space-y-5">

    {{-- STAT CARDS --}}
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-4">

        {{-- Omset Hari Ini --}}
        <div class="col-span-2 xl:col-span-1 rounded-2xl p-5 relative overflow-hidden text-white" style="background:linear-gradient(135deg,#f97316,#f59e0b)">
            <div class="absolute -right-4 -top-4 w-24 h-24 rounded-full bg-white/10"></div>
            <div class="absolute right-4 bottom-0 w-12 h-12 rounded-full bg-white/10"></div>
            <p class="text-xs font-bold text-orange-100 uppercase tracking-wider">Omset Hari Ini</p>
            <p class="text-2xl sm:text-3xl font-black mt-1 leading-tight">Rp {{ number_format($revenueToday, 0, ',', '.') }}</p>
            <div class="flex items-center gap-1.5 mt-2">
                <svg class="w-3.5 h-3.5 text-orange-200" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                <span class="text-xs font-semibold text-orange-100">Total pesanan selesai</span>
            </div>
        </div>

        {{-- Pesanan Aktif --}}
        <div class="rounded-2xl p-5 relative overflow-hidden text-white" style="background:linear-gradient(135deg,#ea580c,#f97316)">
            <div class="absolute -right-3 -top-3 w-20 h-20 rounded-full bg-white/10"></div>
            <p class="text-xs font-bold text-orange-100 uppercase tracking-wider">Pesanan Aktif</p>
            <p class="text-3xl font-black mt-1">{{ $activeOrdersCount }}</p>
            <p class="text-xs font-semibold text-orange-100 mt-2">Perlu disiapkan</p>
        </div>

        {{-- Siap Diambil --}}
        <div class="rounded-2xl p-5 relative overflow-hidden text-white" style="background:linear-gradient(135deg,#059669,#10b981)">
            <div class="absolute -right-3 -top-3 w-20 h-20 rounded-full bg-white/10"></div>
            <p class="text-xs font-bold text-emerald-100 uppercase tracking-wider">Pesanan Selesai</p>
            <p class="text-3xl font-black mt-1">{{ $completedOrdersCount }}</p>
            <p class="text-xs font-semibold text-emerald-100 mt-2">Sudah diambil siswa</p>
        </div>

        {{-- Total Hari Ini --}}
        <div class="rounded-2xl p-5 relative overflow-hidden bg-white border border-slate-200 shadow-xs">
            <div class="absolute -right-3 -top-3 w-20 h-20 rounded-full bg-slate-100"></div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Pesanan</p>
            <p class="text-3xl font-black mt-1 text-slate-900">{{ $totalOrdersToday }}</p>
            <p class="text-xs font-semibold text-slate-400 mt-2">Semua status hari ini</p>
        </div>
    </div>

    {{-- ORDERS TABLE --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-slate-100">
            <div>
                <h2 class="text-sm font-extrabold text-slate-900">Daftar Antrean Pesanan Masuk</h2>
                <p class="text-xs text-slate-500 mt-0.5">Ubah status pesanan secara langsung saat menyiapkan makanan.</p>
            </div>
            {{-- Filter Tabs --}}
            <div class="flex items-center gap-1.5 flex-wrap">
                @foreach(['all' => 'Semua', 'pending' => 'Menunggu', 'diproses' => 'Diproses', 'siap_diambil' => 'Siap Ambil', 'selesai' => 'Selesai'] as $val => $label)
                <a href="{{ route('dashboard', ['status' => $val]) }}"
                    class="px-3 py-1.5 rounded-lg text-[11px] font-bold transition-colors whitespace-nowrap
                    {{ ($activeStatus ?? 'all') === $val
                        ? 'bg-orange-500 text-white shadow-xs shadow-orange-300'
                        : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    {{ $label }}
                </a>
                @endforeach
            </div>
        </div>

        {{-- Orders --}}
        @if($orders->isEmpty())
        <div class="text-center py-16 px-6">
            <div class="w-14 h-14 mx-auto mb-4 rounded-2xl bg-orange-50 flex items-center justify-center">
                <svg class="w-7 h-7 text-orange-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 0 1 0 3.75H5.625a1.875 1.875 0 0 1 0-3.75Z" />
                </svg>
            </div>
            <h3 class="text-sm font-bold text-slate-800">Belum Ada Pesanan</h3>
            <p class="text-xs text-slate-500 mt-1">Pesanan baru dari siswa akan muncul di sini secara otomatis.</p>
        </div>
        @else
        <div class="divide-y divide-slate-100">
            @foreach($orders as $order)
            <div class="px-5 py-4 hover:bg-orange-50/30 transition-colors">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">

                    {{-- Left: info --}}
                    <div class="space-y-1.5 flex-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-sm font-black text-slate-900">#{{ $order->kode_tr }}</span>
                            <span class="px-2.5 py-0.5 text-[10px] font-extrabold rounded-full uppercase tracking-wide
                                @if($order->status === 'pending') bg-orange-100 text-orange-700 border border-orange-200
                                @elseif($order->status === 'diproses') bg-amber-100 text-amber-800 border border-amber-200
                                @elseif($order->status === 'siap_diambil') bg-emerald-100 text-emerald-800 border border-emerald-300 animate-pulse
                                @elseif($order->status === 'selesai') bg-slate-100 text-slate-600
                                @else bg-rose-100 text-rose-700 @endif">
                                {{ str_replace('_', ' ', $order->status) }}
                            </span>
                            <span class="inline-flex items-center gap-1 text-[10px] font-semibold bg-slate-100 text-slate-600 px-2 py-0.5 rounded-md">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                                {{ $order->jam_pengambilan }}
                            </span>
                        </div>
                        <div class="text-xs text-slate-500 flex flex-wrap items-center gap-x-2 gap-y-0.5">
                            <span>Pemesan: <strong class="text-slate-800">{{ $order->nama_pemesan }}</strong></span>
                            <span class="text-slate-300">&bull;</span>
                            <span>Kelas: <strong class="text-slate-800">{{ $order->kelas }}</strong></span>
                            <span class="text-slate-300">&bull;</span>
                            <span class="text-slate-400">{{ $order->created_at->diffForHumans() }}</span>
                        </div>
                        @if($order->catatan)
                        <div class="text-xs text-amber-800 bg-amber-50 border border-amber-200 px-2.5 py-1 rounded-lg inline-block">
                            Catatan: <em>"{{ $order->catatan }}"</em>
                        </div>
                        @endif
                        <div class="text-xs text-slate-600 space-y-0.5">
                            @foreach($order->items as $item)
                            <span class="inline-flex items-center gap-1 mr-2 bg-slate-50 border border-slate-200 px-2 py-0.5 rounded-md">
                                {{ $item->menu->nama_menu ?? 'Menu' }} <strong class="text-orange-600">&times;{{ $item->jumlah }}</strong>
                            </span>
                            @endforeach
                        </div>
                    </div>

                    {{-- Right: price + action --}}
                    <div class="flex items-center gap-3 shrink-0">
                        <div class="text-right">
                            <div class="text-base font-black text-orange-600">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</div>
                        </div>
                        <div class="flex items-center gap-1.5">
                            @if($order->status === 'pending')
                            <form action="{{ route('orders.updateStatus', $order->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="diproses">
                                <button type="submit" class="px-3.5 py-2 rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-bold text-xs shadow-xs shadow-orange-300 transition-all">Proses</button>
                            </form>
                            @elseif($order->status === 'diproses')
                            <form action="{{ route('orders.updateStatus', $order->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="siap_diambil">
                                <button type="submit" class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs transition-all">Siap Ambil</button>
                            </form>
                            @elseif($order->status === 'siap_diambil')
                            <form action="{{ route('orders.updateStatus', $order->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="selesai">
                                <button type="submit" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs shadow-xs transition-all">Lunas ✓</button>
                            </form>
                            @endif
                            @if(!in_array($order->status, ['selesai','dibatalkan']))
                            <form action="{{ route('orders.updateStatus', $order->id) }}" method="POST" onsubmit="return confirm('Batalkan pesanan ini?')">
                                @csrf
                                <input type="hidden" name="status" value="dibatalkan">
                                <button type="submit" class="px-2.5 py-2 rounded-xl text-xs font-semibold text-rose-500 hover:bg-rose-50 border border-transparent hover:border-rose-200 transition-colors">Tolak</button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

</div>
@endsection
