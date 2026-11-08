@props([
    'chartData',
    'periodeLabel' => 'Hari Ini',
    'periode' => 'hari_ini',
    'id' => 'omsetChart_' . uniqid(),
])

@php
    $uniqueId = preg_replace('/[^a-zA-Z0-9]/', '', $id);
    $summary = $chartData['summary'] ?? [
        'total_revenue' => 0,
        'total_orders' => 0,
        'average_order' => 0,
        'qris_revenue' => 0,
        'cash_revenue' => 0,
        'qris_percentage' => 0,
        'cash_percentage' => 0,
        'peak_label' => '-',
        'peak_amount' => 0,
    ];
    $labels = $chartData['labels'] ?? [];
    $revenue = $chartData['revenue'] ?? [];
    $qris = $chartData['qris'] ?? [];
    $cash = $chartData['cash'] ?? [];
    $ordersCount = $chartData['orders_count'] ?? [];
@endphp

<div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/90 shadow-xs overflow-hidden" id="chartCard_{{ $uniqueId }}">

    {{-- CARD HEADER: Title, Periode Pill, & View Switcher --}}
    <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-orange-500 to-amber-500 text-white flex items-center justify-center shrink-0 shadow-md shadow-orange-500/25">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941" />
                </svg>
            </div>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h2 class="text-base sm:text-lg font-black text-slate-900 tracking-tight leading-snug">Grafik Omset Penjualan</h2>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-orange-50 text-orange-700 border border-orange-200/80">
                        <span class="w-1.5 h-1.5 rounded-full bg-orange-500 animate-pulse"></span>
                        {{ $periodeLabel }}
                    </span>
                </div>
                <p class="text-xs text-slate-400 font-medium mt-0.5">
                    Visualisasi pendapatan omset pesanan selesai kantin
                </p>
            </div>
        </div>

        {{-- Interactive View Mode Selector --}}
        <div class="flex items-center bg-slate-100/90 p-1 rounded-xl self-start md:self-auto border border-slate-200/70 text-xs">
            <button type="button"
                id="btnModeTotal_{{ $uniqueId }}"
                onclick="switchOmsetChartMode('{{ $uniqueId }}', 'total')"
                class="px-3 py-1.5 rounded-lg font-bold text-xs transition-all cursor-pointer bg-white text-orange-600 shadow-2xs">
                Total Omset
            </button>
            <button type="button"
                id="btnModeSplit_{{ $uniqueId }}"
                onclick="switchOmsetChartMode('{{ $uniqueId }}', 'split')"
                class="px-3 py-1.5 rounded-lg font-semibold text-xs text-slate-600 hover:text-slate-900 transition-all cursor-pointer">
                QRIS vs Tunai
            </button>
            <button type="button"
                id="btnModeOrders_{{ $uniqueId }}"
                onclick="switchOmsetChartMode('{{ $uniqueId }}', 'orders')"
                class="px-3 py-1.5 rounded-lg font-semibold text-xs text-slate-600 hover:text-slate-900 transition-all cursor-pointer">
                Volume Pesanan
            </button>
        </div>
    </div>

    {{-- INLINE SUMMARY METRICS RIBBON (Sleek, Minimalist, Harmonis) --}}
    <div class="px-4 py-3 sm:px-6 bg-slate-50/70 border-b border-slate-100 flex flex-wrap items-center justify-between gap-y-2 gap-x-6">
        <div class="flex items-baseline gap-2">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Omset:</span>
            <span class="text-base sm:text-lg font-black text-slate-900 tracking-tight">
                Rp {{ number_format($summary['total_revenue'], 0, ',', '.') }}
            </span>
            <span class="text-xs font-semibold text-orange-600">
                ({{ $summary['total_orders'] }} pesanan)
            </span>
        </div>

        <div class="flex items-center gap-4 sm:gap-6 flex-wrap text-xs">
            <div class="flex items-center gap-1.5">
                <span class="text-slate-400 font-medium">Rata-rata:</span>
                <span class="font-bold text-slate-800">Rp {{ number_format($summary['average_order'], 0, ',', '.') }}</span>
            </div>

            <div class="flex items-center gap-1.5">
                <span class="text-slate-400 font-medium">Puncak Omset:</span>
                <span class="font-bold text-slate-800">Rp {{ number_format($summary['peak_amount'], 0, ',', '.') }}</span>
                <span class="text-[11px] text-amber-700 bg-amber-50 px-1.5 py-0.5 rounded border border-amber-200/60 font-semibold truncate max-w-[140px] sm:max-w-none" title="{{ $summary['peak_label'] }}">
                    {{ $summary['peak_label'] }}
                </span>
            </div>

            <div class="flex items-center gap-2">
                <div class="flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-sky-500"></span>
                    <span class="font-bold text-slate-700">{{ $summary['qris_percentage'] }}%</span>
                    <span class="text-[11px] text-slate-400">QRIS</span>
                </div>
                <span class="text-slate-300">·</span>
                <div class="flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span class="font-bold text-slate-700">{{ $summary['cash_percentage'] }}%</span>
                    <span class="text-[11px] text-slate-400">Tunai</span>
                </div>
            </div>
        </div>
    </div>

    {{-- CHART CANVAS --}}
    <div class="p-4 sm:p-6">
        @if($summary['total_revenue'] === 0)
        <div class="mb-3 p-3 rounded-xl bg-orange-50/70 border border-orange-200/70 flex items-center gap-2 text-xs text-orange-800">
            <svg class="w-4 h-4 text-orange-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
            </svg>
            <span>Belum ada transaksi selesai pada periode <strong>{{ $periodeLabel }}</strong>. Anda dapat memilih tanggal lain melalui filter periode di navbar.</span>
        </div>
        @endif

        <div class="relative w-full h-[260px] sm:h-[320px] lg:h-[340px]">
            <canvas id="omsetCanvas_{{ $uniqueId }}"></canvas>
        </div>
    </div>

