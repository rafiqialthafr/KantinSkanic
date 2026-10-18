<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Omset {{ $periodeLabel }} - KantinSkanic</title>
    <link rel="icon" type="image/png" href="{{ asset('img/kanic-logo.png') }}?v=2">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            color: #0f172a;
            background-color: #f1f5f9;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* SCREEN TOOLBAR */
        .toolbar {
            position: sticky;
            top: 0;
            z-index: 50;
            background: #0f172a;
            color: #fff;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        .toolbar-info {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
        }
        .toolbar-badge {
            background: rgba(249, 115, 22, 0.2);
            color: #fb923c;
            border: 1px solid rgba(249, 115, 22, 0.4);
            padding: 3px 8px;
            border-radius: 6px;
            font-weight: 700;
            font-size: 11px;
            text-transform: uppercase;
        }
        .toolbar-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.15s ease-in-out;
            border: none;
        }
        .btn-primary {
            background: #f97316;
            color: #ffffff;
        }
        .btn-primary:hover {
            background: #ea580c;
        }
        .btn-secondary {
            background: #334155;
            color: #e2e8f0;
        }
        .btn-secondary:hover {
            background: #475569;
            color: #ffffff;
        }

        /* PAPER SIMULATION FOR SCREEN */
        .page-container {
            max-width: 210mm;
            margin: 24px auto;
            background: #ffffff;
            padding: 18mm 16mm;
            border-radius: 4px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        }

        /* KOP SURAT HEADER */
        .kop-header {
            display: flex;
            align-items: center;
            gap: 16px;
            padding-bottom: 14px;
        }
        .kop-logo {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 12px;
            flex-shrink: 0;
        }
        .kop-text {
            flex: 1;
            text-align: center;
        }
        .kop-title {
            font-size: 16px;
            font-weight: 800;
            letter-spacing: 0.5px;
            color: #0f172a;
            text-transform: uppercase;
        }
        .kop-school {
            font-size: 18px;
            font-weight: 900;
            letter-spacing: 0.5px;
            color: #ea580c;
            text-transform: uppercase;
            margin-top: 1px;
        }
        .kop-divider {
            border: none;
            border-top: 2.5px solid #0f172a;
            border-bottom: 1px solid #0f172a;
            height: 4px;
            margin-bottom: 18px;
        }

        /* REPORT TITLE BANNER */
        .report-title-section {
            text-align: center;
            margin-bottom: 18px;
        }
        .report-title {
            font-size: 15px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: 0.2px;
            text-transform: uppercase;
        }
        .report-subtitle {
            font-size: 12px;
            color: #475569;
            font-weight: 600;
            margin-top: 3px;
        }

        /* METADATA BOX */
        .meta-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 10px 14px;
            margin-bottom: 18px;
            font-size: 11px;
        }
        .meta-item-label {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            color: #64748b;
            letter-spacing: 0.4px;
        }
        .meta-item-val {
            font-size: 11px;
            font-weight: 800;
            color: #1e293b;
            margin-top: 2px;
        }

        /* SUMMARY CARDS */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 20px;
        }
        .stat-card {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 10px 12px;
            background: #ffffff;
        }
        .stat-card.emerald {
            border-left: 3.5px solid #059669;
            background: #f0fdf4;
        }
        .stat-card.orange {
            border-left: 3.5px solid #ea580c;
            background: #fff7ed;
        }
        .stat-card.amber {
            border-left: 3.5px solid #d97706;
            background: #fffbeb;
        }
        .stat-card.sky {
            border-left: 3.5px solid #0284c7;
            background: #f0f9ff;
        }
        .stat-label {
            font-size: 9.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            color: #64748b;
        }
        .stat-number {
            font-size: 15px;
            font-weight: 900;
            margin-top: 4px;
            letter-spacing: -0.3px;
        }
        .stat-sub {
            font-size: 9px;
            color: #64748b;
            font-weight: 500;
            margin-top: 2px;
        }

        /* TABLE */
        .table-section-title {
            font-size: 12px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10.5px;
        }
        th {
            background-color: #0f172a;
            color: #ffffff;
            font-weight: 800;
            font-size: 9.5px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            padding: 7px 6px;
            border: 1px solid #0f172a;
            text-align: left;
        }
        td {
            padding: 6px 6px;
            border: 1px solid #cbd5e1;
            vertical-align: middle;
        }
        tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: 700; }
        .font-black { font-weight: 800; }
        .badge-stand {
            background: #fed7aa;
            color: #9a3412;
            padding: 1px 5px;
            border-radius: 4px;
            font-weight: 800;
            font-size: 9px;
            display: inline-block;
        }
        .badge-rank {
            width: 18px;
            height: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-size: 9.5px;
            font-weight: 800;
        }
        .rank-1 { background: #fef08a; color: #854d0e; }
        .rank-2 { background: #e2e8f0; color: #334155; }
        .rank-3 { background: #ffedd5; color: #9a3412; }
        .rank-other { background: #f1f5f9; color: #64748b; }

        .table-total-row {
            background-color: #dcfce7 !important;
            font-weight: 800;
            border-top: 2px solid #16a34a;
            border-bottom: 2px solid #16a34a;
        }

        /* PRINT STYLES */
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: #ffffff !important;
                color: #000000 !important;
            }
            .page-container {
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
                border-radius: 0 !important;
            }
            @page {
                size: A4 portrait;
                margin: 10mm 10mm 12mm 10mm;
            }
            tr {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>

    <!-- SCREEN-ONLY ACTION TOOLBAR -->
    <div class="toolbar no-print">
        <div class="toolbar-info">
            <span class="toolbar-badge">Pratinjau PDF</span>
            <span>Laporan Rekapitulasi Omset &bull; <strong>{{ $periodeLabel }}</strong></span>
            <span style="color: #94a3b8; font-size: 11px;">(Pilih 'Save as PDF' / 'Simpan sebagai PDF' saat jendela cetak terbuka)</span>
        </div>
        <div class="toolbar-actions">
            <a href="{{ route('admin.reports.index', ['periode' => $periode]) }}" class="btn btn-secondary">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
                Kembali ke Panel
            </a>
            <button type="button" onclick="window.print()" class="btn btn-primary">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24-1.076-.673-2.13-1.28-3.08A9 9 0 0 0 3 12c0 4.97 4.03 9 9 9s9-4.03 9-9a9 9 0 0 0-2.44-6.191M6.72 13.829a9.001 9.001 0 0 1 10.56 0m-10.56 0A9 9 0 0 0 12 15a9 9 0 0 0 5.28-1.171M12 3v9"/></svg>
                Cetak / Simpan PDF
            </button>
        </div>
    </div>

    <!-- PRINTABLE DOCUMENT -->
    <div class="page-container">

        <!-- KOP SURAT RESMI -->
        <div class="kop-header">
            <img src="{{ asset('img/kanic-logo.png') }}?v=2" alt="Logo KantinSkanic" class="kop-logo">
            <div class="kop-text">
                <div class="kop-title">SISTEM KANTIN DIGITAL TERPADU</div>
                <div class="kop-school">SMKN 1 CIOMAS</div>
            </div>
            <div style="width: 70px;" class="no-print"></div>
        </div>

        <hr class="kop-divider">

        <!-- TITLE -->
        <div class="report-title-section">
            <h1 class="report-title">LAPORAN REKAPITULASI OMSET &amp; PENJUALAN STAND</h1>
            <p class="report-subtitle">Periode: {{ $periodeLabel }} ({{ $periodeDetail }})</p>
        </div>

        <!-- METADATA GRID -->
        <div class="meta-grid">
            <div>
                <div class="meta-item-label">Periode Laporan</div>
                <div class="meta-item-val">{{ $periodeLabel }}</div>
            </div>
            <div>
                <div class="meta-item-label">Tanggal Cetak</div>
                <div class="meta-item-val">{{ $generatedAt->locale('id')->isoFormat('D MMMM Y, HH:mm') }} WIB</div>
            </div>
            <div>
                <div class="meta-item-label">Dicetak Oleh</div>
                <div class="meta-item-val">{{ auth()->user()?->name ?? 'Super Administrator' }}</div>
            </div>
            <div>
                <div class="meta-item-label">Status Verifikasi</div>
                <div class="meta-item-val" style="color: #059669;">Sistem Terverifikasi</div>
            </div>
        </div>

        <!-- SUMMARY STATS CARDS -->
        <div class="stats-grid">
            <div class="stat-card emerald">
                <div class="stat-label">Total Omset Sukses</div>
                <div class="stat-number" style="color: #059669;">Rp {{ number_format($overallTotalRevenue, 0, ',', '.') }}</div>
                <div class="stat-sub">Pesanan selesai terbayar</div>
            </div>
            <div class="stat-card orange">
                <div class="stat-label">Omset QRIS Online</div>
                <div class="stat-number" style="color: #ea580c;">Rp {{ number_format($overallQrisRevenue, 0, ',', '.') }}</div>
                <div class="stat-sub">Rekap transfer admin</div>
            </div>
            <div class="stat-card amber">
                <div class="stat-label">Omset Tunai (COD)</div>
                <div class="stat-number" style="color: #d97706;">Rp {{ number_format($overallCashRevenue, 0, ',', '.') }}</div>
                <div class="stat-sub">Diterima langsung stand</div>
            </div>
            <div class="stat-card sky">
                <div class="stat-label">Pesanan Selesai</div>
                <div class="stat-number" style="color: #0284c7;">{{ number_format($overallCompletedOrders, 0, ',', '.') }}</div>
                <div class="stat-sub">Dari {{ number_format($overallTotalOrders, 0, ',', '.') }} pesanan masuk</div>
            </div>
        </div>

        <!-- STAND PERFORMANCE TABLE -->
        <div class="table-section-title">Performa Penjualan &amp; Omset Tiap Stand Kantin</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 32px;" class="text-center">No</th>
                    <th>Nama Stand</th>
                    <th style="width: 65px;" class="text-center">No. Stand</th>
                    <th>Pemilik</th>
                    <th style="width: 55px;" class="text-center">Menu</th>
                    <th style="width: 65px;" class="text-center">Pesanan</th>
                    <th style="width: 60px;" class="text-center">Selesai</th>
                    <th style="width: 95px;" class="text-right">QRIS (Rp)</th>
                    <th style="width: 95px;" class="text-right">Tunai (Rp)</th>
                    <th style="width: 105px;" class="text-right">Total Omset (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stands as $index => $stand)
                <tr>
                    <td class="text-center font-bold">
                        <span class="badge-rank {{ $index === 0 ? 'rank-1' : ($index === 1 ? 'rank-2' : ($index === 2 ? 'rank-3' : 'rank-other')) }}">
                            {{ $index + 1 }}
                        </span>
                    </td>
                    <td>
                        <span class="font-bold">{{ $stand->nama_stand }}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge-stand">{{ $stand->nomor_stand }}</span>
                    </td>
                    <td>{{ $stand->pemilik }}</td>
                    <td class="text-center">{{ $stand->menus_count }}</td>
                    <td class="text-center">{{ $stand->orders_count }}</td>
                    <td class="text-center font-bold">{{ $stand->orders_selesai_count ?? 0 }}</td>
                    <td class="text-right">{{ number_format($stand->omset_qris ?? 0, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($stand->omset_tunai ?? 0, 0, ',', '.') }}</td>
                    <td class="text-right font-black" style="color: #059669;">
                        {{ number_format($stand->total_omset ?? 0, 0, ',', '.') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="text-center" style="padding: 16px; color: #94a3b8;">Belum ada stand terdaftar.</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="table-total-row">
                    <td colspan="4" class="text-center">TOTAL KESELURUHAN</td>
                    <td class="text-center">{{ $stands->sum('menus_count') }}</td>
                    <td class="text-center">{{ $overallTotalOrders }}</td>
                    <td class="text-center">{{ $overallCompletedOrders }}</td>
                    <td class="text-right">{{ number_format($overallQrisRevenue, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($overallCashRevenue, 0, ',', '.') }}</td>
                    <td class="text-right" style="color: #047857; font-size: 11px;">
                        Rp {{ number_format($overallTotalRevenue, 0, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        </table>

    </div>

    <script>
        // Auto trigger print dialog after document elements render
        window.addEventListener('load', function() {
            setTimeout(function() {
                window.print();
            }, 600);
        });
    </script>
</body>
</html>
