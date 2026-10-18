<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Stand;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class ReportController extends Controller
{
    /**
     * Display sales and revenue summary per stand.
     */
    public function index(Request $request): View
    {
        $data = $this->getReportData($request->query('periode', 'hari_ini'));

        return view('admin.reports.index', $data);
    }

    /**
     * Export report data as Excel (.xls or .csv).
     */
    public function exportExcel(Request $request): Response
    {
        $data = $this->getReportData($request->query('periode', 'hari_ini'));

        if ($request->query('format') === 'csv') {
            $filename = 'laporan-omset-kantinskanic-'.$data['periode'].'-'.Carbon::now()->format('Ymd-His').'.csv';

            return response()->streamDownload(function () use ($data): void {
                $out = fopen('php://output', 'w');
                if (! $out) {
                    return;
                }

                // UTF-8 BOM for Microsoft Excel compatibility
                fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));

                fputcsv($out, ['KANTIN SKANIC - REKAPITULASI LAPORAN OMSET & PENJUALAN']);
                fputcsv($out, ['Periode', $data['periodeLabel'].' ('.$data['periodeDetail'].')']);
                fputcsv($out, ['Waktu Ekspor', $data['generatedAt']->locale('id')->isoFormat('dddd, D MMMM Y - HH:mm').' WIB']);
                fputcsv($out, ['Diunduh Oleh', auth()->user()?->name ?? 'Super Administrator']);
                fputcsv($out, []);

                fputcsv($out, ['RINGKASAN TOTAL PENJUALAN']);
                fputcsv($out, ['Total Omset Sukses (Rp)', $data['overallTotalRevenue']]);
                fputcsv($out, ['Omset QRIS Online (Rp)', $data['overallQrisRevenue']]);
                fputcsv($out, ['Omset Tunai / COD Stand (Rp)', $data['overallCashRevenue']]);
                fputcsv($out, ['Total Pesanan Selesai', $data['overallCompletedOrders']]);
                fputcsv($out, ['Total Seluruh Pesanan Masuk', $data['overallTotalOrders']]);
                fputcsv($out, []);

                fputcsv($out, [
                    'Peringkat',
                    'Nama Stand',
                    'Nomor Stand',
                    'Pemilik Stand',
                    'Menu Aktif',
                    'Total Pesanan',
                    'Pesanan Selesai',
                    'Omset QRIS (Rp)',
                    'Omset Tunai (Rp)',
                    'Total Omset Selesai (Rp)',
                ]);

                foreach ($data['stands'] as $index => $stand) {
                    fputcsv($out, [
                        $index + 1,
                        $stand->nama_stand,
                        $stand->nomor_stand,
                        $stand->pemilik,
                        $stand->menus_count,
                        $stand->orders_count,
                        $stand->orders_selesai_count ?? 0,
                        $stand->omset_qris ?? 0,
                        $stand->omset_tunai ?? 0,
                        $stand->total_omset ?? 0,
                    ]);
                }

                fputcsv($out, [
                    'TOTAL KESELURUHAN',
                    '',
                    '',
                    '',
                    $data['stands']->sum('menus_count'),
                    $data['overallTotalOrders'],
                    $data['overallCompletedOrders'],
                    $data['overallQrisRevenue'],
                    $data['overallCashRevenue'],
                    $data['overallTotalRevenue'],
                ]);

                fclose($out);
            }, $filename, [
                'Content-Type' => 'text/csv; charset=UTF-8',
            ]);
        }

        $filename = 'laporan-omset-kantinskanic-'.$data['periode'].'-'.Carbon::now()->format('Ymd-His').'.xls';
        $content = view('admin.reports.excel', $data)->render();

        return response($content, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    /**
     * Display printable PDF report view.
     */
    public function exportPdf(Request $request): View
    {
        $data = $this->getReportData($request->query('periode', 'hari_ini'));

        return view('admin.reports.pdf', $data);
    }

    /**
     * Compile report dataset based on the selected period.
     *
     * @return array<string, mixed>
     */
    protected function getReportData(string $periode): array
    {
        if (! in_array($periode, ['hari_ini', 'minggu_ini', 'bulan_ini', 'semua'], true)) {
            $periode = 'hari_ini';
        }

        $periodeLabel = match ($periode) {
            'hari_ini' => 'Hari Ini',
            'minggu_ini' => 'Minggu Ini',
            'bulan_ini' => 'Bulan Ini',
            'semua' => 'Keseluruhan',
        };

        $periodeDetail = match ($periode) {
            'hari_ini' => Carbon::today('Asia/Jakarta')->locale('id')->isoFormat('dddd, D MMMM Y'),
            'minggu_ini' => Carbon::now('Asia/Jakarta')->startOfWeek()->locale('id')->isoFormat('D MMMM Y').' — '.Carbon::now('Asia/Jakarta')->endOfWeek()->locale('id')->isoFormat('D MMMM Y'),
            'bulan_ini' => Carbon::now('Asia/Jakarta')->locale('id')->isoFormat('MMMM Y'),
            'semua' => 'Semua Riwayat s/d '.Carbon::now('Asia/Jakarta')->locale('id')->isoFormat('D MMMM Y'),
        };

        $periodFilter = function ($q) use ($periode): void {
            match ($periode) {
                'hari_ini' => $q->whereDate('created_at', today()),
                'minggu_ini' => $q->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]),
                'bulan_ini' => $q->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year),
                'semua' => null,
                default => $q->whereDate('created_at', today()),
            };
        };

        $stands = Stand::withCount([
            'menus',
            'orders' => function ($query) use ($periodFilter): void {
                $periodFilter($query);
            },
            'orders as orders_selesai_count' => function ($query) use ($periodFilter): void {
                $query->where('status', 'selesai');
                $periodFilter($query);
            },
        ])
            ->withSum(['orders as total_omset' => function ($query) use ($periodFilter): void {
                $query->where('status', 'selesai');
                $periodFilter($query);
            }], 'total_harga')
            ->withSum(['orders as omset_qris' => function ($query) use ($periodFilter): void {
                $query->where('status', 'selesai')
                    ->where('metode_pembayaran', 'qris');
                $periodFilter($query);
            }], 'total_harga')
            ->withSum(['orders as omset_tunai' => function ($query) use ($periodFilter): void {
                $query->where('status', 'selesai')
                    ->where(function ($q): void {
                        $q->where('metode_pembayaran', 'tunai')
                            ->orWhereNull('metode_pembayaran');
                    });
                $periodFilter($query);
            }], 'total_harga')
            ->orderByDesc('total_omset')
            ->get();

        $overallQuery = Order::query();
        $periodFilter($overallQuery);

        $overallTotalOrders = (clone $overallQuery)->count();
        $overallCompletedOrders = (clone $overallQuery)->where('status', 'selesai')->count();
        $overallTotalRevenue = (int) ((clone $overallQuery)->where('status', 'selesai')->sum('total_harga') ?? 0);

        // Omset QRIS (Pesanan selesai non-tunai)
        $overallQrisRevenue = (int) ((clone $overallQuery)->where('status', 'selesai')
            ->where('metode_pembayaran', 'qris')
            ->sum('total_harga') ?? 0);

        // Omset COD / Tunai Fisik Stand (Pesanan selesai tunai)
        $overallCashRevenue = (int) ((clone $overallQuery)->where('status', 'selesai')
            ->where(function ($q): void {
                $q->where('metode_pembayaran', 'tunai')
                    ->orWhereNull('metode_pembayaran');
            })
            ->sum('total_harga') ?? 0);

        $generatedAt = Carbon::now('Asia/Jakarta');

        return compact(
            'stands',
            'overallTotalOrders',
            'overallCompletedOrders',
            'overallTotalRevenue',
            'overallQrisRevenue',
            'overallCashRevenue',
            'periode',
            'periodeLabel',
            'periodeDetail',
            'generatedAt'
        );
    }
}