</div>

@push('scripts')
{{-- Load Chart.js locally for instant 0ms load without CDN lag --}}
<script src="{{ asset('js/chart.umd.min.js') }}"></script>
<script>
(function() {
    var uniqueId = '{{ $uniqueId }}';
    var canvas = document.getElementById('omsetCanvas_' + uniqueId);
    if (!canvas) return;

    var chartLabels = @json($labels);
    var revenueData = @json($revenue);
    var qrisData = @json($qris);
    var cashData = @json($cash);
    var ordersCountData = @json($ordersCount);

    function formatRupiahShort(value) {
        if (value >= 1000000) {
            return 'Rp ' + (value / 1000000).toFixed(value % 1000000 === 0 ? 0 : 1) + ' jt';
        }
        if (value >= 1000) {
            return 'Rp ' + (value / 1000).toFixed(0) + ' rb';
        }
        return 'Rp ' + value;
    }

    function formatRupiahFull(value) {
        return 'Rp ' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    var ctx = canvas.getContext('2d');

    // Create Silk Warm KantinSkanic Glow Gradients with multi-stop falloff
    var orangeGradient = ctx.createLinearGradient(0, 0, 0, 320);
    orangeGradient.addColorStop(0, 'rgba(234, 88, 12, 0.26)');
    orangeGradient.addColorStop(0.45, 'rgba(249, 115, 22, 0.08)');
    orangeGradient.addColorStop(1, 'rgba(249, 115, 22, 0.00)');

    var skyGradient = ctx.createLinearGradient(0, 0, 0, 320);
    skyGradient.addColorStop(0, 'rgba(2, 132, 199, 0.22)');
    skyGradient.addColorStop(0.45, 'rgba(2, 132, 199, 0.06)');
    skyGradient.addColorStop(1, 'rgba(2, 132, 199, 0.00)');

    var emeraldGradient = ctx.createLinearGradient(0, 0, 0, 320);
    emeraldGradient.addColorStop(0, 'rgba(16, 185, 129, 0.22)');
    emeraldGradient.addColorStop(0.45, 'rgba(16, 185, 129, 0.06)');
    emeraldGradient.addColorStop(1, 'rgba(16, 185, 129, 0.00)');

    // Datasets configuration — Ultra-Smooth Spline Curve with Monotone Interpolation
    var totalDatasets = [
        {
            label: 'Total Omset',
            data: revenueData,
            borderColor: '#ea580c',
            backgroundColor: orangeGradient,
            borderWidth: 3.2,
            fill: true,
            tension: 0.45,
            cubicInterpolationMode: 'monotone',
            borderCapStyle: 'round',
            borderJoinStyle: 'round',
            pointRadius: 0,
            pointHoverRadius: 7,
            pointHoverBackgroundColor: '#ea580c',
            pointHoverBorderColor: '#ffffff',
            pointHoverBorderWidth: 3,
            pointHitRadius: 25,
            yAxisID: 'y'
        }
    ];

    var splitDatasets = [
        {
            label: 'QRIS Online',
            data: qrisData,
            borderColor: '#0284c7',
            backgroundColor: skyGradient,
            borderWidth: 2.8,
            fill: true,
            tension: 0.45,
            cubicInterpolationMode: 'monotone',
            borderCapStyle: 'round',
            borderJoinStyle: 'round',
            pointRadius: 0,
            pointHoverRadius: 6.5,
            pointHoverBackgroundColor: '#0284c7',
            pointHoverBorderColor: '#ffffff',
            pointHoverBorderWidth: 2.5,
            pointHitRadius: 25,
            yAxisID: 'y'
        },
        {
            label: 'Tunai (COD)',
            data: cashData,
            borderColor: '#059669',
            backgroundColor: emeraldGradient,
            borderWidth: 2.8,
            fill: true,
            tension: 0.45,
            cubicInterpolationMode: 'monotone',
            borderCapStyle: 'round',
            borderJoinStyle: 'round',
            pointRadius: 0,
            pointHoverRadius: 6.5,
            pointHoverBackgroundColor: '#059669',
            pointHoverBorderColor: '#ffffff',
            pointHoverBorderWidth: 2.5,
            pointHitRadius: 25,
            yAxisID: 'y'
        }
    ];

    var ordersDatasets = [
        {
            type: 'bar',
            label: 'Volume Pesanan Selesai',
            data: ordersCountData,
            backgroundColor: 'rgba(249, 115, 22, 0.70)',
            hoverBackgroundColor: '#ea580c',
            borderColor: '#ea580c',
            borderWidth: 0,
            borderRadius: 12,
            borderSkipped: false,
            barPercentage: 0.52,
            categoryPercentage: 0.72,
            yAxisID: 'y'
        }
    ];

    var chartConfig = {
        type: 'line',
        data: {
            labels: chartLabels,
            datasets: totalDatasets
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index'
            },
            elements: {
                line: {
                    tension: 0.45,
                    cubicInterpolationMode: 'monotone',
                    borderCapStyle: 'round',
                    borderJoinStyle: 'round'
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: '#0f172a',
                    titleColor: '#f8fafc',
                    titleFont: {
                        family: "'Plus Jakarta Sans', sans-serif",
                        size: 12,
                        weight: 'bold'
                    },
                    bodyColor: '#cbd5e1',
                    bodyFont: {
                        family: "'Plus Jakarta Sans', sans-serif",
                        size: 11
                    },
                    padding: 12,
                    cornerRadius: 12,
                    displayColors: true,
                    boxPadding: 4,
                    callbacks: {
                        label: function(context) {
                            var currentMode = window['omsetChartMode_' + uniqueId] || 'total';
                            if (currentMode === 'orders') {
                                return ' ' + context.dataset.label + ': ' + context.parsed.y + ' pesanan';
                            }
                            var index = context.dataIndex;
                            var count = ordersCountData[index] || 0;
                            return ' ' + context.dataset.label + ': ' + formatRupiahFull(context.parsed.y) + ' (' + count + ' pesanan)';
                        }
                    }
                }
            },
            scales: {
                x: {
                    border: {
                        display: false
                    },
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#64748b',
                        font: {
                            family: "'Plus Jakarta Sans', sans-serif",
                            size: 11,
                            weight: '600'
                        },
                        padding: 8,
                        maxRotation: 0,
                        autoSkip: true,
                        maxTicksLimit: 12
                    }
                },
                y: {
                    beginAtZero: true,
                    border: {
                        display: false
                    },
                    grid: {
                        color: '#f1f5f9',
                        lineWidth: 1
                    },
                    ticks: {
                        color: '#94a3b8',
                        font: {
                            family: "'Plus Jakarta Sans', sans-serif",
                            size: 10.5
                        },
                        padding: 8,
                        callback: function(val) {
                            var currentMode = window['omsetChartMode_' + uniqueId] || 'total';
                            if (currentMode === 'orders') {
                                return Number.isInteger(val) ? val + ' pesanan' : '';
                            }
                            return formatRupiahShort(val);
                        }
                    }
                }
            }
        }
    };

    var omsetChart = new Chart(ctx, chartConfig);
    window['chartInstance_' + uniqueId] = omsetChart;
    window['omsetChartMode_' + uniqueId] = 'total';

    window.switchOmsetChartMode = function(id, mode) {
        var chart = window['chartInstance_' + id];
        if (!chart) return;

        window['omsetChartMode_' + id] = mode;

        var btnTotal = document.getElementById('btnModeTotal_' + id);
        var btnSplit = document.getElementById('btnModeSplit_' + id);
        var btnOrders = document.getElementById('btnModeOrders_' + id);

        [btnTotal, btnSplit, btnOrders].forEach(function(b) {
            if (!b) return;
            b.classList.remove('bg-white', 'text-orange-600', 'shadow-2xs', 'font-bold');
            b.classList.add('text-slate-600', 'hover:text-slate-900', 'font-semibold');
        });

        if (mode === 'total') {
            if (btnTotal) {
                btnTotal.classList.add('bg-white', 'text-orange-600', 'shadow-2xs', 'font-bold');
                btnTotal.classList.remove('text-slate-600', 'hover:text-slate-900', 'font-semibold');
            }
            chart.config.type = 'line';
            chart.data.datasets = totalDatasets;
        } else if (mode === 'split') {
            if (btnSplit) {
                btnSplit.classList.add('bg-white', 'text-orange-600', 'shadow-2xs', 'font-bold');
                btnSplit.classList.remove('text-slate-600', 'hover:text-slate-900', 'font-semibold');
            }
            chart.config.type = 'line';
            chart.data.datasets = splitDatasets;
        } else if (mode === 'orders') {
            if (btnOrders) {
                btnOrders.classList.add('bg-white', 'text-orange-600', 'shadow-2xs', 'font-bold');
                btnOrders.classList.remove('text-slate-600', 'hover:text-slate-900', 'font-semibold');
            }
            chart.data.datasets = ordersDatasets;
        }

        chart.update('active');
    };
})();
</script>
@endpush
