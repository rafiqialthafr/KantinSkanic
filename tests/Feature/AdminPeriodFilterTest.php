<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Stand;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPeriodFilterTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        $this->admin = User::where('role', 'admin')->first();
    }

    public function test_admin_can_access_dashboard_with_all_periods(): void
    {
        $this->actingAs($this->admin);

        foreach (['hari_ini', 'minggu_ini', 'bulan_ini', 'semua'] as $periode) {
            $response = $this->get(route('admin.dashboard', ['periode' => $periode]));
            $response->assertStatus(200);
            $response->assertSee('Selamat Datang!');
            $response->assertSee('Total Stand Kantin');
        }
    }

    public function test_admin_can_access_stands_management_with_all_periods(): void
    {
        $this->actingAs($this->admin);

        foreach (['hari_ini', 'minggu_ini', 'bulan_ini', 'semua'] as $periode) {
            $response = $this->get(route('admin.stands.index', ['periode' => $periode]));
            $response->assertStatus(200);
            $response->assertSee('Kelola Stand Kantin');
            $response->assertSee('Total Pesanan Masuk');
        }
    }

    public function test_admin_can_access_orders_index_with_all_periods(): void
    {
        $this->actingAs($this->admin);

        foreach (['hari_ini', 'minggu_ini', 'bulan_ini', 'semua'] as $periode) {
            $response = $this->get(route('admin.orders.index', ['periode' => $periode]));
            $response->assertStatus(200);
            $response->assertSee('Riwayat Transaksi');
            $response->assertSee('Total Pesanan');
            $response->assertSee('Pesanan Selesai');
        }
    }

    public function test_admin_can_access_reports_index_with_all_periods(): void
    {
        $this->actingAs($this->admin);

        foreach (['hari_ini', 'minggu_ini', 'bulan_ini', 'semua'] as $periode) {
            $response = $this->get(route('admin.reports.index', ['periode' => $periode]));
            $response->assertStatus(200);
            $response->assertSee('Laporan & Omset');
            $response->assertSee('Total Omset Sukses');
        }
    }

    public function test_admin_can_access_system_index_with_period(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.system.index'));
        $response->assertStatus(200);
        $response->assertSee('Status & Info Sistem');
        $response->assertSee('Statistik Pengguna Terdaftar');
    }

    public function test_stands_order_count_respects_period_filter(): void
    {
        $this->actingAs($this->admin);

        $stand = Stand::first();

        // Create an order from 2 days ago
        $pastOrder = Order::create([
            'kode_tr' => 'TR-TEST-PAST',
            'stand_id' => $stand->id,
            'nama_pemesan' => 'Tester',
            'kelas' => 'XII',
            'total_harga' => 15000,
            'status' => 'selesai',
            'catatan' => null,
        ]);
        $pastOrder->created_at = now()->subDays(2);
        $pastOrder->saveQuietly();

        // 1. When period is 'hari_ini', stand orders_count should be 0
        $responseToday = $this->get(route('admin.stands.index', ['periode' => 'hari_ini']));
        $responseToday->assertStatus(200);
        $standsToday = $responseToday->viewData('stands');
        $this->assertEquals(0, $standsToday->firstWhere('id', $stand->id)->orders_count);

        // 2. When period is 'semua', stand orders_count should include the past order
        $responseAll = $this->get(route('admin.stands.index', ['periode' => 'semua']));
        $responseAll->assertStatus(200);
        $standsAll = $responseAll->viewData('stands');
        $this->assertEquals(1, $standsAll->firstWhere('id', $stand->id)->orders_count);

        // 3. In dashboard, orders_count should also be 0 for today
        $responseDashboardToday = $this->get(route('admin.dashboard', ['periode' => 'hari_ini']));
        $responseDashboardToday->assertStatus(200);
        $standsDashToday = $responseDashboardToday->viewData('stands');
        $this->assertEquals(0, $standsDashToday->firstWhere('id', $stand->id)->orders_count);

        // 4. In dashboard recent orders, past order must not appear when filtered to 'hari_ini'
        $recentOrdersToday = $responseDashboardToday->viewData('recentOrders');
        $this->assertCount(0, $recentOrdersToday);
    }
}
