<?php

namespace Tests\Feature;

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
        $response->assertSee('Jadwal Operasional Pre-Order Kantin');
    }
}
