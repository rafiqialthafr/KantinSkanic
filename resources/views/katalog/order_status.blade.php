@extends('layouts.app')

@section('title', 'Struk & Status Pesanan #' . $order->kode_tr)

@section('content')
<div class="max-w-xl mx-auto px-4 sm:px-6 pt-6 sm:pt-8 pb-24 md:pb-16">

    <!-- Top Bar: Back Link & Auto-Update Ping -->
    <div class="mb-5 flex items-center justify-between">
        <a href="{{ route('katalog.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-orange-600 transition-colors p-1">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
            <span>Kembali ke Daftar Menu</span>
        </a>

        <!-- Auto refresh indicator -->
        @if(in_array($order->status, ['pending', 'diproses', 'siap_diambil']))
        <div class="flex items-center gap-1.5 text-[11px] font-bold text-emerald-700 bg-emerald-50 px-3 py-1 rounded-full border border-emerald-200 shadow-2xs">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
            <span>Auto-update aktif</span>
        </div>
        @else
        <span class="text-[11px] font-bold text-slate-400">Pesanan Selesai</span>
        @endif
    </div>

    <!-- Multi-Stand Split Order Tab Bar (If customer ordered from multiple stands) -->
    @if(isset($batchOrders) && $batchOrders->count() > 1)
    <div class="mb-5 bg-gradient-to-r from-orange-600 via-orange-500 to-amber-500 rounded-3xl p-4 sm:p-5 text-white shadow-lg shadow-orange-500/20">
        <div class="flex items-center justify-between gap-2 mb-2 flex-wrap">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black bg-white text-orange-600 uppercase tracking-wider">
                Multi-Stand Split Order
            </span>
            <span class="text-[11px] text-white/90 font-mono font-bold">{{ $order->batch_kode }}</span>
        </div>
        <h3 class="text-sm sm:text-base font-black">Pesanan Anda Dipecah Menjadi {{ $batchOrders->count() }} Stand</h3>
        <p class="text-xs text-white/90 mt-1">Setiap stand memiliki kode pesanan terpisah. Klik stand di bawah untuk melihat tiketnya:</p>

        <!-- Tab Stand Selector -->
        <div class="flex items-center gap-2 overflow-x-auto mt-3.5 pt-3 border-t border-white/20">
            @foreach($batchOrders as $bOrder)
            <a href="{{ route('order.status', $bOrder->kode_tr) }}"
               class="px-3.5 py-2 rounded-xl text-xs font-black transition-all shrink-0 flex items-center gap-2 {{ $bOrder->id === $order->id ? 'bg-white text-orange-600 shadow-md scale-102' : 'bg-white/20 text-white hover:bg-white/30' }}">
                <span>{{ $bOrder->stand->nama_stand }}</span>
                <span class="font-mono text-[10px] px-1.5 py-0.5 rounded-md {{ $bOrder->id === $order->id ? 'bg-orange-100 text-orange-700' : 'bg-black/20 text-white' }}">#{{ $bOrder->kode_tr }}</span>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Digital Receipt Card -->
    <div class="bg-white rounded-[2rem] border border-slate-200/90 shadow-xl shadow-slate-200/50 overflow-hidden relative">

        <!-- Top Colored Header Bar with Status Gradients -->
        <div class="p-6 sm:p-8 text-center text-white relative overflow-hidden
            @if($order->status === 'siap_diambil') bg-gradient-to-br from-emerald-500 via-teal-600 to-emerald-700
            @elseif($order->status === 'diproses') bg-gradient-to-br from-amber-500 via-orange-500 to-amber-600
            @elseif($order->status === 'selesai') bg-gradient-to-br from-slate-700 via-slate-800 to-slate-900
            @elseif($order->status === 'dibatalkan') bg-gradient-to-br from-rose-600 via-red-600 to-rose-700
            @else bg-gradient-to-br from-orange-500 via-amber-500 to-orange-600 @endif">

            <!-- Ambient Pattern in header -->
            <div class="absolute -right-6 -bottom-6 w-32 h-32 rounded-full bg-white/10 blur-xl pointer-events-none"></div>
            <div class="absolute -left-6 -top-6 w-32 h-32 rounded-full bg-black/10 blur-xl pointer-events-none"></div>

            <span class="inline-block text-[11px] font-extrabold uppercase tracking-widest text-white/85 mb-2 bg-black/15 px-3 py-1 rounded-full backdrop-blur-xs">
                KODE PESANAN KANTIN
            </span>

            <div class="text-4xl sm:text-5xl font-black tracking-tight flex items-center justify-center gap-3">
                <span class="font-mono drop-shadow-xs">#{{ $order->kode_tr }}</span>
                <button type="button" onclick="copyOrderCode('{{ $order->kode_tr }}')" title="Salin Kode Pesanan"
                    class="p-2.5 rounded-2xl bg-white/20 hover:bg-white/30 active:scale-90 transition-all text-white backdrop-blur-sm shadow-xs">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.888A2.25 2.25 0 0 0 13.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 0 1-.75.75H9a.75.75 0 0 1-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 0 1-2.25 2.25H6.75A2.25 2.25 0 0 1 4.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 0 1 1.927-.184" />
                    </svg>
                </button>
            </div>

            <!-- High-Contrast Status Pembayaran Badge -->
            <div class="mt-3.5 flex items-center justify-center">
                @if($order->isLunas() || $order->metode_pembayaran === 'qris')
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-2xl bg-emerald-500 text-white shadow-md border-2 border-emerald-300 font-black text-xs sm:text-sm tracking-wide uppercase">
                    <svg class="w-4 h-4 text-emerald-100" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25a2.25 2.25 0 0 1 13.5 18v-2.25Z" />
                    </svg>
                    <span>QRIS</span>
                </div>
                @else
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-2xl bg-amber-500 text-white shadow-md border-2 border-amber-200 font-black text-xs sm:text-sm tracking-wide uppercase">
                    <svg class="w-4 h-4 text-amber-100" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    <span>BAYAR DI TEMPAT (COD)</span>
                </div>
                @endif
            </div>

            <p class="text-xs sm:text-sm text-white/90 mt-2 font-medium">
                Tunjukkan kode ini kepada penjual saat mengambil pesanan
            </p>
        </div>

        <!-- Perforated Receipt Divider Effect -->
        <div class="relative flex items-center justify-between px-3 -my-3 z-10">
            <div class="w-6 h-6 rounded-full bg-slate-50 -ml-6 shadow-inner"></div>
            <div class="flex-1 border-t-2 border-dashed border-slate-200 mx-2"></div>
            <div class="w-6 h-6 rounded-full bg-slate-50 -mr-6 shadow-inner"></div>
        </div>

        <!-- Visual Status Stepper Tracker -->
        <div class="p-6 sm:p-7 bg-slate-50/70 border-b border-slate-200/80">
            <h4 class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 mb-5 text-center">
                Visual Status Pesanan
            </h4>

            @php
            $steps = [
                'pending' => ['label' => 'Menunggu', 'desc' => 'Menunggu konfirmasi'],
                'siap_diambil' => ['label' => 'Siap Diambil', 'desc' => 'Ambil di stand!'],
                'selesai' => ['label' => 'Selesai', 'desc' => 'Sudah diambil & bayar'],
            ];
            $statusOrder = ['pending', 'siap_diambil', 'selesai'];
            $currentIndex = array_search($order->status, $statusOrder);
            if ($currentIndex === false && $order->status === 'dibatalkan') {
                $currentIndex = -1;
            }
            @endphp

            @if($order->status === 'dibatalkan')
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-center">
                <span class="text-rose-700 font-extrabold text-sm block">Pesanan Ini Telah Dibatalkan</span>
                <span class="text-xs text-rose-600 mt-0.5 block">Silakan hubungi penjual stand atau buat pesanan baru.</span>
            </div>
            @else
            <div class="relative flex items-center justify-between max-w-sm mx-auto">
                <!-- Progress Line Background -->
                <div class="absolute top-4 left-4 right-4 h-1.5 bg-slate-200 rounded-full -z-0"></div>
                <!-- Active Progress Line -->
                <div class="absolute top-4 left-4 h-1.5 bg-gradient-to-r from-orange-500 to-amber-500 rounded-full transition-all duration-500 -z-0"
                    style="width: {{ $currentIndex !== false ? ($currentIndex / 2) * 100 : 0 }}%;"></div>

                @foreach($steps as $key => $step)
                @php
                $stepIndex = array_search($key, $statusOrder);
                $isPassed = $currentIndex !== false && $stepIndex <= $currentIndex;
                    $isCurrent=$currentIndex !==false && $stepIndex===$currentIndex;
                    @endphp
                    <div class="flex flex-col items-center relative z-10">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-black transition-all duration-300 shadow-xs
                                {{ $isCurrent ? 'bg-orange-500 text-white ring-4 ring-orange-100 scale-110' : ($isPassed ? 'bg-orange-500 text-white' : 'bg-slate-200 text-slate-500') }}">
                        @if($isPassed && !$isCurrent)
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                        </svg>
                        @else
                        {{ $loop->iteration }}
                        @endif
                    </div>
                    <span class="text-[10px] font-extrabold mt-2 tracking-tight {{ $isCurrent ? 'text-orange-600' : ($isPassed ? 'text-slate-800' : 'text-slate-400') }}">
                        {{ $step['label'] }}
                    </span>
            </div>
            @endforeach
        </div>

        <!-- Alert Callout if Food is Ready -->
        @if($order->status === 'siap_diambil')
        <div class="mt-6 p-4 rounded-2xl bg-emerald-500 text-white shadow-lg shadow-emerald-500/25 flex items-center gap-3.5 animate-bounce">
            <div class="w-10 h-10 rounded-xl bg-white text-emerald-600 flex items-center justify-center shrink-0 font-black text-xl shadow-xs">
                🔔
            </div>
            <div>
                <h5 class="text-sm font-extrabold">Makanan Anda Sudah Siap!</h5>
                <p class="text-xs text-emerald-50 mt-0.5">Silakan langsung menuju ke <strong class="text-white underline">{{ $order->stand->nama_stand }} ({{ $order->stand->nomor_stand }})</strong>.</p>
            </div>
        </div>
        @elseif($order->status === 'diproses')
        <div class="mt-5 p-3 rounded-xl bg-amber-50 border border-amber-200 flex items-center gap-2.5 text-xs text-amber-800 font-semibold">
            <svg class="w-4 h-4 text-amber-600 shrink-0 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>Penjual sedang menyiapkan pesanan Anda. Tunggu status berubah menjadi Siap Diambil.</span>
        </div>
        @endif
        @endif
    </div>

        <!-- Order Information Details Grid -->
        <div class="p-6 sm:p-8 space-y-6">
            <div class="grid grid-cols-2 gap-4 pb-5 border-b border-slate-100 text-xs">
                <div>
                    <span class="text-slate-400 block mb-0.5 font-bold uppercase tracking-wider text-[10px]">Nama Pemesan</span>
                    <strong class="font-extrabold text-slate-800 text-sm block">{{ $order->nama_pemesan }}</strong>
                    <span class="text-slate-500 block font-medium mt-0.5">Kelas: {{ $order->kelas }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block mb-0.5 font-bold uppercase tracking-wider text-[10px]">Stand Kantin</span>
                    <strong class="font-extrabold text-slate-800 text-sm block">{{ $order->stand->nama_stand }}</strong>
                    <span class="text-orange-600 font-bold block mt-0.5">{{ $order->stand->nomor_stand }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block mb-0.5 font-bold uppercase tracking-wider text-[10px]">Metode Pembayaran</span>
                    @if($order->isLunas() || $order->metode_pembayaran === 'qris')
                    <span class="inline-flex items-center gap-1 font-black text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-300 text-xs">
                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25a2.25 2.25 0 0 1 13.5 18v-2.25Z" /></svg>
                        QRIS
                    </span>
                    <span class="text-[10px] text-emerald-600 font-semibold block mt-1">Tinggal ambil makanan</span>
                    @else
                    <span class="inline-flex items-center gap-1 font-black text-amber-800 bg-amber-50 px-2.5 py-1 rounded-lg border border-amber-300 text-xs">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        Bayar di tempat (COD)
                    </span>
                    <span class="text-[10px] text-amber-700 font-semibold block mt-1">Bayar di meja stand saat ambil</span>
                    @endif
                </div>
                <div>
                    <span class="text-slate-400 block mb-0.5 font-bold uppercase tracking-wider text-[10px]">Waktu Pesan</span>
                    <span class="text-slate-700 font-bold block">{{ $order->created_at->format('d M Y, H:i') }} WIB</span>
                </div>
            </div>

            <!-- Optional Catatan Pembeli -->
            @if($order->catatan)
            <div class="p-3.5 bg-amber-50/80 rounded-2xl border border-amber-200 text-xs">
                <span class="font-bold text-amber-800 block mb-0.5">Catatan Tambahan:</span>
                <p class="text-amber-900 italic font-medium">"{{ $order->catatan }}"</p>
            </div>
            @endif

            <!-- Itemized Order Table -->
            <div>
                <h4 class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 mb-3">Rincian Menu Pesanan</h4>
                <div class="space-y-2">
                    @foreach($order->items as $item)
                    <div class="flex items-center justify-between text-xs py-2 border-b border-dashed border-slate-100 last:border-none">
                        <div class="flex-1 pr-2">
                            <span class="font-bold text-slate-800 text-sm block">{{ $item->menu->nama_menu ?? 'Menu' }}</span>
                            <span class="text-slate-400 text-[11px]">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }} &times; {{ $item->jumlah }} porsi</span>
                        </div>
                        <span class="font-extrabold text-slate-800 text-sm">
                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Total Price Summary -->
            <div class="pt-4 border-t-2 border-slate-200 flex items-center justify-between">
                <div>
                    @if($order->isLunas() || $order->metode_pembayaran === 'qris')
                    <span class="text-xs font-bold text-emerald-700 block">Total Pembayaran (Lunas QRIS)</span>
                    <span class="text-[11px] text-emerald-600 font-medium">Pembeli tinggal mengambil makanan di stand</span>
                    @else
                    <span class="text-xs font-bold text-amber-800 block">Total Tagihan (Bayar Tunai / COD)</span>
                    <span class="text-[11px] text-amber-700 font-medium">Bayar langsung di meja stand saat mengambil</span>
                    @endif
                </div>
                <span class="text-2xl sm:text-3xl font-black {{ ($order->isLunas() || $order->metode_pembayaran === 'qris') ? 'text-emerald-600' : 'text-orange-600' }}">
                    Rp {{ number_format($order->total_harga, 0, ',', '.') }}
                </span>
            </div>
        </div>

    <!-- Action Buttons -->
    <div class="p-6 bg-slate-50/80 border-t border-slate-200 flex flex-col sm:flex-row gap-3">
        <button type="button" onclick="window.location.reload()"
            class="flex-1 py-3 px-4 rounded-xl bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 font-bold text-xs sm:text-sm flex items-center justify-center gap-2 transition-colors shadow-2xs">
            <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
            </svg>
            <span>Perbarui Status</span>
        </button>

        <a href="{{ route('katalog.index') }}"
            class="flex-1 py-3 px-4 rounded-xl bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white font-extrabold text-xs sm:text-sm flex items-center justify-center gap-2 shadow-md shadow-orange-500/20 transition-all">
            <span>Pesan Menu Lain</span>
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
            </svg>
        </a>
    </div>
