<?php

namespace App\Services;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PeriodFilterService
{
    /**
     * Resolve period filter parameters from HTTP request or direct parameters.
     *
     * @return array{
     *     periode: string,
     *     periodeLabel: string,
     *     shortLabel: string,
     *     periodeDetail: string,
     *     isCustom: bool,
     *     startDate: ?Carbon,
     *     endDate: ?Carbon,
     *     startDateStr: ?string,
     *     endDateStr: ?string,
     *     filter: \Closure
     * }
     */
    public static function resolve(Request|array $request): array
    {
        $rawPeriode = is_array($request) ? ($request['periode'] ?? null) : $request->query('periode');
        $rawStart = is_array($request) ? ($request['start_date'] ?? null) : $request->query('start_date');
        $rawEnd = is_array($request) ? ($request['end_date'] ?? null) : $request->query('end_date');

        // Check if custom date range was submitted or requested
        $isExplicitCustom = in_array($rawPeriode, ['kustom', 'custom'], true);
        $hasCustomDates = ! empty($rawStart) || ! empty($rawEnd);

        $startDate = null;
        $endDate = null;
        $isCustom = false;

        if ($isExplicitCustom || $hasCustomDates) {
            try {
                if (! empty($rawStart) && ! empty($rawEnd)) {
                    $startDate = Carbon::parse($rawStart, 'Asia/Jakarta')->startOfDay();
                    $endDate = Carbon::parse($rawEnd, 'Asia/Jakarta')->endOfDay();
                } elseif (! empty($rawStart)) {
                    $startDate = Carbon::parse($rawStart, 'Asia/Jakarta')->startOfDay();
                    $endDate = $startDate->copy()->endOfDay();
                } elseif (! empty($rawEnd)) {
                    $endDate = Carbon::parse($rawEnd, 'Asia/Jakarta')->endOfDay();
                    $startDate = $endDate->copy()->startOfDay();
                }

                if ($startDate && $endDate) {
                    // Ensure start is not after end
                    if ($startDate->gt($endDate)) {
                        $temp = $startDate;
                        $startDate = $endDate->copy()->startOfDay();
                        $endDate = $temp->copy()->endOfDay();
                    }
                    $isCustom = true;
                }
            } catch (\Throwable) {
                $isCustom = false;
                $startDate = null;
                $endDate = null;
            }
        }

        if ($isCustom && $startDate && $endDate) {
            $periode = 'kustom';
            $startDateStr = $startDate->format('Y-m-d');
            $endDateStr = $endDate->format('Y-m-d');

            if ($startDate->isSameDay($endDate)) {
                $periodeLabel = $startDate->locale('id')->isoFormat('D MMM Y');
                $shortLabel = $startDate->locale('id')->isoFormat('D MMM');
                $periodeDetail = $startDate->locale('id')->isoFormat('dddd, D MMMM Y');
            } elseif ($startDate->month === $endDate->month && $startDate->year === $endDate->year) {
                $periodeLabel = $startDate->isoFormat('D').' - '.$endDate->locale('id')->isoFormat('D MMM Y');
                $shortLabel = $startDate->isoFormat('D').'-'.$endDate->locale('id')->isoFormat('D MMM');
                $periodeDetail = $startDate->locale('id')->isoFormat('D MMMM Y').' — '.$endDate->locale('id')->isoFormat('D MMMM Y');
            } elseif ($startDate->year === $endDate->year) {
                $periodeLabel = $startDate->locale('id')->isoFormat('D MMM').' - '.$endDate->locale('id')->isoFormat('D MMM Y');
                $shortLabel = $startDate->locale('id')->isoFormat('D MMM').' - '.$endDate->locale('id')->isoFormat('D MMM');
                $periodeDetail = $startDate->locale('id')->isoFormat('D MMMM Y').' — '.$endDate->locale('id')->isoFormat('D MMMM Y');
            } else {
                $periodeLabel = $startDate->locale('id')->isoFormat('D MMM Y').' - '.$endDate->locale('id')->isoFormat('D MMM Y');
                $shortLabel = $startDate->format('d/m/y').' - '.$endDate->format('d/m/y');
                $periodeDetail = $startDate->locale('id')->isoFormat('D MMMM Y').' — '.$endDate->locale('id')->isoFormat('D MMMM Y');
            }
        } else {
            $validPresets = ['hari_ini', 'minggu_ini', 'bulan_ini', 'semua'];
            $periode = in_array($rawPeriode, $validPresets, true) ? $rawPeriode : 'hari_ini';
            $startDateStr = null;
            $endDateStr = null;

            $now = Carbon::now('Asia/Jakarta');

            switch ($periode) {
                case 'minggu_ini':
                    $startDate = $now->copy()->startOfWeek();
                    $endDate = $now->copy()->endOfWeek();
                    $periodeLabel = 'Minggu Ini';
                    $shortLabel = 'Minggu Ini';
                    $periodeDetail = $startDate->locale('id')->isoFormat('D MMMM Y').' — '.$endDate->locale('id')->isoFormat('D MMMM Y');
                    break;

                case 'bulan_ini':
                    $startDate = $now->copy()->startOfMonth();
                    $endDate = $now->copy()->endOfMonth();
                    $periodeLabel = 'Bulan Ini';
                    $shortLabel = 'Bulan Ini';
                    $periodeDetail = $now->locale('id')->isoFormat('MMMM Y');
                    break;

                case 'semua':
                    $startDate = null;
                    $endDate = null;
                    $periodeLabel = 'Keseluruhan';
                    $shortLabel = 'Semua';
                    $periodeDetail = 'Semua Riwayat s/d '.$now->locale('id')->isoFormat('D MMMM Y');
                    break;

                case 'hari_ini':
                default:
                    $startDate = Carbon::today('Asia/Jakarta')->startOfDay();
                    $endDate = Carbon::today('Asia/Jakarta')->endOfDay();
                    $periodeLabel = 'Hari Ini';
                    $shortLabel = 'Hari Ini';
                    $periodeDetail = $now->locale('id')->isoFormat('dddd, D MMMM Y');
                    break;
            }
        }

        $filter = function ($query, string $column = 'created_at') use ($periode, $startDate, $endDate) {
            if ($periode === 'hari_ini') {
                return $query->whereDate($column, Carbon::today('Asia/Jakarta'));
            }

            if ($periode === 'minggu_ini') {
                return $query->whereBetween($column, [
                    Carbon::now('Asia/Jakarta')->startOfWeek(),
                    Carbon::now('Asia/Jakarta')->endOfWeek(),
                ]);
            }

            if ($periode === 'bulan_ini') {
                return $query->whereMonth($column, Carbon::now('Asia/Jakarta')->month)
                    ->whereYear($column, Carbon::now('Asia/Jakarta')->year);
            }

            if ($periode === 'semua') {
                return $query;
            }

            if ($periode === 'kustom' && $startDate && $endDate) {
                return $query->whereBetween($column, [
                    $startDate->copy()->startOfDay(),
                    $endDate->copy()->endOfDay(),
                ]);
            }

            return $query->whereDate($column, Carbon::today('Asia/Jakarta'));
        };

        return [
            'periode' => $periode,
            'periodeLabel' => $periodeLabel,
            'shortLabel' => $shortLabel,
            'periodeDetail' => $periodeDetail,
            'isCustom' => $isCustom,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'startDateStr' => $startDateStr,
            'endDateStr' => $endDateStr,
            'filter' => $filter,
            'scope' => $filter,
        ];
    }

