@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="page-header">
    <div>
        <h1>Dashboard</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </nav>
    </div>
    <div>
        <span class="text-body-secondary"><i class="cil-calendar me-1"></i> {{ now()->translatedFormat('l, d F Y') }}</span>
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
                    <div class="text-body-secondary text-uppercase fw-semibold small">Tonase Bulan Ini</div>
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
                    <div class="text-body-secondary text-uppercase fw-semibold small">Ritase Bulan Ini</div>
                    <div class="fs-4 fw-bold">{{ $jumlahRitaseBulanIni }} unit</div>
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
                    <div class="text-body-secondary text-uppercase fw-semibold small">Penjualan Bulan Ini</div>
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
                    <div class="text-body-secondary text-uppercase fw-semibold small">Biaya Bulan Ini</div>
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
                <h5 class="card-title mb-0 fw-semibold">Tonase Harian (30 Hari Terakhir)</h5>
            </div>
            <div class="card-body">
                <canvas id="dailyTonnageChart" height="{{ auth()->user()->hasRole('ritase_only') ? '80' : '100' }}"></canvas>
            </div>
        </div>
    </div>
    @if(!auth()->user()->hasRole('ritase_only'))
    <div class="col-xl-4">
        <div class="card">
            <div class="card-header bg-white border-bottom-0 pt-4">
                <h5 class="card-title mb-0 fw-semibold">Revenue Bulanan</h5>
            </div>
            <div class="card-body">
                <canvas id="revenueChart" height="200"></canvas>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
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
    // Revenue Chart
    const revenueData = @json($monthlyRevenue);
    new Chart(document.getElementById('revenueChart'), {
        type: 'doughnut',
        data: {
            labels: revenueData.map(d => d.month),
            datasets: [{
                data: revenueData.map(d => d.revenue),
                backgroundColor: [
                    '#3b82f6', '#10b981', '#f59e0b',
                    '#ef4444', '#8b5cf6', '#06b6d4'
                ],
                borderWidth: 0,
                hoverOffset: 8
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom', labels: { padding: 15, usePointStyle: true } },
                tooltip: {
                    callbacks: {
                        label: ctx => 'Rp ' + ctx.parsed.toLocaleString('id-ID')
                    }
                }
            }
        }
    });
    @endif
});
</script>
@endpush
