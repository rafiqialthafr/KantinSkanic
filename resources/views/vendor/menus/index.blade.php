@extends('layouts.admin')

@section('title', 'Kelola Menu - ' . $stand->nama_stand)
@section('sidebar-role', 'Stand Penjual')
@section('page-title', 'Menu Stand')
@section('page-subtitle', $stand->nama_stand)

@section('sidebar-nav')
<a href="{{ route('vendor.dashboard') }}" class="sidebar-link">
    <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25a2.25 2.25 0 0 1 13.5 18v-2.25Z" /></svg>
    Dashboard Pesanan
</a>

<a href="{{ route('vendor.menus.index') }}" class="sidebar-link active">
    <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 0 1 0 3.75H5.625a1.875 1.875 0 0 1 0-3.75Z" /></svg>
    Menu Saya
</a>

<a href="{{ route('vendor.omset') }}" class="sidebar-link {{ request()->routeIs('vendor.omset') ? 'active' : '' }}">
    <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941" /></svg>
    Lihat Omset
</a>

<div class="pt-3 pb-1 px-2">
    <span class="text-[10px] uppercase tracking-widest font-bold text-slate-500">Status Pesanan</span>
</div>

<a href="{{ route('vendor.dashboard', ['status' => 'pending']) }}" class="sidebar-link">
    <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
    Menunggu
</a>
<a href="{{ route('vendor.dashboard', ['status' => 'siap_diambil']) }}" class="sidebar-link">
    <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" /></svg>
    Siap Diambil
