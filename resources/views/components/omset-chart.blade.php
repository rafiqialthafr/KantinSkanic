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
    $fullLabels = $chartData['full_labels'] ?? $labels;
    $revenue = $chartData['revenue'] ?? [];
    $qris = $chartData['qris'] ?? [];
    $cash = $chartData['cash'] ?? [];
    $ordersCount = $chartData['orders_count'] ?? [];
    $standsData = $chartData['stands'] ?? [
        'labels' => [],
        'revenue' => [],
        'orders_count' => [],
        'shares' => [],
        'list' => [],
    ];
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
                <p class="text-xs text-slate-400 font-medium mt-0.5" id="chartDesc_{{ $uniqueId }}">
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
                id="btnModeStands_{{ $uniqueId }}"
                onclick="switchOmsetChartMode('{{ $uniqueId }}', 'stands')"
                class="px-3 py-1.5 rounded-lg font-semibold text-xs text-slate-600 hover:text-slate-900 transition-all cursor-pointer">
                Per Stand
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
    var fullLabels = @json($fullLabels);
    var revenueData = @json($revenue);
    var qrisData = @json($qris);
    var cashData = @json($cash);
    var ordersCountData = @json($ordersCount);

    var standLabels = @json($standsData['labels'] ?? []);
    var standRevenueData = @json($standsData['revenue'] ?? []);
    var standOrdersData = @json($standsData['orders_count'] ?? []);
    var standSharesData = @json($standsData['shares'] ?? []);

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

    // Create Silk Warm KantinSkanic Glow Gradients with bright, fresh luminosity
    var orangeGradient = ctx.createLinearGradient(0, 0, 0, 320);
    orangeGradient.addColorStop(0, 'rgba(255, 122, 0, 0.35)');
    orangeGradient.addColorStop(0.5, 'rgba(254, 215, 170, 0.12)');
    orangeGradient.addColorStop(1, 'rgba(255, 255, 255, 0.00)');

    var skyGradient = ctx.createLinearGradient(0, 0, 0, 320);
    skyGradient.addColorStop(0, 'rgba(14, 165, 233, 0.32)');
    skyGradient.addColorStop(0.5, 'rgba(186, 230, 253, 0.10)');
    skyGradient.addColorStop(1, 'rgba(255, 255, 255, 0.00)');

    var emeraldGradient = ctx.createLinearGradient(0, 0, 0, 320);
    emeraldGradient.addColorStop(0, 'rgba(16, 185, 129, 0.32)');
    emeraldGradient.addColorStop(0.5, 'rgba(167, 243, 208, 0.10)');
    emeraldGradient.addColorStop(1, 'rgba(255, 255, 255, 0.00)');

    // Datasets configuration — Ultra-Smooth Spline Curve with Monotone Interpolation
    var totalDatasets = [
        {
            label: 'Total Omset',
            data: revenueData,
            borderColor: '#f97316',
            backgroundColor: orangeGradient,
            borderWidth: 3.2,
            fill: true,
            tension: 0.45,
            cubicInterpolationMode: 'monotone',
            borderCapStyle: 'round',
            borderJoinStyle: 'round',
            pointRadius: 0,
            pointHoverRadius: 7,
            pointHoverBackgroundColor: '#f97316',
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
            borderColor: '#0ea5e9',
            backgroundColor: skyGradient,
            borderWidth: 2.8,
            fill: true,
            tension: 0.45,
            cubicInterpolationMode: 'monotone',
            borderCapStyle: 'round',
            borderJoinStyle: 'round',
            pointRadius: 0,
            pointHoverRadius: 6.5,
            pointHoverBackgroundColor: '#0ea5e9',
            pointHoverBorderColor: '#ffffff',
            pointHoverBorderWidth: 2.5,
            pointHitRadius: 25,
            yAxisID: 'y'
        },
        {
            label: 'Tunai (COD)',
            data: cashData,
            borderColor: '#10b981',
            backgroundColor: emeraldGradient,
            borderWidth: 2.8,
            fill: true,
            tension: 0.45,
            cubicInterpolationMode: 'monotone',
            borderCapStyle: 'round',
            borderJoinStyle: 'round',
            pointRadius: 0,
            pointHoverRadius: 6.5,
            pointHoverBackgroundColor: '#10b981',
            pointHoverBorderColor: '#ffffff',
            pointHoverBorderWidth: 2.5,
            pointHitRadius: 25,
            yAxisID: 'y'
        }
    ];

    // Stand Comparison Bar Dataset — Bright radiant warm gradient bars with rounded corners
    var standDatasets = [
        {
            type: 'bar',
            label: 'Omset Stand',
            data: standRevenueData,
            backgroundColor: function(context) {
                var chart = context.chart;
                var c = chart.ctx;
                var area = chart.chartArea;
                if (!area) {
                    return '#fb923c';
                }
                var grad = c.createLinearGradient(0, area.top, 0, area.bottom);
                grad.addColorStop(0, '#fb923c');   // bright radiant warm orange-400 at top
                grad.addColorStop(1, '#f97316');   // vivid warm orange-500 at bottom
                return grad;
            },
            hoverBackgroundColor: '#ff8a3d',
            borderColor: '#f97316',
            borderWidth: 1.5,
            borderRadius: {
                topLeft: 8,
                topRight: 8,
                bottomLeft: 0,
                bottomRight: 0
            },
            borderSkipped: false,
            barPercentage: 0.50,
            categoryPercentage: 0.65,
            maxBarThickness: 46,
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
            layout: {
                padding: {
                    top: 14,
                    right: 18,
                    bottom: 6,
                    left: 10
                }
            },
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
                    backgroundColor: '#1e293b',
                    borderColor: '#334155',
                    borderWidth: 1,
                    titleColor: '#f8fafc',
                    titleFont: {
                        family: "'Plus Jakarta Sans', sans-serif",
                        size: 12,
                        weight: 'bold'
                    },
                    bodyColor: '#e2e8f0',
                    bodyFont: {
                        family: "'Plus Jakarta Sans', sans-serif",
                        size: 11
                    },
                    padding: 12,
                    cornerRadius: 12,
                    displayColors: true,
                    boxPadding: 4,
                    callbacks: {
                        title: function(items) {
                            if (!items || !items.length) return '';
                            var currentMode = window['omsetChartMode_' + uniqueId] || 'total';
                            if (currentMode === 'stands') {
                                return items[0].label;
                            }
                            var index = items[0].dataIndex;
                            return fullLabels[index] || items[0].label;
                        },
                        label: function(context) {
                            var currentMode = window['omsetChartMode_' + uniqueId] || 'total';
                            if (currentMode === 'stands') {
                                var index = context.dataIndex;
                                var count = standOrdersData[index] || 0;
                                var share = standSharesData[index] || 0;
                                return [
                                    ' Omset: ' + formatRupiahFull(context.parsed.y) + ' (' + share + '% dari total)',
                                    ' Volume: ' + count + ' pesanan selesai'
                                ];
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
                    offset: false,
                    border: {
                        display: true,
                        color: '#e2e8f0'
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
                        padding: 10,
                        maxRotation: 0,
                        autoSkip: true,
                        maxTicksLimit: 12
                    }
                },
                y: {
                    beginAtZero: true,
                    border: {
                        display: true,
                        color: '#e2e8f0',
                        dash: [4, 4]
                    },
                    grid: {
                        color: '#f8fafc',
                        lineWidth: 1
                    },
                    ticks: {
                        color: '#64748b',
                        font: {
                            family: "'Plus Jakarta Sans', sans-serif",
                            size: 10.5,
                            weight: '600'
                        },
                        padding: 14,
                        callback: function(val) {
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
        var btnStands = document.getElementById('btnModeStands_' + id);
        var chartDesc = document.getElementById('chartDesc_' + id);

        [btnTotal, btnSplit, btnStands].forEach(function(b) {
            if (!b) return;
            b.classList.remove('bg-white', 'text-orange-600', 'shadow-2xs', 'font-bold');
            b.classList.add('text-slate-600', 'hover:text-slate-900', 'font-semibold');
        });

        if (mode === 'total') {
            if (btnTotal) {
                btnTotal.classList.add('bg-white', 'text-orange-600', 'shadow-2xs', 'font-bold');
                btnTotal.classList.remove('text-slate-600', 'hover:text-slate-900', 'font-semibold');
            }
            if (chartDesc) {
                chartDesc.textContent = 'Visualisasi pendapatan omset pesanan selesai kantin';
            }
            chart.config.type = 'line';
            chart.data.labels = chartLabels;
            chart.data.datasets = totalDatasets;
            chart.options.scales.x.offset = false;
            chart.options.scales.x.ticks.autoSkip = true;
            chart.options.scales.x.ticks.maxRotation = 0;
        } else if (mode === 'split') {
            if (btnSplit) {
                btnSplit.classList.add('bg-white', 'text-orange-600', 'shadow-2xs', 'font-bold');
                btnSplit.classList.remove('text-slate-600', 'hover:text-slate-900', 'font-semibold');
            }
            if (chartDesc) {
                chartDesc.textContent = 'Perbandingan pembayaran non-tunai (QRIS) vs uang tunai (COD)';
            }
            chart.config.type = 'line';
            chart.data.labels = chartLabels;
            chart.data.datasets = splitDatasets;
            chart.options.scales.x.offset = false;
            chart.options.scales.x.ticks.autoSkip = true;
            chart.options.scales.x.ticks.maxRotation = 0;
        } else if (mode === 'stands') {
            if (btnStands) {
                btnStands.classList.add('bg-white', 'text-orange-600', 'shadow-2xs', 'font-bold');
                btnStands.classList.remove('text-slate-600', 'hover:text-slate-900', 'font-semibold');
            }
            if (chartDesc) {
                chartDesc.textContent = 'Perbandingan performa omset masing-masing stand kantin periode ' + @json($periodeLabel);
            }
            chart.config.type = 'bar';
            chart.data.labels = standLabels;
            chart.data.datasets = standDatasets;
            chart.options.scales.x.offset = true;
            chart.options.scales.x.ticks.autoSkip = false;
            chart.options.scales.x.ticks.maxRotation = window.innerWidth < 640 ? 25 : 0;
        }

        chart.update();
    };
})();
</script>
@endpush
