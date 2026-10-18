@extends('layouts.admin')

@section('title', 'Settlement QRIS Stand - Admin Kantin')
@section('sidebar-role', 'Super Administrator')
@section('page-title', 'Settlement QRIS Stand')
@section('page-subtitle', \Carbon\Carbon::now('Asia/Jakarta')->locale('id')->isoFormat('dddd') . ', ' . \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('d F Y'))

@section('sidebar-nav')
@include('admin.partials.sidebar_nav')
@endsection

@section('content')
<div class="p-3.5 sm:p-6 space-y-4 sm:space-y-6">

    {{-- STAT CARDS (3 CARDS: MENUNGGU DITRANSFER, TOTAL QRIS MASUK, SUDAH DITRANSFER) --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
        {{-- Total Saldo QRIS Menunggu Transfer --}}
        <x-stat-card
            title="Menunggu Ditransfer"
            :value="'Rp ' . number_format($qrisPendingPayout, 0, ',', '.')"
            :sublabel="'Hak saldo stand (' . $periodeLabel . ')'"
            :isTimeSensitive="true"
            color="emerald">
            <x-slot:icon>
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
            </x-slot:icon>
        </x-stat-card>

        {{-- Total Pembayaran QRIS Masuk --}}
        <x-stat-card
            title="Total QRIS Masuk"
            :value="'Rp ' . number_format($qrisRevenue, 0, ',', '.')"
            :sublabel="'Dana lunas admin (' . $periodeLabel . ')'"
            :isTimeSensitive="true"
            color="orange">
            <x-slot:icon>
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5ZM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5ZM14.625 3.75c-.621 0-1.125.504-1.125 1.125v4.5c0 .621.504 1.125 1.125 1.125h4.5c.621 0 1.125-.504 1.125-1.125v-4.5c0-.621-.504-1.125-1.125-1.125h-4.5Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.625 14.625h2.25v2.25h-2.25v-2.25ZM19.125 14.625h.375a.75.75 0 0 1 .75.75v.375h-1.125v-1.125ZM14.625 19.125h1.125v1.125h-1.125v-1.125ZM18 18h2.25v2.25H18V18Z" />
                </svg>
            </x-slot:icon>
        </x-stat-card>

        {{-- Total QRIS Sudah Dicairkan --}}
        <x-stat-card
            title="Sudah Ditransfer"
            :value="'Rp ' . number_format($qrisSettledTotal, 0, ',', '.')"
            :sublabel="'Total riwayat payout (' . $periodeLabel . ')'"
            :isTimeSensitive="true"
            color="sky">
            <x-slot:icon>
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
            </x-slot:icon>
        </x-stat-card>
    </div>
    

    {{-- STANDS SETTLEMENT / PAYOUT TABLE --}}
    <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/90 shadow-2xs overflow-hidden">
        <div class="p-4 sm:p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="text-base font-black text-slate-900">Daftar Rekening & Saldo QRIS Stand</h3>
                <p class="text-xs text-slate-500 mt-0.5">Kelola rekening bank/e-wallet stand dan proses transfer pencairan saldo QRIS.</p>
            </div>
            <div class="text-xs text-slate-400">
                Total Stand: <span class="font-bold text-slate-700">{{ $stands->count() }}</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50/80 border-b border-slate-100 text-[10px] uppercase tracking-wider text-slate-500 font-extrabold">
                    <tr>
                        <th class="py-3 px-4 sm:px-6">Stand Kantin</th>
                        <th class="py-3 px-4">Rekening / E-Wallet Tujuan</th>
                        <th class="py-3 px-4 text-right">Pesanan QRIS</th>
                        <th class="py-3 px-4 text-right">Saldo QRIS Tertunda</th>
                        <th class="py-3 px-4 sm:px-6 text-center">Aksi Payout</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($stands as $stand)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        {{-- Stand Kantin --}}
                        <td class="py-4 px-4 sm:px-6">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-2xl bg-orange-100 text-orange-600 font-black text-xs flex items-center justify-center shrink-0">
                                    {{ strtoupper(substr($stand->nama_stand, 0, 1)) }}
                                </div>
                                <div>
                                    <h4 class="font-bold text-sm text-slate-800">{{ $stand->nama_stand }}</h4>
                                    <span class="text-[11px] text-slate-400 block">{{ $stand->pemilik }} &bull; {{ $stand->no_wa ?? '-' }}</span>
                                </div>
                            </div>
                        </td>

                        {{-- Rekening / E-Wallet (Diatur Mandiri oleh Vendor) --}}
                        <td class="py-4 px-4">
                            @if($stand->nomor_rekening)
                            <div class="inline-flex flex-col">
                                <div class="flex items-center gap-1.5">
                                    <span class="font-black text-slate-800 text-xs">{{ $stand->bank_name ?? 'Bank / E-Wallet' }}</span>
                                    <span class="font-mono text-slate-700 font-bold text-[11px] bg-slate-100 px-1.5 py-0.5 rounded">{{ $stand->nomor_rekening }}</span>
                                </div>
                                <span class="text-slate-400 text-[10px] mt-0.5">a.n {{ $stand->atas_nama ?? $stand->pemilik }}</span>
                            </div>
                            @else
                            <div class="inline-flex items-center gap-1.5 text-amber-600 font-bold text-[10px] bg-amber-50 border border-amber-200/80 px-2.5 py-1 rounded-lg" title="Rekening hanya dapat diatur oleh vendor di panel vendor">
                                <svg class="w-3.5 h-3.5 text-amber-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                                </svg>
                                <span>Belum Diatur oleh Vendor</span>
                            </div>
                            @endif
                        </td>

                        {{-- Pesanan QRIS --}}
                        <td class="py-4 px-4 text-right">
                            <span class="font-bold text-slate-700">{{ $stand->qris_completed_orders_count }} selesai</span>
                            @if(($stand->qris_in_progress_orders_count ?? 0) > 0)
                            <span class="block text-[10px] text-amber-600 font-bold">
                                {{ $stand->qris_in_progress_orders_count }} diproses
                            </span>
                            @endif
                        </td>

                        {{-- Saldo Tertunda --}}
                        <td class="py-4 px-4 text-right">
                            @if($stand->saldo_pending > 0)
                            <span class="font-black text-sm text-emerald-600">Rp {{ number_format($stand->saldo_pending, 0, ',', '.') }}</span>
                            <span class="block text-[10px] text-emerald-600 font-bold">Siap Dicairkan</span>
                            @if(($stand->saldo_in_progress ?? 0) > 0)
                            <span class="block text-[10px] text-slate-400 font-medium mt-0.5">
                                + Rp {{ number_format($stand->saldo_in_progress, 0, ',', '.') }} (Diproses)
                            </span>
                            @endif
                            @else
                            <span class="font-bold text-slate-400">Rp 0</span>
                            @if(($stand->saldo_in_progress ?? 0) > 0)
                            <span class="block text-[10px] text-amber-600 font-bold">
                                Rp {{ number_format($stand->saldo_in_progress, 0, ',', '.') }} (Diproses)
                            </span>
                            @else
                            <span class="block text-[10px] text-slate-400">Nihil</span>
                            @endif
                            @endif
                        </td>

                        {{-- Aksi Payout --}}
                        <td class="py-4 px-4 sm:px-6 text-center">
                            @if($stand->saldo_pending > 0)
                            <button type="button"
                                    onclick="openPayoutModal({{ $stand->id }}, '{{ addslashes($stand->nama_stand) }}', {{ $stand->saldo_pending }}, '{{ addslashes($stand->bank_name ?? 'Bank') }}', '{{ addslashes($stand->nomor_rekening ?? '-') }}', '{{ addslashes($stand->atas_nama ?? $stand->pemilik) }}')"
                                    class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white font-black text-xs shadow-xs transition-all cursor-pointer inline-flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                </svg>
                                <span>Cairkan Saldo</span>
                            </button>
                            @else
                            <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-slate-100 text-slate-400 font-bold text-[11px]">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                </svg>
                                <span>Lunas Ditransfer</span>
                            </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-10 text-center text-slate-400">
                            Belum ada data stand terdaftar.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- SETTLEMENT AUDIT HISTORY TABLE --}}
    <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/90 shadow-2xs overflow-hidden">
        <div class="p-4 sm:p-6 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-black text-slate-900">Riwayat Transfer Payout (Audit Trail)</h3>
                <p class="text-xs text-slate-500 mt-0.5">Catatan seluruh bukti transfer dan pencairan saldo QRIS admin ke stand kantin.</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50/80 border-b border-slate-100 text-[10px] uppercase tracking-wider text-slate-500 font-extrabold">
                    <tr>
                        <th class="py-3 px-4 sm:px-6">Kode Settlement</th>
                        <th class="py-3 px-4">Tanggal & Jam</th>
                        <th class="py-3 px-4">Stand Penerima</th>
                        <th class="py-3 px-4 text-right">Nominal Ditransfer</th>
                        <th class="py-3 px-4">Ditransfer Oleh</th>
                        <th class="py-3 px-4 text-center">Bukti Transfer</th>
                        <th class="py-3 px-4 sm:px-6">Catatan / Ref</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($settlements as $settle)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="py-4 px-4 sm:px-6">
                            <span class="font-mono font-bold text-slate-800 bg-slate-100 px-2 py-1 rounded-lg">#{{ $settle->kode_settlement }}</span>
                        </td>
                        <td class="py-4 px-4 text-slate-600">
                            {{ $settle->ditransfer_at ? $settle->ditransfer_at->format('d M Y, H:i') : $settle->created_at->format('d M Y, H:i') }} WIB
                        </td>
                        <td class="py-4 px-4 font-bold text-slate-800">
                            {{ $settle->stand->nama_stand ?? 'Stand' }}
                        </td>
                        <td class="py-4 px-4 text-right font-black text-emerald-600 text-sm">
                            Rp {{ number_format($settle->total_dana, 0, ',', '.') }}
                        </td>
                        <td class="py-4 px-4 text-slate-600 font-medium">
                            {{ $settle->admin->name ?? 'Super Admin' }}
                        </td>
                        <td class="py-4 px-4 text-center">
                            @if($settle->bukti_transfer)
                            <button type="button"
                                    onclick="openImagePreview('{{ asset('storage/' . $settle->bukti_transfer) }}', 'Bukti Transfer #{{ $settle->kode_settlement }} - {{ addslashes($settle->stand->nama_stand ?? 'Stand') }}')"
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl bg-orange-50 hover:bg-orange-100 text-orange-700 font-bold text-[11px] border border-orange-200 transition-all cursor-pointer">
                                <svg class="w-3.5 h-3.5 text-orange-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                </svg>
                                <span>Lihat</span>
                            </button>
                            @else
                            <span class="text-slate-400 text-[10px] italic">Tidak ada foto</span>
                            @endif
                        </td>
                        <td class="py-4 px-4 sm:px-6 text-slate-500 italic">
                            {{ $settle->catatan ?? '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-10 text-center text-slate-400">
                            Belum ada riwayat transfer settlement yang dicatat.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($settlements->hasPages())
        <div class="p-4 border-t border-slate-100">
            {{ $settlements->links() }}
        </div>
        @endif
    </div>
</div>

{{-- MODAL TRANSFER PAYOUT --}}
<div id="payout-modal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" onclick="closePayoutModal()"></div>
    <div class="min-h-full flex items-center justify-center p-4">
        <div class="relative bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-base font-black text-slate-900">Konfirmasi Transfer Saldo QRIS</h3>
                <button type="button" onclick="closePayoutModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-400 hover:text-slate-600 flex items-center justify-center font-bold text-lg">&times;</button>
            </div>

            <form action="{{ route('admin.settlements.payout') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <input type="hidden" name="stand_id" id="modal-stand-id">

                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200">
                    <span class="text-xs text-emerald-800 font-bold block" id="modal-stand-name">Stand Kantin</span>
                    <span class="text-2xl font-black text-emerald-700 block mt-1" id="modal-stand-amount">Rp 0</span>
                    <div class="mt-2 pt-2 border-t border-emerald-200/80 text-[11px] text-emerald-800">
                        <span class="font-bold block" id="modal-bank-info">Rekening: BCA 1234567890</span>
                        <span class="text-emerald-700 block" id="modal-owner-info">a.n Pemilik</span>
                    </div>
                </div>

                {{-- Upload Bukti Foto / Screenshot Transfer --}}
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Foto / Screenshot Bukti Transfer <span class="text-rose-500">*</span>
                    </label>
                    <div class="mt-1">
                        <label for="modal-bukti-transfer" class="flex flex-col items-center justify-center p-4 border-2 border-dashed border-slate-300 hover:border-orange-500 rounded-2xl cursor-pointer bg-slate-50 hover:bg-orange-50/40 transition-all group">
                            <div class="flex flex-col items-center justify-center space-y-1 text-center" id="upload-placeholder">
                                <div class="w-10 h-10 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
                                    </svg>
                                </div>
                                <span class="text-xs font-bold text-slate-700 group-hover:text-orange-600">Unggah Bukti Transfer</span>
                                <span class="text-[10px] text-slate-400">JPG, PNG, atau WEBP (Maksimal 5 MB)</span>
                            </div>

                            {{-- Live Image Preview --}}
                            <div id="upload-preview" class="hidden flex items-center gap-3 w-full">
                                <img id="preview-img" src="" alt="Bukti Transfer" class="w-16 h-16 object-cover rounded-xl border border-slate-200 shrink-0 shadow-2xs">
                                <div class="min-w-0 flex-1 text-left">
                                    <span id="preview-filename" class="text-xs font-bold text-slate-800 block truncate">foto_bukti.png</span>
                                    <span id="preview-filesize" class="text-[10px] text-slate-400 block mt-0.5">240 KB</span>
                                    <span class="text-[10px] text-orange-600 font-semibold mt-1 inline-block">Klik untuk mengganti foto</span>
                                </div>
                            </div>

                            <input type="file" name="bukti_transfer" id="modal-bukti-transfer" accept="image/*" required class="hidden" onchange="previewBukti(this)">
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Tambahan (Opsional)</label>
                    <input type="text" name="catatan" placeholder="Contoh: No Ref #9872 atau Transfer dari Rekening BCA"
                           class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" onclick="closePayoutModal()" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs shadow-xs active:scale-95 transition-all">
                        Konfirmasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- LIGHTBOX IMAGE PREVIEW MODAL --}}