</a>
<a href="{{ route('vendor.dashboard', ['status' => 'selesai']) }}" class="sidebar-link">
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


    <!-- Header with Add Button -->
    <div class="bg-white rounded-3xl border border-slate-200/80 p-5 sm:p-6 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-sm sm:text-xl font-black text-slate-900 tracking-tight">Manajemen Menu Stand</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Tambah menu, perbarui harga, stok, dan toggle ketersediaan (Ready vs Habis).</p>
        </div>

        <div>
            <button type="button" onclick="openAddMenuModal()"
                    class="px-4 py-2.5 rounded-xl bg-orange-500 hover:bg-orange-600 active:scale-95 text-white font-bold text-xs sm:text-sm shadow-md shadow-orange-500/20 transition-all flex items-center gap-2 cursor-pointer">
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
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

        {{-- Total Menu --}}
        <div class="col-span-2 sm:col-span-1 rounded-2xl p-5 relative overflow-hidden text-white" style="background:linear-gradient(135deg,#f97316,#ea580c)">
            <div class="absolute -right-4 -top-4 w-24 h-24 rounded-full bg-white/10"></div>
            <div class="w-9 h-9 rounded-xl bg-white/15 flex items-center justify-center mb-2">
                {{-- lucide: list --}}
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 0 1 0 3.75H5.625a1.875 1.875 0 0 1 0-3.75Z"/></svg>
            </div>
            <p class="text-xs font-bold text-orange-100 uppercase tracking-wider">Total Menu</p>
            <p class="text-3xl font-black mt-1">{{ $totalMenu }}</p>
            <p class="text-xs font-semibold text-orange-100 mt-2">Seluruh item terdaftar</p>
        </div>

        {{-- Menu Aktif --}}
        <div class="rounded-2xl p-5 relative overflow-hidden text-white" style="background:linear-gradient(135deg,#059669,#10b981)">
            <div class="absolute -right-3 -top-3 w-20 h-20 rounded-full bg-white/10"></div>
            <div class="w-9 h-9 rounded-xl bg-white/15 flex items-center justify-center mb-2">
                {{-- lucide: check-circle --}}
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
            </div>
            <p class="text-xs font-bold text-emerald-100 uppercase tracking-wider">Menu Aktif</p>
            <p class="text-3xl font-black mt-1">{{ $menuAktif }}</p>
            <p class="text-xs font-semibold text-emerald-100 mt-2">Tampil di katalog</p>
        </div>

        {{-- Stok Habis --}}
        <div class="rounded-2xl p-5 relative overflow-hidden text-white" style="background:linear-gradient(135deg,#f59e0b,#d97706)">
            <div class="absolute -right-3 -top-3 w-20 h-20 rounded-full bg-white/10"></div>
            <div class="w-9 h-9 rounded-xl bg-white/15 flex items-center justify-center mb-2">
                {{-- lucide: alert-triangle --}}
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
            </div>
            <p class="text-xs font-bold text-amber-100 uppercase tracking-wider">Stok Habis</p>
            <p class="text-3xl font-black mt-1">{{ $menuHabis }}</p>
            <p class="text-xs font-semibold text-amber-100 mt-2">Perlu diisi ulang</p>
        </div>

        {{-- Menu Nonaktif - biru --}}
        <div class="rounded-2xl p-5 relative overflow-hidden text-white" style="background:linear-gradient(135deg,#64748b,#475569)">
            <div class="absolute -right-3 -top-3 w-20 h-20 rounded-full bg-white/10"></div>
            <div class="w-9 h-9 rounded-xl bg-white/15 flex items-center justify-center mb-2">
                {{-- lucide: eye-off --}}
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
            </div>
            <p class="text-xs font-bold text-slate-200 uppercase tracking-wider">Nonaktif</p>
            <p class="text-3xl font-black mt-1">{{ $menuNonaktif }}</p>
            <p class="text-xs font-semibold text-slate-200 mt-2">Tidak tampil di katalog</p>
        </div>
    </div>

    <!-- Menus Table / List -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
        @if($menus->isEmpty())
            <div class="text-center py-16 p-6">
                <div class="w-14 h-14 mx-auto mb-3 rounded-2xl bg-orange-50 text-orange-500 flex items-center justify-center">
                    <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </div>
                <h3 class="text-sm font-bold text-slate-800">Stand Anda Belum Memiliki Menu</h3>
                <p class="text-xs text-slate-500 mt-1">Klik tombol di bawah untu
                    k menambahkan menu pertama stand Anda.</p>
                <button type="button" onclick="openAddMenuModal()" class="mt-4 px-4 py-2 rounded-xl bg-orange-500 text-white font-bold text-xs cursor-pointer">
                    + Tambah Menu
                </button>
            </div>
        @else
            <div class="overflow-x-auto">
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
                                <!-- Menu Name -->
                                <td class="py-4 px-5">
                                    <span class="font-extrabold text-slate-900 text-sm block">{{ $menu->nama_menu }}</span>
                                    <span class="text-[10px] text-slate-400">ID #{{ $menu->id }}</span>
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
                                                class="px-3 py-1 rounded-full text-[10px] font-extrabold uppercase transition-all border cursor-pointer
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
                                            class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors cursor-pointer">
                                        Edit
                                    </button>

                                    <form action="{{ route('vendor.menus.destroy', $menu) }}" method="POST" class="inline" onsubmit="return confirm('Hapus menu ini secara permanen?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2.5 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold text-xs transition-colors cursor-pointer">
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
        <div class="min-h-full flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl relative z-10 border border-slate-200">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                    <h3 class="text-base font-extrabold text-slate-900">Tambah Menu Stand Baru</h3>
                    <button type="button" onclick="closeAddMenuModal()" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form action="{{ route('vendor.menus.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nama Makanan / Minuman *</label>
                        <input type="text" name="nama_menu" required placeholder="Contoh: Nasi Uduk Komplit"
                               class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500 font-medium">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Kategori *</label>
                            <select name="kategori" required class="w-full px-3 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500 font-medium">
                                <option value="makanan">Makanan</option>
                                <option value="minuman">Minuman</option>
                                <option value="snack">Snack</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Stok Awal *</label>
                            <input type="number" name="stok" value="30" min="0" required
                                   class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500 font-medium">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Harga (Rupiah) *</label>
                        <input type="number" name="harga" placeholder="Contoh: 15000" min="500" step="500" required
                               class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500 font-medium">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Foto Menu (Opsional)</label>
                        <input type="file" name="foto" accept="image/*"
                               class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100">
                    </div>

                    <div class="flex items-center gap-2 pt-1">
                        <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-slate-700">
                            <input type="checkbox" name="is_available" value="1" checked class="w-4 h-4 rounded text-orange-600 border-slate-300 focus:ring-orange-500">
                            <span>Status Langsung Tersedia (Ready)</span>
                        </label>
                    </div>

                    <div class="pt-3 flex items-center justify-end gap-2 border-t border-slate-100">
                        <button type="button" onclick="closeAddMenuModal()" class="px-4 py-2 text-xs font-bold text-slate-500 hover:bg-slate-100 rounded-xl">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-xs font-black rounded-xl shadow-md cursor-pointer">Simpan Menu</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Menu -->
    <div id="modal-edit-menu" class="fixed inset-0 z-50 overflow-y-auto hidden">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" onclick="closeEditMenuModal()"></div>
        <div class="min-h-full flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl relative z-10 border border-slate-200">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                    <h3 class="text-base font-extrabold text-slate-900">Perbarui Menu</h3>
                    <button type="button" onclick="closeEditMenuModal()" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form id="form-edit-menu" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nama Makanan / Minuman *</label>
                        <input type="text" id="edit-nama-menu" name="nama_menu" required
                               class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500 font-medium">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Kategori *</label>
                            <select id="edit-kategori" name="kategori" required class="w-full px-3 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500 font-medium">
                                <option value="makanan">Makanan</option>
                                <option value="minuman">Minuman</option>
                                <option value="snack">Snack</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Stok *</label>
                            <input type="number" id="edit-stok" name="stok" min="0" required
                                   class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500 font-medium">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Harga (Rupiah) *</label>
                        <input type="number" id="edit-harga" name="harga" min="500" step="500" required
                               class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500 font-medium">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Ganti Foto Menu (Opsional)</label>
                        <input type="file" name="foto" accept="image/*"
                               class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100">
                    </div>

                    <div class="flex items-center gap-2 pt-1">
                        <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-slate-700">
                            <input type="checkbox" id="edit-is-available" name="is_available" value="1" class="w-4 h-4 rounded text-orange-600 border-slate-300 focus:ring-orange-500">
                            <span>Status Tersedia (Ready)</span>
                        </label>
                    </div>

                    <div class="pt-3 flex items-center justify-end gap-2 border-t border-slate-100">
                        <button type="button" onclick="closeEditMenuModal()" class="px-4 py-2 text-xs font-bold text-slate-500 hover:bg-slate-100 rounded-xl">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-xs font-black rounded-xl shadow-md cursor-pointer">Simpan Perubahan</button>
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

        document.getElementById('modal-edit-menu').classList.remove('hidden');
    }
    function closeEditMenuModal() {
        document.getElementById('modal-edit-menu').classList.add('hidden');
    }
</script>
@endsection
