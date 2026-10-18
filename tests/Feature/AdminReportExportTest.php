<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminReportExportTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $vendor;

    protected User $siswa;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        $this->admin = User::where('role', 'admin')->first();
        $this->vendor = User::where('role', 'penjual')->first();
        $this->siswa = User::create([
            'name' => 'Siswa Pengunjung',
            'email' => 'siswa@kantin.com',
            'password' => bcrypt('password'),
            'role' => 'siswa',
        ]);
    }

    public function test_admin_reports_index_displays_export_buttons_and_data(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.reports.index', ['periode' => 'hari_ini']));
        $response->assertStatus(200);
        $response->assertSee('Ekspor Excel');
        $response->assertSee('Ekspor PDF');
        $response->assertSee(route('admin.reports.export.excel', ['periode' => 'hari_ini']));
        $response->assertSee(route('admin.reports.export.pdf', ['periode' => 'hari_ini']));
        $response->assertSee('Performa Penjualan Tiap Stand Kantin');
    }

    public function test_admin_can_export_reports_to_excel_for_all_periods(): void
    {
        $this->actingAs($this->admin);

        foreach (['hari_ini', 'minggu_ini', 'bulan_ini', 'semua'] as $periode) {
            $response = $this->get(route('admin.reports.export.excel', ['periode' => $periode]));
            $response->assertStatus(200);
            $response->assertHeader('content-type', 'application/vnd.ms-excel; charset=utf-8');
            $this->assertStringContainsString('attachment; filename="laporan-omset-kantinskanic-'.$periode, (string) $response->headers->get('content-disposition'));
            $response->assertSee('KANTIN SKANIC');
            $response->assertSee('TOTAL KESELURUHAN');
        }
    }

    public function test_admin_can_export_reports_to_csv(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.reports.export.excel', ['periode' => 'bulan_ini', 'format' => 'csv']));
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('attachment; filename=laporan-omset-kantinskanic-bulan_ini', (string) $response->headers->get('content-disposition'));
    }

    public function test_admin_can_view_pdf_report_for_all_periods(): void
    {
        $this->actingAs($this->admin);

        foreach (['hari_ini', 'minggu_ini', 'bulan_ini', 'semua'] as $periode) {
            $response = $this->get(route('admin.reports.export.pdf', ['periode' => $periode]));
            $response->assertStatus(200);
            $response->assertSee('LAPORAN REKAPITULASI OMSET & PENJUALAN STAND');
            $response->assertSee('window.print()');
            $response->assertSee('TOTAL KESELURUHAN');
            $response->assertSee('SMKN 1 CIOMAS');
            $response->assertDontSee('Cirebon');
            $response->assertDontSee('Penanggung Jawab / Super Admin');
        }
    }

    public function test_non_admin_cannot_access_export_routes(): void
    {
        // Unauthenticated guest
        $guestExcel = $this->get(route('admin.reports.export.excel'));
        $guestExcel->assertRedirect(route('login'));

        $guestPdf = $this->get(route('admin.reports.export.pdf'));
        $guestPdf->assertRedirect(route('login'));

        // Vendor / Penjual
        $this->actingAs($this->vendor);
        $vendorExcel = $this->get(route('admin.reports.export.excel'));
        $vendorExcel->assertStatus(403);

        $vendorPdf = $this->get(route('admin.reports.export.pdf'));
        $vendorPdf->assertStatus(403);

        // Siswa
        $this->actingAs($this->siswa);
        $siswaExcel = $this->get(route('admin.reports.export.excel'));
        $siswaExcel->assertStatus(403);

        $siswaPdf = $this->get(route('admin.reports.export.pdf'));
        $siswaPdf->assertStatus(403);
    }
}
