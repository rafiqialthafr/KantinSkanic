<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Stand;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    /**
     * Display all menu items across all stands.
     */
    public function index(Request $request)
    {
        $query = Menu::with('stand')->latest();

        if ($request->filled('stand_id')) {
            $query->where('stand_id', $request->integer('stand_id'));
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->string('kategori'));
        }

        if ($request->filled('is_available')) {
            $query->where('is_available', $request->boolean('is_available'));
        }

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where('nama_menu', 'like', "%{$search}%");
        }

        $menus = $query->paginate(16)->withQueryString();
        $stands = Stand::orderBy('nomor_stand')->get();

        $totalMenusCount = Menu::count();
        $availableMenusCount = Menu::where('is_available', true)->where('stok', '>', 0)->count();
        $outOfStockCount = Menu::where('stok', '<=', 0)->orWhere('is_available', false)->count();

        return view('admin.menus.index', compact(
            'menus',
            'stands',
            'totalMenusCount',
            'availableMenusCount',
            'outOfStockCount'
        ));
    }
}
