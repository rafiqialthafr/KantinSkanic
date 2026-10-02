<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Stand;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Display sales and revenue summary per stand.
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

        $stands = Stand::withCount([
            'menus',
            'orders' => function ($query) use ($periodFilter) {
                $periodFilter($query);
            },
        ])
            ->withSum(['orders as total_omset' => function ($query) use ($periodFilter) {
                $query->where('status', 'selesai');
                $periodFilter($query);
            }], 'total_harga')
            ->orderByDesc('total_omset')
            ->get();

        $overallQuery = Order::query();
        $periodFilter($overallQuery);

        $overallTotalOrders = (clone $overallQuery)->count();
        $overallCompletedOrders = (clone $overallQuery)->where('status', 'selesai')->count();
        $overallTotalRevenue = (clone $overallQuery)->where('status', 'selesai')->sum('total_harga');

        return view('admin.reports.index', compact(
            'stands',
            'overallTotalOrders',
            'overallCompletedOrders',
            'overallTotalRevenue',
            'periode',
            'periodeLabel'
        ));
    }
}
