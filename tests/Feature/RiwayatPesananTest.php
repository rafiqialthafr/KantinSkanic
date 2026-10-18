<?php

namespace Tests\Feature;

use App\Models\Menu;
use App\Models\Order;
use App\Models\Stand;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RiwayatPesananTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_guest_can_access_riwayat_pesanan_page(): void
    {
        $response = $this->get('/riwayat-pesanan');

        $response->assertStatus(200);
        $response->assertSee('Riwayat Pesanan');
    }

    public function test_order_history_api_returns_orders_by_codes(): void
    {
        $stand = Stand::first();
        $menu = Menu::where('stand_id', $stand->id)->first();

        $order = Order::create([
            'kode_tr' => 'PO-7788',
            'stand_id' => $stand->id,
            'nama_pemesan' => 'Budi Santoso',
            'kelas' => 'XII RPL 1',
            'total_harga' => 15000,
            'status' => 'diproses',
        ]);

        $order->items()->create([
            'menu_id' => $menu->id,
            'jumlah' => 1,
            'harga_satuan' => 15000,
            'subtotal' => 15000,
        ]);

        $response = $this->postJson('/api/riwayat-pesanan', [
            'codes' => ['PO-7788'],
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'count' => 1,
        ]);
        $response->assertJsonFragment([
            'kode_tr' => 'PO-7788',
            'status' => 'diproses',
            'status_label' => 'Sedang Disiapkan',
        ]);
    }

    public function test_authenticated_user_orders_are_automatically_included(): void
    {
        $student = User::create([
            'name' => 'Siti Siswi',
            'email' => 'siti@siswa.com',
            'password' => bcrypt('password123'),
            'role' => 'siswa',
        ]);

        $stand = Stand::first();
        $menu = Menu::where('stand_id', $stand->id)->first();

        $order = Order::create([
            'kode_tr' => 'PO-9911',
            'stand_id' => $stand->id,
            'nama_pemesan' => 'Siti Siswi',
            'kelas' => 'X PPLG 2',
            'total_harga' => 12000,
            'status' => 'siap_diambil',
        ]);

        $order->items()->create([
            'menu_id' => $menu->id,
            'jumlah' => 1,
            'harga_satuan' => 12000,
            'subtotal' => 12000,
        ]);

        $this->actingAs($student);

        $response = $this->get('/riwayat-pesanan');
        $response->assertStatus(200);
        $response->assertSee('PO-9911');
        $response->assertSee('Siap Diambil');

        $apiResponse = $this->postJson('/api/riwayat-pesanan');
        $apiResponse->assertStatus(200);
        $apiResponse->assertJsonFragment([
            'kode_tr' => 'PO-9911',
            'status' => 'siap_diambil',
        ]);
    }

    public function test_orders_are_scoped_to_specific_student_account_even_if_names_are_identical(): void
    {
        $studentA = User::create([
            'name' => 'Rafi',
            'email' => 'rafi3@siswa.com',
            'password' => bcrypt('password123'),
            'role' => 'siswa',
            'kelas' => 'X PPLG 3',
        ]);

        $studentB = User::create([
            'name' => 'Rafi',
            'email' => 'rafi5@siswa.com',
            'password' => bcrypt('password123'),
            'role' => 'siswa',
            'kelas' => 'X PPLG 5',
        ]);

        $stand = Stand::first();
        $menu = Menu::where('stand_id', $stand->id)->first();

        // Order placed by Student A
        $orderA = Order::create([
            'kode_tr' => 'PO-4411',
            'user_id' => $studentA->id,
            'stand_id' => $stand->id,
            'nama_pemesan' => 'Rafi',
            'kelas' => 'X PPLG 3',
            'total_harga' => 3000,
            'status' => 'selesai',
        ]);
        $orderA->items()->create([
            'menu_id' => $menu->id,
            'jumlah' => 1,
            'harga_satuan' => 3000,
            'subtotal' => 3000,
        ]);

        // Student B logs in (has never ordered)
        $this->actingAs($studentB);

        $response = $this->get('/riwayat-pesanan');
        $response->assertStatus(200);
        $response->assertDontSee('PO-4411');

        $apiResponse = $this->postJson('/api/riwayat-pesanan');
        $apiResponse->assertStatus(200);
        $this->assertCount(0, $apiResponse->json('orders'));
    }

    public function test_belum_bayar_category_filters_unpaid_qris_orders(): void
    {
        $student = User::create([
            'name' => 'Doni Siswa',
            'email' => 'doni@siswa.com',
            'password' => bcrypt('password123'),
            'role' => 'siswa',
        ]);

        $stand = Stand::first();
        $menu = Menu::where('stand_id', $stand->id)->first();

        // 1. Unpaid QRIS order
        $unpaidQrisOrder = Order::create([
            'kode_tr' => 'SKNC-STD1-1111',
            'user_id' => $student->id,
            'stand_id' => $stand->id,
            'nama_pemesan' => 'Doni Siswa',
            'kelas' => 'XI RPL 1',
            'total_harga' => 10000,
            'metode_pembayaran' => 'qris',
            'status_pembayaran' => 'belum_lunas',
            'status' => 'menunggu_pembayaran',
        ]);
        $unpaidQrisOrder->items()->create([
            'menu_id' => $menu->id,
            'jumlah' => 1,
            'harga_satuan' => 10000,
            'subtotal' => 10000,
        ]);

        // 2. Paid QRIS order
        $paidQrisOrder = Order::create([
            'kode_tr' => 'SKNC-STD1-2222',
            'user_id' => $student->id,
            'stand_id' => $stand->id,
            'nama_pemesan' => 'Doni Siswa',
            'kelas' => 'XI RPL 1',
            'total_harga' => 15000,
            'metode_pembayaran' => 'qris',
            'status_pembayaran' => 'lunas',
            'status' => 'diproses',
        ]);
        $paidQrisOrder->items()->create([
            'menu_id' => $menu->id,
            'jumlah' => 1,
            'harga_satuan' => 15000,
            'subtotal' => 15000,
        ]);

        // 3. Cash / COD order
        $codOrder = Order::create([
            'kode_tr' => 'SKNC-STD1-3333',
            'user_id' => $student->id,
            'stand_id' => $stand->id,
            'nama_pemesan' => 'Doni Siswa',
            'kelas' => 'XI RPL 1',
            'total_harga' => 8000,
            'metode_pembayaran' => 'tunai',
            'status_pembayaran' => 'belum_lunas',
            'status' => 'pending',
        ]);
        $codOrder->items()->create([
            'menu_id' => $menu->id,
            'jumlah' => 1,
            'harga_satuan' => 8000,
            'subtotal' => 8000,
        ]);

        $this->actingAs($student);

        // When viewing riwayat pesanan, Belum Bayar tab is visible
        $responseAll = $this->get('/riwayat-pesanan');
        $responseAll->assertStatus(200);
        $responseAll->assertSee('Belum Bayar');
        $responseAll->assertSee('SKNC-STD1-1111');
        $responseAll->assertSee('SKNC-STD1-2222');
        $responseAll->assertSee('SKNC-STD1-3333');

        // When filtering specifically with ?status=belum_bayar
        $responseBelumBayar = $this->get('/riwayat-pesanan?status=belum_bayar');
        $responseBelumBayar->assertStatus(200);
        $responseBelumBayar->assertSee('SKNC-STD1-1111');
        $responseBelumBayar->assertDontSee('SKNC-STD1-2222');
        $responseBelumBayar->assertDontSee('SKNC-STD1-3333');
    }

    public function test_realtime_statuses_endpoint_returns_order_status_by_id_and_code(): void
    {
        $stand = Stand::first();
        $order = Order::create([
            'kode_tr' => 'SKNC-RT-1234',
            'ticket_code' => 'SKNC-RT-1234',
            'stand_id' => $stand->id,
            'nama_pemesan' => 'Realtime Test',
            'kelas' => 'XII RPL',
            'total_harga' => 10000,
            'metode_pembayaran' => 'tunai',
            'status' => 'pending',
            'status_pembayaran' => 'belum_lunas',
        ]);

        $response = $this->postJson(route('api.orders.realtime-statuses'), [
            'order_ids' => [$order->id],
            'order_codes' => ['SKNC-RT-1234'],
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
        $response->assertJsonFragment([
            'id' => $order->id,
            'kode_tr' => 'SKNC-RT-1234',
            'status' => 'pending',
            'is_lunas' => false,
            'stand_name' => $stand->nama_stand,
        ]);
    }

    public function test_selesai_tab_does_not_display_numeric_indicator_badge(): void
    {
        $stand = Stand::first();
        $student = User::create([
            'name' => 'Selesai Test User',
            'email' => 'selesai@user.com',
            'password' => bcrypt('password123'),
            'role' => 'siswa',
        ]);

        Order::create([
            'kode_tr' => 'SKNC-DONE-999',
            'user_id' => $student->id,
            'stand_id' => $stand->id,
            'nama_pemesan' => 'Selesai Test User',
            'kelas' => 'XII RPL',
            'total_harga' => 10000,
            'status' => 'selesai',
            'status_pembayaran' => 'lunas',
        ]);

        $this->actingAs($student);
        $response = $this->get('/riwayat-pesanan');
        $response->assertStatus(200);

        // Confirm Selesai tab exists but does NOT have badge counter
        $response->assertSee('data-tab-key="selesai"', false);
        // The tab contains 'Selesai' label, but shouldn't contain a counter badge for selesai
        $content = $response->getContent();
        preg_match('/data-tab-key="selesai"[^>]*>(.*?)<\/a>/s', $content, $matches);
        $this->assertNotEmpty($matches);
        $selesaiTabHtml = $matches[1];
        $this->assertStringNotContainsString('rounded-full text-[10px]', $selesaiTabHtml);
    }
}
