@extends('layouts.admin')

@section('title', 'Statistik Kontribusi Klien')

@section('content')
    <div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <h1>Kontribusi Klien</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Statistik Kontribusi Klien</li>
                </ol>
            </nav>
        </div>
        
        <div class="d-flex align-items-center gap-3">
            <form action="{{ route('admin.statistik-komparatif.klien') }}" method="GET" class="period-selector-container">
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
    </div>

    {{-- Stats Cards --}}
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card stat-primary h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="stat-icon bg-primary-light me-3">
                        <i class="cil-people"></i>
                    </div>
                    <div>
                        <div class="text-body-secondary text-uppercase fw-semibold small">Klien Berkontribusi</div>
                        <div class="fs-4 fw-bold text-primary">{{ $totalKlien }} Klien</div>
                        <div class="text-muted small">Periode ini</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card stat-success h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="stat-icon bg-success-light me-3">
                        <i class="cil-balance-scale"></i>
                    </div>
                    <div>
                        <div class="text-body-secondary text-uppercase fw-semibold small">Total Tonase Masuk</div>
                        <div class="fs-4 fw-bold text-success">{{ number_format($totalBerat, 2, ',', '.') }} kg</div>
                        <div class="text-muted small">({{ number_format($totalBerat / 1000, 2, ',', '.') }} Ton)</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card stat-warning h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="stat-icon bg-warning-light me-3">
                        <i class="cil-star"></i>
                    </div>
                    <div>
                        <div class="text-body-secondary text-uppercase fw-semibold small">Kontributor Terbesar</div>
                        <div class="fs-5 fw-bold text-truncate text-warning" style="max-width: 180px;" title="{{ $maxContributorName }}">{{ $maxContributorName }}</div>
                        <div class="text-muted small">Tonase tertinggi</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card stat-info h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="stat-icon bg-info-light me-3">
                        <i class="cil-money"></i>
                    </div>
                    <div>
                        <div class="text-body-secondary text-uppercase fw-semibold small">Total Tipping Fee</div>
                        <div class="fs-4 fw-bold text-info">Rp {{ number_format($totalTipping, 0, ',', '.') }}</div>
                        <div class="text-muted small">Biaya Tipping Masuk</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts --}}
    <div class="row g-4 mb-4">
        <div class="col-xl-6">
            <div class="card h-100">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-2">
                    <h5 class="card-title mb-0 fw-bold">Proporsi Kontribusi Tonase Klien</h5>
                    <p class="text-muted small">Persentase total berat sampah yang disumbangkan oleh masing-masing klien</p>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    @if($totalBerat > 0)
                        <div style="position: relative; width: 100%; max-width: 300px; height: 300px;">
                            <canvas id="clientDoughnutChart"></canvas>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="cil-chart-pie text-muted mb-3" style="font-size: 3rem; opacity: 0.3;"></i>
                            <p class="text-muted">Tidak ada data untuk periode terpilih.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card h-100">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-2">
                    <h5 class="card-title mb-0 fw-bold">Rata-rata Berat per Ritase Klien (kg)</h5>
                    <p class="text-muted small">Rata-rata tonase per sekali pengangkutan (rit) untuk setiap klien</p>
                </div>
                <div class="card-body">
                    @if($totalBerat > 0)
                        <div style="position: relative; height: 300px;">
                            <canvas id="clientBarChart"></canvas>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="cil-bar-chart text-muted mb-3" style="font-size: 3rem; opacity: 0.3;"></i>
                            <p class="text-muted">Tidak ada data untuk periode terpilih.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Data Table --}}
    <div class="card">
        <div class="card-header bg-white border-bottom-0 pt-4 pb-2">
            <h5 class="card-title mb-0 fw-bold">Rincian Kontribusi Klien</h5>
        </div>
        <div class="table-responsive p-3">
            <table class="table table-hover table-striped align-middle border-top">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 80px;">No</th>
                        <th>Nama Klien</th>
                        <th class="text-center">Jumlah Ritase (Unit)</th>
                        <th class="text-end">Total Tonase (kg)</th>
                        <th class="text-end">Rata-rata per Rit (kg)</th>
                        <th class="text-end">Total Tipping Fee</th>
                        <th class="text-center" style="width: 150px;">Kontribusi (%)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($klienContributions as $index => $row)
                        @php
                            $percentage = $totalBerat > 0 ? ($row['total_berat'] / $totalBerat) * 100 : 0;
                        @endphp
                        <tr>
                            <td class="text-center text-muted fw-bold">{{ $index + 1 }}</td>
                            <td><strong>{{ $row['name'] }}</strong></td>
                            <td class="text-center fw-semibold text-primary">{{ $row['total_ritase'] }}</td>
                            <td class="text-end font-monospace">{{ number_format($row['total_berat'], 2, ',', '.') }}</td>
                            <td class="text-end font-monospace text-muted">{{ number_format($row['avg_berat'], 2, ',', '.') }}</td>
                            <td class="text-end font-monospace text-success fw-semibold">Rp {{ number_format($row['total_tipping'], 0, ',', '.') }}</td>
                            <td class="text-center">
                                <span class="badge bg-primary text-white fw-bold">{{ number_format($percentage, 1, ',', '.') }}%</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Tidak ada data ritase untuk periode ini.</td>
                        </tr>
                    @endforelse
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
    @if($totalBerat > 0)
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const data = @json($klienContributions);
            
            const labels = data.map(d => d.name);
            const weights = data.map(d => d.total_berat);
            const averages = data.map(d => d.avg_berat);

            const isDark = document.documentElement.getAttribute('data-coreui-theme') === 'dark';
            const gridColor = isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)';
            const textColor = isDark ? '#e6eef8' : '#374151';

            // Premium HSL harmonies color generator
            const generateColors = (num) => {
                const colors = [];
                for (let i = 0; i < num; i++) {
                    const hue = (i * (360 / num)) % 360;
                    colors.push(`hsla(${hue}, 70%, 55%, 0.8)`);
                }
                return colors;
            };

            const borderColors = (num) => {
                const colors = [];
                for (let i = 0; i < num; i++) {
                    const hue = (i * (360 / num)) % 360;
                    colors.push(`hsla(${hue}, 70%, 45%, 1)`);
                }
                return colors;
            };

            const palette = generateColors(data.length);
            const borders = borderColors(data.length);

            // 1. Doughnut Chart (Proporsi Tonase)
            const ctxDoughnut = document.getElementById('clientDoughnutChart').getContext('2d');
            new Chart(ctxDoughnut, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: weights,
                        backgroundColor: palette,
                        borderColor: borders,
                        borderWidth: 1.5,
                        hoverOffset: 12
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false // Use custom table below, hides clutter on doughnut
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const val = context.parsed;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percent = total > 0 ? ((val / total) * 100).toFixed(1) : 0;
                                    return context.label + ': ' + val.toLocaleString('id-ID') + ' kg (' + percent + '%)';
                                }
                            }
                        }
                    },
                    cutout: '60%'
                }
            });

            // 2. Horizontal Bar Chart (Rata-rata berat per ritase)
            const ctxBar = document.getElementById('clientBarChart').getContext('2d');
            new Chart(ctxBar, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Rata-rata per Rit (kg)',
                        data: averages,
                        backgroundColor: 'rgba(59, 130, 246, 0.75)',
                        borderColor: 'rgba(59, 130, 246, 1)',
                        borderWidth: 1.5,
                        borderRadius: 4,
                        barPercentage: 0.6
                    }]
                },
                options: {
                    indexAxis: 'y', // Make it horizontal
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: context => 'Rata-rata: ' + context.parsed.x.toLocaleString('id-ID') + ' kg'
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { color: gridColor },
                            ticks: { color: textColor }
                        },
                        y: {
                            grid: { display: false },
                            ticks: { color: textColor }
                        }
                    }
                }
            });
        });
    </script>
    @endif
@endpush
