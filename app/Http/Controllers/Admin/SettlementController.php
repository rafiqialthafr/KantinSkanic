<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Settlement;
use App\Models\Stand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettlementController extends Controller
{
    /**
     * Display QRIS settlement summary and stands payout table.
     */
    public function index(Request $request)
    {
        // 1. Transaction Recap (All canteen transactions)
        $totalOrdersCount = Order::count();
        $totalRevenue = Order::where('status', 'selesai')->sum('total_harga');

        $cashRevenue = Order::where('status', 'selesai')
            ->where(function ($q) {
                $q->where('metode_pembayaran', 'tunai')
                    ->orWhereNull('metode_pembayaran');
            })
            ->sum('total_harga');

        // Total QRIS Masuk: Semua pembayaran QRIS yang sudah lunas dan tidak dibatalkan (Uang riil masuk ke rekening admin)
        $qrisRevenue = Order::where('metode_pembayaran', 'qris')
            ->where('status_pembayaran', 'lunas')
            ->where('status', '!=', 'dibatalkan')
            ->sum('total_harga');

        // Hak Saldo QRIS Stand yang Siap Dicairkan (Opsi A: Pesanan QRIS yang sudah lunas dan selesai diambil)
        $qrisPendingPayout = Order::where('status', 'selesai')
            ->where('metode_pembayaran', 'qris')
            ->where('status_pembayaran', 'lunas')
            ->where('settlement_status', 'pending')
            ->sum('total_harga');

        // Dana QRIS lunas yang masih dalam proses pengerjaan oleh stand (menunggu pesanan selesai)
        $qrisInProgressPayout = Order::whereIn('status', ['pending', 'diproses', 'siap_diambil'])
            ->where('metode_pembayaran', 'qris')
            ->where('status_pembayaran', 'lunas')
            ->where('settlement_status', 'pending')
            ->sum('total_harga');

        $qrisSettledTotal = Settlement::whereIn('status', ['ditransfer', 'settled'])->sum('total_dana');

        // 2. Stands with QRIS balances to disburse (Opsi A: Saldo dicairkan jika pesanan selesai)
        $stands = Stand::withCount([
            'orders as qris_completed_orders_count' => function ($q) {
                $q->where('status', 'selesai')
                    ->where('metode_pembayaran', 'qris')
                    ->where('status_pembayaran', 'lunas');
            },
            'orders as qris_in_progress_orders_count' => function ($q) {
                $q->whereIn('status', ['pending', 'diproses', 'siap_diambil'])
                    ->where('metode_pembayaran', 'qris')
                    ->where('status_pembayaran', 'lunas');
            },
        ])
            ->get()
            ->map(function ($stand) {
                $stand->saldo_pending = (int) Order::where('stand_id', $stand->id)
                    ->where('status', 'selesai')
                    ->where('metode_pembayaran', 'qris')
                    ->where('status_pembayaran', 'lunas')
                    ->where('settlement_status', 'pending')
                    ->sum('total_harga');

                $stand->saldo_in_progress = (int) Order::where('stand_id', $stand->id)
                    ->whereIn('status', ['pending', 'diproses', 'siap_diambil'])
                    ->where('metode_pembayaran', 'qris')
                    ->where('status_pembayaran', 'lunas')
                    ->where('settlement_status', 'pending')
                    ->sum('total_harga');

                $stand->last_settlement = Settlement::where('stand_id', $stand->id)
                    ->latest()
                    ->first();

                return $stand;
            });

        // 3. Settlement history logs
        $settlements = Settlement::with(['stand', 'admin', 'orders'])
            ->latest()
            ->paginate(15);

        return view('admin.settlements.index', compact(
            'totalOrdersCount',
            'totalRevenue',
            'cashRevenue',
            'qrisRevenue',
            'qrisPendingPayout',
            'qrisInProgressPayout',
            'qrisSettledTotal',
            'stands',
            'settlements'
        ));
    }

    /**
     * Process QRIS payout transfer to a stand.
     */
    public function payout(Request $request)
    {
        $validated = $request->validate([
            'stand_id' => ['required', 'exists:stands,id'],
            'catatan' => ['nullable', 'string', 'max:255'],
            'bukti_transfer' => ['nullable', 'string', 'max:255'],
        ], [
            'stand_id.required' => 'Pilih stand yang akan dicairkan saldonya.',
        ]);

        $stand = Stand::findOrFail($validated['stand_id']);

        return DB::transaction(function () use ($stand, $validated) {
            $orders = Order::where('stand_id', $stand->id)
                ->where('status', 'selesai')
                ->where('metode_pembayaran', 'qris')
                ->where('status_pembayaran', 'lunas')
                ->where('settlement_status', 'pending')
                ->lockForUpdate()
                ->get();

            $totalDana = $orders->sum('total_harga');

            if ($totalDana <= 0) {
                return back()->with('error', "Stand {$stand->nama_stand} tidak memiliki saldo QRIS tertunda untuk dicairkan.");
            }

            $kodeSettlement = 'SETTLE-'.date('Ymd').'-'.rand(1000, 9999);

            $settlement = Settlement::create([
                'stand_id' => $stand->id,
                'kode_settlement' => $kodeSettlement,
                'total_dana' => $totalDana,
                'status' => 'ditransfer',
                'catatan' => $validated['catatan'] ?? 'Pencairan saldo QRIS hasil pre-order ke rekening stand.',
                'bukti_transfer' => $validated['bukti_transfer'] ?? null,
                'ditransfer_at' => now(),
                'ditransfer_oleh' => auth()->id(),
            ]);

            Order::whereIn('id', $orders->pluck('id'))->update([
                'settlement_status' => 'settled',
                'settlement_id' => $settlement->id,
            ]);

            return back()->with('success', 'Berhasil mencairkan saldo QRIS Rp '.number_format($totalDana, 0, ',', '.')." ke {$stand->nama_stand} ({$stand->bank_name} {$stand->nomor_rekening} a.n {$stand->atas_nama}) dengan kode #{$kodeSettlement}.");
        });
    }
}
