@extends('layouts.admin')

@section('title', 'Rekap Hasil Pilahan')

@section('content')
    <div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <h1>Rekap Hasil Pilahan</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Rekap Hasil Pilahan</li>
                </ol>
            </nav>
        </div>

        <div class="d-flex align-items-center gap-3 flex-wrap">
            <form id="filterForm" action="{{ route('admin.statistik-komparatif.hasil-pilahan') }}" method="GET" class="d-flex align-items-center gap-3 flex-wrap">
                {{-- Period Mode Selector --}}
                <div class="period-mode-selector shadow-sm">
                    @foreach(['daily' => 'Harian', 'weekly' => 'Mingguan', 'monthly' => 'Bulanan', 'yearly' => 'Tahunan'] as $mode => $label)
                        <button type="button" class="period-btn {{ $period === $mode ? 'active' : '' }}" data-mode="{{ $mode }}" onclick="setPeriod('{{ $mode }}')">
                            {{ $label }}
                        </button>
                    @endforeach
                    <input type="hidden" name="period" id="periodInput" value="{{ $period }}">
                </div>

                {{-- Date Selectors --}}
                <div class="period-selector shadow-sm">
                    <i class="cil-calendar text-primary me-2"></i>
                    @if($period === 'daily')
                        <select name="month" onchange="this.form.submit()">
                            @foreach($months as $m => $name)
                                <option value="{{ sprintf('%02d', $m) }}" {{ $selectedMonth == $m ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                        <div class="divider"></div>
                    @endif
                    @if($period !== 'yearly')
                        <select name="year" onchange="this.form.submit()">
                            @foreach($years as $y)
                                <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    @else
                        <span class="fw-bold text-muted px-2">Semua Tahun</span>
                    @endif
                </div>

                {{-- Waste Category Filter --}}
                <div class="period-selector shadow-sm">
                    <i class="cil-filter text-success me-2"></i>
                    <select name="waste_category_id" onchange="this.form.submit()">
                        <option value="all" {{ $selectedCategory === 'all' ? 'selected' : '' }}>Semua Jenis</option>
                        @foreach($wasteCategories as $cat)
                            <option value="{{ $cat->id }}" {{ $selectedCategory == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card stat-success h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="stat-icon bg-success-light me-3">
                        <i class="cil-filter"></i>
                    </div>
                    <div>
                        <div class="text-body-secondary text-uppercase fw-semibold small">Total Tonase Pilahan</div>
                        <div class="fs-4 fw-bold text-success">{{ number_format($totalTonase, 2, ',', '.') }} kg</div>
                        <div class="text-muted small">({{ number_format($totalTonase / 1000, 2, ',', '.') }} Ton)</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card stat-primary h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="stat-icon bg-primary-light me-3">
                        <i class="cil-layers"></i>
                    </div>
                    <div>
                        <div class="text-body-secondary text-uppercase fw-semibold small">Total Bal</div>
                        <div class="fs-4 fw-bold text-primary">{{ number_format($totalBal, 0, ',', '.') }}</div>
                        <div class="text-muted small">Bal/Kemasan</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card stat-info h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="stat-icon bg-info-light me-3">
                        <i class="cil-list-rich"></i>
                    </div>
                    <div>
                        <div class="text-body-secondary text-uppercase fw-semibold small">Periode Aktif</div>
                        <div class="fs-4 fw-bold text-info">{{ $periodsWithData }}</div>
                        <div class="text-muted small">
                            @switch($period)
                                @case('daily') Hari dengan data @break
                                @case('weekly') Minggu dengan data @break
                                @case('monthly') Bulan dengan data @break
                                @case('yearly') Tahun dengan data @break
                            @endswitch
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card stat-warning h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="stat-icon bg-warning-light me-3">
                        <i class="cil-speedometer"></i>
                    </div>
                    <div>
                        <div class="text-body-secondary text-uppercase fw-semibold small">Rata-rata per Periode</div>
                        <div class="fs-4 fw-bold text-warning">{{ number_format($avgPerPeriod, 2, ',', '.') }} kg</div>
                        <div class="text-muted small">
                            @switch($period)
                                @case('daily') Rata-rata per Hari @break
                                @case('weekly') Rata-rata per Minggu @break
                                @case('monthly') Rata-rata per Bulan @break
                                @case('yearly') Rata-rata per Tahun @break
                            @endswitch
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Chart --}}
    <div class="card mb-4">
        <div class="card-header bg-white border-bottom-0 pt-4 pb-2 d-flex justify-content-between align-items-start flex-wrap gap-2">
            <div>
                <h5 class="card-title mb-0 fw-bold">Grafik Rekap Hasil Pilahan</h5>
                <p class="text-muted small mb-0">
                    Breakdown tonase per kategori sampah —
                    @switch($period)
                        @case('daily')
                            {{ $months[(int)$selectedMonth] ?? '' }} {{ $selectedYear }}
                        @break
                        @case('weekly')
                        @case('monthly')
                            Tahun {{ $selectedYear }}
                        @break
                        @case('yearly')
                            Seluruh Periode
                        @break
                    @endswitch
                    @if($selectedCategory !== 'all')
                        — Filter: {{ $wasteCategories->firstWhere('id', $selectedCategory)->name ?? '' }}
                    @endif
                </p>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-sm btn-outline-secondary" id="toggleChartType" onclick="toggleChart()">
                    <i class="cil-chart-line me-1"></i> Ganti ke Line
                </button>
            </div>
        </div>
        <div class="card-body">
            @if($totalTonase > 0)
                <div style="position: relative; height: 380px;">
                    <canvas id="pilahanChart"></canvas>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="cil-bar-chart text-muted mb-3" style="font-size: 3rem; opacity: 0.3;"></i>
                    <p class="text-muted">Tidak ada data untuk periode terpilih.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Tab switcher: Rekap per Kategori vs Rekap per Jenis --}}
    <div class="card">
        <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
            <ul class="nav nav-tabs card-header-tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active fw-bold" id="tab-kategori" data-coreui-toggle="tab" data-coreui-target="#tabKategori" type="button" role="tab">
                        <i class="cil-chart-pie me-1"></i> Rekap per Kategori
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold" id="tab-jenis" data-coreui-toggle="tab" data-coreui-target="#tabJenis" type="button" role="tab">
                        <i class="cil-list-rich me-1"></i> Rekap per Jenis Sampah
                    </button>
                </li>
            </ul>
        </div>
        <div class="tab-content">
            {{-- Tab 1: Rekap per Kategori (Organik/Anorganik/B3/Residu) --}}
            <div class="tab-pane fade show active" id="tabKategori" role="tabpanel">
                <div class="table-responsive p-3">
                    <table class="table table-hover table-striped align-middle border-top">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 60px;">No</th>
                                <th>{{ $headerLabel ?? 'Periode' }}</th>
                                @foreach($kategoriList as $kat)
                                    <th class="text-end">{{ $kat }} (kg)</th>
                                @endforeach
                                <th class="text-end fw-bold">Total (kg)</th>
                                <th class="text-end">Bal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $idx = 0; @endphp
                            @foreach($chartData as $row)
                                @if($row['total'] > 0)
                                    @php $idx++; @endphp
                                    <tr>
                                        <td class="text-center text-muted fw-bold">{{ $idx }}</td>
                                        <td><strong>{{ $row['period_label'] }}</strong></td>
                                        <td class="text-end font-monospace text-success">{{ number_format($row['Organik'], 2, ',', '.') }}</td>
                                        <td class="text-end font-monospace text-primary">{{ number_format($row['Anorganik'], 2, ',', '.') }}</td>
                                        <td class="text-end font-monospace text-danger">{{ number_format($row['B3'], 2, ',', '.') }}</td>
                                        <td class="text-end font-monospace text-secondary">{{ number_format($row['Residu'], 2, ',', '.') }}</td>
                                        <td class="text-end font-monospace fw-bold">{{ number_format($row['total'], 2, ',', '.') }}</td>
                                        <td class="text-end font-monospace">{{ number_format($row['bal'], 0, ',', '.') }}</td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-dark fw-bold">
                                <td colspan="2" class="text-center">TOTAL</td>
                                @foreach($kategoriList as $kat)
                                    <td class="text-end font-monospace">
                                        {{ number_format(collect($chartData)->sum($kat), 2, ',', '.') }}
                                    </td>
                                @endforeach
                                <td class="text-end font-monospace">{{ number_format($totalTonase, 2, ',', '.') }}</td>
                                <td class="text-end font-monospace">{{ number_format($totalBal, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                    @if($totalTonase == 0)
                        <p class="text-center text-muted py-3">Tidak ada data untuk periode terpilih.</p>
                    @endif
                </div>
            </div>

            {{-- Tab 2: Rekap per Jenis Sampah (detail) --}}
            <div class="tab-pane fade" id="tabJenis" role="tabpanel">
                <div class="table-responsive p-3">
                    <table class="table table-hover table-striped align-middle border-top">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 60px;">No</th>
                                <th>{{ $headerLabel ?? 'Periode' }}</th>
                                @foreach($allJenis as $jenis)
                                    <th class="text-end">{{ $jenis }} (kg)</th>
                                @endforeach
                                <th class="text-end fw-bold">Total (kg)</th>
                                <th class="text-end">Bal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $idx2 = 0; @endphp
                            @foreach($chartData as $row)
                                @if($row['total'] > 0)
                                    @php $idx2++; @endphp
                                    <tr>
                                        <td class="text-center text-muted fw-bold">{{ $idx2 }}</td>
                                        <td><strong>{{ $row['period_label'] }}</strong></td>
                                        @foreach($allJenis as $jenis)
                                            <td class="text-end font-monospace">
                                                {{ number_format($row['jenis_breakdown'][$jenis]['tonase'] ?? 0, 2, ',', '.') }}
                                            </td>
                                        @endforeach
                                        <td class="text-end font-monospace fw-bold">{{ number_format($row['total'], 2, ',', '.') }}</td>
                                        <td class="text-end font-monospace">{{ number_format($row['bal'], 0, ',', '.') }}</td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-dark fw-bold">
                                <td colspan="2" class="text-center">TOTAL</td>
                                @foreach($allJenis as $jenis)
                                    <td class="text-end font-monospace">
                                        @php
                                            $jenisTotal = collect($chartData)->sum(function ($item) use ($jenis) {
                                                return $item['jenis_breakdown'][$jenis]['tonase'] ?? 0;
                                            });
                                        @endphp
                                        {{ number_format($jenisTotal, 2, ',', '.') }}
                                    </td>
                                @endforeach
                                <td class="text-end font-monospace">{{ number_format($totalTonase, 2, ',', '.') }}</td>
                                <td class="text-end font-monospace">{{ number_format($totalBal, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                    @if(count($allJenis) === 0)
                        <p class="text-center text-muted py-3">Tidak ada data jenis sampah untuk periode terpilih.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .period-mode-selector {
            display: flex;
            align-items: center;
            background: #ffffff;
            border-radius: 50px;
            border: 1px solid rgba(0, 0, 0, 0.08);
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .period-mode-selector:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
            border-color: var(--cui-primary);
        }
        .period-btn {
            border: none;
            background: transparent;
            padding: 0.45rem 1rem;
            font-weight: 600;
            font-size: 0.85rem;
            color: #64748b;
            cursor: pointer;
            transition: all 0.2s;
            white-space: nowrap;
        }
        .period-btn:hover {
            background: rgba(var(--cui-primary-rgb), 0.08);
            color: var(--cui-primary);
        }
        .period-btn.active {
            background: var(--cui-primary);
            color: #fff;
        }
        .period-btn:first-child {
            border-radius: 50px 0 0 50px;
        }
        .period-btn:last-of-type {
            border-radius: 0 50px 50px 0;
        }

        .period-selector-container {
            transition: all 0.3s ease;
        }
        .period-selector {
            display: flex;
            align-items: center;
            background: #ffffff;
            padding: 0.4rem 1.25rem;
            border-radius: 50px;
            border: 1px solid rgba(0, 0, 0, 0.08);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .period-selector:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
            border-color: var(--cui-primary);
        }
        .period-selector select {
            border: none;
            background: transparent;
            font-weight: 700;
            color: #334155;
            cursor: pointer;
            outline: none;
            padding: 0.25rem 0.5rem;
            font-size: 0.9rem;
        }
        .period-selector .divider {
            width: 1px;
            height: 18px;
            background: rgba(0, 0, 0, 0.1);
            margin: 0 0.5rem;
        }

        /* Dark mode */
        [data-coreui-theme="dark"] .period-mode-selector {
            background: #1e293b;
            border-color: rgba(255, 255, 255, 0.1);
        }
        [data-coreui-theme="dark"] .period-btn {
            color: #94a3b8;
        }
        [data-coreui-theme="dark"] .period-btn:hover {
            background: rgba(99, 102, 241, 0.15);
            color: #818cf8;
        }
        [data-coreui-theme="dark"] .period-btn.active {
            background: var(--cui-primary);
            color: #fff;
        }
        [data-coreui-theme="dark"] .period-selector {
            background: #1e293b;
            border-color: rgba(255, 255, 255, 0.1);
        }
        [data-coreui-theme="dark"] .period-selector select {
            color: #f1f5f9;
        }
        [data-coreui-theme="dark"] .period-selector .divider {
            background: rgba(255, 255, 255, 0.1);
        }

        .nav-tabs .nav-link {
            border: none;
            color: #64748b;
            padding: 0.75rem 1.25rem;
        }
        .nav-tabs .nav-link.active {
            color: var(--cui-primary);
            border-bottom: 3px solid var(--cui-primary);
            background: transparent;
        }
    </style>
@endpush

@push('scripts')
    <script>
        function setPeriod(mode) {
            document.getElementById('periodInput').value = mode;
            document.getElementById('filterForm').submit();
        }

        @if($totalTonase > 0)
        let pilahanChart = null;
        let currentChartType = 'bar';

        function toggleChart() {
            currentChartType = currentChartType === 'bar' ? 'line' : 'bar';
            const btn = document.getElementById('toggleChartType');
            btn.innerHTML = currentChartType === 'bar'
                ? '<i class="cil-chart-line me-1"></i> Ganti ke Line'
                : '<i class="cil-bar-chart me-1"></i> Ganti ke Bar';
            renderChart();
        }

        function renderChart() {
            const data = @json($chartData);
            const labels = data.map(d => String(d.period_label));

            const isDark = document.documentElement.getAttribute('data-coreui-theme') === 'dark';
            const gridColor = isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)';
            const textColor = isDark ? '#e6eef8' : '#374151';

            const kategoriColors = {
                'Organik':    { bg: 'rgba(25, 135, 84, 0.75)',  border: '#198754' },
                'Anorganik':  { bg: 'rgba(13, 110, 253, 0.75)', border: '#0d6efd' },
                'B3':         { bg: 'rgba(220, 53, 69, 0.75)',  border: '#dc3545' },
                'Residu':     { bg: 'rgba(108, 117, 125, 0.75)', border: '#6c757d' }
            };

            const kategoriList = @json($kategoriList);
            const datasets = kategoriList.map(kat => ({
                label: kat + ' (kg)',
                data: data.map(d => d[kat] || 0),
                backgroundColor: kategoriColors[kat]?.bg || 'rgba(100,100,100,0.5)',
                borderColor: kategoriColors[kat]?.border || '#666',
                borderWidth: currentChartType === 'line' ? 2.5 : 1.5,
                borderRadius: currentChartType === 'bar' ? 4 : 0,
                barPercentage: 0.7,
                categoryPercentage: 0.8,
                fill: currentChartType === 'line' ? false : undefined,
                tension: currentChartType === 'line' ? 0.3 : 0,
                pointRadius: currentChartType === 'line' ? 3 : 0,
                pointHoverRadius: currentChartType === 'line' ? 6 : 0,
            }));

            const ctx = document.getElementById('pilahanChart').getContext('2d');

            if (pilahanChart) {
                pilahanChart.destroy();
            }

            pilahanChart = new Chart(ctx, {
                type: currentChartType,
                data: { labels, datasets },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: textColor,
                                usePointStyle: true,
                                padding: 15,
                                font: { weight: 'bold' }
                            }
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ' + context.parsed.y.toLocaleString('id-ID') + ' kg';
                                },
                                footer: function(tooltipItems) {
                                    let sum = 0;
                                    tooltipItems.forEach(item => { sum += item.parsed.y; });
                                    return 'Total: ' + sum.toLocaleString('id-ID') + ' kg';
                                }
                            }
                        }
                    },
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    scales: {
                        x: {
                            stacked: currentChartType === 'bar',
                            grid: { display: false },
                            ticks: { color: textColor }
                        },
                        y: {
                            stacked: currentChartType === 'bar',
                            grid: { color: gridColor },
                            ticks: {
                                color: textColor,
                                callback: function(value) {
                                    return value.toLocaleString('id-ID') + ' kg';
                                }
                            }
                        }
                    }
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            renderChart();
        });
        @endif
    </script>
@endpush
