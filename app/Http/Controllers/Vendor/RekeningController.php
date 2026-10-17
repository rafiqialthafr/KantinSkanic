<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Settlement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RekeningController extends Controller
{
    /**
     * Display vendor's payout bank account configuration & payout settlement history.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $stand = $user->stand;
        abort_unless($stand, 403, 'Akun tidak terhubung ke Stand.');

        // Saldo QRIS Siap Dicairkan (Pesanan Selesai, Belum Dicairkan Admin)
        $saldoPending = (int) Order::where('stand_id', $stand->id)
            ->where('status', 'selesai')
            ->where('metode_pembayaran', 'qris')
            ->where('status_pembayaran', 'lunas')
            ->where('settlement_status', 'pending')
            ->sum('total_harga');

        // Saldo QRIS Dalam Proses (Pesanan Masih Berjalan)
        $saldoInProgress = (int) Order::where('stand_id', $stand->id)
            ->whereIn('status', ['pending', 'diproses', 'siap_diambil'])
            ->where('metode_pembayaran', 'qris')
            ->where('status_pembayaran', 'lunas')
            ->where('settlement_status', 'pending')
            ->sum('total_harga');

        // Total Saldo QRIS yang Sudah Ditransfer Admin
        $totalSettled = (int) Settlement::where('stand_id', $stand->id)
            ->whereIn('status', ['ditransfer', 'settled'])
            ->sum('total_dana');

        // Riwayat Payout dari Admin ke Stand Ini
        $settlements = Settlement::with('admin')
            ->where('stand_id', $stand->id)
            ->latest()
            ->paginate(10);

        return view('vendor.rekening', compact(
            'stand',
            'saldoPending',
            'saldoInProgress',
            'totalSettled',
            'settlements'
        ));
    }

    /**
     * Update vendor's payout bank account / e-wallet details.
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $stand = $user->stand;
        abort_unless($stand, 403, 'Akun tidak terhubung ke Stand.');

        $validated = $request->validate([
            'bank_name' => ['required', 'string', 'max:50'],
            'nomor_rekening' => ['required', 'string', 'max:50'],
            'atas_nama' => ['required', 'string', 'max:100'],
            'no_wa' => ['nullable', 'string', 'max:20'],
            'deskripsi' => ['nullable', 'string', 'max:500'],
        ], [
            'bank_name.required' => 'Nama bank atau e-wallet wajib dipilih atau diisi.',
            'nomor_rekening.required' => 'Nomor rekening atau nomor HP e-wallet wajib diisi.',
            'atas_nama.required' => 'Nama pemilik rekening wajib diisi sesuai akun / buku tabungan.',
        ]);

        $stand->update($validated);

        return back()->with('success', "Rekening pencairan saldo QRIS berhasil disimpan ({$stand->bank_name} - {$stand->nomor_rekening} a.n {$stand->atas_nama}).");
    }
}
