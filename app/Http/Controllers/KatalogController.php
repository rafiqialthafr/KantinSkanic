<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\OrderCheckoutController;
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
        $stands = Stand::where('is_active', true)
            ->withCount(['menus' => function ($q) use ($request) {
                $q->where('is_available', true);
                if ($request->filled('kategori')) {
                    if ($request->kategori === 'terlaris') {
                        $q->whereHas('orderItems', function ($oq) {
                            $oq->whereHas('order', function ($o) {
                                $o->where('status', 'selesai');
                            });
                        });
                    } elseif (in_array($request->kategori, ['makanan', 'minuman', 'snack'])) {
                        $q->where('kategori', $request->kategori);
                    }
                }
            }])
            ->get();

        $query = Menu::with('stand')
            ->withSum(['orderItems as total_terjual' => function ($q) {
                $q->whereHas('order', function ($oq) {
                    $oq->where('status', 'selesai');
                });
            }], 'jumlah')
            ->where('is_available', true)
            ->whereHas('stand', function ($sq) {
                $sq->where('is_active', true);
            });

        // Filter by Stand
        if ($request->filled('stand') && $request->stand !== 'all') {
            $query->where('stand_id', $request->stand);
        }

        // Filter by Kategori
        if ($request->filled('kategori')) {
            if ($request->kategori === 'terlaris') {
                $query->whereHas('orderItems', function ($oq) {
                    $oq->whereHas('order', function ($o) {
                        $o->where('status', 'selesai');
                    });
                })->orderByDesc('total_terjual')->limit(10);
            } elseif (in_array($request->kategori, ['makanan', 'minuman', 'snack'])) {
                $query->where('kategori', $request->kategori)->orderBy('nama_menu', 'asc');
            } else {
                $query->orderBy('nama_menu', 'asc');
            }
        } else {
            $query->orderBy('nama_menu', 'asc');
        }

        // Search by keyword
        if ($request->filled('q')) {
            $search = trim($request->q);
            $query->where(function ($q) use ($search) {
                $q->where('nama_menu', 'like', "%{$search}%")
                    ->orWhere('kategori', 'like', "%{$search}%")
                    ->orWhereHas('stand', function ($sq) use ($search) {
                        $sq->where('nama_stand', 'like', "%{$search}%")
                            ->orWhere('pemilik', 'like', "%{$search}%");
                    });
            });
        }

        $menus = $query->get();

        if ($request->filled('q')) {
            $search = strtolower(trim($request->q));
            $menus = $menus->sortByDesc(function ($menu) use ($search) {
                return $this->calculateSearchRelevance($menu, $search);
            })->values();
        }

        return view('katalog.index', [
            'stands' => $stands,
            'menus' => $menus,
            'activeStand' => $request->stand ?? 'all',
            'activeKategori' => $request->kategori ?? 'all',
            'search' => $request->q ?? '',
        ]);
    }

    /**
     * API endpoint for real-time stock sync across catalog cards.
     */
    public function menuStocks()
    {
        $stocks = Menu::select('id', 'stok', 'is_available')->get()->map(function ($menu) {
            return [
                'id' => $menu->id,
                'stok' => max(0, (int) $menu->stok),
                'is_available' => (bool) $menu->is_available,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $stocks,
        ]);
    }

    /**
     * Show student checkout confirmation page (Redirects directly to cart drawer in catalog).
     */
    public function showCheckout(Request $request)
    {
        return redirect()->route('katalog.index', ['open_cart' => 1]);
    }

    /**
     * Handle student checkout with strict real-time stock validation and decrement.
     */
    public function checkout(Request $request)
    {
        return app(OrderCheckoutController::class)->checkout($request);
    }

    /**
     * Display order status and digital receipt.
     */
    public function orderStatus($kode_tr)
    {
        $order = Order::with(['stand.user', 'items.menu'])
            ->where('kode_tr', $kode_tr)
            ->firstOrFail();

        $batchOrders = collect([$order]);
        if (! empty($order->batch_kode)) {
            $batchOrders = Order::with(['stand.user', 'items.menu'])
                ->where('batch_kode', $order->batch_kode)
                ->orderBy('id', 'asc')
                ->get();
        }

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
            'batchOrders' => $batchOrders,
            'otherOrders' => $otherOrders,
        ]);
    }

    /**
     * Display customer order history page.
     */
    public function orderHistory(Request $request)
    {
        // Otomatis hapus pesanan QRIS yang sudah kedaluwarsa (> 24 jam) dan kembalikan stok menu
        OrderCheckoutController::cleanupExpiredUnpaidOrders();

        $statusFilter = $request->query('status', 'all');
        $searchQuery = trim($request->query('q', ''));

        $query = Order::with(['stand.user', 'items.menu', 'parentOrder']);

        if (auth()->check()) {
            // User login: hanya tampilkan riwayat pesanan milik akun yang sedang login (berdasarkan user_id)
            $user = auth()->user();
            $query->where(function ($q) use ($user) {
                $q->where('user_id', $user->id);

                // Untuk pesanan legacy sebelum user_id ada: cocokkan nama dan kelas
                if (! empty($user->kelas)) {
                    $q->orWhere(function ($sub) use ($user) {
                        $sub->whereNull('user_id')
                            ->where('nama_pemesan', $user->name)
                            ->where('kelas', $user->kelas);
                    });
                } else {
                    $q->orWhere(function ($sub) use ($user) {
                        $sub->whereNull('user_id')
                            ->where('nama_pemesan', $user->name);
                    });
                }
            });

            if (! empty($searchQuery)) {
                $cleanCode = strtoupper($searchQuery);
                if (! str_starts_with($cleanCode, 'PO-') && ! str_starts_with($cleanCode, 'TKT-') && ! str_starts_with($cleanCode, 'SKNC-') && is_numeric($cleanCode)) {
                    $cleanCode = 'SKNC-'.$cleanCode;
                }
                $query->where(function ($sq) use ($searchQuery, $cleanCode) {
                    $sq->where('kode_tr', 'like', "%{$searchQuery}%")
                        ->orWhere('ticket_code', 'like', "%{$searchQuery}%")
                        ->orWhere('batch_kode', 'like', "%{$searchQuery}%")
                        ->orWhere('kode_tr', $cleanCode)
                        ->orWhereHas('parentOrder', function ($po) use ($searchQuery) {
                            $po->where('order_id', 'like', "%{$searchQuery}%");
                        })
                        ->orWhereHas('stand', function ($st) use ($searchQuery) {
                            $st->where('nama_stand', 'like', "%{$searchQuery}%");
                        })
                        ->orWhereHas('items.menu', function ($mn) use ($searchQuery) {
                            $mn->where('nama_menu', 'like', "%{$searchQuery}%");
                        });
                });
            }
        } else {
            // User TIDAK login: hanya cari jika ada query pencarian
            if (! empty($searchQuery)) {
                $cleanCode = strtoupper($searchQuery);
                $query->where(function ($sq) use ($searchQuery, $cleanCode) {
                    $sq->where('kode_tr', 'like', "%{$searchQuery}%")
                        ->orWhere('ticket_code', 'like', "%{$searchQuery}%")
                        ->orWhere('batch_kode', 'like', "%{$searchQuery}%")
                        ->orWhere('kode_tr', $cleanCode)
                        ->orWhereHas('parentOrder', function ($po) use ($searchQuery) {
                            $po->where('order_id', 'like', "%{$searchQuery}%");
                        })
                        ->orWhereHas('stand', function ($st) use ($searchQuery) {
                            $st->where('nama_stand', 'like', "%{$searchQuery}%");
                        })
                        ->orWhereHas('items.menu', function ($mn) use ($searchQuery) {
                            $mn->where('nama_menu', 'like', "%{$searchQuery}%");
                        });
                });
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        // Calculate counts for each tab
        $countsQuery = clone $query;
        $allOrdersForCounts = $countsQuery->select('id', 'parent_order_id', 'batch_kode', 'status', 'metode_pembayaran', 'status_pembayaran')->get();

        $belumBayarBatches = $allOrdersForCounts->filter(function ($o) {
            return $o->metode_pembayaran === 'qris'
                && $o->status_pembayaran !== 'lunas'
                && $o->status !== 'dibatalkan';
        })->groupBy(function ($o) {
            return $o->parent_order_id ? ('parent_'.$o->parent_order_id) : ($o->batch_kode ?: ('order_'.$o->id));
        });

        $belumBayarSuborderIds = $allOrdersForCounts->filter(function ($o) {
            return $o->metode_pembayaran === 'qris'
                && $o->status_pembayaran !== 'lunas'
                && $o->status !== 'dibatalkan';
        })->pluck('id')->all();

        $statusCounts = [
            'all' => $allOrdersForCounts->whereNotIn('id', $belumBayarSuborderIds)->count() + $belumBayarBatches->count(),
            'belum_bayar' => $belumBayarBatches->count(),
            'pending' => $allOrdersForCounts->whereNotIn('id', $belumBayarSuborderIds)->whereIn('status', ['pending', 'menunggu_pembayaran', 'diproses'])->count(),
            'siap_diambil' => $allOrdersForCounts->where('status', 'siap_diambil')->count(),
            'selesai' => $allOrdersForCounts->where('status', 'selesai')->count(),
        ];

        if ($statusFilter === 'belum_bayar') {
            $query->where('metode_pembayaran', 'qris')
                ->where('status_pembayaran', '!=', 'lunas')
                ->where('status', '!=', 'dibatalkan');
        } elseif ($statusFilter === 'pending') {
            $query->whereIn('status', ['pending', 'menunggu_pembayaran', 'diproses'])
                ->where(function ($sq) {
                    $sq->where('metode_pembayaran', '!=', 'qris')
                        ->orWhere('status_pembayaran', 'lunas');
                });
        } elseif ($statusFilter === 'siap_diambil') {
            $query->where('status', 'siap_diambil');
        } elseif ($statusFilter === 'selesai') {
            $query->where('status', 'selesai');
        }

        $rawOrders = $query->latest()->take(50)->get();

        // Group unpaid QRIS orders belonging to the same checkout batch so user pays once
        $displayItems = collect();
        $processedBatchKeys = [];

        foreach ($rawOrders as $order) {
            $isUnpaidQris = $order->isQris() && ! $order->isLunas() && $order->status !== 'dibatalkan';

            if ($isUnpaidQris) {
                $batchKey = $order->parent_order_id ? ('parent_'.$order->parent_order_id) : ($order->batch_kode ?: ('order_'.$order->id));
                if (in_array($batchKey, $processedBatchKeys, true)) {
                    continue;
                }
                $processedBatchKeys[] = $batchKey;

                // Gather all suborders belonging to this batch
                $batchSuborders = $rawOrders->filter(function ($item) use ($order) {
                    if ($order->parent_order_id && $item->parent_order_id === $order->parent_order_id) {
                        return true;
                    }
                    if ($order->batch_kode && $item->batch_kode === $order->batch_kode) {
                        return true;
                    }

                    return $item->id === $order->id;
                })->values();

                if ($order->parentOrder && $batchSuborders->count() < $order->parentOrder->subOrders()->count()) {
                    $order->parentOrder->loadMissing(['subOrders.stand.user', 'subOrders.items.menu']);
                    $batchSuborders = $order->parentOrder->subOrders;
                }

                $batchCode = $order->batch_kode ?: ($order->parentOrder?->order_id ?: $order->kode_tr);
                $grossAmount = $order->parentOrder?->gross_amount ?? $batchSuborders->sum('total_harga');

                $displayItems->push([
                    'type' => 'batch_unpaid',
                    'batch_code' => $batchCode,
                    'parent_order' => $order->parentOrder,
                    'gross_amount' => $grossAmount,
                    'created_at' => $order->created_at,
                    'nama_pemesan' => $order->nama_pemesan,
                    'kelas' => $order->kelas,
                    'catatan' => $order->catatan,
                    'sub_orders' => $batchSuborders,
                    'order_ids' => $batchSuborders->pluck('id')->all(),
                    'primary_order' => $order,
                ]);
            } else {
                $displayItems->push([
                    'type' => 'single_order',
                    'order' => $order,
                ]);
            }
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'count' => $rawOrders->count(),
                'orders' => $this->formatOrdersForResponse($rawOrders),
            ]);
        }

        return view('katalog.riwayat', [
            'orders' => $rawOrders,
            'displayItems' => $displayItems,
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
                $user = auth()->user();
                $userCondition = function ($sub) use ($user) {
                    $sub->where('user_id', $user->id);

                    if (! empty($user->kelas)) {
                        $sub->orWhere(function ($s) use ($user) {
                            $s->whereNull('user_id')
                                ->where('nama_pemesan', $user->name)
                                ->where('kelas', $user->kelas);
                        });
                    } else {
                        $sub->orWhere(function ($s) use ($user) {
                            $s->whereNull('user_id')
                                ->where('nama_pemesan', $user->name);
                        });
                    }
                };

                if ($hasCondition) {
                    $q->orWhere($userCondition);
                } else {
                    $q->where($userCondition);
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

    /**
     * Calculate search relevance score for a menu item.
     * Higher score indicates higher relevance in search results.
     */
    protected function calculateSearchRelevance(Menu $menu, string $search): int
    {
        $name = strtolower(trim($menu->nama_menu));
        $stand = strtolower(trim($menu->stand?->nama_stand ?? ''));
        $pemilik = strtolower(trim($menu->stand?->pemilik ?? ''));
        $kategori = strtolower(trim($menu->kategori ?? ''));

        if ($name === $search) {
            return 2000;
        }

        $tokens = preg_split('/\s+/', $search, -1, PREG_SPLIT_NO_EMPTY) ?: [$search];
        $totalScore = 0;

        foreach ($tokens as $token) {
            $tokenScore = 0;

            // 1. Menu name starts with token (Highest priority!)
            if (str_starts_with($name, $token)) {
                $tokenScore += 800;
            }

            // 2. Any word in menu name starts with token
            $words = preg_split('/[\s,\-_]+/', $name, -1, PREG_SPLIT_NO_EMPTY) ?: [];
            foreach ($words as $idx => $word) {
                if ($word === $token) {
                    $tokenScore += 650;
                    break;
                } elseif (str_starts_with($word, $token)) {
                    $tokenScore += max(500 - ($idx * 15), 350);
                    break;
                }
            }

            // 3. Substring match inside words
            if ($tokenScore === 0 && str_contains($name, $token)) {
                $pos = strpos($name, $token);
                $tokenScore += max(250 - ($pos * 5), 80);
            }

            // 4. Stand / Pemilik match
            if ($stand === $token || $pemilik === $token) {
                $tokenScore += 200;
            } elseif (str_starts_with($stand, $token) || str_starts_with($pemilik, $token)) {
                $tokenScore += 150;
            } elseif (str_contains($stand, $token) || str_contains($pemilik, $token)) {
                $tokenScore += 60;
            }

            // 5. Category match
            if ($kategori === $token || str_starts_with($kategori, $token)) {
                $tokenScore += 50;
            }

            if ($tokenScore === 0) {
                return 0; // If any token fails to match, zero out
            }

            $totalScore += $tokenScore;
        }

        return $totalScore;
    }
}
