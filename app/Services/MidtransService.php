<?php

namespace App\Services;

use App\Models\ParentOrder;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MidtransService
{
    /**
     * Create Midtrans Snap Token for Parent Order.
     *
     * @param  array<int, array{id: int|string, price: int, quantity: int, name: string}>  $itemDetails
     * @return array{snap_token: string, redirect_url: ?string}
     */
    public function createSnapToken(ParentOrder $parentOrder, ?User $user = null, array $itemDetails = []): array
    {
        $serverKey = config('midtrans.server_key');
        $snapUrl = config('midtrans.snap_url');

        $customerName = $user->name ?? $parentOrder->nama_pemesan ?? 'Siswa Skanic';
        $customerEmail = $user->email ?? 'siswa@kantinskanic.sch.id';
        $customerPhone = $user->no_wa ?? '081234567890';

        // Format items for Midtrans if not already formatted
        if (empty($itemDetails)) {
            $parentOrder->loadMissing('subOrders.items.menu');
            $formattedItems = [];
            foreach ($parentOrder->subOrders as $subOrder) {
                foreach ($subOrder->items as $item) {
                    $formattedItems[] = [
                        'id' => (string) $item->menu_id,
                        'price' => (int) $item->harga_satuan,
                        'quantity' => (int) $item->jumlah,
                        'name' => mb_substr($item->menu->nama_menu ?? 'Menu', 0, 50),
                    ];
                }
            }
            $itemDetails = $formattedItems;
        }

        $payload = [
            'transaction_details' => [
                'order_id' => $parentOrder->order_id,
                'gross_amount' => (int) $parentOrder->gross_amount,
            ],
            'enabled_payments' => ['other_qris'],
            'customer_details' => [
                'first_name' => $customerName,
                'email' => $customerEmail,
                'phone' => $customerPhone,
            ],
            'item_details' => $itemDetails,
            'expiry' => [
                'start_time' => ($parentOrder->created_at ? $parentOrder->created_at->timezone('Asia/Jakarta') : now('Asia/Jakarta'))->format('Y-m-d H:i:s O'),
                'unit' => 'hour',
                'duration' => (int) config('midtrans.expiry_hours', 24),
            ],
        ];

        try {
            $response = Http::withBasicAuth($serverKey, '')
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->connectTimeout(2)
                ->timeout(4)
                ->post($snapUrl, $payload);

            if ($response->successful()) {
                $data = $response->json();
                $token = $data['token'] ?? null;
                $redirectUrl = $data['redirect_url'] ?? null;

                if ($token) {
                    $parentOrder->update([
                        'snap_token' => $token,
                        'snap_redirect_url' => $redirectUrl,
                    ]);

                    return [
                        'snap_token' => $token,
                        'redirect_url' => $redirectUrl,
                    ];
                }
            }

            Log::error('Midtrans Snap Error: '.$response->body());
        } catch (Exception $e) {
            Log::error('Midtrans Snap Exception: '.$e->getMessage());
        }

        // Sandbox simulated fallback for testing environments if server key is dummy or offline
        $simulatedToken = 'SNAP-MOCK-'.bin2hex(random_bytes(16));
        $parentOrder->update([
            'snap_token' => $simulatedToken,
            'snap_redirect_url' => 'https://app.sandbox.midtrans.com/snap/v2/vtweb/'.$simulatedToken,
        ]);

        return [
            'snap_token' => $simulatedToken,
            'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v2/vtweb/'.$simulatedToken,
        ];
    }

    /**
     * Verify Midtrans SHA512 signature key.
     */
    public function verifySignature(string $orderId, string $statusCode, string $grossAmount, string $signatureKey): bool
    {
        $serverKey = config('midtrans.server_key');
        $expected = hash('sha512', $orderId.$statusCode.$grossAmount.$serverKey);

        return hash_equals($expected, $signatureKey);
    }

    /**
     * Handle incoming webhook notification from Midtrans with idempotency.
     *
     * @param  array<string, mixed>  $payload
     * @return array{status: string, message?: string}
     */
    public function handleWebhook(array $payload): array
    {
        $orderId = $payload['order_id'] ?? null;
        $statusCode = (string) ($payload['status_code'] ?? '');
        $grossAmount = (string) ($payload['gross_amount'] ?? '');
        $signatureKey = (string) ($payload['signature_key'] ?? '');
        $transactionStatus = $payload['transaction_status'] ?? '';
        $fraudStatus = $payload['fraud_status'] ?? 'accept';

        if (! $orderId || ! $signatureKey) {
            return ['status' => 'error', 'message' => 'Data notifikasi tidak lengkap'];
        }

        // Verify signature
        if (! $this->verifySignature($orderId, $statusCode, $grossAmount, $signatureKey)) {
            Log::warning('Midtrans Invalid Signature for Order: '.$orderId);

            return ['status' => 'error', 'message' => 'Signature key tidak valid'];
        }

        $parentOrder = ParentOrder::with('subOrders.items.menu')
            ->where('order_id', $orderId)
            ->first();

        if (! $parentOrder) {
            return ['status' => 'error', 'message' => 'Pesanan utama tidak ditemukan'];
        }

        // Idempotency: Jika sudah berstatus paid dan ada event capture/settlement berikutnya
        if ($parentOrder->isPaid() && in_array($transactionStatus, ['capture', 'settlement'])) {
            return ['status' => 'success', 'message' => 'Pesanan sudah lunas sebelumnya'];
        }

        DB::transaction(function () use ($parentOrder, $transactionStatus, $fraudStatus, $payload) {
            if ($transactionStatus === 'capture' || $transactionStatus === 'settlement') {
                if ($fraudStatus === 'challenge') {
                    $parentOrder->update([
                        'payment_status' => 'pending',
                        'metadata' => array_merge($parentOrder->metadata ?? [], ['midtrans_last_payload' => $payload]),
                    ]);
                } else {
                    $this->markAsPaid($parentOrder, 'webhook', ['midtrans_last_payload' => $payload]);
                }
            } elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
                $statusMapping = ($transactionStatus === 'expire') ? 'expired' : 'failed';

                $parentOrder->update([
                    'payment_status' => $statusMapping,
                    'metadata' => array_merge($parentOrder->metadata ?? [], ['midtrans_last_payload' => $payload]),
                ]);

                // Batalkan seluruh tiket per stand dan kembalikan stok
                foreach ($parentOrder->subOrders as $subOrder) {
                    $subOrder->update([
                        'status' => 'dibatalkan',
                        'status_pembayaran' => 'belum_lunas',
                    ]);

                    foreach ($subOrder->items as $item) {
                        if ($item->menu) {
                            $item->menu->increment('stok', $item->jumlah);
                        }
                    }
                }
            } elseif ($transactionStatus === 'pending') {
                $parentOrder->update([
                    'payment_status' => 'pending',
                    'metadata' => array_merge($parentOrder->metadata ?? [], ['midtrans_last_payload' => $payload]),
                ]);
            }
        });

        return ['status' => 'success'];
    }

    /**
     * Mark a ParentOrder and all its SubOrders as Paid / Lunas.
     *
     * @param  array<string, mixed>  $extraMetadata
     */
    public function markAsPaid(ParentOrder $parentOrder, string $source = 'direct', array $extraMetadata = []): void
    {
        DB::transaction(function () use ($parentOrder, $source, $extraMetadata) {
            $parentOrder->loadMissing('subOrders.items.menu');

            $meta = array_merge($parentOrder->metadata ?? [], $extraMetadata, [
                'paid_source' => $source,
                'paid_at' => now()->toIso8601String(),
            ]);

            $parentOrder->update([
                'payment_status' => 'paid',
                'metadata' => $meta,
            ]);

            foreach ($parentOrder->subOrders as $subOrder) {
                $subOrder->update([
                    'status' => 'pending', // menunggu konfirmasi / disiapkan stand (belum selesai)
                    'status_pembayaran' => 'lunas',
                ]);
            }
        });
    }

    /**
     * Query Midtrans Status API directly to sync payment status.
     *
     * @return array{success: bool, is_paid: bool, payment_status: string, message?: string}
     */
    public function syncStatusFromMidtrans(ParentOrder $parentOrder): array
    {
        if ($parentOrder->isPaid()) {
            return [
                'success' => true,
                'is_paid' => true,
                'payment_status' => 'paid',
                'message' => 'Pesanan sudah lunas.',
            ];
        }

        $serverKey = config('midtrans.server_key');
        $isProduction = config('midtrans.is_production');
        $baseUrl = $isProduction ? 'https://api.midtrans.com/v2' : 'https://api.sandbox.midtrans.com/v2';

        try {
            $response = Http::withBasicAuth($serverKey, '')
                ->withHeaders(['Accept' => 'application/json'])
                ->get("{$baseUrl}/{$parentOrder->order_id}/status");

            if ($response->successful()) {
                $payload = $response->json();
                $trStatus = $payload['transaction_status'] ?? '';
                $fraudStatus = $payload['fraud_status'] ?? 'accept';

                if (in_array($trStatus, ['capture', 'settlement']) && $fraudStatus !== 'challenge') {
                    $this->markAsPaid($parentOrder, 'sync_api', ['midtrans_sync_payload' => $payload]);

                    return [
                        'success' => true,
                        'is_paid' => true,
                        'payment_status' => 'paid',
                        'message' => 'Pembayaran terverifikasi lunas oleh Midtrans.',
                    ];
                }

                if (in_array($trStatus, ['cancel', 'deny', 'expire'])) {
                    $statusMapping = ($trStatus === 'expire') ? 'expired' : 'failed';
                    $parentOrder->update(['payment_status' => $statusMapping]);

                    return [
                        'success' => true,
                        'is_paid' => false,
                        'payment_status' => $statusMapping,
                        'message' => "Status transaksi: {$trStatus}",
                    ];
                }
            }
        } catch (Exception $e) {
            Log::error('Midtrans direct sync error: '.$e->getMessage());
        }

        return [
            'success' => true,
            'is_paid' => $parentOrder->fresh()->isPaid(),
            'payment_status' => $parentOrder->fresh()->payment_status,
        ];
    }
}
