@extends('layouts.admin')

@section('title', 'Rekening Pencairan QRIS - ' . $stand->nama_stand)
@section('sidebar-role', 'Stand Penjual')
@section('page-title', $stand->nama_stand)
@section('page-subtitle', 'Pengaturan Rekening & Histori Pencairan Saldo QRIS')

@section('sidebar-nav')
@include('vendor.partials.sidebar_nav')
@endsection

@section('content')
<div class="p-3.5 sm:p-6 space-y-4 sm:space-y-6 max-w-5xl">

    {{-- FORM ATUR REKENING --}}
    <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/90 shadow-2xs p-4 sm:p-7">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-4 mb-5 sm:mb-6">
            <div>
                <h3 class="text-base font-black text-slate-900">Rekening / E-Wallet Pencairan Stand</h3>
                <p class="text-xs text-slate-500 mt-0.5">Pastikan nomor rekening atau nomor handphone e-wallet benar dan aktif.</p>
            </div>
            <div>
                @if($stand->nomor_rekening)
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold border border-emerald-200">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    Rekening Aktif
                </span>
                @else
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-50 text-rose-700 text-xs font-bold border border-rose-200">
                    <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                    Belum Mengatur Rekening
                </span>
                @endif
            </div>
        </div>

        <form action="{{ route('vendor.rekening.update') }}" method="POST" class="space-y-4 sm:space-y-5">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5 sm:gap-4">
                {{-- Bank / E-Wallet --}}
                <div>
                    <label for="bank_name" class="block text-xs font-bold text-slate-700 mb-1">
                        Bank / E-Wallet <span class="text-rose-500">*</span>
                    </label>
                    <input list="bank_suggestions" type="text" id="bank_name" name="bank_name"
                           value="{{ old('bank_name', $stand->bank_name) }}" required
                           placeholder="BCA, Mandiri, BRI, DANA, GoPay..."
                           class="w-full min-h-[44px] px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all font-medium">
                    <datalist id="bank_suggestions">
                        <option value="BCA">
                        <option value="BRI">
                        <option value="Mandiri">
                        <option value="BNI">
                        <option value="BSI">
                        <option value="CIMB Niaga">
                        <option value="Permata">
                        <option value="DANA">
                        <option value="GoPay">
                        <option value="OVO">
                        <option value="ShopeePay">
                        <option value="LinkAja">
                    </datalist>
                    <span class="text-[10px] text-slate-400 mt-1 block">Bisa bank konvensional atau e-wallet</span>
                </div>

                {{-- Nomor Rekening --}}
                <div>
                    <label for="nomor_rekening" class="block text-xs font-bold text-slate-700 mb-1">
                        Nomor Rekening / No. HP <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="nomor_rekening" name="nomor_rekening"
                           value="{{ old('nomor_rekening', $stand->nomor_rekening) }}" required
                           placeholder="Contoh: 8920192831 atau 0812..."
                           class="w-full min-h-[44px] px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all font-mono font-medium">
                    <span class="text-[10px] text-slate-400 mt-1 block">Nomor rekening atau nomor HP e-wallet</span>
                </div>

                {{-- Atas Nama Rekening --}}
                <div>
                    <label for="atas_nama" class="block text-xs font-bold text-slate-700 mb-1">
                        Atas Nama Penerima <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="atas_nama" name="atas_nama"
                           value="{{ old('atas_nama', $stand->atas_nama ?? $stand->pemilik) }}" required
                           placeholder="Contoh: {{ $stand->pemilik }}"
                           class="w-full min-h-[44px] px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all font-medium">
                    <span class="text-[10px] text-slate-400 mt-1 block">Nama persis sesuai buku tabungan / akun</span>
                </div>
            </div>

            {{-- Optional stand details --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 sm:gap-4 pt-3 border-t border-slate-100">
                <div>
                    <label for="no_wa" class="block text-xs font-bold text-slate-700 mb-1">No. WhatsApp Pemilik</label>
                    <input type="text" id="no_wa" name="no_wa"
                           value="{{ old('no_wa', $stand->no_wa) }}"
                           placeholder="081234567890"
                           class="w-full min-h-[44px] px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all font-medium">
                </div>
                <div>
                    <label for="deskripsi" class="block text-xs font-bold text-slate-700 mb-1">Deskripsi Singkat Stand</label>
                    <input type="text" id="deskripsi" name="deskripsi"
                           value="{{ old('deskripsi', $stand->deskripsi) }}"
                           placeholder="Menu andalan stand..."
                           class="w-full min-h-[44px] px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all font-medium">
                </div>
            </div>

            <div class="pt-3 sm:pt-4 flex items-center justify-end">
                <button type="submit"
                        class="w-full sm:w-auto min-h-[44px] px-5 py-2.5 rounded-xl bg-orange-500 hover:bg-orange-600 active:scale-95 text-white font-black text-xs sm:text-sm shadow-md shadow-orange-500/25 transition-all flex items-center justify-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                    </svg>
                    <span>Simpan Pengaturan Rekening</span>
                </button>
            </div>
        </form>
    </div>

    {{-- RIWAYAT PENCAIRAN SALDO (AUDIT TRAIL STAND) --}}
    <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/90 shadow-2xs overflow-hidden">
        <div class="px-4 py-3.5 sm:px-6 sm:py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-sm sm:text-base font-black text-slate-900">Riwayat Pencairan Saldo QRIS</h3>
                <p class="text-[11px] sm:text-xs text-slate-500 mt-0.5">Catatan transfer pencairan dana yang telah dikirim oleh Admin Kantin ke rekening Anda.</p>
            </div>
            <span class="text-xs font-bold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-full border border-slate-200 shrink-0 ml-2">
                {{ $settlements->total() }} transfer
            </span>
        </div>

        @if($settlements->isEmpty())
        <div class="py-12 px-4 text-center text-slate-400">
            <div class="max-w-xs mx-auto space-y-2">
                <div class="w-12 h-12 mx-auto rounded-2xl bg-orange-50 text-orange-500 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </div>
                <p class="text-xs font-bold text-slate-700">Belum Ada Riwayat Pencairan Saldo QRIS</p>
                <p class="text-[11px] text-slate-400">Dana pesanan selesai akan ditransfer oleh Admin Kantin secara berkala ke rekening terdaftar.</p>
            </div>
        </div>
        @else
        {{-- MOBILE CARD VIEW (< md) --}}
        <div class="block md:hidden divide-y divide-slate-100 p-3 sm:p-4 space-y-3">
            @foreach($settlements as $settle)
            <div class="p-3.5 rounded-2xl bg-slate-50/70 border border-slate-200/80 space-y-2.5">
                <div class="flex items-center justify-between gap-2">
                    <span class="font-mono font-bold text-xs text-slate-800 bg-white border border-slate-200 px-2 py-0.5 rounded-md">
                        #{{ $settle->kode_settlement }}
                    </span>
                    <span class="text-[11px] text-slate-500 font-medium">
                        {{ $settle->ditransfer_at ? $settle->ditransfer_at->format('d M Y, H:i') : $settle->created_at->format('d M Y, H:i') }} WIB
                    </span>
                </div>

                <div class="flex items-center justify-between py-2 px-3 rounded-xl bg-white border border-slate-200/70">
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Nominal Ditransfer</span>
                        <span class="text-base font-black text-emerald-600">Rp {{ number_format($settle->total_dana, 0, ',', '.') }}</span>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Admin</span>
                        <span class="text-xs font-bold text-slate-700">{{ $settle->admin->name ?? 'Admin Kantin' }}</span>
                    </div>
                </div>

                <div class="flex items-center justify-between gap-2 pt-1 text-xs">
                    <div class="min-w-0 flex-1">
                        @if($settle->catatan)
                        <p class="text-[11px] text-slate-500 italic truncate" title="{{ $settle->catatan }}">
                            Ref: {{ $settle->catatan }}
                        </p>
                        @else
                        <span class="text-[11px] text-slate-400 italic">Tanpa catatan tambahan</span>
                        @endif
                    </div>

                    @if($settle->bukti_transfer)
                    <button type="button"
                            onclick="openImagePreview('{{ asset('storage/' . $settle->bukti_transfer) }}', 'Bukti Transfer #{{ $settle->kode_settlement }}')"
                            class="min-h-[38px] px-3 py-1.5 rounded-xl bg-orange-50 hover:bg-orange-100 text-orange-700 font-bold text-xs border border-orange-200 flex items-center gap-1.5 cursor-pointer shrink-0">
                        <svg class="w-3.5 h-3.5 text-orange-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                        </svg>
                        <span>Lihat Bukti</span>
                    </button>
                    @else
                    <span class="text-slate-400 text-[10px] italic">Tidak ada bukti</span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        {{-- DESKTOP TABLE VIEW (md+) --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50/80 border-b border-slate-100 text-[10px] uppercase tracking-wider text-slate-500 font-extrabold">
                    <tr>
                        <th class="py-3.5 px-6">Kode Settlement</th>
                        <th class="py-3.5 px-4">Tanggal & Jam</th>
                        <th class="py-3.5 px-4 text-right">Nominal Ditransfer</th>
                        <th class="py-3.5 px-4">Admin Pengirim</th>
                        <th class="py-3.5 px-4 text-center">Bukti Transfer</th>
                        <th class="py-3.5 px-6">Catatan / Ref</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($settlements as $settle)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="py-4 px-6">
                            <span class="font-mono font-bold text-slate-800 bg-slate-100 px-2.5 py-1 rounded-lg">#{{ $settle->kode_settlement }}</span>
                        </td>
                        <td class="py-4 px-4 text-slate-600">
                            {{ $settle->ditransfer_at ? $settle->ditransfer_at->format('d M Y, H:i') : $settle->created_at->format('d M Y, H:i') }} WIB
                        </td>
                        <td class="py-4 px-4 text-right font-black text-emerald-600 text-sm">
                            Rp {{ number_format($settle->total_dana, 0, ',', '.') }}
                        </td>
                        <td class="py-4 px-4 text-slate-600 font-medium">
                            {{ $settle->admin->name ?? 'Admin Kantin' }}
                        </td>
                        <td class="py-4 px-4 text-center">
                            @if($settle->bukti_transfer)
                            <button type="button"
                                    onclick="openImagePreview('{{ asset('storage/' . $settle->bukti_transfer) }}', 'Bukti Transfer #{{ $settle->kode_settlement }}')"
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
                        <td class="py-4 px-6 text-slate-500 italic">
                            {{ $settle->catatan ?? '-' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        @if($settlements->hasPages())
        <div class="p-3.5 sm:p-4 border-t border-slate-100">
            {{ $settlements->links() }}
        </div>
        @endif
    </div>

</div>

{{-- LIGHTBOX IMAGE PREVIEW MODAL --}}
<div id="image-preview-modal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-xs transition-opacity" onclick="closeImagePreview()"></div>
    <div class="min-h-full flex items-center justify-center p-3 sm:p-6">
        <div class="relative bg-white rounded-2xl sm:rounded-3xl max-w-2xl w-full p-4 sm:p-6 shadow-2xl space-y-3 z-10">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="min-w-0 pr-3">
                    <h3 class="text-sm sm:text-base font-black text-slate-900 truncate" id="lightbox-title">Bukti Transfer</h3>
                    <p class="text-[11px] text-slate-400">Bukti transfer pencairan saldo QRIS dari Admin</p>
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
                <button type="button" onclick="closeImagePreview()" class="min-h-[38px] px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-xs font-bold text-slate-700 cursor-pointer">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<script>
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
