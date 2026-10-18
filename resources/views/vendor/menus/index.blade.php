@extends('layouts.admin')

@section('title', 'Kelola Menu - ' . $stand->nama_stand)
@section('sidebar-role', 'Stand Penjual')
@section('page-title', 'Menu Stand')
@section('page-subtitle', $stand->nama_stand)

@section('sidebar-nav')
@include('vendor.partials.sidebar_nav')
@endsection

@section('content')
<div class="p-3.5 sm:p-6 space-y-4 sm:space-y-6">

    <!-- Header with Add Button -->
    <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/90 p-4 sm:p-6 shadow-2xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3.5 sm:gap-4">
        <div>
            <h1 class="text-base sm:text-xl font-black text-slate-900 tracking-tight">Manajemen Menu Stand</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Tambah menu, perbarui harga, stok, dan toggle status ketersediaan.</p>
        </div>

        <div>
            <button type="button" onclick="openAddMenuModal()"
                class="w-full sm:w-auto min-h-[44px] sm:min-h-[40px] px-4 py-2.5 rounded-xl bg-orange-500 hover:bg-orange-600 active:scale-95 text-white font-bold text-xs sm:text-sm shadow-md shadow-orange-500/20 transition-all flex items-center justify-center gap-2 cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <span>Tambah Menu Baru</span>
            </button>
        </div>
    </div>

    {{-- STAT CARDS MENU --}}
    @php
    $totalMenu = $menus->count();
    $menuAktif = $menus->where('is_available', true)->count();
    $menuNonaktif = $menus->where('is_available', false)->count();
    $menuHabis = $menus->where('stok', 0)->count();
    @endphp
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">

        {{-- Total Menu --}}
        <div class="rounded-2xl p-4 sm:p-5 relative overflow-hidden text-white" style="background:linear-gradient(135deg,#f97316,#ea580c)">
            <div class="absolute -right-4 -top-4 w-24 h-24 rounded-full bg-white/10"></div>
            <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-white/15 flex items-center justify-center mb-1.5 sm:mb-2">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 0 1 0 3.75H5.625a1.875 1.875 0 0 1 0-3.75Z" />
                </svg>
            </div>
            <p class="text-[10px] sm:text-xs font-bold text-orange-100 uppercase tracking-wider">Total Menu</p>
            <p class="text-2xl sm:text-3xl font-black mt-0.5 sm:mt-1">{{ $totalMenu }}</p>
            <p class="text-[10px] sm:text-xs font-semibold text-orange-100 mt-1 sm:mt-2 truncate">Seluruh item terdaftar</p>
        </div>

        {{-- Menu Aktif --}}
        <div class="rounded-2xl p-4 sm:p-5 relative overflow-hidden text-white" style="background:linear-gradient(135deg,#059669,#10b981)">
            <div class="absolute -right-3 -top-3 w-20 h-20 rounded-full bg-white/10"></div>
            <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-white/15 flex items-center justify-center mb-1.5 sm:mb-2">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
            </div>
            <p class="text-[10px] sm:text-xs font-bold text-emerald-100 uppercase tracking-wider">Menu Aktif</p>
            <p class="text-2xl sm:text-3xl font-black mt-0.5 sm:mt-1">{{ $menuAktif }}</p>
            <p class="text-[10px] sm:text-xs font-semibold text-emerald-100 mt-1 sm:mt-2 truncate">Tampil di katalog</p>
        </div>

        {{-- Stok Habis --}}
        <div class="rounded-2xl p-4 sm:p-5 relative overflow-hidden text-white" style="background:linear-gradient(135deg,#f59e0b,#d97706)">
            <div class="absolute -right-3 -top-3 w-20 h-20 rounded-full bg-white/10"></div>
            <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-white/15 flex items-center justify-center mb-1.5 sm:mb-2">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                </svg>
            </div>
            <p class="text-[10px] sm:text-xs font-bold text-amber-100 uppercase tracking-wider">Stok Habis</p>
            <p class="text-2xl sm:text-3xl font-black mt-0.5 sm:mt-1">{{ $menuHabis }}</p>
            <p class="text-[10px] sm:text-xs font-semibold text-amber-100 mt-1 sm:mt-2 truncate">Perlu diisi ulang</p>
        </div>

        {{-- Menu Nonaktif --}}
        <div class="rounded-2xl p-4 sm:p-5 relative overflow-hidden text-white" style="background:linear-gradient(135deg,#64748b,#475569)">
            <div class="absolute -right-3 -top-3 w-20 h-20 rounded-full bg-white/10"></div>
            <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-white/15 flex items-center justify-center mb-1.5 sm:mb-2">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                </svg>
            </div>
            <p class="text-[10px] sm:text-xs font-bold text-slate-200 uppercase tracking-wider">Nonaktif</p>
            <p class="text-2xl sm:text-3xl font-black mt-0.5 sm:mt-1">{{ $menuNonaktif }}</p>
            <p class="text-[10px] sm:text-xs font-semibold text-slate-200 mt-1 sm:mt-2 truncate">Tidak tampil di katalog</p>
        </div>
    </div>

    <!-- Menus Table / List Container -->
    <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/90 shadow-2xs overflow-hidden">
        <div class="px-4 py-3.5 sm:px-6 sm:py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="text-sm sm:text-base font-black text-slate-900">Daftar Menu Stand</h2>
                <p class="text-[11px] sm:text-xs text-slate-500 mt-0.5">Kelola item makanan, minuman, dan snack stand Anda</p>
            </div>
            <span class="text-xs font-bold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-full border border-slate-200">
                {{ $menus->count() }} item
            </span>
        </div>

        @if($menus->isEmpty())
        <div class="text-center py-16 p-6">
            <div class="w-14 h-14 mx-auto mb-3 rounded-2xl bg-orange-50 text-orange-500 flex items-center justify-center">
                <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
            </div>
            <h3 class="text-sm font-bold text-slate-800">Stand Anda Belum Memiliki Menu</h3>
            <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Klik tombol di bawah untuk menambahkan menu pertama stand Anda ke katalog.</p>
            <button type="button" onclick="openAddMenuModal()" class="mt-4 px-4 py-2.5 min-h-[44px] rounded-xl bg-orange-500 hover:bg-orange-600 active:scale-95 text-white font-bold text-xs shadow-md shadow-orange-500/20 cursor-pointer inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <span>+ Tambah Menu Baru</span>
            </button>
        </div>
        @else
        {{-- MOBILE CARD VIEW (< md) --}}
        <div class="block md:hidden divide-y divide-slate-100 p-3 sm:p-4 space-y-3">
            @foreach($menus as $menu)
            <div class="p-3.5 rounded-2xl bg-slate-50/70 border border-slate-200/80 space-y-3">
                <div class="flex items-start gap-3">
                    <div class="w-14 h-14 rounded-xl bg-slate-100 overflow-hidden flex items-center justify-center shrink-0 border border-slate-200/80">
                        @if($menu->foto)
                        <img src="{{ asset('storage/' . $menu->foto) }}" alt="{{ $menu->nama_menu }}" class="w-full h-full object-cover">
                        @else
                        <span class="text-base font-black text-slate-400">{{ strtoupper(substr($menu->nama_menu, 0, 1)) }}</span>
                        @endif
                    </div>

                    <div class="min-w-0 flex-1">
                        <div class="flex items-start justify-between gap-1">
                            <h3 class="font-bold text-slate-900 text-sm leading-snug line-clamp-2">{{ $menu->nama_menu }}</h3>
                            <span class="text-[10px] text-slate-400 font-mono shrink-0">#{{ $menu->id }}</span>
                        </div>
                        <div class="flex items-center gap-1.5 mt-1.5 flex-wrap">
                            @if($menu->kategori === 'makanan')
                            <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded-md bg-amber-50 text-amber-700 border border-amber-200">Makanan</span>
                            @elseif($menu->kategori === 'minuman')
                            <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded-md bg-sky-50 text-sky-700 border border-sky-200">Minuman</span>
                            @else
                            <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200">Snack</span>
                            @endif

                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase {{ $menu->is_available ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $menu->is_available ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                                {{ $menu->is_available ? 'Ready' : 'Habis' }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2 py-2 px-3 rounded-xl bg-white border border-slate-200/70 text-xs">
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Harga</span>
                        <span class="font-black text-slate-900 text-sm">Rp {{ number_format($menu->harga, 0, ',', '.') }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Sisa Stok</span>
                        <span class="font-bold text-sm {{ $menu->stok > 0 ? 'text-slate-800' : 'text-rose-600' }}">
                            {{ $menu->stok }} <span class="text-xs font-normal text-slate-400">porsi</span>
                        </span>
                    </div>
                </div>

                <div class="pt-1 flex items-center gap-2">
                    <form action="{{ route('vendor.menus.toggle', $menu) }}" method="POST" class="flex-1">
                        @csrf
                        <button type="submit"
                            class="w-full min-h-[40px] px-3 py-2 rounded-xl text-xs font-bold transition-all border flex items-center justify-center gap-1.5 cursor-pointer {{ $menu->is_available ? 'bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border-emerald-200' : 'bg-rose-50 hover:bg-rose-100 text-rose-700 border-rose-200' }}">
                            <span class="w-2 h-2 rounded-full {{ $menu->is_available ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                            <span>{{ $menu->is_available ? 'Ubah ke Habis' : 'Ubah ke Ready' }}</span>
                        </button>
                    </form>

                    <button type="button"
                        data-menu="{{ json_encode($menu) }}"
                        onclick="openEditMenuModalFromBtn(this)"
                        class="min-h-[40px] px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors flex items-center justify-center gap-1.5 cursor-pointer">
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                        </svg>
                        <span>Edit</span>
                    </button>

                    <form action="{{ route('vendor.menus.destroy', $menu) }}" method="POST" onsubmit="return confirm('Hapus menu ini secara permanen?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="min-h-[40px] px-3 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold text-xs transition-colors flex items-center justify-center cursor-pointer" title="Hapus menu">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>

        {{-- DESKTOP TABLE VIEW (md+) --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/70 text-[10px] font-extrabold uppercase tracking-wider text-slate-400">
                        <th class="py-3.5 px-5">Nama Menu</th>
                        <th class="py-3.5 px-4">Kategori</th>
                        <th class="py-3.5 px-4">Harga</th>
                        <th class="py-3.5 px-4">Stok</th>
                        <th class="py-3.5 px-4">Status (ON/OFF)</th>
                        <th class="py-3.5 px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($menus as $menu)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <!-- Menu Name & Thumbnail -->
                        <td class="py-4 px-5">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-xl bg-slate-100 overflow-hidden flex items-center justify-center shrink-0 border border-slate-200">
                                    @if($menu->foto)
                                    <img src="{{ asset('storage/' . $menu->foto) }}" alt="{{ $menu->nama_menu }}" class="w-full h-full object-cover">
                                    @else
                                    <span class="text-sm font-black text-slate-400">{{ strtoupper(substr($menu->nama_menu, 0, 1)) }}</span>
                                    @endif
                                </div>
                                <div>
                                    <span class="font-extrabold text-slate-900 text-sm block">{{ $menu->nama_menu }}</span>
                                    <span class="text-[10px] text-slate-400">ID #{{ $menu->id }}</span>
                                </div>
                            </div>
                        </td>

                        <!-- Category -->
                        <td class="py-4 px-4">
                            @if($menu->kategori === 'makanan')
                            <span class="px-2.5 py-1 text-[10px] font-bold uppercase rounded-md bg-amber-50 text-amber-700 border border-amber-200">Makanan</span>
                            @elseif($menu->kategori === 'minuman')
                            <span class="px-2.5 py-1 text-[10px] font-bold uppercase rounded-md bg-sky-50 text-sky-700 border border-sky-200">Minuman</span>
                            @else
                            <span class="px-2.5 py-1 text-[10px] font-bold uppercase rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200">Snack</span>
                            @endif
                        </td>

                        <!-- Price -->
                        <td class="py-4 px-4 font-black text-slate-900 text-sm">
                            Rp {{ number_format($menu->harga, 0, ',', '.') }}
                        </td>

                        <!-- Stock -->
                        <td class="py-4 px-4">
                            <span class="font-bold {{ $menu->stok > 0 ? 'text-slate-800' : 'text-rose-600' }}">
                                {{ $menu->stok }} porsi
                            </span>
                        </td>

                        <!-- Availability Toggle ON/OFF -->
                        <td class="py-4 px-4">
                            <form action="{{ route('vendor.menus.toggle', $menu) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit"
                                    class="px-3 py-1.5 rounded-full text-[10px] font-extrabold uppercase transition-all border cursor-pointer
                                                {{ $menu->is_available ? 'bg-emerald-50 text-emerald-700 border-emerald-300 hover:bg-emerald-100' : 'bg-rose-50 text-rose-700 border-rose-300 hover:bg-rose-100' }}"
                                    title="Klik untuk ubah ketersediaan menu">
                                    {{ $menu->is_available ? '● Tersedia' : '○ Habis' }}
                                </button>
                            </form>
                        </td>

                        <!-- Actions: Edit & Delete -->
                        <td class="py-4 px-5 text-right space-x-2">
                            <button type="button"
                                data-menu="{{ json_encode($menu) }}"
                                onclick="openEditMenuModalFromBtn(this)"
                                class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors cursor-pointer">
                                Edit
                            </button>

                            <form action="{{ route('vendor.menus.destroy', $menu) }}" method="POST" class="inline" onsubmit="return confirm('Hapus menu ini secara permanen?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold text-xs transition-colors cursor-pointer">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    <!-- Modal Tambah Menu Baru -->
    <div id="modal-add-menu" class="fixed inset-0 z-50 overflow-y-auto hidden">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" onclick="closeAddMenuModal()"></div>
        <div class="min-h-full flex items-center justify-center p-3 sm:p-6">
            <div class="bg-white rounded-2xl sm:rounded-3xl max-w-lg w-full p-4 sm:p-6 shadow-2xl relative z-10 border border-slate-200 max-h-[92vh] overflow-y-auto">
                <div class="flex items-center justify-between pb-3.5 sm:pb-4 border-b border-slate-100 mb-4">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900">Tambah Menu Stand Baru</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Lengkapi formulir untuk menambahkan makanan atau minuman</p>
                    </div>
                    <button type="button" onclick="closeAddMenuModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center font-bold text-lg cursor-pointer shrink-0">
                        &times;
                    </button>
                </div>

                <form action="{{ route('vendor.menus.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nama Makanan / Minuman *</label>
                        <input type="text" name="nama_menu" required placeholder="Contoh: Nasi Uduk Komplit"
                            class="w-full min-h-[44px] px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500 font-medium">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Kategori *</label>
                            <select name="kategori" required class="w-full min-h-[44px] px-3 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500 font-medium">
                                <option value="makanan">Makanan</option>
                                <option value="minuman">Minuman</option>
                                <option value="snack">Snack</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Stok Awal *</label>
                            <input type="number" name="stok" value="30" min="0" required
                                class="w-full min-h-[44px] px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500 font-medium">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Harga (Rupiah) *</label>
                        <input type="number" name="harga" placeholder="Contoh: 15000" min="500" step="500" required
                            class="w-full min-h-[44px] px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500 font-medium">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Foto Menu (Opsional)</label>
                        <input type="file" name="foto" accept="image/*"
                            class="w-full text-xs text-slate-500 file:mr-3 file:py-2.5 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100">
                        <p class="text-[11px] text-slate-400 mt-1">Format: JPG, JPEG, PNG, WEBP, JFIF, AVIF (Maks. 5 MB)</p>
                    </div>

                    <div class="flex items-center gap-2 pt-1">
                        <label class="flex items-center gap-2.5 cursor-pointer text-xs font-semibold text-slate-700 select-none">
                            <input type="checkbox" name="is_available" value="1" checked class="w-4 h-4 rounded text-orange-600 border-slate-300 focus:ring-orange-500">
                            <span>Status Langsung Tersedia (Ready)</span>
                        </label>
                    </div>

                    <div class="pt-3 flex items-center justify-end gap-2 border-t border-slate-100">
                        <button type="button" onclick="closeAddMenuModal()" class="min-h-[42px] px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl cursor-pointer">Batal</button>
                        <button type="submit" class="min-h-[42px] px-5 py-2 bg-orange-500 hover:bg-orange-600 text-white text-xs font-black rounded-xl shadow-md shadow-orange-500/25 cursor-pointer">Simpan Menu</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Menu -->
    <div id="modal-edit-menu" class="fixed inset-0 z-50 overflow-y-auto hidden">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" onclick="closeEditMenuModal()"></div>
        <div class="min-h-full flex items-center justify-center p-3 sm:p-6">
            <div class="bg-white rounded-2xl sm:rounded-3xl max-w-lg w-full p-4 sm:p-6 shadow-2xl relative z-10 border border-slate-200 max-h-[92vh] overflow-y-auto">
                <div class="flex items-center justify-between pb-3.5 sm:pb-4 border-b border-slate-100 mb-4">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900">Perbarui Menu</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Ubah rincian informasi dan status menu stand</p>
                    </div>
                    <button type="button" onclick="closeEditMenuModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center font-bold text-lg cursor-pointer shrink-0">
                        &times;
                    </button>
                </div>

                <form id="form-edit-menu" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nama Makanan / Minuman *</label>
                        <input type="text" id="edit-nama-menu" name="nama_menu" required
                            class="w-full min-h-[44px] px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500 font-medium">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Kategori *</label>
                            <select id="edit-kategori" name="kategori" required class="w-full min-h-[44px] px-3 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500 font-medium">
                                <option value="makanan">Makanan</option>
                                <option value="minuman">Minuman</option>
                                <option value="snack">Snack</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Stok *</label>
                            <input type="number" id="edit-stok" name="stok" min="0" required
                                class="w-full min-h-[44px] px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500 font-medium">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Harga (Rupiah) *</label>
                        <input type="number" id="edit-harga" name="harga" min="500" step="500" required
                            class="w-full min-h-[44px] px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500 font-medium">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Ganti Foto Menu (Opsional)</label>
                        <div id="edit-foto-preview-box" class="mb-2 hidden flex items-center gap-3 p-2.5 bg-slate-50 border border-slate-200 rounded-xl">
                            <img id="edit-foto-preview-img" src="" alt="Foto Menu" class="w-12 h-12 rounded-lg object-cover border border-slate-200">
                            <div>
                                <span class="text-xs font-bold text-slate-700 block">Foto Saat Ini</span>
                                <span class="text-[10px] text-slate-400">Pilih file baru di bawah jika ingin mengganti</span>
                            </div>
                        </div>
                        <input type="file" name="foto" accept="image/*"
                            class="w-full text-xs text-slate-500 file:mr-3 file:py-2.5 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100">
                        <p class="text-[11px] text-slate-400 mt-1">Format: JPG, JPEG, PNG, WEBP, JFIF, AVIF (Maks. 5 MB)</p>
                    </div>

                    <div class="flex items-center gap-2 pt-1">
                        <label class="flex items-center gap-2.5 cursor-pointer text-xs font-semibold text-slate-700 select-none">
                            <input type="checkbox" id="edit-is-available" name="is_available" value="1" class="w-4 h-4 rounded text-orange-600 border-slate-300 focus:ring-orange-500">
                            <span>Status Tersedia (Ready)</span>
                        </label>
                    </div>

                    <div class="pt-3 flex items-center justify-end gap-2 border-t border-slate-100">
                        <button type="button" onclick="closeEditMenuModal()" class="min-h-[42px] px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl cursor-pointer">Batal</button>
                        <button type="submit" class="min-h-[42px] px-5 py-2 bg-orange-500 hover:bg-orange-600 text-white text-xs font-black rounded-xl shadow-md shadow-orange-500/25 cursor-pointer">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

<script>
    function openAddMenuModal() {
        document.getElementById('modal-add-menu').classList.remove('hidden');
    }

    function closeAddMenuModal() {
        document.getElementById('modal-add-menu').classList.add('hidden');
    }

    function openEditMenuModalFromBtn(btn) {
        const menu = JSON.parse(btn.getAttribute('data-menu'));
        const form = document.getElementById('form-edit-menu');
        form.action = '/vendor/menus/' + menu.id;

        document.getElementById('edit-nama-menu').value = menu.nama_menu;
        document.getElementById('edit-kategori').value = menu.kategori;
        document.getElementById('edit-stok').value = menu.stok;
        document.getElementById('edit-harga').value = menu.harga;
        document.getElementById('edit-is-available').checked = (menu.is_available == 1);

        const previewBox = document.getElementById('edit-foto-preview-box');
        const previewImg = document.getElementById('edit-foto-preview-img');
        if (menu.foto) {
            previewImg.src = '/storage/' + menu.foto;
            previewBox.classList.remove('hidden');
        } else {
            previewBox.classList.add('hidden');
        }

        document.getElementById('modal-edit-menu').classList.remove('hidden');
    }

    function closeEditMenuModal() {
        document.getElementById('modal-edit-menu').classList.add('hidden');
    }
</script>
@endsection