<div id="image-preview-modal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm transition-opacity" onclick="closeImagePreview()"></div>
    <div class="min-h-full flex items-center justify-center p-3 sm:p-6">
        <div class="relative bg-white rounded-3xl max-w-2xl w-full p-4 sm:p-6 shadow-2xl space-y-3 z-10">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="min-w-0 pr-3">
                    <h3 class="text-sm sm:text-base font-black text-slate-900 truncate" id="lightbox-title">Bukti Transfer</h3>
                    <p class="text-[11px] text-slate-400">Bukti transfer pencairan saldo QRIS</p>
                </div>
                <button type="button" onclick="closeImagePreview()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center font-bold text-lg cursor-pointer shrink-0">&times;</button>
            </div>

            <div class="rounded-2xl bg-slate-100 overflow-hidden flex items-center justify-center max-h-[70vh] p-2">
                <img id="lightbox-img" src="" alt="Bukti Transfer" class="max-h-[65vh] w-auto object-contain rounded-xl shadow-xs">
            </div>

            <div class="flex items-center justify-between pt-2">
                <a id="lightbox-link" href="#" target="_blank" class="inline-flex items-center gap-1.5 text-xs font-bold text-orange-600 hover:text-orange-700">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                    </svg>
                    <span>Buka Foto Asli Tab Baru</span>
                </a>
                <button type="button" onclick="closeImagePreview()" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-xs font-bold text-slate-700 cursor-pointer">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function previewBukti(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('preview-img').src = e.target.result;
                document.getElementById('preview-filename').innerText = file.name;
                document.getElementById('preview-filesize').innerText = (file.size / 1024).toFixed(1) + ' KB';
                document.getElementById('upload-placeholder').classList.add('hidden');
                document.getElementById('upload-preview').classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }
    }

    function openPayoutModal(standId, standName, amount, bankName, accountNo, ownerName) {
        document.getElementById('modal-stand-id').value = standId;
        document.getElementById('modal-stand-name').innerText = standName;
        document.getElementById('modal-stand-amount').innerText = 'Rp ' + amount.toLocaleString('id-ID');
        document.getElementById('modal-bank-info').innerText = 'Tujuan: ' + bankName + ' - ' + accountNo;
        document.getElementById('modal-owner-info').innerText = 'a.n ' + ownerName;

        const fileInput = document.getElementById('modal-bukti-transfer');
        if (fileInput) fileInput.value = '';
        document.getElementById('upload-placeholder')?.classList.remove('hidden');
        document.getElementById('upload-preview')?.classList.add('hidden');

        document.getElementById('payout-modal').classList.remove('hidden');
    }

    function closePayoutModal() {
        document.getElementById('payout-modal').classList.add('hidden');
        const fileInput = document.getElementById('modal-bukti-transfer');
        if (fileInput) fileInput.value = '';
        document.getElementById('upload-placeholder')?.classList.remove('hidden');
        document.getElementById('upload-preview')?.classList.add('hidden');
    }

    function openImagePreview(url, title) {
        document.getElementById('lightbox-img').src = url;
        document.getElementById('lightbox-link').href = url;
        document.getElementById('lightbox-title').innerText = title;
        document.getElementById('image-preview-modal').classList.remove('hidden');
    }

    function closeImagePreview() {
        document.getElementById('image-preview-modal').classList.add('hidden');
    }
</script>
@endsection
