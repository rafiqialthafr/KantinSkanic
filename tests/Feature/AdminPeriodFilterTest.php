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
            $response->assertSee('Grafik Omset Penjualan');
            $response->assertSee('Total Omset');
            $response->assertSee('Puncak Omset');
            $this->assertArrayHasKey('omsetChartData', $response->original->getData());
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
            $response->assertSee('Omset QRIS');
            $response->assertSee('Omset Tunai (COD)');
            $response->assertDontSee('Rata-Rata / Transaksi');
        }
    }

    public function test_admin_can_access_settlements_index_with_all_periods(): void
    {
        $this->actingAs($this->admin);

        foreach (['hari_ini', 'minggu_ini', 'bulan_ini', 'semua'] as $periode) {
            $response = $this->get(route('admin.settlements.index', ['periode' => $periode]));
            $response->assertStatus(200);
            $response->assertSee('Settlement QRIS Stand');
            $response->assertSee('Menunggu Ditransfer');
            $response->assertSee('Total QRIS Masuk');
            $response->assertSee('Sudah Ditransfer');
            $response->assertDontSee('Total Tunai (COD)');
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

    public function test_admin_and_vendor_can_filter_by_custom_date_range(): void
    {
        $this->actingAs($this->admin);

        $stand = Stand::first();

        // Order 1: 5 days ago
        $order5DaysAgo = Order::create([
            'kode_tr' => 'TR-CUSTOM-5D',
            'stand_id' => $stand->id,
            'nama_pemesan' => 'Siswa 5 Hari Lalu',
            'kelas' => 'X PPLG 1',
            'total_harga' => 20000,
            'status' => 'selesai',
            'metode_pembayaran' => 'tunai',
            'status_pembayaran' => 'lunas',
        ]);
        $order5DaysAgo->created_at = now()->subDays(5);
        $order5DaysAgo->saveQuietly();

        // Order 2: 10 days ago
        $order10DaysAgo = Order::create([
            'kode_tr' => 'TR-CUSTOM-10D',
            'stand_id' => $stand->id,
            'nama_pemesan' => 'Siswa 10 Hari Lalu',
            'kelas' => 'X PPLG 2',
            'total_harga' => 35000,
            'status' => 'selesai',
            'metode_pembayaran' => 'qris',
            'status_pembayaran' => 'lunas',
        ]);
        $order10DaysAgo->created_at = now()->subDays(10);
        $order10DaysAgo->saveQuietly();

        // Filter range: 6 days ago to 4 days ago (should only capture Order 1)
        $start = now()->subDays(6)->format('Y-m-d');
        $end = now()->subDays(4)->format('Y-m-d');

        // 1. Admin Dashboard with custom dates
        $respDash = $this->get(route('admin.dashboard', [
            'periode' => 'kustom',
            'start_date' => $start,
            'end_date' => $end,
        ]));
        $respDash->assertStatus(200);
        $this->assertEquals(1, $respDash->viewData('totalOrders'));
        $this->assertEquals(20000, $respDash->viewData('totalRevenue'));

        // 2. Admin Orders with custom dates
        $respOrders = $this->get(route('admin.orders.index', [
            'start_date' => $start,
            'end_date' => $end,
        ]));
        $respOrders->assertStatus(200);
        $this->assertEquals(1, $respOrders->viewData('totalOrdersCount'));
        $this->assertEquals(20000, $respOrders->viewData('totalRevenue'));

        // 3. Admin Reports with custom dates
        $respReports = $this->get(route('admin.reports.index', [
            'start_date' => $start,
            'end_date' => $end,
        ]));
        $respReports->assertStatus(200);
        $this->assertEquals(20000, $respReports->viewData('overallTotalRevenue'));

        // 4. Vendor dashboard & omset with custom dates
        $vendorUser = $stand->user;
        $this->actingAs($vendorUser);

        $respVendorDash = $this->get(route('vendor.dashboard', [
            'start_date' => $start,
            'end_date' => $end,
        ]));
        $respVendorDash->assertStatus(200);
        $this->assertEquals(1, $respVendorDash->viewData('totalOrdersToday'));
        $this->assertEquals(20000, $respVendorDash->viewData('revenueToday'));

        $respVendorOmset = $this->get(route('vendor.omset', [
            'periode' => 'kustom',
            'start_date' => $start,
            'end_date' => $end,
        ]));
        $respVendorOmset->assertStatus(200);
        $this->assertEquals(20000, $respVendorOmset->viewData('totalOmset'));
        $this->assertEquals(1, $respVendorOmset->viewData('totalOrders'));
    }

    public function test_admin_dashboard_chart_includes_stand_comparison_data(): void
    {
        $this->actingAs($this->admin);

        $stands = Stand::take(2)->get();
        $stand1 = $stands[0];
        $stand2 = $stands[1];

        // Create an order for stand 1 today
        Order::create([
            'kode_tr' => 'TR-STAND-COMP-1',
            'stand_id' => $stand1->id,
            'nama_pemesan' => 'Siswa Stand 1',
            'kelas' => 'XII RPL',
            'total_harga' => 50000,
            'metode_pembayaran' => 'qris',
            'status' => 'selesai',
            'catatan' => null,
        ]);

        $response = $this->get(route('admin.dashboard', ['periode' => 'hari_ini']));
        $response->assertStatus(200);
        $response->assertSee('Per Stand');

        $chartData = $response->viewData('omsetChartData');
        $this->assertArrayHasKey('stands', $chartData);
        $this->assertArrayHasKey('labels', $chartData['stands']);
        $this->assertArrayHasKey('revenue', $chartData['stands']);
        $this->assertArrayHasKey('orders_count', $chartData['stands']);
        $this->assertArrayHasKey('shares', $chartData['stands']);

        $stand1Index = array_search($stand1->nama_stand, $chartData['stands']['labels'], true);
        $this->assertNotFalse($stand1Index);
        $this->assertEquals(50000, $chartData['stands']['revenue'][$stand1Index]);
        $this->assertEquals(1, $chartData['stands']['orders_count'][$stand1Index]);
    }
}
