<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Resolve vendor'\''s stand_id — all queries MUST use this to scope data.
     */
    protected function standId(): int
    {
        $stand = Auth::user()->stand;
        abort_unless($stand, 403, 'Akun tidak terhubung ke Stand.');

        return $stand->id;
    }

    /**
     * Vendor dashboard — incoming orders, strictly scoped to own stand.
     */
    public function index(Request $request)
    {
        $stand = Auth::user()->stand;
        $standId = $stand->id;

        $today = now()->startOfDay();

        // Stat metrics
        $activeOrdersCount = Order::where('stand_id', $standId)->whereIn('status', ['pending', 'diproses', 'siap_diambil'])->count();
        $totalOrdersToday = Order::where('stand_id', $standId)->where('created_at', '>=', $today)->count();
        $completedOrdersCount = Order::where('stand_id', $standId)->where('status', 'selesai')->where('created_at', '>=', $today)->count();
        $revenueToday = Order::where('stand_id', $standId)->where('status', 'selesai')->where('created_at', '>=', $today)->sum('total_harga');

        // Orders listing with strict stand scoping
        $query = Order::with('items.menu')->where('stand_id', $standId);

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('jam') && in_array($request->jam, ['Istirahat 1', 'Istirahat 2'])) {
            $query->where('jam_pengambilan', $request->jam);
        }

        $orders = $query->latest()->get();

        return view('vendor.dashboard', compact(
            'stand', 'orders',
            'activeOrdersCount', 'totalOrdersToday', 'completedOrdersCount', 'revenueToday',
        ) + ['activeStatus' => $request->status ?? 'all', 'activeJam' => $request->jam ?? 'all']);
    }

    /**
     * Update order status — strictly scoped so vendor can only modify their own orders.
     */
    public function updateStatus(Request $request, Order $order)
    {
        abort_unless($order->stand_id === $this->standId(), 403, 'Pesanan bukan milik stand Anda.');

        $validated = $request->validate([
            'status' => ['required', 'in:pending,diproses,siap_diambil,selesai,dibatalkan'],
        ]);

        $order->update(['status' => $validated['status']]);

        $labels = [
            'pending' => 'Menunggu Konfirmasi',
            'diproses' => 'Sedang Diproses',
            'siap_diambil' => 'Siap Diambil',
            'selesai' => 'Selesai',
            'dibatalkan' => 'Dibatalkan',
        ];

        $label = $labels[$validated['status']] ?? $validated['status'];

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Status #{$order->kode_tr} diubah ke: {$label}",
                'new_status' => $validated['status'],
            ]);
        }

        return back()->with('success', "Status pesanan #{$order->kode_tr} menjadi: {$label}");
    }

    /**
     * Verify order by kode_tr (scan / input kode).
     */
    public function verify(Request $request)
    {
        $validated = $request->validate([
            'kode_tr' => ['required', 'string'],
        ]);

        $raw = strtoupper(trim($validated['kode_tr']));
        $code = str_starts_with($raw, 'PO-') ? $raw : 'PO-'.ltrim($raw, 'PO-');

        $order = Order::with('items.menu')
            ->where('stand_id', $this->standId())
            ->where('kode_tr', $code)
            ->first();

        if (! $order) {
            return back()->withErrors(['kode_tr' => "Pesanan {$code} tidak ditemukan di stand Anda."]);
        }

        // Mark as selesai upon pickup verification
        $order->update(['status' => 'selesai']);

        return redirect()->route('vendor.dashboard')
            ->with('verified_order', $order->id)
            ->with('success', "Pesanan #{$order->kode_tr} berhasil diverifikasi & diselesaikan ({$order->nama_pemesan} - {$order->kelas})");
    }
}
