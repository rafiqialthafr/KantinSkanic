<?php

namespace Tests\Feature;

use App\Models\Menu;
use App\Models\Order;
use App\Models\Stand;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KantinOrderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_guest_can_view_katalog(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('KantinSkanic');
        $response->assertSee('Kantin Bu Agus');
        $response->assertSee('Kantin Pak Jaka');
        $response->assertSee('Kantin Bunda');
    }

    public function test_guest_can_filter_katalog_by_stand_and_category(): void
    {
        $stand = Stand::where('nama_stand', 'Kantin Bu Agus')->first();

        $response = $this->get('/?stand='.$stand->id.'&kategori=makanan');
        $response->assertStatus(200);
        $response->assertSee('Nasi Goreng Spesial Telur');
    }

    public function test_catalog_stand_indicator_only_counts_available_menus(): void
    {
        $stand = Stand::where('nama_stand', 'Kantin Pak Jaka')->first();
        $this->assertNotNull($stand);

        // Deactivate one menu
        $menuToDeactivate = $stand->menus()->first();
        $menuToDeactivate->update(['is_available' => false]);

        $availableCount = $stand->menus()->where('is_available', true)->count();
        $totalCount = $stand->menus()->count();
        $this->assertNotEquals($availableCount, $totalCount);

        $response = $this->get('/?stand='.$stand->id);
        $response->assertStatus(200);

        // Verify that the stand's menus_count in view matches available count, not total count
        $responseStands = $response->viewData('stands');
        $targetStand = $responseStands->firstWhere('id', $stand->id);
        $this->assertEquals($availableCount, $targetStand->menus_count);

        // Verify that the rendered menus count matches
        $renderedMenus = $response->viewData('menus');
        $this->assertEquals($availableCount, $renderedMenus->count());
    }

    public function test_guest_accessing_checkout_is_redirected_to_login(): void
    {
        $response = $this->get('/checkout');
        $response->assertRedirect('/login');
    }

    public function test_student_quick_register_creates_account_and_auto_logins(): void
    {
        $response = $this->post('/register', [
            'name' => 'Rizki Siswa',
            'kelas' => 'XI PPLG 1',
            'no_wa' => '081234567890',
            'email' => 'rizki@siswa.sch.id',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertAuthenticated();
        $user = User::where('email', 'rizki@siswa.sch.id')->first();
        $this->assertNotNull($user);
        $this->assertEquals('siswa', $user->role);
        $this->assertEquals('XI PPLG 1', session('siswa_kelas'));
    }

    public function test_authenticated_student_can_checkout_and_receive_unique_kode_tr(): void
    {
        $student = User::create([
            'name' => 'Ahmad Budi',
            'email' => 'budi@siswa.com',
            'password' => bcrypt('password123'),
            'role' => 'siswa',
        ]);

        $this->actingAs($student);

        $menu1 = Menu::where('nama_menu', 'Nasi Goreng Spesial Telur')->first();
        $menu2 = Menu::where('nama_menu', 'Es Teh Manis Jumbo')->first();

        $payload = [
            'nama_pemesan' => 'Ahmad Budi',
            'kelas' => 'XI PPLG 2',
            'catatan' => 'Pedas sedang ya',
            'items' => [
                ['menu_id' => $menu1->id, 'jumlah' => 2],
                ['menu_id' => $menu2->id, 'jumlah' => 1],
            ],
        ];

        $response = $this->post('/checkout', $payload);

        $order = Order::where('nama_pemesan', 'Ahmad Budi')->first();
        $this->assertNotNull($order);
        $this->assertStringStartsWith('PO-', $order->kode_tr);
        $this->assertEquals(2 * 15000 + 4000, $order->total_harga);
        $this->assertEquals('pending', $order->status);
        $this->assertCount(2, $order->items);

        $response->assertRedirect(route('order.status', $order->kode_tr));

        // Test status page
        $statusResponse = $this->get(route('order.status', $order->kode_tr));
        $statusResponse->assertStatus(200);
        $statusResponse->assertSee($order->kode_tr);
        $statusResponse->assertSee('Ahmad Budi');
        $statusResponse->assertSee('XI PPLG 2');
    }

    public function test_vendor_login_redirects_to_vendor_dashboard(): void
    {
        $loginResponse = $this->post('/login', [
            'email' => 'buagus@kantin.com',
            'password' => 'password',
        ]);

        $loginResponse->assertRedirect(route('vendor.dashboard'));
        $this->assertAuthenticated();

        $dashboardResponse = $this->get(route('vendor.dashboard'));
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('Kantin Bu Agus');
        $dashboardResponse->assertSee('Pesanan Aktif');
    }

    public function test_vendor_cannot_access_admin_dashboard(): void
    {
        $vendor = User::where('email', 'buagus@kantin.com')->first();
        $this->actingAs($vendor);

        $response = $this->get(route('admin.dashboard'));
        $response->assertStatus(403);
    }

    public function test_strict_multi_tenant_vendor_scoping(): void
    {
        $vendorBuAgus = User::where('email', 'buagus@kantin.com')->first();
        $standPakJaka = Stand::where('nama_stand', 'Kantin Pak Jaka')->first();

        // Create an order for Stand Pak Jaka
        $orderPakJaka = $standPakJaka->orders()->create([
            'kode_tr' => 'PO-7777',
            'nama_pemesan' => 'Siti',
            'kelas' => 'XII AKL 1',
            'total_harga' => 14000,
            'status' => 'pending',
        ]);

        // Bu Agus tries to update Pak Jaka's order status -> must be rejected (403)
        $this->actingAs($vendorBuAgus);
        $response = $this->post(route('vendor.orders.updateStatus', $orderPakJaka), [
            'status' => 'diproses',
        ]);

        $response->assertStatus(403);
        $this->assertEquals('pending', $orderPakJaka->fresh()->status);
    }

    public function test_vendor_can_update_own_order_status_and_verify_pickup(): void
    {
        $vendor = User::where('email', 'buagus@kantin.com')->first();
        $stand = $vendor->stand;

        $order = $stand->orders()->create([
            'kode_tr' => 'PO-9999',
            'nama_pemesan' => 'Rina',
            'kelas' => 'X TKJ 1',
            'total_harga' => 15000,
            'status' => 'pending',
        ]);

        $this->actingAs($vendor);

        // Update to diproses
        $resp1 = $this->post(route('vendor.orders.updateStatus', $order), [
            'status' => 'diproses',
        ]);
        $resp1->assertSessionHas('success');
        $this->assertEquals('diproses', $order->fresh()->status);

        // Update to siap_diambil
        $resp2 = $this->post(route('vendor.orders.updateStatus', $order), [
            'status' => 'siap_diambil',
        ]);
        $this->assertEquals('siap_diambil', $order->fresh()->status);

        // Verify pickup by code PO-9999
        $resp3 = $this->post(route('vendor.orders.verify'), [
            'kode_tr' => 'PO-9999',
        ]);
        $resp3->assertSessionHas('success');
        $this->assertEquals('selesai', $order->fresh()->status);
    }

    public function test_vendor_dashboard_filters_orders_by_period(): void
    {
        $vendor = User::where('email', 'buagus@kantin.com')->first();
        $this->actingAs($vendor);

        foreach (['hari_ini', 'minggu_ini', 'bulan_ini', 'semua'] as $periode) {
            $resp = $this->get(route('vendor.dashboard', ['periode' => $periode]));
            $resp->assertStatus(200);
            $resp->assertSee('Statistik');
            $resp->assertSee('Dashboard Pesanan');
        }
    }

    public function test_vendor_can_view_omset_page_for_all_periods(): void
    {
        $vendor = User::where('email', 'buagus@kantin.com')->first();
        $this->actingAs($vendor);

        foreach (['hari_ini', 'minggu_ini', 'bulan_ini', 'semua'] as $periode) {
            $resp = $this->get(route('vendor.omset', ['periode' => $periode]));
            $resp->assertStatus(200);
            $resp->assertSee('Laporan Omset');
            $resp->assertSee('Total Omset');
            $resp->assertSee('Menu Terlaris');
            $resp->assertSee('Status Pesanan');
            $resp->assertSee('Menunggu');
            $resp->assertSee('Siap Diambil');
        }
    }

    public function test_vendor_can_crud_menu_scoped_to_stand(): void
    {
        $vendor = User::where('email', 'buagus@kantin.com')->first();
        $this->actingAs($vendor);

        // Index
        $indexResp = $this->get(route('vendor.menus.index'));
        $indexResp->assertStatus(200);
        $indexResp->assertSee('Manajemen Menu Stand');

        // Store
        $storeResp = $this->post(route('vendor.menus.store'), [
            'nama_menu' => 'Ayam Geprek Sambal Korek',
            'kategori' => 'makanan',
            'harga' => 17000,
            'stok' => 20,
            'is_available' => 1,
        ]);
        $storeResp->assertRedirect(route('vendor.menus.index'));

        $menu = Menu::where('nama_menu', 'Ayam Geprek Sambal Korek')->first();
        $this->assertNotNull($menu);
        $this->assertEquals($vendor->stand_id, $menu->stand_id);
        $this->assertTrue($menu->is_available);

        // Toggle
        $this->post(route('vendor.menus.toggle', $menu));
        $this->assertFalse($menu->fresh()->is_available);

        // Update
        $this->put(route('vendor.menus.update', $menu), [
            'nama_menu' => 'Ayam Geprek Sambal Ijo',
            'kategori' => 'makanan',
            'harga' => 18000,
            'stok' => 25,
            'is_available' => 1,
        ]);
        $this->assertEquals('Ayam Geprek Sambal Ijo', $menu->fresh()->nama_menu);
        $this->assertEquals(18000, $menu->fresh()->harga);

        // Destroy
        $this->delete(route('vendor.menus.destroy', $menu));
        $this->assertNull(Menu::find($menu->id));
    }

    public function test_admin_login_redirects_to_admin_dashboard_and_can_create_stand(): void
    {
        $loginResponse = $this->post('/login', [
            'email' => 'admin@kantin.com',
            'password' => 'password',
        ]);

        $loginResponse->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticated();

        $admin = User::where('email', 'admin@kantin.com')->first();
        $this->actingAs($admin);

        $response = $this->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Dashboard');
        $response->assertSee('Selamat Datang!');
        $response->assertSee('Total Stand Kantin');

        // Admin stores a new stand + vendor atomically
        $storeResp = $this->post(route('admin.stands.store'), [
            'nama_stand' => 'Kantin Mang Ujang',
            'nomor_stand' => 'Stand 06',
            'pemilik' => 'Mang Ujang',
            'no_wa' => '089988776655',
            'deskripsi' => 'Es Kelapa Muda & Gorengan Renyah',
            'email' => 'ujang@kantin.com',
            'password' => 'password123',
        ]);

        $storeResp->assertRedirect(route('admin.dashboard'));

        $stand = Stand::where('nama_stand', 'Kantin Mang Ujang')->first();
        $this->assertNotNull($stand);

        $vendorUser = User::where('email', 'ujang@kantin.com')->first();
        $this->assertNotNull($vendorUser);
        $this->assertEquals('penjual', $vendorUser->role);
        $this->assertEquals($stand->id, $vendorUser->stand_id);
    }
}
