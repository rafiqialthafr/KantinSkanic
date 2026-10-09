<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
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

        // ── Periode filter (same logic as admin ReportController) ──
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

        $applyPeriode = function ($q) use ($periode) {
            match ($periode) {
                'hari_ini' => $q->whereDate('created_at', today()),
                'minggu_ini' => $q->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]),
                'bulan_ini' => $q->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year),
                'semua' => null,
                default => $q->whereDate('created_at', today()),
            };
        };

        $today = now()->startOfDay();

        // Stat metrics — scoped by selected periode
        $baseQuery = Order::where('stand_id', $standId);
        $applyPeriode($baseQuery);

        $activeOrdersCount = Order::where('stand_id', $standId)->whereIn('status', ['pending', 'diproses', 'siap_diambil'])->count();
        $totalOrdersToday = (clone $baseQuery)->count();
        $completedOrdersCount = (clone $baseQuery)->where('status', 'selesai')->count();
        $revenueToday = (clone $baseQuery)->where('status', 'selesai')->sum('total_harga');

        // Orders listing with strict stand scoping and selected periode
        $query = Order::with('items.menu')->where('stand_id', $standId);
        $applyPeriode($query);

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $orders = $query->latest()->get();

        return view('vendor.dashboard', compact(
            'stand', 'orders',
            'activeOrdersCount', 'totalOrdersToday', 'completedOrdersCount', 'revenueToday',
            'periode', 'periodeLabel',
        ) + ['activeStatus' => $request->status ?? 'all']);
    }

    /**
     * Omset / revenue report page — scoped to vendor's own stand.
     */
    public function omset(Request $request)
    {
        $stand = Auth::user()->stand;
        $standId = $stand->id;

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

        $applyPeriode = function ($q) use ($periode) {
            match ($periode) {
                'hari_ini' => $q->whereDate('created_at', today()),
                'minggu_ini' => $q->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]),
                'bulan_ini' => $q->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year),
                'semua' => null,
                default => $q->whereDate('created_at', today()),
            };
        };

        $baseQuery = Order::where('stand_id', $standId);
        $applyPeriode($baseQuery);

        $totalOmset = (clone $baseQuery)->where('status', 'selesai')->sum('total_harga');
        $totalOrders = (clone $baseQuery)->count();
        $selesaiOrders = (clone $baseQuery)->where('status', 'selesai')->count();
        $batalOrders = (clone $baseQuery)->where('status', 'dibatalkan')->count();

        // Top selling menus in the period (using 'jumlah' and 'subtotal' columns from order_items)
        $topMenus = OrderItem::selectRaw('menu_id, SUM(jumlah) as total_qty, SUM(subtotal) as total_revenue')
            ->whereHas('order', function ($q) use ($standId, $applyPeriode) {
                $q->where('stand_id', $standId)->where('status', 'selesai');
                $applyPeriode($q);
            })
            ->with('menu')
            ->groupBy('menu_id')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get();

        // Orders list for detail in the period
        $ordersQuery = Order::where('stand_id', $standId)
            ->where('status', 'selesai')
            ->with('items.menu');
        $applyPeriode($ordersQuery);
        $orders = $ordersQuery->latest()->get();

        $activeOrdersCount = Order::where('stand_id', $standId)->whereIn('status', ['pending', 'diproses', 'siap_diambil'])->count();

        return view('vendor.omset', compact(
            'stand', 'periode', 'periodeLabel',
            'totalOmset', 'totalOrders', 'selesaiOrders', 'batalOrders',
            'topMenus', 'orders', 'activeOrdersCount',
        ));
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

        $previousStatus = $order->status;
        $newStatus = $validated['status'];

        // Jika pesanan dibatalkan, otomatis kembalikan stok menu
        if ($previousStatus !== 'dibatalkan' && $newStatus === 'dibatalkan') {
            foreach ($order->items as $item) {
                if ($item->menu_id) {
                    Menu::where('id', $item->menu_id)->increment('stok', $item->jumlah);
                }
            }
        } elseif ($previousStatus === 'dibatalkan' && $newStatus !== 'dibatalkan') {
            // Jika pesanan batal diaktifkan kembali, kurangi stok kembali
            foreach ($order->items as $item) {
                if ($item->menu_id) {
                    Menu::where('id', $item->menu_id)->decrement('stok', $item->jumlah);
                }
            }
        }

        $order->update(['status' => $newStatus]);

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
