@extends('layouts.admin')

@section('title', 'Kelola Menu - ' . $stand->nama_stand)
@section('sidebar-role', 'Stand Penjual')
@section('page-title', 'Menu Saya')
@section('page-subtitle', $stand->nama_stand)

@section('sidebar-nav')
<a href="{{ route('dashboard') }}" class="sidebar-link">
    <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" /></svg>
    Dashboard
</a>
<a href="{{ route('menus.index') }}" class="sidebar-link active">
    <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 0 1 0 3.75H5.625a1.875 1.875 0 0 1 0-3.75Z" /></svg>
    Menu Saya
</a>
<a href="{{ route('katalog.index', ['stand' => $stand->id]) }}" target="_blank" class="sidebar-link">
    <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" /></svg>
    Pratinjau Stand
</a>
@endsection

@section('content')
<div class="p-4 sm:p-6 space-y-5 pb-16">
    <!-- Header with Back & Add Button -->
    <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('dashboard') }}" class="text-xs font-semibold text-slate-500 hover:text-orange-600 transition-colors flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>
                    <span>Dashboard Pesanan</span>
                </a>
                <span class="text-xs text-slate-300">&bull;</span>
                <span class="text-xs font-bold text-orange-600">{{ $stand->nama_stand }}</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Manajemen Menu Stand</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Tambah menu baru, atur harga, jumlah stok, dan status ketersediaan.</p>
        </div>

        <div>
            <button type="button" onclick="openAddMenuModal()"
                    class="px-4 py-2.5 rounded-xl bg-orange-500 hover:bg-orange-600 active:scale-95 text-white font-bold text-xs sm:text-sm shadow-md shadow-orange-500/20 transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <span>Tambah Menu Baru</span>
            </button>
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
                <p class="text-xs text-slate-500 mt-1">Klik tombol di bawah untuk menambahkan menu pertama Anda.</p>
                <button type="button" onclick="openAddMenuModal()" class="mt-4 px-4 py-2 rounded-xl bg-orange-500 text-white font-bold text-xs">
                    + Tambah Menu
                </button>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50/70 text-[11px] font-extrabold uppercase tracking-wider text-slate-400">
                            <th class="py-3.5 px-5">Nama Menu</th>
                            <th class="py-3.5 px-4">Kategori</th>
                            <th class="py-3.5 px-4">Harga</th>
                            <th class="py-3.5 px-4">Stok</th>
                            <th class="py-3.5 px-4">Status</th>
                            <th class="py-3.5 px-5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @foreach($menus as $menu)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <!-- Menu Name -->
                                <td class="py-4 px-5">
                                    <span class="font-bold text-slate-900 text-sm block">{{ $menu->nama_menu }}</span>
                                    <span class="text-[11px] text-slate-400">ID #{{ $menu->id }}</span>
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

                                <!-- Availability Toggle -->
                                <td class="py-4 px-4">
                                    <form action="{{ route('menus.toggle', $menu->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit"
                                                class="px-2.5 py-1 rounded-full text-[11px] font-bold transition-all border
                                                {{ $menu->is_available ? 'bg-emerald-50 text-emerald-700 border-emerald-300 hover:bg-emerald-100' : 'bg-rose-50 text-rose-700 border-rose-300 hover:bg-rose-100' }}">
                                            {{ $menu->is_available ? '● Tersedia' : '○ Habis' }}
                                        </button>
                                    </form>
                                </td>

                                <!-- Actions: Edit & Delete -->
                                <td class="py-4 px-5 text-right space-x-2">
                                    <button type="button"
                                            data-menu="{{ json_encode($menu) }}"
                                            onclick="openEditMenuModalFromBtn(this)"
                                            class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition-colors cursor-pointer">
                                        Edit
                                    </button>

                                    <form action="{{ route('menus.destroy', $menu->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus menu ini secara permanen?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2.5 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 font-semibold text-xs transition-colors">
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

                <form action="{{ route('menus.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nama Makanan / Minuman *</label>
                        <input type="text" name="nama_menu" required placeholder="Contoh: Nasi Uduk Komplit"
                               class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Kategori *</label>
                            <select name="kategori" required class="w-full px-3 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500">
                                <option value="makanan">Makanan</option>
                                <option value="minuman">Minuman</option>
                                <option value="snack">Snack</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Stok Awal *</label>
                            <input type="number" name="stok" value="30" min="0" required
                                   class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Harga (Rupiah) *</label>
                        <input type="number" name="harga" placeholder="Contoh: 15000" min="500" step="500" required
                               class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500">
                    </div>

                    <div class="flex items-center gap-2 pt-1">
                        <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-slate-700">
                            <input type="checkbox" name="is_available" value="1" checked class="w-4 h-4 rounded text-orange-600 border-slate-300 focus:ring-orange-500">
                            <span>Status Langsung Tersedia (Aktif)</span>
                        </label>
                    </div>

                    <div class="pt-3 flex items-center justify-end gap-2">
                        <button type="button" onclick="closeAddMenuModal()" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-xs font-bold">Batal</button>
                        <button type="submit" class="px-4 py-2.5 rounded-xl bg-orange-500 hover:bg-orange-600 text-white text-xs font-bold shadow-md shadow-orange-500/20">Simpan Menu</button>
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
                    <h3 class="text-base font-extrabold text-slate-900">Edit Menu Stand</h3>
                    <button type="button" onclick="closeEditMenuModal()" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form id="form-edit-menu" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nama Makanan / Minuman *</label>
                        <input type="text" id="edit-nama-menu" name="nama_menu" required
                               class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Kategori *</label>
                            <select id="edit-kategori" name="kategori" required class="w-full px-3 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500">
                                <option value="makanan">Makanan</option>
                                <option value="minuman">Minuman</option>
                                <option value="snack">Snack</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Stok *</label>
                            <input type="number" id="edit-stok" name="stok" min="0" required
                                   class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Harga (Rupiah) *</label>
                        <input type="number" id="edit-harga" name="harga" min="500" step="500" required
                               class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500">
                    </div>

                    <div class="flex items-center gap-2 pt-1">
                        <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-slate-700">
                            <input type="checkbox" id="edit-is-available" name="is_available" value="1" class="w-4 h-4 rounded text-orange-600 border-slate-300 focus:ring-orange-500">
                            <span>Status Tersedia (Dapat Dipesan Siswa)</span>
                        </label>
                    </div>

                    <div class="pt-3 flex items-center justify-end gap-2">
                        <button type="button" onclick="closeEditMenuModal()" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-xs font-bold">Batal</button>
                        <button type="submit" class="px-4 py-2.5 rounded-xl bg-orange-500 hover:bg-orange-600 text-white text-xs font-bold shadow-md shadow-orange-500/20">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openAddMenuModal() {
    document.getElementById('modal-add-menu').classList.remove('hidden');
}
function closeAddMenuModal() {
    document.getElementById('modal-add-menu').classList.add('hidden');
}

function openEditMenuModalFromBtn(btn) {
    try {
        const menu = JSON.parse(btn.getAttribute('data-menu'));
        openEditMenuModal(menu);
    } catch(e) {
        console.error(e);
    }
}

function openEditMenuModal(menu) {
    document.getElementById('form-edit-menu').action = `/dashboard/menus/${menu.id}`;
    document.getElementById('edit-nama-menu').value = menu.nama_menu;
    document.getElementById('edit-kategori').value = menu.kategori;
    document.getElementById('edit-harga').value = menu.harga;
    document.getElementById('edit-stok').value = menu.stok;
    document.getElementById('edit-is-available').checked = Boolean(menu.is_available);

    document.getElementById('modal-edit-menu').classList.remove('hidden');
}
function closeEditMenuModal() {
    document.getElementById('modal-edit-menu').classList.add('hidden');
}
</script>
@endpush
