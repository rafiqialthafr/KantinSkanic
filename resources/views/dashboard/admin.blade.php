@extends('layouts.admin')

@section('title', 'Admin Dashboard - Kantin Skanic')
@section('sidebar-role', 'Sistem Administrator')
@section('page-title', 'Panel Admin')
@section('page-subtitle', 'Monitoring seluruh stand & transaksi pre-order siswa')

@section('sidebar-nav')
<a href="{{ route('dashboard') }}" class="sidebar-link active">
    <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" /></svg>
    Dashboard
</a>

<div class="pt-3 pb-1 px-2">
    <span class="text-[10px] uppercase tracking-widest font-bold text-slate-600">Kelola</span>
</div>

<a href="{{ route('katalog.index') }}" target="_blank" class="sidebar-link">
    <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" /></svg>
    Daftar Menu Siswa
</a>
@endsection

@section('content')
<div class="p-4 sm:p-6 space-y-5">

    {{-- STAT CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

        {{-- Total Stand --}}
        <div class="rounded-2xl p-5 relative overflow-hidden text-white" style="background:linear-gradient(135deg,#f97316,#f59e0b)">
            <div class="absolute -right-4 -top-4 w-24 h-24 rounded-full bg-white/10"></div>
            <div class="absolute right-6 bottom-2 w-10 h-10 rounded-full bg-white/10"></div>
            <div class="w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center mb-3">
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016 2.993 2.993 0 0 0 2.25-1.016 3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" /></svg>
            </div>
            <p class="text-xs font-bold text-orange-100 uppercase tracking-wider">Total Stand Kantin</p>
            <p class="text-4xl font-black mt-1">{{ $stands->count() }}</p>
            <p class="text-xs text-orange-100 mt-2 font-semibold">Stand aktif beroperasi</p>
        </div>

        {{-- Total Orders --}}
        <div class="rounded-2xl p-5 relative overflow-hidden text-white" style="background:linear-gradient(135deg,#ea580c,#f97316)">
            <div class="absolute -right-4 -top-4 w-24 h-24 rounded-full bg-white/10"></div>
            <div class="w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center mb-3">
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" /></svg>
            </div>
            <p class="text-xs font-bold text-orange-100 uppercase tracking-wider">Total Semua Pesanan</p>
            <p class="text-4xl font-black mt-1">{{ $totalOrders }}</p>
            <p class="text-xs text-orange-100 mt-2 font-semibold">Akumulasi seluruh stand</p>
        </div>

        {{-- Total Revenue --}}
        <div class="rounded-2xl p-5 relative overflow-hidden text-white" style="background:linear-gradient(135deg,#059669,#10b981)">
            <div class="absolute -right-4 -top-4 w-24 h-24 rounded-full bg-white/10"></div>
            <div class="w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center mb-3">
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
            </div>
            <p class="text-xs font-bold text-emerald-100 uppercase tracking-wider">Total Perputaran Omset</p>
            <p class="text-2xl sm:text-3xl font-black mt-1">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
            <p class="text-xs text-emerald-100 mt-2 font-semibold">Pesanan lunas terselesaikan</p>
        </div>
    </div>

    {{-- TWO COLUMN --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">

        {{-- STANDS LIST --}}
        <div class="xl:col-span-1 bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h2 class="text-sm font-extrabold text-slate-900">Stand Terdaftar</h2>
                <p class="text-xs text-slate-500 mt-0.5">{{ $stands->count() }} stand beroperasi</p>
            </div>
            <div class="divide-y divide-slate-100">
                @foreach($stands as $st)
                <div class="px-4 py-3.5 hover:bg-orange-50/30 transition-colors">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3 flex-1 min-w-0">
                            <div class="w-9 h-9 rounded-xl shrink-0 flex items-center justify-center font-black text-sm text-white shadow-sm" style="background:linear-gradient(135deg,#f97316,#f59e0b)">
                                {{ strtoupper(substr($st->nama_stand, 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-1.5">
                                    <span class="text-xs font-black text-slate-900 truncate">{{ $st->nama_stand }}</span>
                                    <span class="text-[9px] font-bold bg-orange-100 text-orange-700 px-1.5 py-0.5 rounded-md shrink-0">{{ $st->nomor_stand }}</span>
                                </div>
                                <p class="text-[10px] text-slate-500 mt-0.5 truncate">{{ $st->user->name ?? '-' }}</p>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-[10px] text-slate-500">{{ $st->menus_count }} menu</span>
                                    <span class="text-slate-300">&bull;</span>
                                    <span class="text-[10px] text-slate-500">{{ $st->orders_count }} pesanan</span>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('katalog.index', ['stand' => $st->id]) }}" target="_blank" class="shrink-0 p-1.5 rounded-lg text-orange-500 hover:bg-orange-50 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" /></svg>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- RECENT ORDERS TABLE --}}
        <div class="xl:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-extrabold text-slate-900">20 Transaksi Terakhir</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Aktivitas pre-order terbaru dari semua stand</p>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/70 text-[10px] font-extrabold uppercase tracking-wider text-slate-400">
                            <th class="py-3 px-4">Kode & Waktu</th>
                            <th class="py-3 px-4">Stand</th>
                            <th class="py-3 px-4">Pemesan</th>
                            <th class="py-3 px-4">Total</th>
                            <th class="py-3 px-4">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($recentOrders as $ro)
                        <tr class="hover:bg-orange-50/20 transition-colors">
                            <td class="py-3 px-4">
                                <span class="font-black text-slate-900 block">#{{ $ro->kode_tr }}</span>
                                <span class="text-[10px] text-slate-400">{{ $ro->created_at->format('d/m H:i') }}</span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="font-semibold text-slate-700">{{ $ro->stand->nama_stand }}</span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="font-bold text-slate-800 block">{{ $ro->nama_pemesan }}</span>
                                <span class="text-[10px] text-slate-400">Kelas {{ $ro->kelas }}</span>
                            </td>
                            <td class="py-3 px-4 font-black text-orange-600">
                                Rp {{ number_format($ro->total_harga, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full uppercase
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
                            <td colspan="6" class="text-center py-10 text-slate-400 text-xs">Belum ada transaksi di sistem.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
