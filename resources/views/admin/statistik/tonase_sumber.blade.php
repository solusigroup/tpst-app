@extends('layouts.admin')

@section('title', 'Statistik Tonase per Sumber')

@section('content')
    <div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <h1>Tonase per Sumber</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Tonase per Sumber</li>
                </ol>
            </nav>
        </div>
        
        <div class="d-flex align-items-center gap-3">
            <form id="filterForm" action="{{ route('admin.statistik-komparatif.tonase-sumber') }}" method="GET" class="period-selector-container">
                <div class="period-selector shadow-sm">
                    <i class="cil-layers text-primary me-2"></i>
                    <select name="sumber" onchange="this.form.submit()">
                        @foreach($sumberLabel as $key => $label)
                            <option value="{{ $key }}" {{ $sumber == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    <div class="divider"></div>
                    <i class="cil-calendar text-primary me-2"></i>
                    <select name="year" onchange="this.form.submit()">
                        @foreach($years as $y)
                            <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                        @endforeach
                    </select>
                    <div class="divider"></div>
                    <select name="compare_year" onchange="this.form.submit()">
                        <option value="">-- Tanpa Perbandingan --</option>
                        @foreach($years as $y)
                            @if($y != $selectedYear)
                                <option value="{{ $y }}" {{ isset($compareYear) && $compareYear == $y ? 'selected' : '' }}>Bandingkan dengan {{ $y }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
    </div>

    @if($compareYear)
        {{-- Stats Cards for Comparison Mode --}}
        <div class="row g-4 mb-4">
            <div class="col-sm-6 col-xl-3">
                <div class="card stat-card stat-primary h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-primary-light me-3">
                            <i class="cil-chart"></i>
                        </div>
                        <div>
                            <div class="text-body-secondary text-uppercase fw-semibold small">Tonase {{ $selectedYear }} ({{ $sumberLabel[$sumber] }})</div>
                            <div class="fs-4 fw-bold text-primary">{{ number_format($totalTonaseTon, 2, ',', '.') }} Ton</div>
                            <div class="text-muted small">({{ number_format($totalTonase, 2, ',', '.') }} kg)</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card stat-card stat-info h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-info-light me-3">
                            <i class="cil-chart"></i>
                        </div>
                        <div>
                            <div class="text-body-secondary text-uppercase fw-semibold small">Tonase {{ $compareYear }} ({{ $sumberLabel[$sumber] }})</div>
                            <div class="fs-4 fw-bold text-info">{{ number_format($totalCompareTon, 2, ',', '.') }} Ton</div>
                            <div class="text-muted small">({{ number_format($totalCompareTonase, 2, ',', '.') }} kg)</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card stat-card {{ $totalDiff >= 0 ? 'stat-success' : 'stat-danger' }} h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon {{ $totalDiff >= 0 ? 'bg-success-light text-success' : 'bg-danger-light text-danger' }} me-3">
                            <i class="{{ $totalDiff >= 0 ? 'cil-arrow-circle-top' : 'cil-arrow-circle-bottom' }}"></i>
                        </div>
                        <div>
                            <div class="text-body-secondary text-uppercase fw-semibold small">Selisih Tonnage</div>
                            <div class="fs-4 fw-bold {{ $totalDiff >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $totalDiff >= 0 ? '+' : '' }}{{ number_format($totalDiff / 1000, 2, ',', '.') }} Ton
                            </div>
                            <div class="text-muted small">Tahun {{ $selectedYear }} vs {{ $compareYear }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card stat-card {{ $totalDiffPercent >= 0 ? 'stat-success' : 'stat-danger' }} h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon {{ $totalDiffPercent >= 0 ? 'bg-success-light text-success' : 'bg-danger-light text-danger' }} me-3">
                            <i class="cil-chart-pie"></i>
                        </div>
                        <div>
                            <div class="text-body-secondary text-uppercase fw-semibold small">Pertumbuhan</div>
                            <div class="fs-4 fw-bold {{ $totalDiffPercent >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $totalDiffPercent >= 0 ? '+' : '' }}{{ number_format($totalDiffPercent, 1, ',', '.') }}%
                            </div>
                            <div class="text-muted small">Persentase Kenaikan/Penurunan</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        {{-- Stats Cards for Normal Mode --}}
        <div class="row g-4 mb-4">
            <div class="col-sm-6 col-xl-3">
                <div class="card stat-card stat-primary h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-primary-light me-3">
                            <i class="cil-chart"></i>
                        </div>
                        <div>
                            <div class="text-body-secondary text-uppercase fw-semibold small">Total Tonase {{ $selectedYear }}</div>
                            <div class="fs-4 fw-bold text-primary">{{ number_format($totalTonaseTon, 2, ',', '.') }} Ton</div>
                            <div class="text-muted small">({{ number_format($totalTonase, 2, ',', '.') }} kg)</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card stat-card stat-info h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-info-light me-3">
                            <i class="cil-layers"></i>
                        </div>
                        <div>
                            <div class="text-body-secondary text-uppercase fw-semibold small">Sumber</div>
                            <div class="fs-4 fw-bold text-info">{{ $sumberLabel[$sumber] }}</div>
                            <div class="text-muted small">Filter Aktif</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card stat-card stat-success h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-success-light me-3">
                            <i class="cil-speedometer"></i>
                        </div>
                        <div>
                            <div class="text-body-secondary text-uppercase fw-semibold small">Rata-rata per Bulan</div>
                            <div class="fs-4 fw-bold text-success">{{ number_format($avgTonasePerMonth / 1000, 2, ',', '.') }} Ton</div>
                            <div class="text-muted small">({{ number_format($avgTonasePerMonth, 2, ',', '.') }} kg)</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card stat-card stat-warning h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-warning-light me-3">
                            <i class="cil-calendar"></i>
                        </div>
                        <div>
                            <div class="text-body-secondary text-uppercase fw-semibold small">Bulan Aktif</div>
                            <div class="fs-4 fw-bold text-warning">{{ collect($chartData)->filter(fn($r) => $r['tonase_kg'] > 0)->count() }} Bulan</div>
                            <div class="text-muted small">Bulan dengan data tonase</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Main Chart --}}
    <div class="card mb-4">
        <div class="card-header bg-white border-bottom-0 pt-4 pb-2">
            @if($compareYear)
                <h5 class="card-title mb-0 fw-bold">Grafik Perbandingan Tonase ({{ $selectedYear }} vs {{ $compareYear }}) — {{ $sumberLabel[$sumber] }}</h5>
                <p class="text-muted small">Visualisasi bulanan membandingkan tonase sampah masuk antara tahun {{ $selectedYear }} dengan tahun {{ $compareYear }}</p>
            @else
                <h5 class="card-title mb-0 fw-bold">Grafik Tonase per Bulan ({{ $selectedYear }}) — {{ $sumberLabel[$sumber] }}</h5>
                <p class="text-muted small">Visualisasi bulanan tonase sampah masuk berdasarkan sumber: {{ $sumberLabel[$sumber] }}</p>
            @endif
        </div>
        <div class="card-body">
            <div style="position: relative; height: 350px;">
                <canvas id="tonaseChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Data Table --}}
    <div class="card">
        <div class="card-header bg-white border-bottom-0 pt-4 pb-2 d-flex flex-wrap justify-content-between align-items-center">
            <div>
                <h5 class="card-title mb-0 fw-bold">
                    Tabel Rincian Bulanan 
                    @if($compareYear)
                        Perbandingan Tahun {{ $selectedYear }} vs {{ $compareYear }}
                    @else
                        Tahun {{ $selectedYear }}
                    @endif
                    — {{ $sumberLabel[$sumber] }}
                </h5>
            </div>
            <div class="d-flex gap-2 mt-2 mt-md-0">
                <a href="{{ route('admin.statistik-komparatif.tonase-sumber.export-pdf', request()->query()) }}" 
                   class="btn btn-sm btn-outline-danger" target="_blank">
                    <i class="cil-file me-1"></i> Export PDF
                </a>
                <a href="{{ route('admin.statistik-komparatif.tonase-sumber.export-excel', request()->query()) }}" 
                   class="btn btn-sm btn-outline-success">
                    <i class="cil-spreadsheet me-1"></i> Export Excel
                </a>
            </div>
        </div>
        <div class="table-responsive p-3">
            @if($compareYear)
                <table class="table table-hover table-striped align-middle border-top">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 60px;">No</th>
                            <th>Bulan</th>
                            <th class="text-end">Tonase {{ $selectedYear }} (kg)</th>
                            <th class="text-end">Tonase {{ $selectedYear }} (Ton)</th>
                            <th class="text-end">Tonase {{ $compareYear }} (kg)</th>
                            <th class="text-end">Tonase {{ $compareYear }} (Ton)</th>
                            <th class="text-end">Selisih (kg)</th>
                            <th class="text-center" style="width: 150px;">Pertumbuhan (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($chartData as $index => $row)
                            <tr>
                                <td class="text-center text-muted fw-bold">{{ $index + 1 }}</td>
                                <td><strong>{{ $row['month_name'] }}</strong></td>
                                <td class="text-end font-monospace text-primary fw-semibold">{{ number_format($row['tonase_kg'], 2, ',', '.') }}</td>
                                <td class="text-end font-monospace text-primary">{{ number_format($row['tonase_ton'], 3, ',', '.') }}</td>
                                <td class="text-end font-monospace text-muted">{{ number_format($row['compare_kg'], 2, ',', '.') }}</td>
                                <td class="text-end font-monospace text-muted">{{ number_format($row['compare_ton'], 3, ',', '.') }}</td>
                                <td class="text-end font-monospace {{ $row['diff'] >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $row['diff'] >= 0 ? '+' : '' }}{{ number_format($row['diff'], 2, ',', '.') }}
                                </td>
                                <td class="text-center">
                                    @if($row['diff_percent'] >= 0)
                                        <span class="badge bg-success text-white fw-bold px-2 py-1">+{{ number_format($row['diff_percent'], 1, ',', '.') }}%</span>
                                    @else
                                        <span class="badge bg-danger text-white fw-bold px-2 py-1">{{ number_format($row['diff_percent'], 1, ',', '.') }}%</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light fw-bold">
                        <tr>
                            <td colspan="2" class="text-center">TOTAL</td>
                            <td class="text-end font-monospace text-primary">{{ number_format($totalTonase, 2, ',', '.') }}</td>
                            <td class="text-end font-monospace text-primary">{{ number_format($totalTonaseTon, 3, ',', '.') }}</td>
                            <td class="text-end font-monospace text-muted">{{ number_format($totalCompareTonase, 2, ',', '.') }}</td>
                            <td class="text-end font-monospace text-muted">{{ number_format($totalCompareTon, 3, ',', '.') }}</td>
                            <td class="text-end font-monospace {{ $totalDiff >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $totalDiff >= 0 ? '+' : '' }}{{ number_format($totalDiff, 2, ',', '.') }}
                            </td>
                            <td class="text-center">
                                @if($totalDiffPercent >= 0)
                                    <span class="badge bg-success text-white fw-bold px-2 py-1">+{{ number_format($totalDiffPercent, 1, ',', '.') }}%</span>
                                @else
                                    <span class="badge bg-danger text-white fw-bold px-2 py-1">{{ number_format($totalDiffPercent, 1, ',', '.') }}%</span>
                                @endif
                            </td>
                        </tr>
                    </tfoot>
                </table>
            @else
                <table class="table table-hover table-striped align-middle border-top">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 60px;">No</th>
                            <th>Bulan</th>
                            <th class="text-end">Tonase (kg)</th>
                            <th class="text-end">Tonase (Ton)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($chartData as $index => $row)
                            <tr>
                                <td class="text-center text-muted fw-bold">{{ $index + 1 }}</td>
                                <td><strong>{{ $row['month_name'] }}</strong></td>
                                <td class="text-end font-monospace">{{ number_format($row['tonase_kg'], 2, ',', '.') }}</td>
                                <td class="text-end font-monospace text-primary fw-semibold">{{ number_format($row['tonase_ton'], 3, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light fw-bold">
                        <tr>
                            <td colspan="2" class="text-center">TOTAL</td>
                            <td class="text-end font-monospace">{{ number_format($totalTonase, 2, ',', '.') }}</td>
                            <td class="text-end font-monospace text-primary">{{ number_format($totalTonaseTon, 3, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            @endif
        </div>
    </div>
@endsection

@push('styles')
    <style>
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

        .bg-success-light {
            background-color: rgba(25, 135, 84, 0.15) !important;
        }
        .bg-danger-light {
            background-color: rgba(220, 53, 69, 0.15) !important;
        }
        .bg-warning-light {
            background-color: rgba(255, 193, 7, 0.15) !important;
        }
        [data-coreui-theme="dark"] .bg-success-light {
            background-color: rgba(40, 167, 69, 0.2) !important;
        }
        [data-coreui-theme="dark"] .bg-danger-light {
            background-color: rgba(220, 53, 69, 0.2) !important;
        }
        [data-coreui-theme="dark"] .bg-warning-light {
            background-color: rgba(255, 193, 7, 0.2) !important;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const data = @json($chartData);
            
            const months = data.map(d => d.month_name);
            const tonaseVals = data.map(d => d.tonase_ton);
            
            const compareYear = @json($compareYear);
            const compareTonVals = data.map(d => d.compare_ton);

            const isDark = document.documentElement.getAttribute('data-coreui-theme') === 'dark';
            const gridColor = isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)';
            const textColor = isDark ? '#e6eef8' : '#374151';

            const ctx = document.getElementById('tonaseChart').getContext('2d');
            
            let datasets = [];
            let options = {};

            if (compareYear) {
                datasets = [
                    {
                        type: 'bar',
                        label: 'Tonase ' + @json($selectedYear) + ' (Ton)',
                        data: tonaseVals,
                        backgroundColor: 'rgba(59, 130, 246, 0.75)',
                        borderColor: 'rgba(59, 130, 246, 1)',
                        borderWidth: 1.5,
                        borderRadius: 6,
                        barPercentage: 0.7,
                        categoryPercentage: 0.6
                    },
                    {
                        type: 'bar',
                        label: 'Tonase ' + compareYear + ' (Ton)',
                        data: compareTonVals,
                        backgroundColor: 'rgba(100, 116, 139, 0.6)',
                        borderColor: 'rgba(100, 116, 139, 1)',
                        borderWidth: 1.5,
                        borderRadius: 6,
                        barPercentage: 0.7,
                        categoryPercentage: 0.6
                    }
                ];

                options = {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { color: textColor, usePointStyle: true, padding: 15 }
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ' + context.parsed.y.toLocaleString('id-ID', {minimumFractionDigits: 3}) + ' Ton';
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { color: textColor }
                        },
                        y: {
                            grid: { color: gridColor },
                            ticks: {
                                color: textColor,
                                callback: function(value) {
                                    return value.toLocaleString('id-ID') + ' Ton';
                                }
                            },
                            title: { display: true, text: 'Tonase (Ton)', color: textColor }
                        }
                    }
                };
            } else {
                // Single year view with gradient bar + trend line
                datasets = [
                    {
                        type: 'bar',
                        label: 'Tonase (Ton)',
                        data: tonaseVals,
                        backgroundColor: 'rgba(59, 130, 246, 0.75)',
                        borderColor: 'rgba(59, 130, 246, 1)',
                        borderWidth: 1.5,
                        borderRadius: 6,
                        barPercentage: 0.7,
                        categoryPercentage: 0.6
                    },
                    {
                        type: 'line',
                        label: 'Tren Tonase',
                        data: tonaseVals,
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        borderColor: '#10b981',
                        borderWidth: 2.5,
                        pointBackgroundColor: '#10b981',
                        pointBorderColor: '#fff',
                        pointHoverRadius: 7,
                        pointRadius: 4,
                        tension: 0.3,
                        fill: true
                    }
                ];

                options = {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { color: textColor, usePointStyle: true, padding: 15 }
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ' + context.parsed.y.toLocaleString('id-ID', {minimumFractionDigits: 3}) + ' Ton';
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { color: textColor }
                        },
                        y: {
                            grid: { color: gridColor },
                            ticks: {
                                color: textColor,
                                callback: function(value) {
                                    return value.toLocaleString('id-ID') + ' Ton';
                                }
                            },
                            title: { display: true, text: 'Tonase (Ton)', color: textColor }
                        }
                    }
                };
            }

            new Chart(ctx, {
                data: {
                    labels: months,
                    datasets: datasets
                },
                options: options
            });
        });
    </script>
@endpush