</div>

<!-- Other recent orders if any -->
@if(count($otherOrders) > 0)
<div class="mt-8">
    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Pesanan Anda Lainnya</h4>
    <div class="space-y-2.5">
        @foreach($otherOrders as $o)
        <a href="{{ route('order.status', $o->kode_tr) }}" class="flex items-center justify-between p-4 bg-white rounded-2xl border border-slate-200 hover:border-orange-300 transition-all shadow-xs group">
            <div>
                <span class="font-bold text-xs sm:text-sm text-slate-800 group-hover:text-orange-600 transition-colors">#{{ $o->kode_tr }} &bull; {{ $o->stand->nama_stand }}</span>
                <span class="text-xs text-slate-400 block mt-0.5">Rp {{ number_format($o->total_harga, 0, ',', '.') }}</span>
            </div>
            <span class="px-2.5 py-1 text-[10px] font-extrabold uppercase rounded-lg
                            @if($o->status === 'siap_diambil') bg-emerald-100 text-emerald-700
                            @elseif($o->status === 'diproses') bg-amber-100 text-amber-700
                            @elseif($o->status === 'selesai') bg-slate-100 text-slate-700
                            @else bg-orange-100 text-orange-700 @endif">
                {{ ucfirst(str_replace('_', ' ', $o->status)) }}
            </span>
        </a>
        @endforeach
    </div>
