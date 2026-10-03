@extends('layouts.admin')

@section('title', 'Kelola Stand - Super Admin')
@section('sidebar-role', 'Super Administrator')
@section('page-title', 'Kelola Stand Kantin')
@section('page-subtitle', \Carbon\Carbon::now('Asia/Jakarta')->locale('id')->isoFormat('dddd') . ', ' . \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('d F Y'))

@section('sidebar-nav')
@include('admin.partials.sidebar_nav')
@endsection

@section('content')
<div class="p-3.5 sm:p-6 space-y-4 sm:space-y-6">

    {{-- STAT CARDS (REUSABLE COMPONENT: MOBILE 2 COLS / TABLET-DESKTOP 4 COLS) --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">
        {{-- Total Stand --}}
        <x-stat-card
            title="Total Stand Kantin"
            :value="$totalStands"
            sublabel="Terdaftar di sistem sekolah"
            color="orange">
            <x-slot:icon>
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016 2.993 2.993 0 0 0 2.25-1.016 3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
                </svg>
            </x-slot:icon>
        </x-stat-card>

        {{-- Stand Aktif --}}
        <x-stat-card
            title="Stand Aktif Buka"
            :value="$activeStands"
            sublabel="Dapat melayani pre-order"
            color="emerald">
            <x-slot:icon>
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
            </x-slot:icon>
        </x-stat-card>

        {{-- Stand Nonaktif --}}
        <x-stat-card
            title="Stand Nonaktif"
            :value="$inactiveStands"
            sublabel="Tutup sementara"
            color="slate">
            <x-slot:icon>
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636" />
                </svg>
            </x-slot:icon>
        </x-stat-card>

        {{-- Total Pesanan Masuk (Waktu) --}}
        <x-stat-card
            title="Total Pesanan Masuk"
            :value="number_format($totalOrdersInPeriod, 0, ',', '.')"
            :sublabel="'Pesanan ' . $periodeLabel"
            :isTimeSensitive="true"
            color="amber">
            <x-slot:icon>
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                </svg>
            </x-slot:icon>
        </x-stat-card>
    </div>

    {{-- STANDS MANAGEMENT CARD / TABLE --}}
    <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-base sm:text-lg font-extrabold text-slate-900 leading-snug">Daftar & Manajemen Stand Kantin</h2>
                <p class="text-xs text-slate-500 mt-0.5">Kelola informasi stand, akun login vendor, status aktif, serta statistik pesanan periode <span class="font-semibold text-slate-700">{{ $periodeLabel }}</span></p>
            </div>
            <a href="{{ route('admin.stands.create') }}"
                class="w-full sm:w-auto min-h-[44px] inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-orange-500 hover:bg-orange-600 active:scale-95 text-white font-bold text-xs shadow-md shadow-orange-500/20 transition-all cursor-pointer shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <span>Tambah Stand Baru</span>
            </a>
        </div>

        {{-- MOBILE CARD VIEW (Kusus layar < md agar rapi & tidak perlu scroll horizontal di smartphone 360px+) --}}
        <div class="block md:hidden divide-y divide-slate-100 p-3 sm:p-4 space-y-3">
            @forelse($stands as $st)
            @php $vendorUser = $st->user ?? ($st->users ? $st->users->first() : null); @endphp
            <div class="p-3.5 sm:p-4 rounded-2xl bg-slate-50/70 border border-slate-200/80 space-y-3">
                {{-- Header Card: Stand Info & Status Toggle --}}
                <div class="flex items-start justify-between gap-2.5">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center font-black text-sm text-white shadow-sm shrink-0" style="background:linear-gradient(135deg,#f97316,#f59e0b)">
                            {{ strtoupper(substr($st->nama_stand, 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span class="font-extrabold text-slate-900 text-sm leading-snug truncate">{{ $st->nama_stand }}</span>
                            </div>
                            <span class="text-[10px] font-bold bg-orange-100 text-orange-700 px-2 py-0.5 rounded-md inline-block mt-0.5">{{ $st->nomor_stand }}</span>
                        </div>
                    </div>

                    {{-- Status Toggle Button (min 44px touch target) --}}
                    <form action="{{ route('admin.stands.toggle', $st) }}" method="POST" class="shrink-0">
                        @csrf
                        <button type="submit" class="min-h-[44px] min-w-[70px] inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl text-[10px] font-extrabold uppercase transition-all cursor-pointer {{ $st->is_active ? 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200' : 'bg-slate-200 text-slate-600 hover:bg-slate-300' }}"
                            title="Klik untuk toggle status aktif/nonaktif">
                            <span class="w-2 h-2 rounded-full {{ $st->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                            <span>{{ $st->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                        </button>
                    </form>
                </div>

                {{-- Detail: Pemilik, Kontak, Akun --}}
                <div class="grid grid-cols-1 gap-2 pt-2 border-t border-slate-200/60 text-xs">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Pemilik</span>
                            <span class="font-bold text-slate-800 block mt-0.5">{{ $st->pemilik }}</span>
                        </div>
                        @if($st->no_wa)
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $st->no_wa) }}" target="_blank"
                            class="min-h-[44px] inline-flex items-center gap-1 text-[11px] text-emerald-600 font-bold hover:underline">
                            <span>WA: {{ $st->no_wa }}</span>
                        </a>
                        @endif
                    </div>

                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Akun Vendor</span>
                        @if($vendorUser)
                        <span class="font-semibold text-slate-700 block mt-0.5 truncate">{{ $vendorUser->email }}</span>
                        <span class="text-[10px] text-slate-400 block">{{ $vendorUser->name }}</span>
                        @else
                        <span class="text-rose-500 font-semibold text-[11px] block mt-0.5">Belum terhubung akun</span>
                        @endif
                    </div>
                </div>

                {{-- Statistik & Aksi --}}
                <div class="flex items-center justify-between gap-2 pt-2.5 border-t border-slate-200/60 flex-wrap">
                    {{-- Mini stats --}}
                    <div class="flex items-center gap-1.5">
                        <div class="px-2.5 py-1 rounded-xl bg-white border border-slate-200/80 text-center min-w-[46px] shadow-2xs">
                            <span class="block font-black text-slate-800 text-xs leading-none">{{ $st->menus_count }}</span>
                            <span class="block text-[9px] font-bold text-slate-400 mt-0.5">Menu</span>
                        </div>
                        <div class="px-2.5 py-1 rounded-xl bg-orange-50 border border-orange-200/60 text-center min-w-[50px] shadow-2xs">
                            <span class="block font-black text-orange-600 text-xs leading-none">{{ $st->orders_count }}</span>
                            <span class="block text-[9px] font-bold text-orange-500 mt-0.5">Pesanan</span>
                        </div>
                    </div>

                    {{-- Actions (min 44x44px touch targets) --}}
                    <div class="flex items-center gap-1.5">
                        <a href="{{ route('admin.stands.edit', $st) }}"
                            class="w-11 h-11 flex items-center justify-center rounded-xl text-slate-500 bg-white hover:text-orange-600 hover:bg-orange-50 border border-slate-200 transition-colors shadow-2xs"
                            title="Edit stand">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                            </svg>
                        </a>
                        <form action="{{ route('admin.stands.destroy', $st) }}" method="POST" class="inline"
                            onsubmit="return confirm('Hapus stand &quot;{{ addslashes($st->nama_stand) }}&quot;? Akun vendor terkait juga akan dihapus. Tindakan ini tidak dapat dibatalkan.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="w-11 h-11 flex items-center justify-center rounded-xl text-slate-500 bg-white hover:text-rose-600 hover:bg-rose-50 border border-slate-200 transition-colors cursor-pointer shadow-2xs"
                                title="Hapus stand">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-10 text-slate-400 text-xs">Belum ada stand yang terdaftar.</div>
            @endforelse
        </div>

        {{-- DESKTOP / TABLET TABLE VIEW (md: ke atas dengan wrapper overflow-x-auto) --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left text-xs min-w-[720px]">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/70 text-[10px] font-extrabold uppercase tracking-wider text-slate-400">
                        <th class="py-3 px-5">Stand</th>
                        <th class="py-3 px-4">Pemilik & Kontak</th>
                        <th class="py-3 px-4">Akun Login Vendor</th>
                        <th class="py-3 px-4 text-center">Statistik <span class="text-orange-500 font-bold">({{ $periodeLabel }})</span></th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($stands as $st)
                    @php $vendorUser = $st->user ?? ($st->users ? $st->users->first() : null); @endphp
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="py-4 px-5">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center font-black text-sm text-white shadow-sm shrink-0" style="background:linear-gradient(135deg,#f97316,#f59e0b)">
                                    {{ strtoupper(substr($st->nama_stand, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="flex items-center gap-1.5">
                                        <span class="font-extrabold text-slate-900 text-sm block">{{ $st->nama_stand }}</span>
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
                        <td class="py-4 px-4 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <div class="px-2.5 py-1.5 rounded-xl bg-slate-100 text-center min-w-[48px]">
                                    <span class="block font-black text-slate-800 text-xs leading-none">{{ $st->menus_count }}</span>
                                    <span class="block text-[9px] font-bold text-slate-400 mt-0.5">Menu</span>
                                </div>
                                <div class="px-2.5 py-1.5 rounded-xl bg-orange-50 text-center min-w-[52px]" title="Pesanan periode {{ $periodeLabel }}">
                                    <span class="block font-black text-orange-600 text-xs leading-none">{{ $st->orders_count }}</span>
                                    <span class="block text-[9px] font-bold text-orange-500 mt-0.5">Pesanan</span>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-4 text-center">
                            <div class="flex items-center justify-center">
                                <form action="{{ route('admin.stands.toggle', $st) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[10px] font-extrabold uppercase transition-all cursor-pointer {{ $st->is_active ? 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200' : 'bg-slate-200 text-slate-600 hover:bg-slate-300' }}"
                                        title="Klik untuk toggle status aktif/nonaktif">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $st->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                        <span>{{ $st->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                        <td class="py-4 px-5 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                {{-- Edit Button --}}
                                <a href="{{ route('admin.stands.edit', $st) }}"
                                    class="p-2 rounded-xl text-slate-400 hover:text-orange-600 hover:bg-orange-50 transition-colors"
                                    title="Edit stand">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                    </svg>
                                </a>
                                {{-- Delete Button --}}
                                <form action="{{ route('admin.stands.destroy', $st) }}" method="POST" class="inline"
                                    onsubmit="return confirm('Hapus stand &quot;{{ addslashes($st->nama_stand) }}&quot;? Akun vendor terkait juga akan dihapus. Tindakan ini tidak dapat dibatalkan.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="p-2 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors cursor-pointer"
                                        title="Hapus stand">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-10 text-slate-400">Belum ada stand yang terdaftar.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection