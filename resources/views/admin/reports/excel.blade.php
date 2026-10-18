<!DOCTYPE html>
<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Laporan Omset KantinSkanic</title>
    <style>
        body, table {
            font-family: Calibri, 'Segoe UI', Arial, sans-serif;
            font-size: 11pt;
            color: #1e293b;
        }
        .title {
            font-size: 16pt;
            font-weight: bold;
            color: #0f172a;
            text-align: left;
        }
        .subtitle {
            font-size: 11pt;
            color: #64748b;
        }
        .meta-label {
            font-weight: bold;
            color: #475569;
            background-color: #f1f5f9;
        }
        .kpi-title {
            font-weight: bold;
            font-size: 10pt;
            color: #475569;
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            text-align: center;
        }
        .kpi-value {
            font-weight: bold;
            font-size: 14pt;
            border: 1px solid #cbd5e1;
            text-align: center;
        }
        .th-header {
            background-color: #0f172a;
            color: #ffffff;
            font-weight: bold;
            font-size: 10.5pt;
            text-align: center;
            vertical-align: middle;
            border: 1px solid #0f172a;
            padding: 8px 6px;
        }
        .td-cell {
            border: 1px solid #cbd5e1;
            padding: 6px;
            vertical-align: middle;
        }
        .td-center {
            text-align: center;
        }
        .td-number {
            text-align: right;
            mso-number-format: "\#\,\#\#0";
        }
        .td-currency {
            text-align: right;
            mso-number-format: "\\Rp\\ *\\ #,##0";
        }
        .row-alt {
            background-color: #f8fafc;
        }
        .total-row {
            background-color: #dcfce7;
            font-weight: bold;
            border-top: 2px solid #16a34a;
            border-bottom: 3px double #16a34a;
        }
    </style>
</head>
<body>
    <table>
        <!-- TITLE BANNER -->
        <tr>
            <td colspan="10" class="title">KANTIN SKANIC &mdash; SMKN 1 CIOMAS</td>
        </tr>
        <tr>
            <td colspan="10" class="subtitle">Laporan Rekapitulasi Omset &amp; Penjualan Stand Kantin</td>
        </tr>
        <tr>
            <td colspan="10"></td>
        </tr>

        <!-- METADATA -->
        <tr>
            <td class="meta-label" colspan="2">Periode Laporan:</td>
            <td colspan="8">{{ $periodeLabel }} ({{ $periodeDetail }})</td>
        </tr>
        <tr>
            <td class="meta-label" colspan="2">Waktu Ekspor:</td>
            <td colspan="8">{{ $generatedAt->locale('id')->isoFormat('dddd, D MMMM Y - HH:mm') }} WIB</td>
        </tr>
        <tr>
            <td class="meta-label" colspan="2">Diunduh Oleh:</td>
            <td colspan="8">{{ auth()->user()?->name ?? 'Super Administrator' }} (Super Admin)</td>
        </tr>
        <tr>
            <td colspan="10"></td>
        </tr>

        <!-- KPI SUMMARY TABLE -->
        <tr>
            <td colspan="2" class="kpi-title">TOTAL OMSET SUKSES</td>
            <td colspan="2" class="kpi-title">OMSET QRIS (NON-TUNAI)</td>
            <td colspan="2" class="kpi-title">OMSET TUNAI (COD STAND)</td>
            <td colspan="2" class="kpi-title">PESANAN SELESAI</td>
            <td colspan="2" class="kpi-title">TOTAL PESANAN</td>
        </tr>
        <tr>
            <td colspan="2" class="kpi-value" style="color: #059669;">Rp {{ number_format($overallTotalRevenue, 0, ',', '.') }}</td>
            <td colspan="2" class="kpi-value" style="color: #ea580c;">Rp {{ number_format($overallQrisRevenue, 0, ',', '.') }}</td>
            <td colspan="2" class="kpi-value" style="color: #d97706;">Rp {{ number_format($overallCashRevenue, 0, ',', '.') }}</td>
            <td colspan="2" class="kpi-value" style="color: #0284c7;">{{ number_format($overallCompletedOrders, 0, ',', '.') }}</td>
            <td colspan="2" class="kpi-value" style="color: #475569;">{{ number_format($overallTotalOrders, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td colspan="10"></td>
        </tr>

        <!-- STAND PERFORMANCE TABLE HEADERS -->
        <tr>
            <th class="th-header" style="width: 50px;">No</th>
            <th class="th-header" style="width: 180px;">Nama Stand</th>
            <th class="th-header" style="width: 90px;">No. Stand</th>
            <th class="th-header" style="width: 140px;">Pemilik</th>
            <th class="th-header" style="width: 90px;">Menu Aktif</th>
            <th class="th-header" style="width: 100px;">Total Pesanan</th>
            <th class="th-header" style="width: 110px;">Pesanan Selesai</th>
            <th class="th-header" style="width: 140px;">Omset QRIS (Rp)</th>
            <th class="th-header" style="width: 140px;">Omset Tunai (Rp)</th>
            <th class="th-header" style="width: 160px;">Total Omset Selesai (Rp)</th>
        </tr>

        <!-- STAND DATA ROWS -->
        @forelse($stands as $index => $stand)
        <tr class="{{ $index % 2 === 1 ? 'row-alt' : '' }}">
            <td class="td-cell td-center">{{ $index + 1 }}</td>
            <td class="td-cell"><strong>{{ $stand->nama_stand }}</strong></td>
            <td class="td-cell td-center">{{ $stand->nomor_stand }}</td>
            <td class="td-cell">{{ $stand->pemilik }}</td>
            <td class="td-cell td-number">{{ $stand->menus_count }}</td>
            <td class="td-cell td-number">{{ $stand->orders_count }}</td>
            <td class="td-cell td-number">{{ $stand->orders_selesai_count ?? 0 }}</td>
            <td class="td-cell td-number">{{ $stand->omset_qris ?? 0 }}</td>
            <td class="td-cell td-number">{{ $stand->omset_tunai ?? 0 }}</td>
            <td class="td-cell td-number" style="font-weight: bold; color: #047857;">{{ $stand->total_omset ?? 0 }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="10" class="td-cell td-center" style="color: #94a3b8; padding: 20px;">Belum ada data stand terdaftar.</td>
        </tr>
        @endforelse

        <!-- TOTAL FOOTER ROW -->
        <tr class="total-row">
            <td class="td-cell td-center" colspan="4">TOTAL KESELURUHAN</td>
            <td class="td-cell td-number">{{ $stands->sum('menus_count') }}</td>
            <td class="td-cell td-number">{{ $overallTotalOrders }}</td>
            <td class="td-cell td-number">{{ $overallCompletedOrders }}</td>
            <td class="td-cell td-number">{{ $overallQrisRevenue }}</td>
            <td class="td-cell td-number">{{ $overallCashRevenue }}</td>
            <td class="td-cell td-number" style="color: #047857; font-size: 11pt;">{{ $overallTotalRevenue }}</td>
        </tr>
    </table>
</body>
</html>
