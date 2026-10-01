<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Stand;

class ReportController extends Controller
{
    /**
     * Display sales and revenue summary per stand.
     */
    public function index()
    {
        $stands = Stand::withCount(['orders', 'menus'])
            ->withSum(['orders as total_omset' => function ($query) {
                $query->where('status', 'selesai');
            }], 'total_harga')
            ->orderByDesc('total_omset')
            ->get();

        $overallTotalOrders = Order::count();
        $overallCompletedOrders = Order::where('status', 'selesai')->count();
        $overallTotalRevenue = Order::where('status', 'selesai')->sum('total_harga');

        return view('admin.reports.index', compact(
            'stands',
            'overallTotalOrders',
            'overallCompletedOrders',
            'overallTotalRevenue'
        ));
    }
}