    /**
     * Compile revenue chart dataset and summary metrics for the given period.
     *
     * @param  array<string, mixed>  $periodData
     * @return array<string, mixed>
     */
    public static function getRevenueChartData(array $periodData, ?int $standId = null): array
    {
        $periode = $periodData['periode'] ?? 'hari_ini';
        $startDate = $periodData['startDate'] ?? Carbon::today('Asia/Jakarta')->startOfDay();
        $endDate = $periodData['endDate'] ?? Carbon::today('Asia/Jakarta')->endOfDay();
        $scope = $periodData['scope'] ?? fn ($q) => $q;

        $query = Order::query()
            ->where('status', 'selesai')
            ->tap($scope);

        if ($standId !== null) {
            $query->where('stand_id', $standId);
        }

        $orders = $query->orderBy('created_at')->get(['id', 'total_harga', 'metode_pembayaran', 'created_at']);

        $buckets = [];

        // 1. Grouping Strategy
        if ($periode === 'hari_ini' || ($startDate && $endDate && $startDate->isSameDay($endDate))) {
            // Jam operasional kantin sekolah (07:00 - 16:00)
            for ($h = 7; $h <= 16; $h++) {
                $timeKey = sprintf('%02d:00', $h);
                $buckets[$timeKey] = [
                    'label' => $timeKey,
                    'full_label' => sprintf('Pukul %02d:00 WIB', $h),
                    'revenue' => 0,
                    'qris' => 0,
                    'cash' => 0,
                    'count' => 0,
                ];
            }

            foreach ($orders as $order) {
                $orderHour = (int) $order->created_at->setTimezone('Asia/Jakarta')->format('G');
                $targetHour = max(7, min(16, $orderHour));
                $timeKey = sprintf('%02d:00', $targetHour);
                $buckets[$timeKey]['revenue'] += (int) $order->total_harga;
                $buckets[$timeKey]['count']++;
                if ($order->metode_pembayaran === 'qris') {
                    $buckets[$timeKey]['qris'] += (int) $order->total_harga;
                } else {
                    $buckets[$timeKey]['cash'] += (int) $order->total_harga;
                }
            }
        } elseif ($periode === 'minggu_ini') {
            // 7 hari dalam minggu berjalan (Senin - Minggu)
            $startOfWeek = $startDate ? $startDate->copy()->startOfWeek() : Carbon::now('Asia/Jakarta')->startOfWeek();
            for ($d = 0; $d < 7; $d++) {
                $currentDay = $startOfWeek->copy()->addDays($d);
                $dateKey = $currentDay->format('Y-m-d');
                $buckets[$dateKey] = [
                    'label' => $currentDay->locale('id')->isoFormat('dddd (D/M)'),
                    'full_label' => $currentDay->locale('id')->isoFormat('dddd, D MMMM Y'),
                    'revenue' => 0,
                    'qris' => 0,
                    'cash' => 0,
                    'count' => 0,
                ];
            }

            foreach ($orders as $order) {
                $dateKey = $order->created_at->setTimezone('Asia/Jakarta')->format('Y-m-d');
                if (isset($buckets[$dateKey])) {
                    $buckets[$dateKey]['revenue'] += (int) $order->total_harga;
                    $buckets[$dateKey]['count']++;
                    if ($order->metode_pembayaran === 'qris') {
                        $buckets[$dateKey]['qris'] += (int) $order->total_harga;
                    } else {
                        $buckets[$dateKey]['cash'] += (int) $order->total_harga;
                    }
                }
            }
        } elseif ($periode === 'bulan_ini') {
            // Setiap tanggal dalam bulan berjalan
            $startOfMonth = $startDate ? $startDate->copy()->startOfMonth() : Carbon::now('Asia/Jakarta')->startOfMonth();
            $endOfMonth = $startDate ? $startDate->copy()->endOfMonth() : Carbon::now('Asia/Jakarta')->endOfMonth();
            $totalDays = $endOfMonth->day;

            for ($d = 1; $d <= $totalDays; $d++) {
                $currentDay = $startOfMonth->copy()->day($d);
                $dateKey = $currentDay->format('Y-m-d');
                $buckets[$dateKey] = [
                    'label' => $currentDay->locale('id')->isoFormat('D MMM'),
                    'full_label' => $currentDay->locale('id')->isoFormat('D MMMM Y'),
                    'revenue' => 0,
                    'qris' => 0,
                    'cash' => 0,
                    'count' => 0,
                ];
            }

            foreach ($orders as $order) {
                $dateKey = $order->created_at->setTimezone('Asia/Jakarta')->format('Y-m-d');
                if (isset($buckets[$dateKey])) {
                    $buckets[$dateKey]['revenue'] += (int) $order->total_harga;
                    $buckets[$dateKey]['count']++;
                    if ($order->metode_pembayaran === 'qris') {
                        $buckets[$dateKey]['qris'] += (int) $order->total_harga;
                    } else {
                        $buckets[$dateKey]['cash'] += (int) $order->total_harga;
                    }
                }
            }
        } elseif ($periode === 'semua') {
            // Rekap 6 bulan terakhir s/d bulan ini
            $now = Carbon::now('Asia/Jakarta');
            for ($m = 5; $m >= 0; $m--) {
                $monthDate = $now->copy()->subMonths($m)->startOfMonth();
                $monthKey = $monthDate->format('Y-m');
                $buckets[$monthKey] = [
                    'label' => $monthDate->locale('id')->isoFormat('MMM Y'),
                    'full_label' => $monthDate->locale('id')->isoFormat('MMMM Y'),
                    'revenue' => 0,
                    'qris' => 0,
                    'cash' => 0,
                    'count' => 0,
                ];
            }

            foreach ($orders as $order) {
                $monthKey = $order->created_at->setTimezone('Asia/Jakarta')->format('Y-m');
                if (! isset($buckets[$monthKey])) {
                    $orderMonth = $order->created_at->setTimezone('Asia/Jakarta')->startOfMonth();
                    $buckets[$monthKey] = [
                        'label' => $orderMonth->locale('id')->isoFormat('MMM Y'),
                        'full_label' => $orderMonth->locale('id')->isoFormat('MMMM Y'),
                        'revenue' => 0,
                        'qris' => 0,
                        'cash' => 0,
                        'count' => 0,
                    ];
                }
                $buckets[$monthKey]['revenue'] += (int) $order->total_harga;
                $buckets[$monthKey]['count']++;
                if ($order->metode_pembayaran === 'qris') {
                    $buckets[$monthKey]['qris'] += (int) $order->total_harga;
                } else {
                    $buckets[$monthKey]['cash'] += (int) $order->total_harga;
                }
            }
            ksort($buckets);
        } else {
            // Periode Kustom (berdasarkan selisih hari)
            $diffDays = $startDate && $endDate ? $startDate->diffInDays($endDate) : 0;

            if ($diffDays <= 62 && $startDate) {
                // Selisih s/d ~2 bulan (<= 62 hari): kelompokkan per hari agar grafik detail dan kurva melengkung dinamis
                for ($d = 0; $d <= $diffDays; $d++) {
                    $currentDay = $startDate->copy()->addDays($d);
                    $dateKey = $currentDay->format('Y-m-d');
                    $buckets[$dateKey] = [
                        'label' => $currentDay->locale('id')->isoFormat('D MMM'),
                        'full_label' => $currentDay->locale('id')->isoFormat('D MMMM Y'),
                        'revenue' => 0,
                        'qris' => 0,
                        'cash' => 0,
                        'count' => 0,
                    ];
                }

                foreach ($orders as $order) {
                    $dateKey = $order->created_at->setTimezone('Asia/Jakarta')->format('Y-m-d');
                    if (isset($buckets[$dateKey])) {
                        $buckets[$dateKey]['revenue'] += (int) $order->total_harga;
                        $buckets[$dateKey]['count']++;
                        if ($order->metode_pembayaran === 'qris') {
                            $buckets[$dateKey]['qris'] += (int) $order->total_harga;
                        } else {
                            $buckets[$dateKey]['cash'] += (int) $order->total_harga;
                        }
                    }
                }
            } elseif ($diffDays <= 180 && $startDate) {
                // Selisih 2 - 6 bulan: kelompokkan per minggu (agar tetap ada banyak titik dan tidak menjadi garis lurus kaku)
                $cursor = $startDate->copy()->startOfWeek();
                $endCursor = $endDate->copy()->endOfWeek();

                while ($cursor->lte($endCursor)) {
                    $weekEnd = $cursor->copy()->endOfWeek();
                    $weekKey = $cursor->format('Y-\WW');
                    $buckets[$weekKey] = [
                        'label' => $cursor->locale('id')->isoFormat('D MMM').' - '.$weekEnd->locale('id')->isoFormat('D MMM'),
                        'full_label' => $cursor->locale('id')->isoFormat('D MMMM Y').' — '.$weekEnd->locale('id')->isoFormat('D MMMM Y'),
                        'revenue' => 0,
                        'qris' => 0,
                        'cash' => 0,
                        'count' => 0,
                    ];
                    $cursor->addWeek();
                }

                foreach ($orders as $order) {
                    $orderTime = $order->created_at->setTimezone('Asia/Jakarta');
                    $weekKey = $orderTime->format('Y-\WW');
                    if (isset($buckets[$weekKey])) {
                        $buckets[$weekKey]['revenue'] += (int) $order->total_harga;
                        $buckets[$weekKey]['count']++;
                        if ($order->metode_pembayaran === 'qris') {
                            $buckets[$weekKey]['qris'] += (int) $order->total_harga;
                        } else {
                            $buckets[$weekKey]['cash'] += (int) $order->total_harga;
                        }
                    }
                }
            } else {
                // Selisih > 6 bulan: kelompokkan per bulan
                $cursor = $startDate ? $startDate->copy()->startOfMonth() : Carbon::now('Asia/Jakarta')->startOfMonth();
                $endCursor = $endDate ? $endDate->copy()->endOfMonth() : Carbon::now('Asia/Jakarta')->endOfMonth();

                while ($cursor->lte($endCursor)) {
                    $monthKey = $cursor->format('Y-m');
                    $buckets[$monthKey] = [
                        'label' => $cursor->locale('id')->isoFormat('MMM Y'),
                        'full_label' => $cursor->locale('id')->isoFormat('MMMM Y'),
                        'revenue' => 0,
                        'qris' => 0,
                        'cash' => 0,
                        'count' => 0,
                    ];
                    $cursor->addMonth();
                }

                foreach ($orders as $order) {
                    $monthKey = $order->created_at->setTimezone('Asia/Jakarta')->format('Y-m');
                    if (isset($buckets[$monthKey])) {
                        $buckets[$monthKey]['revenue'] += (int) $order->total_harga;
                        $buckets[$monthKey]['count']++;
                        if ($order->metode_pembayaran === 'qris') {
                            $buckets[$monthKey]['qris'] += (int) $order->total_harga;
                        } else {
                            $buckets[$monthKey]['cash'] += (int) $order->total_harga;
                        }
                    }
                }
            }
        }

        // 2. Compute Summary Metrics & Arrays
        $labels = [];
        $revenue = [];
        $qris = [];
        $cash = [];
        $counts = [];

        $totalRevenue = 0;
        $totalOrders = 0;
        $qrisRevenue = 0;
        $cashRevenue = 0;
        $peakAmount = 0;
        $peakLabel = '-';

        foreach ($buckets as $item) {
            $labels[] = $item['label'];
            $revenue[] = $item['revenue'];
            $qris[] = $item['qris'];
            $cash[] = $item['cash'];
            $counts[] = $item['count'];

            $totalRevenue += $item['revenue'];
            $totalOrders += $item['count'];
            $qrisRevenue += $item['qris'];
            $cashRevenue += $item['cash'];

            if ($item['revenue'] > $peakAmount) {
                $peakAmount = $item['revenue'];
                $peakLabel = $item['full_label'] ?? $item['label'];
            }
        }

        $averageOrder = $totalOrders > 0 ? (int) round($totalRevenue / $totalOrders) : 0;
        $qrisPercentage = $totalRevenue > 0 ? (int) round(($qrisRevenue / $totalRevenue) * 100) : 0;
        $cashPercentage = $totalRevenue > 0 ? (int) round(($cashRevenue / $totalRevenue) * 100) : 0;

        return [
            'labels' => $labels,
            'revenue' => $revenue,
            'qris' => $qris,
            'cash' => $cash,
            'orders_count' => $counts,
            'summary' => [
                'total_revenue' => $totalRevenue,
                'total_orders' => $totalOrders,
                'average_order' => $averageOrder,
                'qris_revenue' => $qrisRevenue,
                'cash_revenue' => $cashRevenue,
                'qris_percentage' => $qrisPercentage,
                'cash_percentage' => $cashPercentage,
                'peak_label' => $peakLabel,
                'peak_amount' => $peakAmount,
            ],
        ];
    }
}
