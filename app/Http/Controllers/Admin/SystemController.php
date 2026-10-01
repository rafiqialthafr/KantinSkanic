<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Order;
use App\Models\Stand;
use App\Models\User;

class SystemController extends Controller
{
    /**
     * Display system status, operational PO hours, and statistics.
     */
    public function index()
    {
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

        return view('admin.system.index', compact('userCounts', 'systemStats'));
    }
}
