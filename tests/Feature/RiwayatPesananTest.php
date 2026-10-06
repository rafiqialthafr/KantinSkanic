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
        $response->assertSee('SIAP DIAMBIL');

        $apiResponse = $this->postJson('/api/riwayat-pesanan');
        $apiResponse->assertStatus(200);
        $apiResponse->assertJsonFragment([
            'kode_tr' => 'PO-9911',
            'status' => 'siap_diambil',
        ]);
    }
}