</div>
@endif

<!-- Toast Copy Notification -->
<div id="copy-toast" class="fixed bottom-20 md:bottom-6 left-1/2 -translate-x-1/2 bg-slate-900 text-white px-5 py-2.5 rounded-2xl shadow-2xl text-xs font-bold transition-all transform translate-y-20 opacity-0 pointer-events-none z-[60] flex items-center gap-2">
    <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
    </svg>
    <span>Kode pesanan berhasil disalin!</span>
</div>

</div>
@endsection

@push('scripts')
<script>
    function copyOrderCode(code) {
        navigator.clipboard.writeText(code).then(() => {
            showCopyToast();
        }).catch(() => {
            prompt('Salin kode pesanan:', code);
        });
    }

    function showCopyToast() {
        const toast = document.getElementById('copy-toast');
        if (toast) {
            toast.classList.remove('translate-y-20', 'opacity-0');
            setTimeout(() => {
                toast.classList.add('translate-y-20', 'opacity-0');
            }, 2500);
        }
    }

    // Auto-save this order code to local storage for quick access in modal
    try {
        const code = '{{ $order->kode_tr }}';
        if (typeof saveOrderToHistoryStorage === 'function') {
            saveOrderToHistoryStorage(code);
        } else {
            let orders = JSON.parse(localStorage.getItem('kantinskanic_order_history') || '[]');
            if (!orders.includes(code)) {
                orders.unshift(code);
                localStorage.setItem('kantinskanic_order_history', JSON.stringify(orders));
            }
        }
    } catch (e) {}

    // Auto refresh status every 5 seconds if order is active
    @if(in_array($order->status, ['pending', 'diproses', 'siap_diambil', 'menunggu_pembayaran']) || (!$order->isLunas() && $order->isQris()))
    setInterval(() => {
        window.location.reload();
    }, 5000);
    @endif
</script>
@endpush