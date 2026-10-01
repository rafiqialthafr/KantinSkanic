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
        $query = Order::with(['stand', 'items.menu'])->latest();

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
        $totalOrdersCount = Order::count();
        $pendingOrdersCount = Order::where('status', 'pending')->count();
        $selesaiOrdersCount = Order::where('status', 'selesai')->count();
        $totalRevenue = Order::where('status', 'selesai')->sum('total_harga');

        return view('admin.orders.index', compact(
            'orders',
            'stands',
            'totalOrdersCount',
            'pendingOrdersCount',
            'selesaiOrdersCount',
            'totalRevenue'
        ));
    }
}
