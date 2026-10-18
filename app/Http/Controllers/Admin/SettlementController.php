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
        $periode = $request->query('periode', 'hari_ini');
        if (! in_array($periode, ['hari_ini', 'minggu_ini', 'bulan_ini', 'semua'], true)) {
            $periode = 'hari_ini';
        }

        $periodeLabel = match ($periode) {
            'hari_ini' => 'Hari Ini',
            'minggu_ini' => 'Minggu Ini',
            'bulan_ini' => 'Bulan Ini',
            'semua' => 'Keseluruhan',
        };

        $periodFilter = function ($q) use ($periode) {
            match ($periode) {
                'hari_ini' => $q->whereDate('created_at', today()),
                'minggu_ini' => $q->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]),
                'bulan_ini' => $q->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year),
                'semua' => null,
                default => $q->whereDate('created_at', today()),
            };
        };

        // 1. Transaction Recap (Khusus QRIS sesuai periode)
        // Total QRIS Masuk: Semua pembayaran QRIS yang sudah lunas dan tidak dibatalkan
        $qrisRevenueQuery = Order::where('metode_pembayaran', 'qris')
            ->where('status_pembayaran', 'lunas')
            ->where('status', '!=', 'dibatalkan');
        $periodFilter($qrisRevenueQuery);
        $qrisRevenue = $qrisRevenueQuery->sum('total_harga');

        // Hak Saldo QRIS Stand yang Siap Dicairkan (Pesanan QRIS lunas & selesai diambil)
        $qrisPendingPayoutQuery = Order::where('status', 'selesai')
            ->where('metode_pembayaran', 'qris')
            ->where('status_pembayaran', 'lunas')
            ->where('settlement_status', 'pending');
        $periodFilter($qrisPendingPayoutQuery);
        $qrisPendingPayout = $qrisPendingPayoutQuery->sum('total_harga');

        // Dana QRIS lunas yang masih dalam proses pengerjaan oleh stand
        $qrisInProgressQuery = Order::whereIn('status', ['pending', 'diproses', 'siap_diambil'])
            ->where('metode_pembayaran', 'qris')
            ->where('status_pembayaran', 'lunas')
            ->where('settlement_status', 'pending');
        $periodFilter($qrisInProgressQuery);
        $qrisInProgressPayout = $qrisInProgressQuery->sum('total_harga');

        // Total dana QRIS yang sudah berhasil ditransfer
        $settledQuery = Settlement::whereIn('status', ['ditransfer', 'settled']);
        $periodFilter($settledQuery);
        $qrisSettledTotal = $settledQuery->sum('total_dana');

        // 2. Stands with QRIS balances to disburse (disesuaikan dengan filter periode)
        $stands = Stand::withCount([
            'orders as qris_completed_orders_count' => function ($q) use ($periodFilter) {
                $q->where('status', 'selesai')
                    ->where('metode_pembayaran', 'qris')
                    ->where('status_pembayaran', 'lunas');
                $periodFilter($q);
            },
            'orders as qris_in_progress_orders_count' => function ($q) use ($periodFilter) {
                $q->whereIn('status', ['pending', 'diproses', 'siap_diambil'])
                    ->where('metode_pembayaran', 'qris')
                    ->where('status_pembayaran', 'lunas');
                $periodFilter($q);
            },
        ])
            ->get()
            ->map(function ($stand) use ($periodFilter) {
                $pendingQuery = Order::where('stand_id', $stand->id)
                    ->where('status', 'selesai')
                    ->where('metode_pembayaran', 'qris')
                    ->where('status_pembayaran', 'lunas')
                    ->where('settlement_status', 'pending');
                $periodFilter($pendingQuery);
                $stand->saldo_pending = (int) $pendingQuery->sum('total_harga');

                $inProgressQuery = Order::where('stand_id', $stand->id)
                    ->whereIn('status', ['pending', 'diproses', 'siap_diambil'])
                    ->where('metode_pembayaran', 'qris')
                    ->where('status_pembayaran', 'lunas')
                    ->where('settlement_status', 'pending');
                $periodFilter($inProgressQuery);
                $stand->saldo_in_progress = (int) $inProgressQuery->sum('total_harga');

                $stand->last_settlement = Settlement::where('stand_id', $stand->id)
                    ->latest()
                    ->first();

                return $stand;
            });

        // 3. Settlement history logs (disesuaikan dengan filter periode)
        $settlementsQuery = Settlement::with(['stand', 'admin', 'orders'])->latest();
        $periodFilter($settlementsQuery);
        $settlements = $settlementsQuery->paginate(15)->withQueryString();

        return view('admin.settlements.index', compact(
            'qrisRevenue',
            'qrisPendingPayout',
            'qrisInProgressPayout',
            'qrisSettledTotal',
            'stands',
            'settlements',
            'periode',
            'periodeLabel'
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
            'bukti_transfer' => ['required', 'image', 'mimes:jpeg,png,jpg,webp,jfif', 'max:5120'],
        ], [
            'stand_id.required' => 'Pilih stand yang akan dicairkan saldonya.',
            'bukti_transfer.required' => 'Bukti foto atau screenshot transfer wajib diunggah.',
            'bukti_transfer.image' => 'Bukti transfer harus berupa file gambar.',
            'bukti_transfer.mimes' => 'Format bukti transfer harus JPG, PNG, atau WEBP.',
            'bukti_transfer.max' => 'Ukuran file bukti transfer maksimal 5 MB.',
        ]);

        $stand = Stand::findOrFail($validated['stand_id']);

        $buktiPath = null;
        if ($request->hasFile('bukti_transfer')) {
            $buktiPath = $request->file('bukti_transfer')->store('settlements', 'public');
        }

        return DB::transaction(function () use ($stand, $validated, $buktiPath) {
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
                'bukti_transfer' => $buktiPath,
                'ditransfer_at' => now(),
                'ditransfer_oleh' => auth()->id(),
            ]);

            Order::whereIn('id', $orders->pluck('id'))->update([
                'settlement_status' => 'settled',
                'settlement_id' => $settlement->id,
            ]);

            return back()->with('success', 'Berhasil mencairkan saldo QRIS Rp '.number_format($totalDana, 0, ',', '.')." ke {$stand->nama_stand} ({$stand->bank_name} {$stand->nomor_rekening} a.n {$stand->atas_nama}) dengan bukti transfer tersimpan.");
        });
    }
}
