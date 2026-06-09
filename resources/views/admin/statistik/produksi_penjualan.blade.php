@extends('layouts.admin')

@section('title', 'Statistik Produksi vs Penjualan')

@section('content')
    <div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <h1>Produksi vs Penjualan</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Statistik Produksi vs Penjualan</li>
                </ol>
            </nav>
        </div>
        
        <div class="d-flex align-items-center gap-3">
            <form action="{{ route('admin.statistik-komparatif.produksi-penjualan') }}" method="GET" class="period-selector-container">
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
            <div class="card stat-card stat-success h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="stat-icon bg-success-light me-3">
                        <i class="cil-filter"></i>
                    </div>
                    <div>
                        <div class="text-body-secondary text-uppercase fw-semibold small">Produksi Hasil Pilahan</div>
                        <div class="fs-4 fw-bold text-success">{{ number_format($totalProduksi, 2, ',', '.') }} kg</div>
                        <div class="text-muted small">({{ number_format($totalProduksi / 1000, 2, ',', '.') }} Ton)</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card stat-primary h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="stat-icon bg-primary-light me-3">
                        <i class="cil-cart"></i>
                    </div>
                    <div>
                        <div class="text-body-secondary text-uppercase fw-semibold small">Volume Terjual (Sales)</div>
                        <div class="fs-4 fw-bold text-primary">{{ number_format($totalPenjualan, 2, ',', '.') }} kg</div>
                        <div class="text-muted small">({{ number_format($totalPenjualan / 1000, 2, ',', '.') }} Ton)</div>
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
                        <div class="text-body-secondary text-uppercase fw-semibold small">Nilai Penjualan POS</div>
                        <div class="fs-4 fw-bold text-info">Rp {{ number_format($totalRupiahPenjualan, 0, ',', '.') }}</div>
                        <div class="text-muted small">Transaksi Penjualan B2B</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card {{ $deltaTotal >= 0 ? 'stat-warning' : 'stat-danger' }} h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="stat-icon {{ $deltaTotal >= 0 ? 'bg-warning-light text-warning' : 'bg-danger-light text-danger' }} me-3">
                        <i class="cil-storage"></i>
                    </div>
                    <div>
                        <div class="text-body-secondary text-uppercase fw-semibold small">Delta Stok (Sisa / Defisit)</div>
                        <div class="fs-4 fw-bold {{ $deltaTotal >= 0 ? 'text-warning' : 'text-danger' }}">{{ number_format($deltaTotal, 2, ',', '.') }} kg</div>
                        <div class="text-muted small">{{ $deltaTotal >= 0 ? 'Produksi Belum Terjual' : 'Defisit Stok Terjual' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Chart --}}
    <div class="card mb-4">
        <div class="card-header bg-white border-bottom-0 pt-4 pb-2">
            <h5 class="card-title mb-0 fw-bold">Grafik Pemilahan vs Penjualan Kategori Sampah</h5>
            <p class="text-muted small">Perbandingan volume hasil produksi pilahan dengan volume penjualan produk untuk setiap kategori daur ulang</p>
        </div>
        <div class="card-body">
            @if($totalProduksi > 0 || $totalPenjualan > 0)
                <div style="position: relative; height: 350px;">
                    <canvas id="compareChart"></canvas>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="cil-bar-chart text-muted mb-3" style="font-size: 3rem; opacity: 0.3;"></i>
                    <p class="text-muted">Tidak ada data untuk periode terpilih.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Data Table --}}
    <div class="card">
        <div class="card-header bg-white border-bottom-0 pt-4 pb-2">
            <h5 class="card-title mb-0 fw-bold">Rincian Perbandingan Kategori Produk</h5>
        </div>
        <div class="table-responsive p-3">
            <table class="table table-hover table-striped align-middle border-top">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 80px;">No</th>
                        <th>Kategori Sampah</th>
                        <th class="text-center">Golongan Utama</th>
                        <th class="text-end">Hasil Pilah / Produksi (kg)</th>
                        <th class="text-end">Terjual (kg)</th>
                        <th class="text-end">Delta / Sisa Stok (kg)</th>
                        <th class="text-end">Nilai Penjualan (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($compareData as $index => $row)
                        <tr>
                            <td class="text-center text-muted fw-bold">{{ $index + 1 }}</td>
                            <td><strong>{{ $row['category_name'] }}</strong></td>
                            <td class="text-center">
                                <span class="badge bg-secondary-light text-dark text-uppercase fw-semibold">
                                    {{ $row['kategori_utama'] }}
                                </span>
                            </td>
                            <td class="text-end font-monospace text-success fw-semibold">{{ number_format($row['produksi'], 2, ',', '.') }}</td>
                            <td class="text-end font-monospace text-primary fw-semibold">{{ number_format($row['penjualan'], 2, ',', '.') }}</td>
                            <td class="text-end font-monospace {{ $row['delta'] >= 0 ? 'text-warning' : 'text-danger' }}">
                                {{ number_format($row['delta'], 2, ',', '.') }}
                            </td>
                            <td class="text-end font-monospace text-info fw-bold">Rp {{ number_format($row['nilai_jual'], 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Tidak ada kategori produk daur ulang aktif.</td>
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

        .bg-secondary-light {
            background-color: rgba(108, 117, 125, 0.15) !important;
        }
        [data-coreui-theme="dark"] .bg-secondary-light {
            background-color: rgba(248, 249, 250, 0.1) !important;
            color: #e6eef8 !important;
        }
    </style>
@endpush

@push('scripts')
    @if($totalProduksi > 0 || $totalPenjualan > 0)
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const data = @json($compareData);
            
            // Only plot categories that have production or sales to avoid clutter
            const filteredData = data.filter(d => d.produksi > 0 || d.penjualan > 0);
            
            const labels = filteredData.map(d => d.category_name);
            const produksiVals = filteredData.map(d => d.produksi);
            const penjualanVals = filteredData.map(d => d.penjualan);

            const isDark = document.documentElement.getAttribute('data-coreui-theme') === 'dark';
            const gridColor = isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)';
            const textColor = isDark ? '#e6eef8' : '#374151';

            const ctx = document.getElementById('compareChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Hasil Produksi Pilah (kg)',
                            data: produksiVals,
                            backgroundColor: 'rgba(25, 135, 84, 0.75)', // Green
                            borderColor: '#198754',
                            borderWidth: 1.5,
                            borderRadius: 4,
                            barPercentage: 0.7,
                            categoryPercentage: 0.6
                        },
                        {
                            label: 'Terjual (kg)',
                            data: penjualanVals,
                            backgroundColor: 'rgba(13, 110, 253, 0.75)', // Blue
                            borderColor: '#0d6efd',
                            borderWidth: 1.5,
                            borderRadius: 4,
                            barPercentage: 0.7,
                            categoryPercentage: 0.6
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
                                    return value.toLocaleString('id-ID') + ' kg';
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
    @endif
@endpush
