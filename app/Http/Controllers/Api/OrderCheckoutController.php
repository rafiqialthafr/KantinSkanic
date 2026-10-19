<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Order;
use App\Models\ParentOrder;
use App\Services\MidtransService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class OrderCheckoutController extends Controller
{
    public function __construct(
        protected MidtransService $midtransService
    ) {}

    /**
     * Process checkout and create Parent-Child (Split) Orders.
     */
    public function checkout(Request $request): Response
    {
        $user = auth()->user();

        // Harmonize field names: items can contain quantity/jumlah, notes/catatan
        $rawItems = $request->input('items', []);
        $normalizedItems = [];
        foreach ($rawItems as $raw) {
            $normalizedItems[] = [
                'menu_id' => $raw['menu_id'] ?? null,
                'quantity' => (int) ($raw['quantity'] ?? $raw['jumlah'] ?? 1),
                'notes' => $raw['notes'] ?? $raw['catatan'] ?? null,
            ];
        }
        $request->merge(['items' => $normalizedItems]);

        $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.menu_id' => ['required', 'exists:menus,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:50'],
            'items.*.notes' => ['nullable', 'string', 'max:255'],
            'payment_method' => ['nullable', 'string', 'in:qris,cod,tunai'],
            'metode_pembayaran' => ['nullable', 'string', 'in:qris,cod,tunai'],
            'nama_pemesan' => ['nullable', 'string', 'max:100'],
            'kelas' => ['nullable', 'string', 'max:50'],
            'catatan' => ['nullable', 'string', 'max:500'],
        ], [
            'items.required' => 'Keranjang belanja masih kosong.',
            'items.min' => 'Pilih minimal satu menu untuk dipesan.',
        ]);

        $namaPemesan = $user?->name ?? $request->input('nama_pemesan') ?? 'Siswa Skanic';
        $kelas = $user?->kelas ?? $request->input('kelas') ?? session('siswa_kelas') ?? 'Siswa';
        $globalCatatan = $request->input('catatan');

        $methodInput = $request->input('payment_method') ?: $request->input('metode_pembayaran', 'qris');
        $paymentMethod = ($methodInput === 'tunai' || $methodInput === 'cod') ? 'cod' : 'qris';

        $items = $request->input('items');
        $menuIds = array_column($items, 'menu_id');

        try {
            $checkoutResult = DB::transaction(function () use ($user, $namaPemesan, $kelas, $globalCatatan, $paymentMethod, $items, $menuIds) {
                // 1. Lock and validate menu prices from database directly
                $menus = Menu::with('stand')->whereIn('id', $menuIds)->lockForUpdate()->get()->keyBy('id');

                foreach ($items as $item) {
                    $menu = $menus[$item['menu_id']] ?? null;
                    if (! $menu) {
                        throw new \InvalidArgumentException('Menu tidak ditemukan.');
                    }

                    if (! $menu->is_available) {
                        throw new \InvalidArgumentException("Maaf, menu '{$menu->nama_menu}' sedang tidak tersedia.");
                    }

                    if ($menu->stok < $item['quantity']) {
                        $stokMsg = $menu->stok <= 0
                            ? "Maaf, stok menu '{$menu->nama_menu}' sudah habis."
                            : "Maaf, stok menu '{$menu->nama_menu}' hanya tersisa {$menu->stok}.";

                        throw new \InvalidArgumentException($stokMsg);
                    }
                }

                // 2. Group items by stand_id
                $standGroups = [];
                $grossAmount = 0;
                $midtransItemDetails = [];

                foreach ($items as $item) {
                    $menu = $menus[$item['menu_id']];
                    $standId = $menu->stand_id;
                    $qty = (int) $item['quantity'];
                    $itemSubtotal = $menu->harga * $qty;
                    $grossAmount += $itemSubtotal;

                    if (! isset($standGroups[$standId])) {
                        $standGroups[$standId] = [
                            'stand' => $menu->stand,
                            'items' => [],
                            'subtotal' => 0,
                        ];
                    }

                    $standGroups[$standId]['items'][] = [
                        'menu' => $menu,
                        'quantity' => $qty,
                        'price' => $menu->harga,
                        'subtotal' => $itemSubtotal,
                        'notes' => $item['notes'] ?? $globalCatatan,
                    ];
                    $standGroups[$standId]['subtotal'] += $itemSubtotal;

                    $midtransItemDetails[] = [
                        'id' => (string) $menu->id,
                        'price' => (int) $menu->harga,
                        'quantity' => $qty,
                        'name' => mb_substr($menu->nama_menu, 0, 50),
                    ];
                }

                // 3. Create Parent Order
                $parentOrderId = ParentOrder::generateOrderId();
                $parentPaymentStatus = ($paymentMethod === 'cod') ? 'pending_cash' : 'pending';
                $parentOrder = ParentOrder::create([
                    'order_id' => $parentOrderId,
                    'user_id' => $user?->id,
                    'nama_pemesan' => $namaPemesan,
                    'kelas' => $kelas,
                    'gross_amount' => $grossAmount,
                    'payment_method' => $paymentMethod,
                    'payment_status' => $parentPaymentStatus,
                    'metadata' => [
                        'catatan' => $globalCatatan,
                    ],
                ]);

                // 4. Create SubOrders per Stand & decrement real-time stock
                $allTicketCodes = [];
                foreach ($standGroups as $standId => $group) {
                    $stand = $group['stand'];
                    $standNum = $stand?->nomor_stand ?: 'STD0'.$standId;
                    $cleanStandNum = strtoupper(preg_replace('/[^A-Z0-9]/', '', $standNum));

                    do {
                        $uniqueCode = 'SKNC-'.$cleanStandNum.'-'.rand(1000, 9999);
                    } while (Order::where('kode_tr', $uniqueCode)->orWhere('ticket_code', $uniqueCode)->exists());

                    $allTicketCodes[] = $uniqueCode;

                    $initialSubOrderStatus = ($paymentMethod === 'qris') ? 'menunggu_pembayaran' : 'pending';
                    $metodeOrder = ($paymentMethod === 'qris') ? 'qris' : 'tunai';

                    $subOrder = Order::create([
                        'parent_order_id' => $parentOrder->id,
                        'kode_tr' => $uniqueCode,
                        'ticket_code' => $uniqueCode,
                        'batch_kode' => $parentOrder->order_id,
                        'user_id' => $user?->id,
                        'stand_id' => $standId,
                        'nama_pemesan' => $namaPemesan,
                        'kelas' => $kelas,
                        'total_harga' => $group['subtotal'],
                        'metode_pembayaran' => $metodeOrder,
                        'status_pembayaran' => 'belum_lunas',
                        'settlement_status' => 'pending',
                        'status' => $initialSubOrderStatus,
                        'catatan' => $globalCatatan,
                    ]);

                    foreach ($group['items'] as $gItem) {
                        $subOrder->items()->create([
                            'menu_id' => $gItem['menu']->id,
                            'jumlah' => $gItem['quantity'],
                            'harga_satuan' => $gItem['price'],
                            'subtotal' => $gItem['subtotal'],
                            'notes' => $gItem['notes'],
                        ]);

                        // Decrement stock
                        $gItem['menu']->decrement('stok', $gItem['quantity']);
                    }
                }

                return [
                    'parentOrder' => $parentOrder,
                    'grossAmount' => $grossAmount,
                    'allTicketCodes' => $allTicketCodes,
                    'midtransItemDetails' => $midtransItemDetails,
                ];
            });
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        $parentOrder = $checkoutResult['parentOrder'];
        $grossAmount = $checkoutResult['grossAmount'];
        $allTicketCodes = $checkoutResult['allTicketCodes'];
        $midtransItemDetails = $checkoutResult['midtransItemDetails'];

        // 5. If QRIS: request Snap Token from Midtrans Snap Sandbox outside DB transaction
        $snapToken = null;
        $redirectUrl = route('order.history');

        if ($paymentMethod === 'qris') {
            $snapResult = $this->midtransService->createSnapToken($parentOrder, $user, $midtransItemDetails);
            $snapToken = $snapResult['snap_token'];
        }

        if (! $request->expectsJson() && ! $request->is('api/*')) {
            return redirect()->route('order.status', $allTicketCodes[0] ?? $parentOrder->order_id);
        }

        $tickets = $parentOrder->subOrders()->with(['stand', 'items.menu'])->get()->map(function ($sub) {
            return [
                'ticket_code' => $sub->ticket_code,
                'stand_name' => $sub->stand?->nama_stand ?? 'Stand Kantin',
                'total_harga' => $sub->total_harga,
                'items' => $sub->items->map(function ($it) {
                    return [
                        'nama_menu' => $it->menu?->nama_menu ?? 'Menu',
                        'jumlah' => $it->jumlah,
                        'harga_satuan' => $it->harga_satuan,
                        'subtotal' => $it->subtotal,
                    ];
                })->values()->all(),
            ];
        })->values()->all();

        return response()->json([
            'success' => true,
            'message' => 'Pesanan berhasil dibuat.',
            'payment_method' => $paymentMethod,
            'order_id' => $parentOrder->order_id,
            'snap_token' => $snapToken,
            'gross_amount' => $grossAmount,
            'all_codes' => $allTicketCodes,
            'tickets' => $tickets,
            'redirect_url' => $redirectUrl,
        ]);
    }

    /**
     * Get or regenerate Snap Token for an existing Parent Order.
     */
    public function getSnapToken(string $orderId): JsonResponse
    {
        $parentOrder = ParentOrder::with('subOrders.items.menu')->where('order_id', $orderId)->first();

        if (! $parentOrder) {
            // Also allow finding by ticket code or suborder
            $subOrder = Order::where('ticket_code', $orderId)->orWhere('kode_tr', $orderId)->first();
            if ($subOrder) {
                if ($subOrder->parent_order_id) {
                    $parentOrder = ParentOrder::with('subOrders.items.menu')->find($subOrder->parent_order_id);
                } else {
                    $parentOrder = ParentOrder::firstOrCreate(
                        ['order_id' => $subOrder->batch_kode ?: $subOrder->kode_tr],
                        [
                            'user_id' => $subOrder->user_id,
                            'nama_pemesan' => $subOrder->nama_pemesan,
                            'kelas' => $subOrder->kelas,
                            'gross_amount' => $subOrder->total_harga,
                            'payment_method' => $subOrder->metode_pembayaran,
                            'payment_status' => $subOrder->status_pembayaran === 'lunas' ? 'settlement' : 'pending',
                        ]
                    );
                    $subOrder->update(['parent_order_id' => $parentOrder->id, 'batch_kode' => $parentOrder->order_id]);
                    $parentOrder->load('subOrders.items.menu');
                }
            }
        }

        if (! $parentOrder) {
            return response()->json(['success' => false, 'message' => 'Pesanan tidak ditemukan.'], 404);
        }

        if ($parentOrder->isPaid()) {
            return response()->json(['success' => false, 'message' => 'Pesanan sudah lunas.'], 400);
        }

        $expiryHours = (int) config('midtrans.expiry_hours', 24);
        if ($parentOrder->created_at && now()->greaterThanOrEqualTo($parentOrder->created_at->copy()->addHours($expiryHours))) {
            return response()->json(['success' => false, 'message' => 'Waktu pembayaran QRIS telah kedaluwarsa (24 jam). Silakan buat pesanan baru.'], 400);
        }

        $parentOrder->loadMissing(['subOrders.stand', 'subOrders.items.menu']);
        $tickets = $parentOrder->subOrders->map(function ($sub) {
            return [
                'ticket_code' => $sub->ticket_code ?: $sub->kode_tr,
                'stand_name' => $sub->stand?->nama_stand ?? 'Stand Kantin',
                'total_harga' => $sub->total_harga,
                'items' => $sub->items->map(function ($it) {
                    return [
                        'nama_menu' => $it->menu?->nama_menu ?? 'Menu',
                        'jumlah' => $it->jumlah,
                        'harga_satuan' => $it->harga_satuan,
                        'subtotal' => $it->subtotal,
                    ];
                })->values()->all(),
            ];
        })->values()->all();

        $token = $parentOrder->snap_token;
        if (! $token) {
            $snapResult = $this->midtransService->createSnapToken($parentOrder, auth()->user());
            $token = $snapResult['snap_token'];
        }

        return response()->json([
            'success' => true,
            'order_id' => $parentOrder->order_id,
            'snap_token' => $token,
            'gross_amount' => $parentOrder->gross_amount,
            'nama_pemesan' => $parentOrder->nama_pemesan,
            'kelas' => $parentOrder->kelas,
            'tickets' => $tickets,
            'all_codes' => $parentOrder->subOrders->pluck('ticket_code')->all(),
            'redirect_url' => route('order.history'),
        ]);
    }

    /**
     * Check and sync payment status directly with Midtrans or confirm client-side payment.
     */
    public function syncStatus(string $orderId, Request $request): JsonResponse
    {
        $parentOrder = ParentOrder::with('subOrders.items.menu')->where('order_id', $orderId)->first();

        if (! $parentOrder) {
            $subOrder = Order::where('ticket_code', $orderId)->orWhere('kode_tr', $orderId)->first();
            if ($subOrder) {
                if ($subOrder->parent_order_id) {
                    $parentOrder = ParentOrder::with('subOrders.items.menu')->find($subOrder->parent_order_id);
                } else {
                    $parentOrder = ParentOrder::firstOrCreate(
                        ['order_id' => $subOrder->batch_kode ?: $subOrder->kode_tr],
                        [
                            'user_id' => $subOrder->user_id,
                            'nama_pemesan' => $subOrder->nama_pemesan,
                            'kelas' => $subOrder->kelas,
                            'gross_amount' => $subOrder->total_harga,
                            'payment_method' => $subOrder->metode_pembayaran,
                            'payment_status' => $subOrder->status_pembayaran === 'lunas' ? 'settlement' : 'pending',
                        ]
                    );
                    $subOrder->update(['parent_order_id' => $parentOrder->id, 'batch_kode' => $parentOrder->order_id]);
                    $parentOrder->load('subOrders.items.menu');
                }
            }
        }

        if (! $parentOrder) {
            return response()->json(['success' => false, 'message' => 'Pesanan tidak ditemukan.'], 404);
        }

        // If client sends force_confirm (from Snap onSuccess callback) or simulated payment
        $forceConfirm = (bool) $request->input('force_confirm', false);
        $result = $request->input('result', []);
        $trStatus = $result['transaction_status'] ?? '';

        if ($forceConfirm || in_array($trStatus, ['settlement', 'capture'])) {
            $this->midtransService->markAsPaid($parentOrder, 'client_callback', ['client_result' => $result]);

            return response()->json([
                'success' => true,
                'is_paid' => true,
                'payment_status' => 'paid',
                'order_id' => $parentOrder->order_id,
                'message' => 'Pembayaran QRIS berhasil dikonfirmasi.',
            ]);
        }

        // Otherwise sync with Midtrans API
        $syncResult = $this->midtransService->syncStatusFromMidtrans($parentOrder);

        return response()->json(array_merge($syncResult, [
            'order_id' => $parentOrder->order_id,
        ]));
    }

    /**
     * Get real-time status of Parent Order and its SubOrders.
     */
    public function getStatus(string $orderId): JsonResponse
    {
        $parentOrder = ParentOrder::with(['subOrders.stand', 'subOrders.items.menu'])
            ->where('order_id', $orderId)
            ->first();

        if (! $parentOrder) {
            $subOrder = Order::where('ticket_code', $orderId)->orWhere('kode_tr', $orderId)->first();
            if ($subOrder && $subOrder->parent_order_id) {
                $parentOrder = ParentOrder::with(['subOrders.stand', 'subOrders.items.menu'])->find($subOrder->parent_order_id);
            }
        }

        if (! $parentOrder) {
            return response()->json(['success' => false, 'message' => 'Pesanan tidak ditemukan.'], 404);
        }

        return response()->json([
            'success' => true,
            'order_id' => $parentOrder->order_id,
            'payment_method' => $parentOrder->payment_method,
            'payment_status' => $parentOrder->payment_status,
            'is_paid' => $parentOrder->isPaid(),
            'gross_amount' => $parentOrder->gross_amount,
            'tickets' => $parentOrder->subOrders->map(fn ($sub) => [
                'ticket_code' => $sub->ticket_code,
                'stand_name' => $sub->stand?->nama_stand ?? 'Stand',
                'status' => $sub->status,
                'status_pembayaran' => $sub->status_pembayaran,
                'total_harga' => $sub->total_harga,
            ]),
        ]);
    }

    /**
     * Cancel an uncompleted QRIS checkout, restore menu stock, and remove pending records.
     */
    public function cancelCheckout(string $orderId): JsonResponse
    {
        $parentOrder = ParentOrder::with('subOrders.items.menu')
            ->where('order_id', $orderId)
            ->first();

        if (! $parentOrder) {
            $subOrder = Order::with('items.menu')->where('ticket_code', $orderId)->orWhere('kode_tr', $orderId)->orWhere('id', $orderId)->first();
            if ($subOrder) {
                if ($subOrder->parent_order_id) {
                    $parentOrder = ParentOrder::with('subOrders.items.menu')->find($subOrder->parent_order_id);
                } else {
                    if ($subOrder->isLunas()) {
                        return response()->json(['success' => false, 'message' => 'Pesanan yang sudah lunas tidak dapat dibatalkan.'], 400);
                    }
                    DB::transaction(function () use ($subOrder) {
                        foreach ($subOrder->items as $item) {
                            if ($item->menu) {
                                $item->menu->increment('stok', $item->jumlah);
                            }
                            $item->delete();
                        }
                        $subOrder->delete();
                    });

                    return response()->json([
                        'success' => true,
                        'message' => 'Checkout berhasil dibatalkan dan stok telah dikembalikan.',
                    ]);
                }
            }
        }

        if (! $parentOrder) {
            return response()->json(['success' => false, 'message' => 'Pesanan tidak ditemukan.'], 404);
        }

        // Only allow cancelling if still unpaid
        if ($parentOrder->isPaid() || $parentOrder->payment_status === 'settlement') {
            return response()->json(['success' => false, 'message' => 'Pesanan yang sudah lunas tidak dapat dibatalkan.'], 400);
        }

        DB::transaction(function () use ($parentOrder) {
            foreach ($parentOrder->subOrders as $subOrder) {
                foreach ($subOrder->items as $item) {
                    if ($item->menu) {
                        $item->menu->increment('stok', $item->jumlah);
                    }
                    $item->delete();
                }
                $subOrder->delete();
            }

            $parentOrder->delete();
        });

        return response()->json([
            'success' => true,
            'message' => 'Checkout berhasil dibatalkan dan stok telah dikembalikan.',
        ]);
    }

    /**
     * Clean up unpaid QRIS orders that have exceeded the 24-hour expiration window.
     * Restores stock to menus and automatically deletes pending checkout records so they disappear.
     */
    public static function cleanupExpiredUnpaidOrders(): int
    {
        $expiryHours = (int) config('midtrans.expiry_hours', 24);
        $cutoffTime = now()->subHours($expiryHours);
        $cleanedCount = 0;

        // 1. Expired unpaid ParentOrders
        $expiredParentOrders = ParentOrder::with('subOrders.items.menu')
            ->where('payment_method', 'qris')
            ->where('payment_status', '!=', 'settlement')
            ->where('created_at', '<=', $cutoffTime)
            ->get();

        foreach ($expiredParentOrders as $parentOrder) {
            if (! $parentOrder->isPaid()) {
                DB::transaction(function () use ($parentOrder) {
                    foreach ($parentOrder->subOrders as $subOrder) {
                        foreach ($subOrder->items as $item) {
                            if ($item->menu) {
                                $item->menu->increment('stok', $item->jumlah);
                            }
                            $item->delete();
                        }
                        $subOrder->delete();
                    }
                    $parentOrder->delete();
                });
                $cleanedCount++;
            }
        }

        // 2. Expired standalone Orders (qris, not lunas, not dibatalkan, no parent_order_id)
        $expiredSingleOrders = Order::with('items.menu')
            ->where('metode_pembayaran', 'qris')
            ->where('status_pembayaran', '!=', 'lunas')
            ->where('status', '!=', 'dibatalkan')
            ->whereNull('parent_order_id')
            ->where('created_at', '<=', $cutoffTime)
            ->get();

        foreach ($expiredSingleOrders as $singleOrder) {
            DB::transaction(function () use ($singleOrder) {
                foreach ($singleOrder->items as $item) {
                    if ($item->menu) {
                        $item->menu->increment('stok', $item->jumlah);
                    }
                    $item->delete();
                }
                $singleOrder->delete();
            });
            $cleanedCount++;
        }

        return $cleanedCount;
    }

    /**
     * Get real-time status of multiple orders for history page.
     */
    public function getRealtimeStatuses(Request $request): JsonResponse
    {
        self::cleanupExpiredUnpaidOrders();

        $orderIds = $request->input('order_ids', []);
        $orderCodes = $request->input('order_codes', []);

        if (empty($orderIds) && empty($orderCodes)) {
            return response()->json(['success' => true, 'orders' => []]);
        }

        $query = Order::with(['parentOrder', 'stand']);

        if (! empty($orderIds) && ! empty($orderCodes)) {
            $query->where(function ($q) use ($orderIds, $orderCodes) {
                $q->whereIn('id', $orderIds)
                    ->orWhereIn('kode_tr', $orderCodes)
                    ->orWhereIn('ticket_code', $orderCodes);
            });
        } elseif (! empty($orderIds)) {
            $query->whereIn('id', $orderIds);
        } else {
            $query->where(function ($q) use ($orderCodes) {
                $q->whereIn('kode_tr', $orderCodes)
                    ->orWhereIn('ticket_code', $orderCodes);
            });
        }

        $orders = $query->get();

        foreach ($orders as $order) {
            if ($order->isQris() && ! $order->isLunas() && $order->parentOrder) {
                try {
                    $this->midtransService->syncStatusFromMidtrans($order->parentOrder);
                    $order->refresh();
                } catch (\Throwable $e) {
                }
            }
        }

        $data = $orders->map(function ($order) {
            return [
                'id' => $order->id,
                'kode_tr' => $order->kode_tr,
                'ticket_code' => $order->ticket_code ?: $order->kode_tr,
                'status' => $order->status,
                'status_pembayaran' => $order->status_pembayaran,
                'is_lunas' => $order->isLunas(),
                'is_qris' => $order->isQris(),
                'stand_name' => $order->stand?->nama_stand ?? 'Stand Kantin',
                'stand_num' => $order->stand?->nomor_stand ?? '-',
                'batch_kode' => $order->batch_kode ?: ($order->parentOrder?->order_id ?: $order->kode_tr),
            ];
        });

        return response()->json([
            'success' => true,
            'orders' => $data,
        ]);
    }
}
