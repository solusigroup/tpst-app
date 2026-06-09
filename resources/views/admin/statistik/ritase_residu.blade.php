@extends('layouts.admin')

@section('title', 'Statistik Ritase vs Residu')

@section('content')
    <div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <h1>Ritase vs Residu</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Statistik Ritase vs Residu</li>
                </ol>
            </nav>
        </div>
        
        <div class="d-flex align-items-center gap-3">
            <form action="{{ route('admin.statistik-komparatif.ritase-residu') }}" method="GET" class="period-selector-container">
                <div class="period-selector shadow-sm">
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
                            <i class="cil-truck"></i>
                        </div>
                        <div>
                            <div class="text-body-secondary text-uppercase fw-semibold small">Ritase Masuk (Tahun {{ $selectedYear }})</div>
                            <div class="fs-4 fw-bold text-primary">{{ number_format($totalRitase, 2, ',', '.') }} kg</div>
                            <div class="text-muted small">({{ number_format($totalRitase / 1000, 2, ',', '.') }} Ton)</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card stat-card stat-info h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-info-light me-3">
                            <i class="cil-truck"></i>
                        </div>
                        <div>
                            <div class="text-body-secondary text-uppercase fw-semibold small">Ritase Masuk (Tahun {{ $compareYear }})</div>
                            <div class="fs-4 fw-bold text-info">{{ number_format($totalCompareRitase, 2, ',', '.') }} kg</div>
                            <div class="text-muted small">({{ number_format($totalCompareRitase / 1000, 2, ',', '.') }} Ton)</div>
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
                                {{ $totalDiff >= 0 ? '+' : '' }}{{ number_format($totalDiff, 2, ',', '.') }} kg
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
                            <div class="text-body-secondary text-uppercase fw-semibold small">Pertumbuhan Ritase</div>
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
                            <i class="cil-truck"></i>
                        </div>
                        <div>
                            <div class="text-body-secondary text-uppercase fw-semibold small">Total Masuk (Ritase)</div>
                            <div class="fs-4 fw-bold text-primary">{{ number_format($totalRitase, 2, ',', '.') }} kg</div>
                            <div class="text-muted small">({{ number_format($totalRitase / 1000, 2, ',', '.') }} Ton)</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card stat-card stat-danger h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-danger-light me-3">
                            <i class="cil-trash"></i>
                        </div>
                        <div>
                            <div class="text-body-secondary text-uppercase fw-semibold small">Total Residu Keluar</div>
                            <div class="fs-4 fw-bold text-danger">{{ number_format($totalResidu, 2, ',', '.') }} kg</div>
                            <div class="text-muted small">({{ number_format($totalResidu / 1000, 2, ',', '.') }} Ton)</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card stat-card stat-success h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-success-light me-3">
                            <i class="cil-filter"></i>
                        </div>
                        <div>
                            <div class="text-body-secondary text-uppercase fw-semibold small">Volume Tereduksi</div>
                            <div class="fs-4 fw-bold text-success">{{ number_format($totalReduced, 2, ',', '.') }} kg</div>
                            <div class="text-muted small">({{ number_format($totalReduced / 1000, 2, ',', '.') }} Ton)</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card stat-card stat-info h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-info-light me-3">
                            <i class="cil-chart-pie"></i>
                        </div>
                        <div>
                            <div class="text-body-secondary text-uppercase fw-semibold small">Rata-rata Recovery Rate</div>
                            <div class="fs-4 fw-bold text-info">{{ number_format($avgRecoveryRate, 1, ',', '.') }}%</div>
                            <div class="text-muted small">Diverted from Landfill</div>
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
                <h5 class="card-title mb-0 fw-bold">Grafik Perbandingan Ritase Masuk (Tahun {{ $selectedYear }} vs {{ $compareYear }})</h5>
                <p class="text-muted small">Visualisasi bulanan membandingkan berat masuk sampah antara periode tahun {{ $selectedYear }} dengan tahun {{ $compareYear }}</p>
            @else
                <h5 class="card-title mb-0 fw-bold">Grafik Perbandingan Sampah Masuk vs Residu ({{ $selectedYear }})</h5>
                <p class="text-muted small">Visualisasi bulanan berat netto sampah ritase masuk dibandingkan residu akhir yang dikirim ke TPA</p>
            @endif
        </div>
        <div class="card-body">
            <div style="position: relative; height: 350px;">
                <canvas id="ritaseResiduChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Data Table --}}
    <div class="card">
        <div class="card-header bg-white border-bottom-0 pt-4 pb-2">
            <h5 class="card-title mb-0 fw-bold">
                Tabel Rincian Bulanan 
                @if($compareYear)
                    Perbandingan Tahun {{ $selectedYear }} vs {{ $compareYear }}
                @else
                    Tahun {{ $selectedYear }}
                @endif
            </h5>
        </div>
        <div class="table-responsive p-3">
            @if($compareYear)
                <table class="table table-hover table-striped align-middle border-top">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 80px;">No</th>
                            <th>Bulan</th>
                            <th class="text-end">Tonnage {{ $selectedYear }} (kg)</th>
                            <th class="text-end">Tonnage {{ $compareYear }} (kg)</th>
                            <th class="text-end">Selisih (kg)</th>
                            <th class="text-center" style="width: 180px;">Pertumbuhan (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($chartData as $index => $row)
                            <tr>
                                <td class="text-center text-muted fw-bold">{{ $index + 1 }}</td>
                                <td><strong>{{ $row['month_name'] }}</strong></td>
                                <td class="text-end font-monospace text-primary fw-semibold">{{ number_format($row['ritase'], 2, ',', '.') }}</td>
                                <td class="text-end font-monospace text-muted">{{ number_format($row['compare_ritase'], 2, ',', '.') }}</td>
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
                </table>
            @else
                <table class="table table-hover table-striped align-middle border-top">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 80px;">No</th>
                            <th>Bulan</th>
                            <th class="text-end">Ritase Masuk (kg)</th>
                            <th class="text-end">Residu Keluar (kg)</th>
                            <th class="text-end">Volume Tereduksi (kg)</th>
                            <th class="text-center" style="width: 200px;">Recovery Rate (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($chartData as $index => $row)
                            <tr>
                                <td class="text-center text-muted fw-bold">{{ $index + 1 }}</td>
                                <td><strong>{{ $row['month_name'] }}</strong></td>
                                <td class="text-end font-monospace">{{ number_format($row['ritase'], 2, ',', '.') }}</td>
                                <td class="text-end font-monospace text-danger">{{ number_format($row['residu'], 2, ',', '.') }}</td>
                                <td class="text-end font-monospace text-success fw-semibold">{{ number_format($row['reduced'], 2, ',', '.') }}</td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        <div class="progress w-100" style="height: 8px;" title="{{ $row['rate'] }}%">
                                            <div class="progress-bar bg-info" role="progressbar" style="width: {{ $row['rate'] }}%" aria-valuenow="{{ $row['rate'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <span class="badge bg-light text-dark fw-bold" style="min-width: 50px;">{{ $row['rate'] }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
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
        [data-coreui-theme="dark"] .bg-success-light {
            background-color: rgba(40, 167, 69, 0.2) !important;
        }
        [data-coreui-theme="dark"] .bg-danger-light {
            background-color: rgba(220, 53, 69, 0.2) !important;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const data = @json($chartData);
            
            const months = data.map(d => d.month_name);
            const ritaseVals = data.map(d => d.ritase);
            const residuVals = data.map(d => d.residu);
            const rateVals = data.map(d => d.rate);
            
            const compareYear = @json($compareYear);
            const compareRitaseVals = data.map(d => d.compare_ritase);

            const isDark = document.documentElement.getAttribute('data-coreui-theme') === 'dark';
            const gridColor = isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)';
            const textColor = isDark ? '#e6eef8' : '#374151';

            const ctx = document.getElementById('ritaseResiduChart').getContext('2d');
            
            let datasets = [];
            let options = {};

            if (compareYear) {
                // YoY comparison of Ritase
                datasets = [
                    {
                        type: 'bar',
                        label: 'Ritase ' + @json($selectedYear) + ' (kg)',
                        data: ritaseVals,
                        backgroundColor: 'rgba(59, 130, 246, 0.75)', // Vibrant blue
                        borderColor: 'rgba(59, 130, 246, 1)',
                        borderWidth: 1.5,
                        borderRadius: 6,
                        barPercentage: 0.7,
                        categoryPercentage: 0.6
                    },
                    {
                        type: 'bar',
                        label: 'Ritase ' + compareYear + ' (kg)',
                        data: compareRitaseVals,
                        backgroundColor: 'rgba(100, 116, 139, 0.6)', // Slate gray comparison
                        borderColor: 'rgba(100, 116, 139, 1)',
                        borderWidth: 1.5,
                        borderRadius: 6,
                        barPercentage: 0.7,
                        categoryPercentage: 0.6
                    },
                    {
                        type: 'line',
                        label: 'Residu ' + @json($selectedYear) + ' (kg)',
                        data: residuVals,
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        borderColor: 'rgba(239, 68, 68, 0.85)', // Red line
                        borderWidth: 2,
                        pointBackgroundColor: 'rgba(239, 68, 68, 1)',
                        pointBorderColor: '#fff',
                        pointRadius: 3,
                        tension: 0.25
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
                                    return context.dataset.label + ': ' + context.parsed.y.toLocaleString('id-ID') + ' kg';
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
                                    return value >= 1000 ? (value / 1000) + ' Ton' : value + ' kg';
                                }
                            }
                        }
                    }
                };
            } else {
                // Default Ritase vs Residu
                datasets = [
                    {
                        type: 'bar',
                        label: 'Ritase Masuk (kg)',
                        data: ritaseVals,
                        backgroundColor: 'rgba(59, 130, 246, 0.75)',
                        borderColor: 'rgba(59, 130, 246, 1)',
                        borderWidth: 1.5,
                        borderRadius: 6,
                        yAxisID: 'y',
                        barPercentage: 0.7,
                        categoryPercentage: 0.6
                    },
                    {
                        type: 'bar',
                        label: 'Residu Terbuang (kg)',
                        data: residuVals,
                        backgroundColor: 'rgba(239, 68, 68, 0.75)',
                        borderColor: 'rgba(239, 68, 68, 1)',
                        borderWidth: 1.5,
                        borderRadius: 6,
                        yAxisID: 'y',
                        barPercentage: 0.7,
                        categoryPercentage: 0.6
                    },
                    {
                        type: 'line',
                        label: 'Recovery Rate (%)',
                        data: rateVals,
                        backgroundColor: 'rgba(16, 185, 129, 0.2)',
                        borderColor: '#10b981',
                        borderWidth: 3,
                        pointBackgroundColor: '#10b981',
                        pointBorderColor: '#fff',
                        pointHoverRadius: 7,
                        pointRadius: 4,
                        tension: 0.3,
                        yAxisID: 'yPercent'
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
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.datasetIndex === 2) {
                                        label += context.parsed.y + '%';
                                    } else {
                                        label += context.parsed.y.toLocaleString('id-ID') + ' kg';
                                    }
                                    return label;
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
                            type: 'linear',
                            display: true,
                            position: 'left',
                            grid: { color: gridColor },
                            ticks: {
                                color: textColor,
                                callback: function(value) {
                                    return value >= 1000 ? (value / 1000) + ' Ton' : value + ' kg';
                                }
                            },
                            title: { display: true, text: 'Berat Sampah (kg)', color: textColor }
                        },
                        yPercent: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            grid: { drawOnChartArea: false },
                            min: 0,
                            max: 100,
                            ticks: { color: textColor, callback: value => value + '%' },
                            title: { display: true, text: 'Persentase Reduksi (%)', color: textColor }
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
