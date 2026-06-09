@extends('layouts.admin')

@section('title', 'Statistik Pendapatan vs Beban')

@section('content')
    <div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <h1>Pendapatan vs Beban</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Statistik Pendapatan vs Beban</li>
                </ol>
            </nav>
        </div>
        
        <div class="d-flex align-items-center gap-3">
            <form action="{{ route('admin.statistik-komparatif.keuangan') }}" method="GET" class="period-selector-container">
                <div class="period-selector shadow-sm">
                    <i class="cil-calendar text-primary me-2"></i>
                    <select name="year" onchange="this.form.submit()">
                        @foreach($years as $y)
                            <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
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
                        <i class="cil-money"></i>
                    </div>
                    <div>
                        <div class="text-body-secondary text-uppercase fw-semibold small">Total Pendapatan</div>
                        <div class="fs-4 fw-bold text-success">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
                        <div class="text-muted small">Tahun {{ $selectedYear }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card stat-danger h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="stat-icon bg-danger-light me-3">
                        <i class="cil-wallet"></i>
                    </div>
                    <div>
                        <div class="text-body-secondary text-uppercase fw-semibold small">Total Beban/Biaya</div>
                        <div class="fs-4 fw-bold text-danger">Rp {{ number_format($totalExpense, 0, ',', '.') }}</div>
                        <div class="text-muted small">Tahun {{ $selectedYear }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card {{ $totalNetProfit >= 0 ? 'stat-success' : 'stat-danger' }} h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="stat-icon {{ $totalNetProfit >= 0 ? 'bg-success-light text-success' : 'bg-danger-light text-danger' }} me-3">
                        <i class="{{ $totalNetProfit >= 0 ? 'cil-arrow-circle-top' : 'cil-arrow-circle-bottom' }}"></i>
                    </div>
                    <div>
                        <div class="text-body-secondary text-uppercase fw-semibold small">Laba/Rugi Bersih</div>
                        <div class="fs-4 fw-bold {{ $totalNetProfit >= 0 ? 'text-success' : 'text-danger' }}">
                            Rp {{ number_format($totalNetProfit, 0, ',', '.') }}
                        </div>
                        <div class="text-muted small">{{ $totalNetProfit >= 0 ? 'Laba (Net Profit)' : 'Rugi Bersih' }}</div>
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
                        <div class="text-body-secondary text-uppercase fw-semibold small">Net Profit Margin</div>
                        <div class="fs-4 fw-bold text-info">{{ number_format($profitMargin, 1, ',', '.') }}%</div>
                        <div class="text-muted small">Rasio profitabilitas</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Financial Chart --}}
    <div class="card mb-4">
        <div class="card-header bg-white border-bottom-0 pt-4 pb-2">
            <h5 class="card-title mb-0 fw-bold">Grafik Tren Keuangan Bulanan ({{ $selectedYear }})</h5>
            <p class="text-muted small">Melacak fluktuasi bulanan pendapatan vs beban operasional dan non-operasional</p>
        </div>
        <div class="card-body">
            <div style="position: relative; height: 350px;">
                <canvas id="financialComparisonChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Data Table --}}
    <div class="card">
        <div class="card-header bg-white border-bottom-0 pt-4 pb-2">
            <h5 class="card-title mb-0 fw-bold">Rincian Laba Rugi Bulanan ({{ $selectedYear }})</h5>
        </div>
        <div class="table-responsive p-3">
            <table class="table table-hover table-striped align-middle border-top">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 80px;">No</th>
                        <th>Bulan</th>
                        <th class="text-end">Pendapatan (Rp)</th>
                        <th class="text-end">Beban / Biaya (Rp)</th>
                        <th class="text-end">Laba / Rugi Bersih (Rp)</th>
                        <th class="text-center" style="width: 150px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($chartData as $index => $row)
                        <tr>
                            <td class="text-center text-muted fw-bold">{{ $index + 1 }}</td>
                            <td><strong>{{ $row['month_name'] }}</strong></td>
                            <td class="text-end font-monospace text-success fw-semibold">Rp {{ number_format($row['revenue'], 0, ',', '.') }}</td>
                            <td class="text-end font-monospace text-danger">Rp {{ number_format($row['expense'], 0, ',', '.') }}</td>
                            <td class="text-end font-monospace fw-bold {{ $row['net_profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                                Rp {{ number_format($row['net_profit'], 0, ',', '.') }}
                            </td>
                            <td class="text-center">
                                @if($row['net_profit'] >= 0)
                                    <span class="badge bg-success-light text-success fw-bold px-3 py-2 border border-success-subtle">
                                        <i class="cil-chevron-double-up me-1"></i> UNTUNG
                                    </span>
                                @else
                                    <span class="badge bg-danger-light text-danger fw-bold px-3 py-2 border border-danger-subtle">
                                        <i class="cil-chevron-double-down me-1"></i> RUGI
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
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
        [data-coreui-theme="dark"] .period-selector {
            background: #1e293b;
            border-color: rgba(255, 255, 255, 0.1);
        }
        [data-coreui-theme="dark"] .period-selector select {
            color: #f1f5f9;
        }

        /* Badge lighting in light/dark mode override */
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
            const revenues = data.map(d => d.revenue);
            const expenses = data.map(d => d.expense);
            const netProfits = data.map(d => d.net_profit);

            const isDark = document.documentElement.getAttribute('data-coreui-theme') === 'dark';
            const gridColor = isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)';
            const textColor = isDark ? '#e6eef8' : '#374151';

            const ctx = document.getElementById('financialComparisonChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: months,
                    datasets: [
                        {
                            label: 'Pendapatan (Rp)',
                            data: revenues,
                            backgroundColor: 'rgba(25, 135, 84, 0.1)',
                            borderColor: '#198754', // Pure success green
                            borderWidth: 3,
                            pointBackgroundColor: '#198754',
                            pointBorderColor: '#fff',
                            pointHoverRadius: 6,
                            pointHoverBackgroundColor: '#198754',
                            pointRadius: 4,
                            fill: true,
                            tension: 0.3
                        },
                        {
                            label: 'Beban / Biaya (Rp)',
                            data: expenses,
                            backgroundColor: 'rgba(220, 53, 69, 0.05)',
                            borderColor: '#dc3545', // Pure danger red
                            borderWidth: 3,
                            pointBackgroundColor: '#dc3545',
                            pointBorderColor: '#fff',
                            pointHoverRadius: 6,
                            pointHoverBackgroundColor: '#dc3545',
                            pointRadius: 4,
                            fill: true,
                            tension: 0.3
                        },
                        {
                            type: 'bar',
                            label: 'Laba/Rugi Bersih (Rp)',
                            data: netProfits,
                            backgroundColor: function(context) {
                                const index = context.dataIndex;
                                const val = context.dataset.data[index];
                                return val >= 0 ? 'rgba(25, 135, 84, 0.3)' : 'rgba(220, 53, 69, 0.3)';
                            },
                            borderColor: function(context) {
                                const index = context.dataIndex;
                                const val = context.dataset.data[index];
                                return val >= 0 ? '#198754' : '#dc3545';
                            },
                            borderWidth: 1,
                            borderRadius: 4,
                            barPercentage: 0.4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: textColor,
                                usePointStyle: true,
                                padding: 15
                            }
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': Rp ' + context.parsed.y.toLocaleString('id-ID');
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
                                    if (Math.abs(value) >= 1000000) {
                                        return 'Rp ' + (value / 1000000).toLocaleString('id-ID') + ' jt';
                                    }
                                    return 'Rp ' + value.toLocaleString('id-ID');
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
@endpush
