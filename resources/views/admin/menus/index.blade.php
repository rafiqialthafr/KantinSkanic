@extends('layouts.admin')

@section('title', 'Semua Menu Kantin - Super Admin')
@section('sidebar-role', 'Super Administrator')
@section('page-title', 'Semua Menu Kantin')
@section('page-subtitle', 'Monitoring daftar produk makanan & minuman yang dijual seluruh stand kantin')

@section('sidebar-nav')
    @include('admin.partials.sidebar_nav')
@endsection

@section('content')
<div class="p-4 sm:p-6 space-y-6">

    {{-- STAT CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="rounded-2xl p-5 bg-white border border-slate-200/90 shadow-xs">
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Menu Terdaftar</p>
            <p class="text-3xl font-black text-slate-900 mt-1">{{ number_format($totalMenusCount, 0, ',', '.') }}</p>
            <p class="text-xs text-slate-500 mt-1 font-medium">Dari seluruh stand aktif</p>
        </div>

        <div class="rounded-2xl p-5 bg-white border border-slate-200/90 shadow-xs">
            <p class="text-[11px] font-bold text-emerald-500 uppercase tracking-wider">Menu Tersedia</p>
            <p class="text-3xl font-black text-emerald-600 mt-1">{{ number_format($availableMenusCount, 0, ',', '.') }}</p>
            <p class="text-xs text-emerald-600/80 mt-1 font-medium">Siap dipesan siswa di katalog</p>
        </div>

        <div class="rounded-2xl p-5 bg-white border border-slate-200/90 shadow-xs">
            <p class="text-[11px] font-bold text-rose-500 uppercase tracking-wider">Stok Habis / Nonaktif</p>
            <p class="text-3xl font-black text-rose-600 mt-1">{{ number_format($outOfStockCount, 0, ',', '.') }}</p>
            <p class="text-xs text-rose-600/80 mt-1 font-medium">Sementara tidak dapat dipesan</p>
        </div>
    </div>

    {{-- FILTER FORM --}}
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs p-5">
        <form method="GET" action="{{ route('admin.menus.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div>
                <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1">Cari Menu</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama menu..."
                       class="w-full text-xs rounded-xl border border-slate-200 px-3 py-2 text-slate-800 placeholder-slate-400 focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20">
            </div>

            <div>
                <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1">Pilih Stand</label>
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
                <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1">Kategori</label>
                <select name="kategori" class="w-full text-xs rounded-xl border border-slate-200 px-3 py-2 text-slate-800 focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20">
                    <option value="">Semua Kategori</option>
                    <option value="makanan" {{ request('kategori') === 'makanan' ? 'selected' : '' }}>Makanan</option>
                    <option value="minuman" {{ request('kategori') === 'minuman' ? 'selected' : '' }}>Minuman</option>
                    <option value="snack" {{ request('kategori') === 'snack' ? 'selected' : '' }}>Snack / Camilan</option>
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1">Ketersediaan</label>
                <select name="is_available" class="w-full text-xs rounded-xl border border-slate-200 px-3 py-2 text-slate-800 focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20">
                    <option value="">Semua Status</option>
                    <option value="1" {{ request('is_available') === '1' ? 'selected' : '' }}>Tersedia</option>
                    <option value="0" {{ request('is_available') === '0' ? 'selected' : '' }}>Habis / Nonaktif</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 px-4 rounded-xl bg-orange-500 hover:bg-orange-600 active:scale-95 text-white font-bold text-xs shadow-sm transition-all cursor-pointer">
                    Filter
                </button>
                @if(request()->anyFilled(['search', 'stand_id', 'kategori', 'is_available']))
                <a href="{{ route('admin.menus.index') }}" class="py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs transition-colors">
                    Reset
                </a>
                @endif
            </div>
        </form>
    </div>

    {{-- MENUS TABLE --}}
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/70 text-[10px] font-extrabold uppercase tracking-wider text-slate-400">
                        <th class="py-3 px-5">Menu</th>
                        <th class="py-3 px-4">Stand</th>
                        <th class="py-3 px-4">Kategori</th>
                        <th class="py-3 px-4">Harga</th>
                        <th class="py-3 px-4">Sisa Stok</th>
                        <th class="py-3 px-5 text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($menus as $menu)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="py-3.5 px-5">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-slate-100 overflow-hidden flex items-center justify-center shrink-0 border border-slate-200/70">
                                    @if($menu->foto)
                                        <img src="{{ asset('storage/' . $menu->foto) }}" alt="{{ $menu->nama_menu }}" class="w-full h-full object-cover">
                                    @else
                                        <span class="text-xs font-black text-slate-400">{{ strtoupper(substr($menu->nama_menu, 0, 1)) }}</span>
                                    @endif
                                </div>
                                <span class="font-bold text-slate-800 text-xs">{{ $menu->nama_menu }}</span>
                            </div>
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="font-bold text-slate-700 block">{{ $menu->stand->nama_stand ?? '-' }}</span>
                            <span class="text-[10px] text-orange-600 font-semibold">{{ $menu->stand->nomor_stand ?? '' }}</span>
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 font-bold text-[10px] uppercase">
                                {{ $menu->kategori }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 font-black text-slate-900">
                            Rp {{ number_format($menu->harga, 0, ',', '.') }}
                        </td>
                        <td class="py-3.5 px-4 font-bold text-slate-700">
                            {{ $menu->stok }} porsi
                        </td>
                        <td class="py-3.5 px-5 text-right">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase {{ $menu->is_available && $menu->stok > 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-700' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $menu->is_available && $menu->stok > 0 ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                                <span>{{ $menu->is_available && $menu->stok > 0 ? 'Tersedia' : 'Habis' }}</span>
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-12 text-slate-400 font-medium">
                            Tidak ada data menu yang ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($menus->hasPages())
        <div class="px-5 py-4 border-t border-slate-100 bg-slate-50/50">
            {{ $menus->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
