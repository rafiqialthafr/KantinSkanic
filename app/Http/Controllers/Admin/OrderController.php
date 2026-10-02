<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Stand;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display all orders across all stands with filter and search.
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

        $query = Order::with(['stand', 'items.menu'])->latest();
        $periodFilter($query);

        if ($request->filled('stand_id')) {
            $query->where('stand_id', $request->integer('stand_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('kode_tr', 'like', "%{$search}%")
                    ->orWhere('nama_pemesan', 'like', "%{$search}%")
                    ->orWhere('kelas', 'like', "%{$search}%");
            });
        }

        if ($request->filled('jam_pengambilan')) {
            $query->where('jam_pengambilan', 'like', '%'.$request->string('jam_pengambilan').'%');
        }

        $orders = $query->paginate(15)->withQueryString();

        $stands = Stand::orderBy('nomor_stand')->get();

        $baseOrderQuery = Order::query();
        $periodFilter($baseOrderQuery);

        $totalOrdersCount = (clone $baseOrderQuery)->count();
        $pendingOrdersCount = (clone $baseOrderQuery)->where('status', 'pending')->count();
        $selesaiOrdersCount = (clone $baseOrderQuery)->where('status', 'selesai')->count();
        $totalRevenue = (clone $baseOrderQuery)->where('status', 'selesai')->sum('total_harga');

        return view('admin.orders.index', compact(
            'orders',
            'stands',
            'totalOrdersCount',
            'pendingOrdersCount',
            'selesaiOrdersCount',
            'totalRevenue',
            'periode',
            'periodeLabel'
        ));
    }
}
