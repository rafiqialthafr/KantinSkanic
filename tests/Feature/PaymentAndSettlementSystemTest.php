<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Stand;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentAndSettlementSystemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_get_checkout_redirects_to_katalog_with_open_cart(): void
    {
        $student = User::where('role', 'siswa')->first() ?: User::create([
            'name' => 'Budi Siswa',
            'email' => 'budi_test@siswa.sch.id',
            'password' => bcrypt('password123'),
            'role' => 'siswa',
            'kelas' => 'X PPLG 1',
        ]);

        $this->actingAs($student);

        $response = $this->get('/checkout');
        $response->assertRedirect(route('katalog.index', ['open_cart' => 1]));
    }

    public function test_login_redirects_to_katalog_beranda(): void
    {
        $student = User::create([
            'name' => 'Siswa Test',
            'email' => 'siswa_login@sch.id',
            'password' => bcrypt('password123'),
            'role' => 'siswa',
            'kelas' => 'X PPLG 2',
        ]);

        $response = $this->withSession(['url.intended' => route('checkout.show')])
            ->post('/login', [
                'email' => 'siswa_login@sch.id',
                'password' => 'password123',
            ]);

        $response->assertRedirect(route('katalog.index'));
    }

    public function test_multi_stand_order_splits_orders_with_shared_batch_kode(): void
    {
        $student = User::create([
            'name' => 'Althafr Siswa',
            'email' => 'althafr@siswa.sch.id',
            'password' => bcrypt('password123'),
            'role' => 'siswa',
            'kelas' => 'X PPLG 5',
        ]);

        $this->actingAs($student);

        $stand1 = Stand::where('nama_stand', 'Kantin Bu Agus')->first();
        $stand2 = Stand::where('nama_stand', 'Kantin Pak Jaka')->first();

        $menu1 = $stand1->menus()->first();
        $menu2 = $stand2->menus()->first();

        $payload = [
            'nama_pemesan' => 'Althafr Siswa',
            'kelas' => 'X PPLG 5',
            'catatan' => 'Jangan pakai sambal',
            'metode_pembayaran' => 'qris',
            'items' => [
                ['menu_id' => $menu1->id, 'jumlah' => 2],
                ['menu_id' => $menu2->id, 'jumlah' => 1],
            ],
        ];

        $response = $this->post('/checkout', $payload);

        $orders = Order::where('nama_pemesan', 'Althafr Siswa')->get();
        $this->assertCount(2, $orders);

        // Verify shared batch_kode (e.g. ORDER-YYYYMMDD-XXXX)
        $batchKode = $orders->first()->batch_kode;
        $this->assertNotNull($batchKode);
        $this->assertTrue(str_starts_with($batchKode, 'ORDER-') || str_starts_with($batchKode, 'BATCH-'));
        $this->assertEquals($batchKode, $orders->last()->batch_kode);

        // Verify QRIS method
        foreach ($orders as $order) {
            $this->assertEquals('qris', $order->metode_pembayaran);
            $this->assertContains($order->status_pembayaran, ['belum_lunas', 'lunas']);
            $this->assertEquals('pending', $order->settlement_status);
        }

        // Check order status page shows batch tab
        $firstOrder = $orders->first();
        $statusPage = $this->get(route('order.status', $firstOrder->kode_tr));
        $statusPage->assertStatus(200);
        $statusPage->assertSee('Multi-Stand Split Order');
    }

    public function test_cash_order_is_marked_belum_lunas_and_can_be_marked_paid_by_vendor(): void
    {
        $student = User::create([
            'name' => 'Rina Siswi',
            'email' => 'rina@siswa.sch.id',
            'password' => bcrypt('password123'),
            'role' => 'siswa',
            'kelas' => 'X AKL 2',
        ]);

        $this->actingAs($student);

        $stand = Stand::where('nama_stand', 'Kantin Bu Agus')->first();
        $menu = $stand->menus()->first();

        $payload = [
            'nama_pemesan' => 'Rina Siswi',
            'kelas' => 'X AKL 2',
            'metode_pembayaran' => 'tunai',
            'items' => [
                ['menu_id' => $menu->id, 'jumlah' => 1],
            ],
        ];

        $this->post('/checkout', $payload);

        $order = Order::where('nama_pemesan', 'Rina Siswi')->first();
        $this->assertNotNull($order);
        $this->assertEquals('tunai', $order->metode_pembayaran);
        $this->assertEquals('belum_lunas', $order->status_pembayaran);

        // Check student receipt shows BAYAR DI TEMPAT
        $receipt = $this->get(route('order.status', $order->kode_tr));
        $receipt->assertStatus(200);
        $receipt->assertSee('BAYAR DI TEMPAT');

        // Vendor marks order as paid
        $vendor = User::where('email', 'buagus@kantin.com')->first();
        $this->actingAs($vendor);

        $vendorDashboard = $this->get(route('vendor.dashboard'));
        $vendorDashboard->assertStatus(200);
        $vendorDashboard->assertSee('BELUM LUNAS (COD)');
        $vendorDashboard->assertSee('Tandai Lunas Tunai');

        $markPaidResponse = $this->post(route('vendor.orders.markPaid', $order));
        $markPaidResponse->assertSessionHas('success');

        $order->refresh();
        $this->assertEquals('lunas', $order->status_pembayaran);
    }

    public function test_super_admin_can_view_settlements_and_process_payout(): void
    {
        $admin = User::where('role', 'admin')->first();
        $this->assertNotNull($admin);

        // Create completed QRIS orders for a stand
        $stand = Stand::first();
        $stand->update([
            'bank_name' => 'BCA',
            'nomor_rekening' => '5420192831',
            'atas_nama' => 'Ibu Agus',
        ]);

        $order = Order::create([
            'kode_tr' => 'PO-9911',
            'batch_kode' => 'BATCH-TEST-1',
            'stand_id' => $stand->id,
            'nama_pemesan' => 'Siswa QRIS',
            'kelas' => 'XI RPL',
            'total_harga' => 50000,
            'metode_pembayaran' => 'qris',
            'status_pembayaran' => 'lunas',
            'settlement_status' => 'pending',
            'status' => 'selesai',
        ]);

        $this->actingAs($admin);

        $response = $this->get(route('admin.settlements.index'));
        $response->assertStatus(200);
        $response->assertSee('Settlement QRIS Stand');
        $response->assertSee($stand->nama_stand);
        $response->assertSee('5420192831');
        $response->assertSee('Rp 50.000');

        // Execute payout
        $payoutResponse = $this->post(route('admin.settlements.payout'), [
            'stand_id' => $stand->id,
            'catatan' => 'Transfer via BCA ref #882193',
        ]);

        $payoutResponse->assertSessionHas('success');

        $order->refresh();
        $this->assertEquals('settled', $order->settlement_status);
        $this->assertNotNull($order->settlement_id);

        $settlement = $order->settlement;
        $this->assertEquals(50000, $settlement->total_dana);
        $this->assertEquals($admin->id, $settlement->ditransfer_oleh);
    }
}
