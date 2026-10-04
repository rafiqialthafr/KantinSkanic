<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Order;
use App\Models\Stand;
use Illuminate\Http\Request;

class KatalogController extends Controller
{
    /**
     * Display menu catalog with stands and filters.
     */
    public function index(Request $request)
    {
        $stands = Stand::withCount('menus')->get();

        $query = Menu::with('stand')->where('is_available', true);

        // Filter by Stand
        if ($request->filled('stand') && $request->stand !== 'all') {
            $query->where('stand_id', $request->stand);
        }

        // Filter by Kategori
        if ($request->filled('kategori') && in_array($request->kategori, ['makanan', 'minuman', 'snack'])) {
            $query->where('kategori', $request->kategori);
        }

        // Search by keyword
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('nama_menu', 'like', "%{$search}%")
                    ->orWhereHas('stand', function ($sq) use ($search) {
                        $sq->where('nama_stand', 'like', "%{$search}%");
                    });
            });
        }

        $menus = $query->orderBy('nama_menu', 'asc')->get();

        return view('katalog.index', [
            'stands' => $stands,
            'menus' => $menus,
            'activeStand' => $request->stand ?? 'all',
            'activeKategori' => $request->kategori ?? 'all',
            'search' => $request->q ?? '',
        ]);
    }

    /**
     * Show student checkout confirmation page.
     */
    public function showCheckout(Request $request)
    {
        return view('katalog.checkout', [
            'user' => $request->user(),
            'defaultKelas' => session('siswa_kelas', ''),
            'defaultWa' => session('siswa_wa', ''),
        ]);
    }

    /**
     * Handle student guest checkout.
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'nama_pemesan' => ['required', 'string', 'max:100'],
            'kelas' => ['required', 'string', 'max:50'],
            'catatan' => ['nullable', 'string', 'max:500'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.menu_id' => ['required', 'exists:menus,id'],
            'items.*.jumlah' => ['required', 'integer', 'min:1', 'max:50'],
        ], [
            'nama_pemesan.required' => 'Nama pemesan wajib diisi.',
            'kelas.required' => 'Kelas wajib diisi.',
            'items.required' => 'Keranjang belanja masih kosong.',
            'items.min' => 'Pilih minimal satu menu untuk dipesan.',
        ]);

        $itemInputs = $request->input('items');
        $menuIds = array_column($itemInputs, 'menu_id');
        $menus = Menu::with('stand')->whereIn('id', $menuIds)->get()->keyBy('id');

        // Group ordered items by stand_id
        $itemsByStand = [];
        foreach ($itemInputs as $item) {
            $menuId = $item['menu_id'];
            $qty = (int) $item['jumlah'];

            if (! isset($menus[$menuId])) {
                continue;
            }

            $menu = $menus[$menuId];
            $standId = $menu->stand_id;

            if (! isset($itemsByStand[$standId])) {
                $itemsByStand[$standId] = [];
            }

            $itemsByStand[$standId][] = [
                'menu' => $menu,
                'jumlah' => $qty,
                'harga_satuan' => $menu->harga,
                'subtotal' => $menu->harga * $qty,
            ];
        }

        if (empty($itemsByStand)) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Menu yang dipilih tidak valid.'], 422);
            }

            return back()->withErrors(['items' => 'Menu yang dipilih tidak valid.']);
        }

        $createdOrders = [];

        foreach ($itemsByStand as $standId => $groupItems) {
            // Generate unique kode_tr (e.g. PO-8921)
            do {
                $candidateCode = 'PO-'.rand(1000, 9999);
            } while (Order::where('kode_tr', $candidateCode)->exists());

            $standTotal = array_sum(array_column($groupItems, 'subtotal'));

            $order = Order::create([
                'kode_tr' => $candidateCode,
                'stand_id' => $standId,
                'nama_pemesan' => $request->nama_pemesan,
                'kelas' => $request->kelas,
                'total_harga' => $standTotal,
                'status' => 'pending',
                'catatan' => $request->catatan,
            ]);

            foreach ($groupItems as $gItem) {
                $order->items()->create([
                    'menu_id' => $gItem['menu']->id,
                    'jumlah' => $gItem['jumlah'],
                    'harga_satuan' => $gItem['harga_satuan'],
                    'subtotal' => $gItem['subtotal'],
                ]);

                // Reduce stock
                if ($gItem['menu']->stok >= $gItem['jumlah']) {
                    $gItem['menu']->decrement('stok', $gItem['jumlah']);
                }
            }

            $createdOrders[] = $order;
        }

        $firstOrder = $createdOrders[0];
        $allCodes = array_column($createdOrders, 'kode_tr');

        // Store active orders in session for easy reference
        session()->put('recent_orders', array_unique(array_merge(session()->get('recent_orders', []), $allCodes)));

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil dibuat!',
                'redirect_url' => route('order.status', $firstOrder->kode_tr),
                'kode_tr' => $firstOrder->kode_tr,
                'all_codes' => $allCodes,
            ]);
        }

        return redirect()->route('order.status', $firstOrder->kode_tr)
            ->with('success', 'Pesanan Anda berhasil dikirim ke kantin!');
    }

    /**
     * Display order status and digital receipt.
     */
    public function orderStatus($kode_tr)
    {
        $order = Order::with(['stand.user', 'items.menu'])
            ->where('kode_tr', $kode_tr)
            ->firstOrFail();

        $recentCodes = session()->get('recent_orders', []);
        $otherOrders = [];
        if (! empty($recentCodes)) {
            $otherOrders = Order::with('stand')
                ->whereIn('kode_tr', $recentCodes)
                ->where('kode_tr', '!=', $kode_tr)
                ->latest()
                ->take(5)
                ->get();
        }

        return view('katalog.order_status', [
            'order' => $order,
            'otherOrders' => $otherOrders,
        ]);
    }
}
