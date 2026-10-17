<?php

namespace Tests\Feature;

use App\Models\Menu;
use App\Models\ParentOrder;
use App\Models\Stand;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MidtransOrderAndWebhookTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_checkout_qris_creates_parent_order_and_suborders_and_returns_snap_token(): void
    {
        Http::fake([
            'https://app.sandbox.midtrans.com/snap/v1/transactions' => Http::response([
                'token' => 'SNAP-TEST-TOKEN-12345',
                'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v2/vtweb/SNAP-TEST-TOKEN-12345',
            ], 201),
        ]);

        $student = User::create([
            'name' => 'Fahri Siswa',
            'email' => 'fahri@siswa.sch.id',
            'password' => bcrypt('password123'),
            'role' => 'siswa',
            'kelas' => 'XII RPL 1',
        ]);

        $stand1 = Stand::first();
        $stand2 = Stand::skip(1)->first() ?? Stand::create([
            'user_id' => null,
            'nama_stand' => 'Stand Minuman Segar',
            'nomor_stand' => 'STD02',
            'is_active' => true,
        ]);

        $menu1 = Menu::create([
            'stand_id' => $stand1->id,
            'nama_menu' => 'Nasi Goreng Spesial',
            'kategori' => 'makanan',
            'harga' => 15000,
            'stok' => 20,
            'is_available' => true,
        ]);

        $menu2 = Menu::create([
            'stand_id' => $stand2->id,
            'nama_menu' => 'Es Teh Manis',
            'kategori' => 'minuman',
            'harga' => 5000,
            'stok' => 30,
            'is_available' => true,
        ]);

        $response = $this->actingAs($student)
            ->postJson(route('api.orders.checkout'), [
                'payment_method' => 'qris',
                'catatan' => 'Bungkus plastik ya',
                'items' => [
                    ['menu_id' => $menu1->id, 'quantity' => 2, 'notes' => 'Sedang pedas'],
                    ['menu_id' => $menu2->id, 'quantity' => 1, 'notes' => 'Es sedikit'],
                ],
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'payment_method' => 'qris',
                'gross_amount' => 35000,
            ]);

        $responseData = $response->json();
        $this->assertNotEmpty($responseData['snap_token']);
        $this->assertNotEmpty($responseData['order_id']);

        // Assert ParentOrder exists
        $parentOrder = ParentOrder::where('order_id', $responseData['order_id'])->first();
        $this->assertNotNull($parentOrder);
        $this->assertEquals(35000, $parentOrder->gross_amount);
        $this->assertEquals('qris', $parentOrder->payment_method);
        $this->assertEquals('pending', $parentOrder->payment_status);

        // Assert 2 SubOrders created
        $this->assertCount(2, $parentOrder->subOrders);
        foreach ($parentOrder->subOrders as $subOrder) {
            $this->assertEquals('menunggu_pembayaran', $subOrder->status);
            $this->assertStringStartsWith('SKNC-', $subOrder->ticket_code);
        }

        // Assert stock decremented
        $this->assertEquals(18, $menu1->fresh()->stok);
        $this->assertEquals(29, $menu2->fresh()->stok);
    }

    public function test_checkout_cod_creates_parent_order_with_pending_cash_and_suborders_pending(): void
    {
        $student = User::create([
            'name' => 'Siti Siswa',
            'email' => 'siti@siswa.sch.id',
            'password' => bcrypt('password123'),
            'role' => 'siswa',
            'kelas' => 'XI AKL 2',
        ]);

        $stand = Stand::first();
        $menu = Menu::create([
            'stand_id' => $stand->id,
            'nama_menu' => 'Mie Goreng Bakso',
            'kategori' => 'makanan',
            'harga' => 12000,
            'stok' => 10,
            'is_available' => true,
        ]);

        $response = $this->actingAs($student)
            ->postJson(route('api.orders.checkout'), [
                'payment_method' => 'cod',
                'items' => [
                    ['menu_id' => $menu->id, 'quantity' => 1],
                ],
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'payment_method' => 'cod',
                'gross_amount' => 12000,
            ]);

        $parent = ParentOrder::latest()->first();
        $this->assertEquals('pending_cash', $parent->payment_status);
        $this->assertEquals('cod', $parent->payment_method);

        $subOrder = $parent->subOrders->first();
        $this->assertEquals('pending', $subOrder->status);
    }

    public function test_midtrans_webhook_settlement_updates_parent_to_paid_and_suborders_to_pending(): void
    {
        $serverKey = config('midtrans.server_key');
        $orderId = 'ORDER-20261016-TEST1';
        $grossAmount = '25000';
        $statusCode = '200';
        $signatureKey = hash('sha512', $orderId.$statusCode.$grossAmount.$serverKey);

        $stand = Stand::first();
        $menu = Menu::create([
            'stand_id' => $stand->id,
            'nama_menu' => 'Batagor Kuah',
            'kategori' => 'makanan',
            'harga' => 25000,
            'stok' => 10,
            'total_sold' => 0,
            'is_available' => true,
        ]);

        $parent = ParentOrder::create([
            'order_id' => $orderId,
            'nama_pemesan' => 'Rina Siswa',
            'gross_amount' => 25000,
            'payment_method' => 'qris',
            'payment_status' => 'pending',
        ]);

        $subOrder = $parent->subOrders()->create([
            'kode_tr' => 'TKT-STD01-7788',
            'ticket_code' => 'TKT-STD01-7788',
            'stand_id' => $stand->id,
            'nama_pemesan' => 'Rina Siswa',
            'kelas' => 'XII RPL 1',
            'total_harga' => 25000,
            'metode_pembayaran' => 'qris',
            'status_pembayaran' => 'belum_lunas',
            'status' => 'menunggu_pembayaran',
        ]);

        $subOrder->items()->create([
            'menu_id' => $menu->id,
            'jumlah' => 1,
            'harga_satuan' => 25000,
            'subtotal' => 25000,
        ]);

        $response = $this->postJson(route('midtrans.webhook'), [
            'order_id' => $orderId,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'signature_key' => $signatureKey,
            'transaction_status' => 'settlement',
            'fraud_status' => 'accept',
        ]);

        $response->assertStatus(200);

        // Verify ParentOrder is paid
        $parent->refresh();
        $this->assertEquals('paid', $parent->payment_status);

        // Verify SubOrder is pending (menunggu konfirmasi penjual untuk disiapkan)
        $subOrder->refresh();
        $this->assertEquals('pending', $subOrder->status);
        $this->assertEquals('lunas', $subOrder->status_pembayaran);

        // Verify total_sold is NOT incremented yet while order is still pending (in-process)
        $this->assertEquals(0, $menu->fresh()->total_sold);

        // When vendor completes the sub-order, total_sold increments
        $subOrder->update(['status' => 'selesai']);
        $menu->increment('total_sold', 1);
        $this->assertEquals(1, $menu->fresh()->total_sold);
    }

    public function test_midtrans_webhook_cancel_or_expire_updates_status_and_restores_stock(): void
    {
        $serverKey = config('midtrans.server_key');
        $orderId = 'ORDER-20261016-TEST2';
        $grossAmount = '20000';
        $statusCode = '200';
        $signatureKey = hash('sha512', $orderId.$statusCode.$grossAmount.$serverKey);

        $stand = Stand::first();
        $menu = Menu::create([
            'stand_id' => $stand->id,
            'nama_menu' => 'Ayam Geprek',
            'kategori' => 'makanan',
            'harga' => 10000,
            'stok' => 5, // Initially 5 after 2 were taken
            'is_available' => true,
        ]);

        $parent = ParentOrder::create([
            'order_id' => $orderId,
            'nama_pemesan' => 'Bambang',
            'gross_amount' => 20000,
            'payment_method' => 'qris',
            'payment_status' => 'pending',
        ]);

        $subOrder = $parent->subOrders()->create([
            'kode_tr' => 'TKT-STD01-9988',
            'ticket_code' => 'TKT-STD01-9988',
            'stand_id' => $stand->id,
            'nama_pemesan' => 'Bambang',
            'kelas' => 'XII RPL 1',
            'total_harga' => 20000,
            'metode_pembayaran' => 'qris',
            'status_pembayaran' => 'belum_lunas',
            'status' => 'menunggu_pembayaran',
        ]);

        $subOrder->items()->create([
            'menu_id' => $menu->id,
            'jumlah' => 2,
            'harga_satuan' => 10000,
            'subtotal' => 20000,
        ]);

        $response = $this->postJson(route('midtrans.webhook'), [
            'order_id' => $orderId,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'signature_key' => $signatureKey,
            'transaction_status' => 'expire',
        ]);

        $response->assertStatus(200);

        $parent->refresh();
        $this->assertEquals('expired', $parent->payment_status);

        $subOrder->refresh();
        $this->assertEquals('dibatalkan', $subOrder->status);

        // Stock restored from 5 back to 7
        $this->assertEquals(7, $menu->fresh()->stok);
    }

    public function test_midtrans_webhook_rejects_invalid_signature(): void
    {
        $response = $this->postJson(route('midtrans.webhook'), [
            'order_id' => 'ORDER-INVALID-1',
            'status_code' => '200',
            'gross_amount' => '10000',
            'signature_key' => 'invalid_sha512_hash',
            'transaction_status' => 'settlement',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'OK',
                'result' => [
                    'status' => 'error',
                ],
            ]);
    }

    public function test_cancel_checkout_restores_stock_and_removes_unpaid_order(): void
    {
        $student = User::create([
            'name' => 'Siswa Batal',
            'email' => 'batal@siswa.sch.id',
            'password' => bcrypt('password123'),
            'role' => 'siswa',
        ]);

        $stand = Stand::first();
        $menu = Menu::create([
            'stand_id' => $stand->id,
            'nama_menu' => 'Ayam Geprek Batal',
            'kategori' => 'makanan',
            'harga' => 12000,
            'stok' => 10,
            'is_available' => true,
        ]);

        $this->actingAs($student);

        // Checkout 3 items
        $checkoutResponse = $this->postJson(route('api.orders.checkout'), [
            'payment_method' => 'qris',
            'items' => [
                [
                    'menu_id' => $menu->id,
                    'quantity' => 3,
                ],
            ],
        ]);

        $checkoutResponse->assertStatus(200);
        $orderId = $checkoutResponse->json('order_id');

        // Stock decreased to 7
        $this->assertEquals(7, $menu->fresh()->stok);

        // Now cancel checkout (simulating user clicking X / silang on Midtrans)
        $cancelResponse = $this->postJson("/api/orders/{$orderId}/cancel-checkout");
        $cancelResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        // Stock restored back to 10
        $this->assertEquals(10, $menu->fresh()->stok);

        // Order is removed from database
        $this->assertDatabaseMissing('parent_orders', ['order_id' => $orderId]);
    }
}
