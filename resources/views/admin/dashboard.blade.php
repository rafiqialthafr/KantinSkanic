@extends('layouts.admin')

@section('title', 'Admin Dashboard - Kantin Skanic')
@section('sidebar-role', 'Super Administrator')
@section('page-title', 'Dashboard')
@section('page-subtitle', \Carbon\Carbon::now('Asia/Jakarta')->locale('id')->isoFormat('dddd') . ', ' . \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('d F Y'))

@section('sidebar-nav')
    @include('admin.partials.sidebar_nav')
@endsection

@section('content')
<div class="p-4 sm:p-6 space-y-6">

    {{-- WELCOME HEADER (SESUAI GAMBAR) --}}
    <div class="space-y-1">
        <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight flex items-center gap-2">
            Selamat Datang! <span class="inline-block">👋</span>
        </h2>
        <p class="text-xs sm:text-sm text-slate-400 font-medium">Kondisi Terkini Kantin SMKN 1 Ciomas
            
        </p>
    </div>

    {{-- 1. STAT CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

        {{-- Total Stand --}}
        <div class="rounded-2xl p-5 relative overflow-hidden text-white" style="background:linear-gradient(135deg,#f97316,#ea580c)">
            <div class="absolute -right-4 -top-4 w-24 h-24 rounded-full bg-white/10"></div>
            <div class="w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center mb-3">
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016 2.993 2.993 0 0 0 2.25-1.016 3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" /></svg>
            </div>
            <p class="text-xs font-bold text-orange-100 uppercase tracking-wider">Total Stand Kantin</p>
            <p class="text-3xl font-black mt-1">{{ $stands->count() }}</p>
            <p class="text-xs text-orange-100 mt-2 font-medium">{{ $stands->where('is_active', true)->count() }} stand aktif</p>
        </div>

        {{-- Total Orders --}}
        <div class="rounded-2xl p-5 relative overflow-hidden text-white" style="background:linear-gradient(135deg,#f59e0b,#d97706)">
            <div class="absolute -right-4 -top-4 w-24 h-24 rounded-full bg-white/10"></div>
            <div class="w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center mb-3">
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" /></svg>
            </div>
            <p class="text-xs font-bold text-amber-100 uppercase tracking-wider">Total Semua Pesanan</p>
            <p class="text-3xl font-black mt-1">{{ $totalOrders }}</p>
            <p class="text-xs text-amber-100 mt-2 font-medium">Akumulasi seluruh stand</p>
        </div>

        {{-- Total Revenue --}}
        <div class="rounded-2xl p-5 relative overflow-hidden text-white" style="background:linear-gradient(135deg,#059669,#10b981)">
            <div class="absolute -right-4 -top-4 w-24 h-24 rounded-full bg-white/10"></div>
            <div class="w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center mb-3">
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
            </div>
            <p class="text-xs font-bold text-emerald-100 uppercase tracking-wider">Total Omset Selesai</p>
            <p class="text-2xl font-black mt-1 leading-tight">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
            <p class="text-xs text-emerald-100 mt-2 font-medium">Pesanan lunas terselesaikan</p>
        </div>

        {{-- Operational Hours --}}
        <div class="rounded-2xl p-5 relative overflow-hidden bg-white border border-slate-200 shadow-xs">
            <div class="w-9 h-9 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center mb-3">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
            </div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Jam Operasional PO</p>
            <p class="text-sm font-extrabold text-slate-800 mt-1">Ist. 1: 09:45 - 10:15</p>
            <p class="text-sm font-extrabold text-slate-800">Ist. 2: 12:00 - 12:45</p>
            <p class="text-[11px] text-emerald-600 font-semibold mt-1 flex items-center gap-1">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <span>Sistem PO Aktif</span>
            </p>
        </div>
    </div>

    {{-- 2. STANDS MANAGEMENT TABLE --}}
    <div id="daftar-stand" class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden scroll-mt-6">
        <div class="px-5 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-base font-extrabold text-slate-900">Manajemen Stand & Kredensial Vendor</h2>
                <p class="text-xs text-slate-500 mt-0.5">Kelola data stand, akun login penjual, status aktif, dan reset kata sandi</p>
            </div>
            <a href="{{ route('admin.stands.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-orange-500 hover:bg-orange-600 active:scale-95 text-white font-bold text-xs shadow-md shadow-orange-500/20 transition-all cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <span>Tambah Stand Baru</span>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/70 text-[10px] font-extrabold uppercase tracking-wider text-slate-400">
                        <th class="py-3 px-5">Stand</th>
                        <th class="py-3 px-4">Pemilik & Kontak</th>
                        <th class="py-3 px-4">Akun Login Vendor</th>
                        <th class="py-3 px-4">Statistik</th>
                        <th class="py-3 px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($stands as $st)
                    @php $vendorUser = $st->user ?? $st->users->first(); @endphp
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="py-4 px-5">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center font-black text-sm text-white shadow-sm shrink-0" style="background:linear-gradient(135deg,#f97316,#f59e0b)">
                                    {{ strtoupper(substr($st->nama_stand, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="flex items-center gap-1.5">
                                        <span class="font-extrabold text-slate-900 text-sm block">{{ $st->nama_stand }}</span>
                                        <span class="w-2 h-2 rounded-full {{ $st->is_active ? 'bg-emerald-500' : 'bg-slate-300' }}" title="{{ $st->is_active ? 'Stand Aktif' : 'Stand Nonaktif' }}"></span>
                                    </div>
                                    <span class="text-[10px] font-bold bg-orange-100 text-orange-700 px-1.5 py-0.5 rounded-md inline-block mt-0.5">{{ $st->nomor_stand }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-4">
                            <span class="font-bold text-slate-800 block">{{ $st->pemilik }}</span>
                            @if($st->no_wa)
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $st->no_wa) }}" target="_blank" class="text-[11px] text-emerald-600 hover:underline font-semibold flex items-center gap-1 mt-0.5">
                                <span>WA: {{ $st->no_wa }}</span>
                            </a>
                            @else
                            <span class="text-[10px] text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="py-4 px-4">
                            @if($vendorUser)
                            <span class="font-semibold text-slate-700 block">{{ $vendorUser->email }}</span>
                            <span class="text-[10px] text-slate-400">{{ $vendorUser->name }}</span>
                            @else
                            <span class="text-rose-500 font-semibold text-[11px]">Belum terhubung akun</span>
                            @endif
                        </td>
                        <td class="py-4 px-4">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 font-bold text-[10px]">{{ $st->menus_count }} Menu</span>
                                <span class="px-2 py-0.5 rounded-md bg-orange-50 text-orange-700 font-bold text-[10px]">{{ $st->orders_count }} Pesanan</span>
                            </div>
                        </td>
                        <td class="py-4 px-5 text-right">
                            <div class="flex items-center justify-end gap-2">
                                @if($vendorUser)
                                <a href="{{ route('admin.vendors.resetPassword', $vendorUser) }}"
                                   class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-orange-50 hover:text-orange-600 text-slate-700 font-bold text-[11px] border border-slate-200 transition-colors"
                                   title="Reset password vendor">
                                    Reset Password
                                </a>
                                @endif
                                <form action="{{ route('admin.stands.toggle', $st) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1.5 rounded-lg text-[11px] font-bold border transition-colors cursor-pointer {{ $st->is_active ? 'border-emerald-200 bg-emerald-50 text-emerald-700 hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200' : 'border-slate-200 bg-slate-100 text-slate-500 hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-200' }}"
                                            title="Klik untuk ubah status aktif/nonaktif">
                                        {{ $st->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </button>
                                </form>
                                <a href="{{ route('katalog.index', ['stand' => $st->id]) }}" target="_blank"
                                   class="p-1.5 rounded-lg text-slate-400 hover:text-orange-600 hover:bg-slate-100 transition-colors"
                                   title="Lihat katalog stand">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-10 text-slate-400">Belum ada stand yang terdaftar.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- 3. RECENT ORDERS TABLE (20 TRANSAKSI TERAKHIR) --}}
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="text-base font-extrabold text-slate-900">20 Transaksi Terbaru (Seluruh Stand)</h2>
                <p class="text-xs text-slate-500 mt-0.5">Aktivitas pesanan pre-order masuk di seluruh kantin sekolah</p>
            </div>
            <a href="{{ route('admin.orders.index') }}" class="text-xs font-bold text-orange-600 hover:text-orange-700 flex items-center gap-1 transition-colors">
                <span>Lihat Semua Riwayat</span>
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                </svg>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/70 text-[10px] font-extrabold uppercase tracking-wider text-slate-400">
                        <th class="py-3 px-5">Kode & Waktu</th>
                        <th class="py-3 px-4">Stand</th>
                        <th class="py-3 px-4">Pemesan</th>
                        <th class="py-3 px-4">Jam Ambil</th>
                        <th class="py-3 px-4">Total</th>
                        <th class="py-3 px-5">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentOrders as $ro)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="py-3.5 px-5">
                            <span class="font-black text-slate-900 block font-mono">#{{ $ro->kode_tr }}</span>
                            <span class="text-[10px] text-slate-400">{{ $ro->created_at->format('d/m H:i') }}</span>
                        </td>
                        <td class="py-3.5 px-4 font-bold text-slate-700">
                            {{ $ro->stand->nama_stand ?? '-' }}
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="font-extrabold text-slate-800 block">{{ $ro->nama_pemesan }}</span>
                            <span class="text-[10px] text-slate-400">{{ $ro->kelas }}</span>
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="px-2 py-0.5 rounded-md bg-orange-50 text-orange-700 font-bold text-[10px] border border-orange-100">{{ $ro->jam_pengambilan }}</span>
                        </td>
                        <td class="py-3.5 px-4 font-black text-orange-600">
                            Rp {{ number_format($ro->total_harga, 0, ',', '.') }}
                        </td>
                        <td class="py-3.5 px-5">
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
                        <td colspan="6" class="text-center py-10 text-slate-400">Belum ada transaksi di sistem.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
