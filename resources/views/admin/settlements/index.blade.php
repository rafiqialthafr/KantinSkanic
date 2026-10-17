@extends('layouts.admin')

@section('title', 'Settlement & Rekapitulasi Pembayaran - Super Admin')
@section('sidebar-role', 'Super Administrator')
@section('page-title', 'Settlement QRIS Stand')
@section('page-subtitle', \Carbon\Carbon::now('Asia/Jakarta')->locale('id')->isoFormat('dddd') . ', ' . \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('d F Y'))

@section('sidebar-nav')
@include('admin.partials.sidebar_nav')
@endsection

@section('content')
<div class="p-3.5 sm:p-6 space-y-6">

    {{-- HEADER BANNER --}}
    <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 rounded-3xl p-5 sm:p-6 text-white shadow-xl shadow-slate-900/10 relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-48 h-48 rounded-full bg-emerald-500/10 blur-2xl pointer-events-none"></div>
        <div class="max-w-2xl relative z-10">
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 text-xs font-black uppercase tracking-wider mb-2 border border-emerald-500/30">
                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                Sistem Settlement Keuangan Kantin
            </div>
            <h2 class="text-xl sm:text-2xl font-black tracking-tight">Rekapitulasi Pembayaran QRIS & Tunai</h2>
            <p class="text-xs sm:text-sm text-slate-300 mt-1 leading-relaxed">
                Pembayaran via <strong>QRIS masuk ke rekening Admin Kantin</strong> dan perlu ditransfer ke masing-masing stand secara berkala. Sedangkan pembayaran <strong>Tunai (COD) langsung diterima fisik</strong> di meja stand masing-masing.
            </p>
        </div>
    </div>

    {{-- FLASH MESSAGES --}}
    @if(session('success'))
    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center gap-2">
        <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
        </svg>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold flex items-center gap-2">
        <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
        </svg>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    {{-- STAT CARDS (FINANCIAL OVERVIEW) --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-4">
        {{-- Total Saldo QRIS Menunggu Transfer (Highlight) --}}
        <div class="col-span-2 sm:col-span-1 rounded-3xl p-5 bg-gradient-to-br from-emerald-600 to-teal-700 text-white shadow-lg shadow-emerald-600/20 relative overflow-hidden">
            <div class="absolute -right-3 -top-3 w-20 h-20 rounded-full bg-white/10"></div>
            <span class="text-[10px] font-black uppercase tracking-wider text-emerald-200 block">Menunggu Ditransfer</span>
            <span class="text-2xl font-black mt-1 block tracking-tight">Rp {{ number_format($qrisPendingPayout, 0, ',', '.') }}</span>
            <span class="text-[11px] text-emerald-100 font-medium mt-2 block">Hak saldo QRIS stand kantin</span>
        </div>

        {{-- Total Pembayaran QRIS Masuk --}}
        <div class="rounded-3xl p-5 bg-white border border-slate-200/90 shadow-2xs">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Total QRIS Masuk</span>
            <span class="text-xl font-black text-slate-900 mt-1 block">Rp {{ number_format($qrisRevenue, 0, ',', '.') }}</span>
            <span class="text-[11px] text-slate-500 font-medium mt-1 block">Uang masuk rekening admin</span>
        </div>

        {{-- Total Pembayaran Tunai (Fisik di Stand) --}}
        <div class="rounded-3xl p-5 bg-white border border-slate-200/90 shadow-2xs">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Total Tunai (COD) Stand</span>
            <span class="text-xl font-black text-slate-900 mt-1 block">Rp {{ number_format($cashRevenue, 0, ',', '.') }}</span>
            <span class="text-[11px] text-slate-500 font-medium mt-1 block">Uang fisik langsung ke stand</span>
        </div>

        {{-- Total QRIS Sudah Dicairkan --}}
        <div class="rounded-3xl p-5 bg-white border border-slate-200/90 shadow-2xs">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Total QRIS Sudah Ditransfer</span>
            <span class="text-xl font-black text-emerald-600 mt-1 block">Rp {{ number_format($qrisSettledTotal, 0, ',', '.') }}</span>
            <span class="text-[11px] text-emerald-700 font-semibold mt-1 block">Histori payout berhasil</span>
        </div>
    </div>

    {{-- STANDS SETTLEMENT / PAYOUT TABLE --}}
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-2xs overflow-hidden">
        <div class="p-5 sm:p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="text-base font-black text-slate-900">Daftar Rekening & Saldo QRIS Stand</h3>
                <p class="text-xs text-slate-500 mt-0.5">Transfer saldo QRIS pembeli ke masing-masing stand kantin sesuai rekening yang terdaftar.</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50/80 border-b border-slate-100 text-[10px] uppercase tracking-wider text-slate-500 font-bold">
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

                        <td class="py-4 px-4">
                            @if($stand->nomor_rekening)
                            <div class="inline-flex flex-col">
                                <span class="font-black text-slate-800 text-xs">{{ $stand->bank_name ?? 'Bank / E-Wallet' }}</span>
                                <span class="font-mono text-slate-600 font-bold text-[11px]">{{ $stand->nomor_rekening }}</span>
                                <span class="text-slate-400 text-[10px]">a.n {{ $stand->atas_nama ?? $stand->pemilik }}</span>
                            </div>
                            @else
                            <span class="text-rose-500 font-bold text-[11px] bg-rose-50 px-2 py-1 rounded-lg">Belum Atur Rekening</span>
                            @endif
                        </td>

                        <td class="py-4 px-4 text-right">
                            <span class="font-bold text-slate-700">{{ $stand->qris_completed_orders_count }} pesanan</span>
                        </td>

                        <td class="py-4 px-4 text-right">
                            @if($stand->saldo_pending > 0)
                            <span class="font-black text-sm text-emerald-600">Rp {{ number_format($stand->saldo_pending, 0, ',', '.') }}</span>
                            <span class="block text-[10px] text-amber-600 font-bold">Siap Dicairkan</span>
                            @else
                            <span class="font-bold text-slate-400">Rp 0</span>
                            <span class="block text-[10px] text-slate-400">Nihil</span>
                            @endif
                        </td>

                        <td class="py-4 px-4 sm:px-6 text-center">
                            @if($stand->saldo_pending > 0)
                            <button type="button" onclick="openPayoutModal({{ $stand->id }}, '{{ addslashes($stand->nama_stand) }}', {{ $stand->saldo_pending }}, '{{ addslashes($stand->bank_name ?? 'Bank') }}', '{{ addslashes($stand->nomor_rekening ?? '-') }}', '{{ addslashes($stand->atas_nama ?? $stand->pemilik) }}')"
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
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-2xs overflow-hidden">
        <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-black text-slate-900">Riwayat Transfer Payout (Audit Trail)</h3>
                <p class="text-xs text-slate-500 mt-0.5">Catatan seluruh bukti transfer dan pencairan saldo QRIS admin ke stand kantin.</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50/80 border-b border-slate-100 text-[10px] uppercase tracking-wider text-slate-500 font-bold">
                    <tr>
                        <th class="py-3 px-4 sm:px-6">Kode Settlement</th>
                        <th class="py-3 px-4">Tanggal & Jam</th>
                        <th class="py-3 px-4">Stand Penerima</th>
                        <th class="py-3 px-4 text-right">Total Dana Ditransfer</th>
                        <th class="py-3 px-4">Ditransfer Oleh</th>
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
                        <td class="py-4 px-4 text-right font-black text-emerald-600">
                            Rp {{ number_format($settle->total_dana, 0, ',', '.') }}
                        </td>
                        <td class="py-4 px-4 text-slate-600">
                            {{ $settle->admin->name ?? 'Super Admin' }}
                        </td>
                        <td class="py-4 px-4 sm:px-6 text-slate-500 italic">
                            {{ $settle->catatan ?? '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-10 text-center text-slate-400">
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
                <button type="button" onclick="closePayoutModal()" class="text-slate-400 hover:text-slate-600 font-bold">&times;</button>
            </div>

            <form action="{{ route('admin.settlements.payout') }}" method="POST" class="space-y-4">
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

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Catatan / Bukti Referensi Transfer (Opsional)</label>
                    <input type="text" name="catatan" placeholder="Contoh: Transfer via BCA / DANA No Ref #9872"
                           class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" onclick="closePayoutModal()" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs shadow-xs active:scale-95 transition-all">
                        Konfirmasi Sudah Ditransfer &check;
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openPayoutModal(standId, standName, amount, bankName, accountNo, ownerName) {
        document.getElementById('modal-stand-id').value = standId;
        document.getElementById('modal-stand-name').innerText = standName;
        document.getElementById('modal-stand-amount').innerText = 'Rp ' + amount.toLocaleString('id-ID');
        document.getElementById('modal-bank-info').innerText = 'Tujuan: ' + bankName + ' - ' + accountNo;
        document.getElementById('modal-owner-info').innerText = 'a.n ' + ownerName;

        document.getElementById('payout-modal').classList.remove('hidden');
    }

    function closePayoutModal() {
        document.getElementById('payout-modal').classList.add('hidden');
    }
</script>
@endsection
