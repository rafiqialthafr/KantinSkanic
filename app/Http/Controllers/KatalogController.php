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

    /**
     * Display customer order history page.
     */
    public function orderHistory(Request $request)
    {
        $statusFilter = $request->query('status', 'all');
        $searchQuery = trim($request->query('q', ''));

        // Collect all related codes
        $recentCodes = session()->get('recent_orders', []);

        $query = Order::with(['stand.user', 'items.menu']);

        $candidateCodes = $recentCodes;
        if ($request->filled('codes')) {
            $inputCodes = is_array($request->input('codes')) ? $request->input('codes') : explode(',', (string) $request->input('codes'));
            $candidateCodes = array_unique(array_merge($candidateCodes, array_filter(array_map('trim', $inputCodes))));
        }

        $query->where(function ($q) use ($candidateCodes, $searchQuery) {
            $hasCondition = false;

            if (! empty($candidateCodes)) {
                $q->whereIn('kode_tr', $candidateCodes);
                $hasCondition = true;
            }

            if (auth()->check()) {
                if ($hasCondition) {
                    $q->orWhere('nama_pemesan', auth()->user()->name);
                } else {
                    $q->where('nama_pemesan', auth()->user()->name);
                }
                $hasCondition = true;
            }

            if (! empty($searchQuery)) {
                $cleanCode = strtoupper($searchQuery);
                if (! str_starts_with($cleanCode, 'PO-') && is_numeric($cleanCode)) {
                    $cleanCode = 'PO-'.$cleanCode;
                }
                $searchCallback = function ($sq) use ($searchQuery, $cleanCode) {
                    $sq->where('kode_tr', 'like', "%{$searchQuery}%")
                        ->orWhere('kode_tr', $cleanCode)
                        ->orWhereHas('stand', function ($st) use ($searchQuery) {
                            $st->where('nama_stand', 'like', "%{$searchQuery}%");
                        })
                        ->orWhereHas('items.menu', function ($mn) use ($searchQuery) {
                            $mn->where('nama_menu', 'like', "%{$searchQuery}%");
                        });
                };

                if ($hasCondition) {
                    $q->orWhere($searchCallback);
                } else {
                    $q->where($searchCallback);
                }
                $hasCondition = true;
            }

            // Fallback if guest has no saved codes and no search: do not leak everyone's orders
            if (! $hasCondition) {
                $q->whereRaw('1 = 0');
            }
        });

        // Calculate counts for each Shopee tab
        $countsQuery = clone $query;
        $allOrdersForCounts = $countsQuery->select('id', 'status')->get();
        $statusCounts = [
            'all' => $allOrdersForCounts->count(),
            'pending' => $allOrdersForCounts->where('status', 'pending')->count(),
            'diproses' => $allOrdersForCounts->where('status', 'diproses')->count(),
            'siap_diambil' => $allOrdersForCounts->where('status', 'siap_diambil')->count(),
            'selesai' => $allOrdersForCounts->where('status', 'selesai')->count(),
            'dibatalkan' => $allOrdersForCounts->where('status', 'dibatalkan')->count(),
        ];

        if ($statusFilter === 'aktif') {
            $query->whereIn('status', ['pending', 'diproses', 'siap_diambil']);
        } elseif (in_array($statusFilter, ['pending', 'diproses', 'siap_diambil', 'selesai', 'dibatalkan'])) {
            $query->where('status', $statusFilter);
        }

        $orders = $query->latest()->take(50)->get();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'count' => $orders->count(),
                'orders' => $this->formatOrdersForResponse($orders),
            ]);
        }

        return view('katalog.riwayat', [
            'orders' => $orders,
            'statusFilter' => $statusFilter,
            'searchQuery' => $searchQuery,
            'statusCounts' => $statusCounts,
        ]);
    }

    /**
     * API for Riwayat Pesanan modal.
     */
    public function orderHistoryApi(Request $request)
    {
        $inputCodes = $request->input('codes', []);
        if (is_string($inputCodes)) {
            $inputCodes = array_filter(array_map('trim', explode(',', $inputCodes)));
        }
        $recentCodes = session()->get('recent_orders', []);
        $allCodes = array_unique(array_merge($recentCodes, is_array($inputCodes) ? $inputCodes : []));

        $searchCode = trim((string) $request->input('search_code', ''));
        if (! empty($searchCode)) {
            $cleanCode = strtoupper($searchCode);
            if (! str_starts_with($cleanCode, 'PO-') && is_numeric($cleanCode)) {
                $cleanCode = 'PO-'.$cleanCode;
            }
            $allCodes[] = $cleanCode;
            $allCodes[] = strtoupper($searchCode);
            $allCodes = array_unique($allCodes);
        }

        $query = Order::with(['stand.user', 'items.menu']);

        $query->where(function ($q) use ($allCodes, $searchCode) {
            $hasCondition = false;
            if (! empty($allCodes)) {
                $q->whereIn('kode_tr', $allCodes);
                $hasCondition = true;
            }

            if (auth()->check()) {
                if ($hasCondition) {
                    $q->orWhere('nama_pemesan', auth()->user()->name);
                } else {
                    $q->where('nama_pemesan', auth()->user()->name);
                }
                $hasCondition = true;
            }

            if (! empty($searchCode)) {
                if ($hasCondition) {
                    $q->orWhere('kode_tr', 'like', "%{$searchCode}%");
                } else {
                    $q->where('kode_tr', 'like', "%{$searchCode}%");
                }
                $hasCondition = true;
            }

            if (! $hasCondition) {
                $q->whereRaw('1 = 0');
            }
        });

        $statusFilter = $request->input('status', 'all');
        if ($statusFilter === 'aktif') {
            $query->whereIn('status', ['pending', 'diproses', 'siap_diambil']);
        } elseif ($statusFilter === 'selesai') {
            $query->where('status', 'selesai');
        } elseif ($statusFilter === 'dibatalkan') {
            $query->where('status', 'dibatalkan');
        }

        $orders = $query->latest()->take(30)->get();

        return response()->json([
            'success' => true,
            'count' => $orders->count(),
            'orders' => $this->formatOrdersForResponse($orders),
        ]);
    }

    /**
     * Format orders for API / JSON response.
     */
    protected function formatOrdersForResponse($orders): array
    {
        return $orders->map(function ($order) {
            $statusLabels = [
                'pending' => 'Menunggu Konfirmasi',
                'diproses' => 'Sedang Disiapkan',
                'siap_diambil' => 'Siap Diambil',
                'selesai' => 'Selesai',
                'dibatalkan' => 'Dibatalkan',
            ];

            return [
                'id' => $order->id,
                'kode_tr' => $order->kode_tr,
                'stand_nama' => $order->stand->nama_stand ?? 'Stand Kantin',
                'nama_pemesan' => $order->nama_pemesan,
                'kelas' => $order->kelas,
                'status' => $order->status,
                'status_label' => $statusLabels[$order->status] ?? ucfirst($order->status),
                'total_harga' => $order->total_harga,
                'total_harga_formatted' => 'Rp '.number_format($order->total_harga, 0, ',', '.'),
                'created_at_formatted' => $order->created_at->format('d M Y, H:i').' WIB',
                'created_at_relative' => $order->created_at->diffForHumans(),
                'items_count' => $order->items->sum('jumlah'),
                'items_summary' => $order->items->map(function ($item) {
                    return $item->jumlah.'x '.($item->menu->nama_menu ?? 'Menu');
                })->join(', '),
                'url' => route('order.status', $order->kode_tr),
            ];
        })->values()->all();
    }
}
