@extends('layouts.admin')

@section('title', 'Dashboard - ' . $stand->nama_stand)
@section('sidebar-role', 'Stand Penjual')
@section('page-title', $stand->nama_stand)
@section('page-subtitle', \Carbon\Carbon::now('Asia/Jakarta')->locale('id')->isoFormat('dddd') . ', ' . \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('d F Y'))

@section('sidebar-nav')
@include('vendor.partials.sidebar_nav')
@endsection

@section('content')
<div class="p-3.5 sm:p-6 space-y-4 sm:space-y-6">

    @if(!$stand->nomor_rekening)
    <div class="p-3.5 sm:p-4 rounded-2xl bg-gradient-to-r from-rose-50 to-orange-50 border border-rose-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-2xs">
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
        <a href="{{ route('vendor.rekening.index') }}" class="w-full sm:w-auto min-h-[44px] px-4 py-2 rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-bold text-xs shrink-0 inline-flex items-center justify-center gap-1.5 shadow-xs transition-all">
            <span>Atur Rekening Sekarang</span>
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
            </svg>
        </a>
    </div>
    @endif

    {{-- STATISTIK (HANYA DITAMPILKAN DI TABLET/DESKTOP, DI MOBILE LANGSUNG FOKUS KE PESANAN) --}}
    <div class="hidden md:block space-y-4">
        <div class="flex items-center justify-between gap-2">
            <div>
                <h2 class="text-base sm:text-lg font-black text-slate-900 tracking-tight">Ringkasan Statistik</h2>
                <p class="text-xs text-slate-500 mt-0.5">Periode: <span class="font-bold text-orange-600">{{ $periodeLabel }}</span></p>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">
            {{-- Omset (terpengaruh periode) --}}
            <x-stat-card
                title="Omset"
                :value="'Rp ' . number_format($revenueToday, 0, ',', '.')"
                sublabel="Pesanan selesai lunas"
                color="orange"
                :isTimeSensitive="true">
                <x-slot:icon>
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941"/>
                    </svg>
                </x-slot:icon>
            </x-stat-card>

            {{-- Pesanan Aktif (real-time, tidak terikat periode) --}}
            <x-stat-card
                title="Pesanan Aktif"
                :value="$activeOrdersCount"
                sublabel="Perlu disiapkan"
                :badge="$activeOrdersCount > 0"
                color="amber">
                <x-slot:icon>
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                    </svg>
                </x-slot:icon>
            </x-stat-card>

            {{-- Pesanan Selesai (terpengaruh periode) --}}
            <x-stat-card
                title="Pesanan Selesai"
                :value="$completedOrdersCount"
                sublabel="Sudah diambil"
                color="emerald"
                :isTimeSensitive="true">
                <x-slot:icon>
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
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
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007Z"/>
                    </svg>
                </x-slot:icon>
            </x-stat-card>
        </div>
    </div>

    <div>
        <!-- Status Filter Tabs -->
        <div class="flex items-center gap-1.5 sm:gap-2 overflow-x-auto no-scrollbar pb-1">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider shrink-0 mr-1 hidden sm:inline">Status:</span>
            <a href="{{ route('vendor.dashboard', array_merge(request()->query(), ['status' => 'all'])) }}"
               class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all shrink-0 min-h-[38px] flex items-center {{ ($activeStatus ?? 'all') === 'all' ? 'bg-slate-900 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
                Semua Status
            </a>
            <a href="{{ route('vendor.dashboard', array_merge(request()->query(), ['status' => 'pending'])) }}"
               class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all shrink-0 min-h-[38px] flex items-center {{ ($activeStatus ?? '') === 'pending' ? 'bg-orange-500 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
                Perlu Disiapkan
            </a>
            <a href="{{ route('vendor.dashboard', array_merge(request()->query(), ['status' => 'siap_diambil'])) }}"
               class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all shrink-0 min-h-[38px] flex items-center {{ ($activeStatus ?? '') === 'siap_diambil' ? 'bg-blue-600 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
                Siap Diambil
            </a>
            <a href="{{ route('vendor.dashboard', array_merge(request()->query(), ['status' => 'menunggu_pembayaran'])) }}"
               class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all shrink-0 min-h-[38px] flex items-center {{ ($activeStatus ?? '') === 'menunggu_pembayaran' ? 'bg-amber-500 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
                Belum Bayar
            </a>
            <a href="{{ route('vendor.dashboard', array_merge(request()->query(), ['status' => 'selesai'])) }}"
               class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all shrink-0 min-h-[38px] flex items-center {{ ($activeStatus ?? '') === 'selesai' ? 'bg-slate-600 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
                Selesai
            </a>
        </div>
    </div>

    {{-- ORDERS CARDS / LIST --}}
    <div class="space-y-3 sm:space-y-4">
        @forelse($orders as $order)
        <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/80 shadow-2xs hover:shadow-xs transition-all p-4 sm:p-5 space-y-3.5">
            {{-- Header: Order ID, Customer & Primary Status --}}
            <div class="flex items-start justify-between gap-3 border-b border-slate-100 pb-3">
                <div class="space-y-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="font-mono text-xs font-black bg-slate-100 text-slate-800 px-2.5 py-1 rounded-xl">#{{ $order->kode_tr }}</span>
                        <h4 class="font-extrabold text-slate-900 text-sm sm:text-base leading-snug">{{ $order->nama_pemesan }}</h4>
                        <span class="text-[11px] font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-lg">Kelas {{ $order->kelas }}</span>
                    </div>
                    <div class="flex items-center gap-1.5 text-[11px] text-slate-400">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        <span>{{ $order->created_at->format('d M Y, H:i') }} WIB</span>
                    </div>
                </div>

                {{-- Single Distinct Status Badge --}}
                <div class="shrink-0">
                    @if($order->status === 'menunggu_pembayaran')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-extrabold bg-amber-50 text-amber-700 border border-amber-200/80">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                            Menunggu Pembayaran
                        </span>
                    @elseif($order->status === 'pending')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-extrabold bg-orange-50 text-orange-700 border border-orange-200/80">
                            <span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span>
                            Perlu Disiapkan
                        </span>
                    @elseif($order->status === 'siap_diambil')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-extrabold bg-blue-50 text-blue-700 border border-blue-200/80">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                            Siap Diambil
                        </span>
                    @elseif($order->status === 'selesai')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200/80">
                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                            Selesai
                        </span>
                    @elseif($order->status === 'dibatalkan')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-extrabold bg-rose-50 text-rose-700 border border-rose-200/80">
                            Dibatalkan
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-extrabold bg-slate-100 text-slate-700 border border-slate-200">
                            {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                        </span>
                    @endif
                </div>
            </div>

            {{-- Items Ordered --}}
            <div class="py-1 space-y-2">
                @foreach($order->items as $item)
                <div class="flex items-center justify-between text-xs sm:text-sm">
                    <div class="flex items-center gap-2.5 min-w-0 pr-3">
                        <span class="w-6 h-6 rounded-lg bg-orange-50 text-orange-600 font-extrabold text-xs flex items-center justify-center shrink-0 border border-orange-100">
                            {{ $item->jumlah }}x
                        </span>
                        <span class="font-bold text-slate-800 truncate">{{ $item->menu->nama_menu ?? 'Menu' }}</span>
                    </div>
                    <span class="font-bold text-slate-600 shrink-0">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                </div>
                @endforeach

                @if($order->catatan)
                <div class="mt-2.5 p-2.5 sm:p-3 rounded-xl bg-slate-50 border border-slate-100 text-xs text-slate-600 flex items-start gap-2">
                    <svg class="w-4 h-4 text-slate-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                    </svg>
                    <div>
                        <span class="font-bold text-slate-700">Catatan:</span>
                        <span class="italic text-slate-600 ml-1">{{ $order->catatan }}</span>
                    </div>
                </div>
                @endif
            </div>

            {{-- Footer: Total, Payment Status & Action Buttons --}}
            <div class="pt-3 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center justify-between sm:justify-start gap-2.5 flex-wrap">
                    <div class="flex items-baseline gap-1.5">
                        <span class="text-xs text-slate-400">Total:</span>
                        <span class="text-base sm:text-lg font-black text-slate-900">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</span>
                    </div>

                    {{-- Payment Status Tag --}}
                    @if($order->isQris())
                        @if($order->isLunas())
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800 border border-emerald-200">
                                <svg class="w-3 h-3 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                                LUNAS (QRIS)
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black bg-amber-100 text-amber-900 border border-amber-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                BELUM LUNAS (QRIS)
                            </span>
                        @endif
                    @elseif($order->isLunas())
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800 border border-emerald-200">
                            LUNAS (TUNAI)
                        </span>
                    @else
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-amber-100 text-amber-900 border border-amber-200">
                            BELUM LUNAS (COD)
                        </span>
                    @endif
                </div>

                {{-- Sequential Progression & Actions --}}
                <div class="flex items-center gap-2 flex-wrap w-full sm:w-auto justify-end">
                    @if($order->status === 'menunggu_pembayaran')
                        <div class="flex items-center justify-between w-full sm:w-auto gap-3">
                            <span class="text-xs text-slate-400 italic">Menunggu pembayaran siswa via QRIS</span>
                            <form action="{{ route('vendor.orders.updateStatus', $order) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini?')">
                                @csrf
                                <input type="hidden" name="status" value="dibatalkan">
                                <button type="submit" class="min-h-[38px] px-3.5 py-1.5 rounded-xl text-rose-600 hover:bg-rose-50 font-bold text-xs transition-colors cursor-pointer border border-rose-200 flex items-center justify-center">
                                    Batalkan
                                </button>
                            </form>
                        </div>
                    @else
                        @if($order->isTunai() && $order->isBelumLunas() && !in_array($order->status, ['selesai', 'dibatalkan']))
                        <form action="{{ route('vendor.orders.markPaid', $order) }}" method="POST" class="inline w-full sm:w-auto">
                            @csrf
                            <button type="submit" class="w-full sm:w-auto min-h-[40px] px-3.5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 active:scale-95 text-white font-extrabold text-xs transition-all shadow-xs cursor-pointer flex items-center justify-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                                <span>Tandai Lunas Tunai</span>
                            </button>
                        </form>
                        @endif

                        @if($order->status === 'pending')
                        <form action="{{ route('vendor.orders.updateStatus', $order) }}" method="POST" class="inline flex-1 sm:flex-initial">
                            @csrf
                            <input type="hidden" name="status" value="siap_diambil">
                            <button type="submit" class="w-full sm:w-auto min-h-[40px] px-4 py-2 rounded-xl bg-orange-500 hover:bg-orange-600 active:scale-95 text-white font-extrabold text-xs transition-all shadow-xs cursor-pointer flex items-center justify-center gap-1.5">
                                <span>Tandai Siap Diambil</span>
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                            </button>
                        </form>
                        @elseif($order->status === 'siap_diambil')
                        <form action="{{ route('vendor.orders.updateStatus', $order) }}" method="POST" class="inline flex-1 sm:flex-initial">
                            @csrf
                            <input type="hidden" name="status" value="selesai">
                            <button type="submit" class="w-full sm:w-auto min-h-[40px] px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white font-extrabold text-xs transition-all shadow-xs cursor-pointer flex items-center justify-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                                <span>Selesaikan (Sudah Diambil)</span>
                            </button>
                        </form>
                        @endif

                        @if(!in_array($order->status, ['selesai', 'dibatalkan']))
                        <form action="{{ route('vendor.orders.updateStatus', $order) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini?')">
                            @csrf
                            <input type="hidden" name="status" value="dibatalkan">
                            <button type="submit" class="min-h-[40px] px-3 py-2 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 font-bold text-xs transition-colors cursor-pointer flex items-center justify-center">
                                Batalkan
                            </button>
                        </form>
                        @endif
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-12 sm:py-16 bg-white rounded-2xl sm:rounded-3xl border border-dashed border-slate-300 p-6 sm:p-8">
            <div class="w-14 h-14 mx-auto mb-3 rounded-2xl bg-orange-50 text-orange-400 flex items-center justify-center">
                <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
            </div>
            <h3 class="text-sm font-bold text-slate-800">Tidak Ada Pesanan</h3>
            <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">Belum ada pesanan masuk untuk periode {{ strtolower($periodeLabel) }} dengan filter yang dipilih saat ini.</p>
        </div>
        @endforelse
    </div>

</div>
@endsection
