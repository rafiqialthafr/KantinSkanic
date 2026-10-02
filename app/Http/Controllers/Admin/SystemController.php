<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Order;
use App\Models\Stand;
use App\Models\User;
use Illuminate\Http\Request;

class SystemController extends Controller
{
    /**
     * Display system status, operational PO hours, and statistics.
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

        $userCounts = [
            'admin' => User::where('role', 'admin')->count(),
            'penjual' => User::where('role', 'penjual')->count(),
            'siswa' => User::where('role', 'siswa')->count(),
            'total' => User::count(),
        ];

        $systemStats = [
            'stands_count' => Stand::count(),
            'menus_count' => Menu::count(),
            'orders_count' => Order::count(),
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
        ];

        return view('admin.system.index', compact('userCounts', 'systemStats', 'periode', 'periodeLabel'));
    }
}
