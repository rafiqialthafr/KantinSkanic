<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Order;
use App\Models\Stand;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display seller or admin dashboard.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            return $this->adminDashboard($request);
        }

        return $this->penjualDashboard($request, $user);
    }

    /**
     * Seller dashboard view.
     */
    protected function penjualDashboard(Request $request, User $user)
    {
        $stand = $user->stand;

        if (! $stand) {
            // Auto create or fail gracefully
            $stand = Stand::create([
                'user_id' => $user->id,
                'nama_stand' => 'Stand '.$user->name,
                'nomor_stand' => 'Stand Baru',
                'deskripsi' => 'Stand makanan dan minuman',
            ]);
        }

        // Metrics for today
        $today = now()->startOfDay();
        $totalOrdersToday = Order::where('stand_id', $stand->id)
            ->where('created_at', '>=', $today)
            ->count();

        $pendingOrdersCount = Order::where('stand_id', $stand->id)
            ->where('status', 'pending')
            ->count();

        $siapDiambilOrdersCount = Order::where('stand_id', $stand->id)
            ->where('status', 'siap_diambil')
            ->count();

        $activeOrdersCount = $pendingOrdersCount + $siapDiambilOrdersCount;

        $completedOrdersCount = Order::where('stand_id', $stand->id)
            ->where('status', 'selesai')
            ->where('created_at', '>=', $today)
            ->count();

        $revenueToday = Order::where('stand_id', $stand->id)
            ->where('status', 'selesai')
            ->where('created_at', '>=', $today)
            ->sum('total_harga');

        // Orders listing
        $ordersQuery = Order::with('items.menu')
            ->where('stand_id', $stand->id);

        if ($request->filled('status') && $request->status !== 'all') {
            $ordersQuery->where('status', $request->status);
        }

        $orders = $ordersQuery->latest()->get();

        return view('dashboard.index', [
            'stand' => $stand,
            'orders' => $orders,
            'totalOrdersToday' => $totalOrdersToday,
            'activeOrdersCount' => $activeOrdersCount,
            'pendingOrdersCount' => $pendingOrdersCount,
            'siapDiambilOrdersCount' => $siapDiambilOrdersCount,
            'completedOrdersCount' => $completedOrdersCount,
            'revenueToday' => $revenueToday,
            'activeStatus' => $request->status ?? 'all',
        ]);
    }

    /**
     * Admin dashboard view.
     */
    protected function adminDashboard(Request $request)
    {
        $stands = Stand::with(['user', 'menus'])->withCount(['orders', 'menus'])->get();
        $totalOrders = Order::count();
        $totalRevenue = Order::where('status', 'selesai')->sum('total_harga');
        $allOrders = Order::with(['stand', 'items.menu'])->latest()->take(20)->get();

        return view('dashboard.admin', [
            'stands' => $stands,
            'totalOrders' => $totalOrders,
            'totalRevenue' => $totalRevenue,
            'recentOrders' => $allOrders,
        ]);
    }

    /**
     * Update order status.
     */
    public function updateOrderStatus(Request $request, Order $order)
    {
        $user = Auth::user();

        // Ensure user owns this stand or is admin
        if ($user->role !== 'admin' && (! $user->stand || $order->stand_id !== $user->stand->id)) {
            abort(403, 'Anda tidak memiliki akses untuk mengubah pesanan ini.');
        }

        $previousStatus = $order->status;
        $newStatus = $validated['status'];

        if ($previousStatus !== 'selesai' && $newStatus === 'selesai') {
            foreach ($order->items as $item) {
                if ($item->menu_id) {
                    Menu::where('id', $item->menu_id)->increment('total_sold', $item->jumlah);
                }
            }
        } elseif ($previousStatus === 'selesai' && $newStatus !== 'selesai') {
            foreach ($order->items as $item) {
                if ($item->menu_id) {
                    Menu::where('id', $item->menu_id)->decrement('total_sold', $item->jumlah);
                }
            }
        }

        $updateData = ['status' => $newStatus];
        if ($newStatus === 'selesai' && $order->isTunai()) {
            $updateData['status_pembayaran'] = 'lunas';
        }

        $order->update($updateData);

        $statusLabels = [
            'pending' => 'Menunggu Konfirmasi',
            'diproses' => 'Sedang Diproses',
            'siap_diambil' => 'Siap Diambil',
            'selesai' => 'Selesai',
            'dibatalkan' => 'Dibatalkan',
        ];

        $label = $statusLabels[$validated['status']] ?? $validated['status'];

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Status pesanan #{$order->kode_tr} berhasil diubah menjadi: {$label}",
                'new_status' => $validated['status'],
            ]);
        }

        return back()->with('success', "Status pesanan #{$order->kode_tr} berhasil diubah menjadi: {$label}");
    }
}
