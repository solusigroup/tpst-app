@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div class="d-flex align-items-center gap-4">
            <div>
                <h1>Dashboard</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </nav>
            </div>

            {{-- Month & Year Selector with Enhanced Aesthetic --}}
            <form action="{{ route('admin.dashboard') }}" method="GET" class="period-selector-container d-none d-md-flex">
                <div class="period-selector shadow-sm">
                    <i class="cil-calendar text-primary me-2"></i>
                    <select name="month" onchange="this.form.submit()">
                        @foreach($months as $m => $name)
                            <option value="{{ sprintf('%02d', $m) }}" {{ $selectedMonth == $m ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                    <div class="divider"></div>
                    <select name="year" onchange="this.form.submit()">
                        @foreach($years as $y)
                            <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
        
        <div class="d-flex flex-wrap align-items-center gap-3">
            {{-- Mobile Selector with Enhanced Aesthetic --}}
            <form action="{{ route('admin.dashboard') }}" method="GET" class="d-flex d-md-none align-items-center">
                <div class="period-selector shadow-sm" style="padding: 0.3rem 0.75rem;">
                    <select name="month" onchange="this.form.submit()" style="font-size: 0.8rem;">
                        @foreach($months as $m => $name)
                            <option value="{{ sprintf('%02d', $m) }}" {{ $selectedMonth == $m ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                    <div class="divider"></div>
                    <select name="year" onchange="this.form.submit()" style="font-size: 0.8rem;">
                        @foreach($years as $y)
                            <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
            </form>

            <div class="dropdown">
                <button class="btn btn-primary btn-lg shadow-sm dropdown-toggle" type="button" data-coreui-toggle="dropdown"
                    aria-expanded="false">
                    <i class="cil-plus me-1"></i> Tambah Transaksi
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                    @can('create_machine_log')
                        <li><a class="dropdown-item py-2" href="{{ route('admin.machine-logs.create') }}"><i
                                     class="cil-memory me-2 text-primary"></i> Tambah Log Mesin</a></li>
                    @endcan
                    @can('create_ritase')
                        <li><a class="dropdown-item py-2" href="{{ route('admin.ritase.create') }}"><i
                                     class="cil-truck me-2 text-primary"></i> Tambah Ritase</a></li>
                    @endcan
                    @can('create_hasil_pilahan')
                        <li><a class="dropdown-item py-2" href="{{ route('admin.hasil-pilahan.create') }}"><i
                                     class="cil-filter me-2 text-primary"></i> Catat Hasil Pilah</a></li>
                    @endcan
                    @can('create_pengangkutan_residu')
                        <li><a class="dropdown-item py-2" href="{{ route('admin.pengangkutan-residu.create') }}"><i
                                     class="cil-trash me-2 text-primary"></i> Catat Residu</a></li>
                    @endcan
                    <div class="dropdown-divider"></div>
                    @can('create_jurnal_kas')
                        <li><a class="dropdown-item py-2" href="{{ route('admin.jurnal-kas.create') }}"><i
                                     class="cil-money me-2 text-success"></i> Catat Jurnal Kas</a></li>
                    @endcan
                    @can('create_attendance')
                        <li><a class="dropdown-item py-2" href="{{ route('admin.hrd.attendance.create') }}"><i
                                     class="cil-calendar-check me-2 text-info"></i> Catat Kehadiran Karyawan</a></li>
                    @endcan
                    @can('create_penjualan')
                        <li><a class="dropdown-item py-2" href="{{ route('admin.penjualan.create') }}"><i
                                     class="cil-cart me-2 text-warning"></i> Catat Penjualan</a></li>
                    @endcan
                </ul>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card stat-success">
                <div class="card-body d-flex align-items-center">
                    <div class="stat-icon bg-success-light me-3">
                        <i class="cil-balance-scale"></i>
                    </div>
                    <div>
                        <div class="text-body-secondary text-uppercase fw-semibold small">Tonase Hari Ini</div>
                        <div class="fs-4 fw-bold">{{ number_format($tonaseHariIni, 2, ',', '.') }} kg</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card stat-success">
                <div class="card-body d-flex align-items-center">
                    <div class="stat-icon bg-success-light me-3">
                        <i class="cil-balance-scale"></i>
                    </div>
                    <div>
                        <div class="text-body-secondary text-uppercase fw-semibold small">Tonase {{ $months[intval($selectedMonth)] }} {{ $selectedYear }}</div>
                        <div class="fs-4 fw-bold">{{ number_format($tonaseBulanIni, 2, ',', '.') }} kg</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card stat-primary">
                <div class="card-body d-flex align-items-center">
                    <div class="stat-icon bg-primary-light me-3">
                        <i class="cil-truck"></i>
                    </div>
                    <div>
                        <div class="text-body-secondary text-uppercase fw-semibold small">Ritase Hari Ini</div>
                        <div class="fs-4 fw-bold">{{ $jumlahRitaseHariIni }} unit</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card stat-primary">
                <div class="card-body d-flex align-items-center">
                    <div class="stat-icon bg-primary-light me-3">
                        <i class="cil-truck"></i>
                    </div>
                    <div>
                        <div class="text-body-secondary text-uppercase fw-semibold small">Ritase {{ $months[intval($selectedMonth)] }} {{ $selectedYear }}</div>
                        <div class="fs-4 fw-bold">{{ $jumlahRitaseBulanIni }} unit</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card stat-success">
                <div class="card-body d-flex align-items-center">
                    <div class="stat-icon bg-success-light me-3">
                        <i class="cil-chart-pie"></i>
                    </div>
                    <div>
                        <div class="text-body-secondary text-uppercase fw-semibold small">Reduce Akumulatif</div>
                        <div class="fs-4 fw-bold">{{ number_format($kemampuanReduceKeseluruhan, 1, ',', '.') }}%</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card stat-success">
                <div class="card-body d-flex align-items-center">
                    <div class="stat-icon bg-success-light me-3">
                        <i class="cil-filter"></i>
                    </div>
                    <div>
                        <div class="text-body-secondary text-uppercase fw-semibold small">Prosentase Terpilah</div>
                        <div class="fs-4 fw-bold">{{ number_format($kemampuanReducePilahan, 1, ',', '.') }}%</div>
                    </div>
                </div>
            </div>
        </div>

        @if(!auth()->user()->hasRole('ritase_only'))
            <div class="col-sm-6 col-xl-3">
                <div class="card stat-card stat-info">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-info-light me-3">
                            <i class="cil-money"></i>
                        </div>
                        <div>
                            <div class="text-body-secondary text-uppercase fw-semibold small">Pendapatan Tipping</div>
                            <div class="fs-4 fw-bold">Rp {{ number_format($pendapatanTipping, 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card stat-card stat-warning">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-warning-light me-3">
                            <i class="cil-chart"></i>
                        </div>
                        <div>
                            <div class="text-body-secondary text-uppercase fw-semibold small">Penjualan {{ $months[intval($selectedMonth)] }} {{ $selectedYear }}</div>
                            <div class="fs-4 fw-bold">Rp {{ number_format($penjualanBulanIni, 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card stat-card stat-danger">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-danger-light me-3">
                            <i class="cil-wallet"></i>
                        </div>
                        <div>
                            <div class="text-body-secondary text-uppercase fw-semibold small">Biaya {{ $months[intval($selectedMonth)] }} {{ $selectedYear }}</div>
                            <div class="fs-4 fw-bold">Rp {{ number_format($biayaBulanIni, 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Charts --}}
    <div class="row g-4 mb-4">
        <div class="{{ auth()->user()->hasRole('ritase_only') ? 'col-xl-12' : 'col-xl-8' }}">
            <div class="card">
                <div class="card-header bg-white border-bottom-0 pt-4">
                    <h5 class="card-title mb-0 fw-semibold">Tonase Harian ({{ $months[intval($selectedMonth)] }} {{ $selectedYear }})</h5>
                </div>
                <div class="card-body">
                    <canvas id="dailyTonnageChart"
                        height="{{ auth()->user()->hasRole('ritase_only') ? '80' : '100' }}"></canvas>
                </div>
            </div>
        </div>
        @if(!auth()->user()->hasRole('ritase_only'))
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-header bg-white border-bottom-0 pt-4">
                        <h5 class="card-title mb-0 fw-semibold">Revenue vs Biaya</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="financialChart" height="200"></canvas>
                    </div>
                </div>
            </div>
        @endif
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
            background: #fff;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
            transform: translateY(-1px);
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
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
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
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Daily Tonnage Chart
            const dailyData = @json($dailyTonnage);
            new Chart(document.getElementById('dailyTonnageChart'), {
                type: 'bar',
                data: {
                    labels: dailyData.map(d => d.date),
                    datasets: [{
                        label: 'Tonase (kg)',
                        data: dailyData.map(d => d.tonnage),
                        backgroundColor: 'rgba(59, 130, 246, 0.2)',
                        borderColor: 'rgba(59, 130, 246, 1)',
                        borderWidth: 2,
                        borderRadius: 6,
                        barPercentage: 0.6,
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(0,0,0,0.05)' },
                            ticks: { callback: v => v.toLocaleString('id-ID') + ' kg' }
                        },
                        x: { grid: { display: false } }
                    }
                }
            });

            @if(!auth()->user()->hasRole('ritase_only'))
                // Financial Comparison Chart
                const financialData = @json($monthlyFinancials);
                new Chart(document.getElementById('financialChart'), {
                    type: 'bar',
                    data: {
                        labels: financialData.map(d => d.month),
                        datasets: [
                            {
                                label: 'Revenue',
                                data: financialData.map(d => d.revenue),
                                backgroundColor: '#3b82f6',
                                borderRadius: 4,
                            },
                            {
                                label: 'Biaya',
                                data: financialData.map(d => d.expense),
                                backgroundColor: '#ef4444',
                                borderRadius: 4,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20 } },
                            tooltip: {
                                callbacks: {
                                    label: ctx => ctx.dataset.label + ': Rp ' + ctx.parsed.y.toLocaleString('id-ID')
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: v => 'Rp ' + (v >= 1000000 ? (v / 1000000) + 'jt' : v.toLocaleString('id-ID'))
                                }
                            }
                        }
                    }
                });
            @endif
    });
    </script>
@endpush