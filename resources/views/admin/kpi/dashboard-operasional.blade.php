@extends('layouts.admin')

@section('title', 'Dashboard Operasional & QC - TPST Performance Management')

@section('content')
<div class="container-fluid">
    {{-- Header & Filter Bar --}}
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body p-3">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <h4 class="mb-1 text-success fw-bold">
                        <i class="cil-truck me-2"></i>Dashboard Operasional & Quality Control
                    </h4>
                    <p class="text-muted mb-0 small">
                        Monitoring Tonase Masuk (Target ≥20 Ton/Hari), Kebersihan Area & Pengendalian Bau (PIC: <strong>Ana Maria & Agung</strong>)
                    </p>
                </div>
                <form action="{{ route('admin.kpi.dashboard.operasional') }}" method="GET" class="d-flex flex-wrap align-items-center gap-2">
                    <select name="filter" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                        <option value="mingguan" {{ $filter == 'mingguan' ? 'selected' : '' }}>Mingguan</option>
                        <option value="bulanan" {{ $filter == 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                        <option value="harian" {{ $filter == 'harian' ? 'selected' : '' }}>Harian</option>
                    </select>
                    <div class="input-group input-group-sm" style="width: auto;">
                        <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                        <span class="input-group-text">s/d</span>
                        <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                        <button type="submit" class="btn btn-success btn-sm">Filter</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Highlight Metrik Operasional --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card text-white bg-success h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="fs-4 fw-semibold">{{ $globalKpi['rata_tonase_harian'] }} <span class="fs-6">Ton/hari</span></div>
                            <div class="small text-white-50">Rerata Tonase Masuk</div>
                        </div>
                        <span class="badge bg-white text-success fw-bold">Target 20 T/h</span>
                    </div>
                    <div class="progress progress-white progress-thin mt-3" style="height: 6px;">
                        <div class="progress-bar bg-white" style="width: {{ min(100, ($globalKpi['rata_tonase_harian']/20)*100) }}%"></div>
                    </div>
                    <small class="text-white-50 d-block mt-2">Pencapaian: {{ round(($globalKpi['rata_tonase_harian']/20)*100, 1) }}%</small>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card text-white bg-primary h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="fs-4 fw-semibold">{{ $globalKpi['kpi_items'][1]['realisasi'] }}</div>
                            <div class="small text-white-50">Kebersihan Area TPST</div>
                        </div>
                        <i class="cil-brush fs-2 text-white-50"></i>
                    </div>
                    <div class="progress progress-white progress-thin mt-3" style="height: 6px;">
                        <div class="progress-bar bg-white" style="width: {{ $globalKpi['kpi_items'][1]['nilai'] }}%"></div>
                    </div>
                    <small class="text-white-50 d-block mt-2">Zero Tumpukan Sampah</small>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card text-white bg-info h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="fs-4 fw-semibold">{{ $globalKpi['kpi_items'][2]['realisasi'] }}</div>
                            <div class="small text-white-50">Indeks Kontrol Bau</div>
                        </div>
                        <i class="cil-eco fs-2 text-white-50"></i>
                    </div>
                    <div class="progress progress-white progress-thin mt-3" style="height: 6px;">
                        <div class="progress-bar bg-white" style="width: {{ $globalKpi['kpi_items'][2]['nilai'] }}%"></div>
                    </div>
                    <small class="text-white-50 d-block mt-2">Standar: 100 (Bebas Bau)</small>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card text-white bg-warning h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="fs-4 fw-semibold text-dark">{{ $anaKpi['total_skor'] }}%</div>
                            <div class="small text-dark text-opacity-75">Skor KPI PIC Ana Maria</div>
                        </div>
                        <span class="badge bg-dark text-white">{{ $anaKpi['predikat'] }}</span>
                    </div>
                    <div class="progress progress-thin mt-3" style="height: 6px;">
                        <div class="progress-bar bg-dark" style="width: {{ min(100, $anaKpi['total_skor']) }}%"></div>
                    </div>
                    <small class="text-dark text-opacity-75 d-block mt-2">Kebersihan, Bau, Sortir, RDF</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Grafik Tonase Harian vs Target 20 Ton --}}
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-dark">
                        <i class="cil-chart-line me-2 text-success"></i>Tren Tonase Harian vs Target (20 Ton/Hari)
                    </h5>
                    <span class="badge bg-light text-dark border">Periode: {{ $startDate }} s/d {{ $endDate }}</span>
                </div>
                <div class="card-body">
                    <canvas id="tonaseChart" height="240"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-dark">
                        <i class="cil-task me-2 text-primary"></i>KPI Ana Maria (Operasional & QC)
                    </h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @foreach($anaKpi['kpi_items'] as $item)
                        <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                            <div>
                                <div class="fw-semibold small">{{ $item['nama'] }}</div>
                                <div class="text-muted" style="font-size: 0.75rem;">Target: {{ $item['target'] }}</div>
                            </div>
                            <div class="text-end">
                                <span class="badge {{ $item['nilai'] >= 100 ? 'bg-success' : 'bg-warning' }}">{{ $item['nilai'] }}%</span>
                                <div class="text-muted" style="font-size: 0.75rem;">Bobot: {{ $item['bobot'] }}%</div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel Riwayat Checklist Kebersihan & Bau Harian --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-dark">
                <i class="cil-clipboard me-2 text-primary"></i>Log Checklist Kebersihan & Bau Harian
            </h5>
            <a href="{{ route('admin.kpi.daily-input.index') }}" class="btn btn-sm btn-primary">
                <i class="cil-plus me-1"></i>Input Checklist Baru
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Area</th>
                            <th>Kebersihan</th>
                            <th>Tumpukan Sampah</th>
                            <th>Kondisi Bau</th>
                            <th class="text-center">Foto Bukti</th>
                            <th>Skor Kebersihan</th>
                            <th>Skor Bau</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($checklists as $chk)
                        <tr>
                            <td>{{ $chk->tanggal->format('d/m/Y') }}</td>
                            <td class="fw-semibold">{{ $chk->area }}</td>
                            <td>
                                <span class="badge {{ $chk->status_kebersihan === 'Bersih' ? 'bg-success' : ($chk->status_kebersihan === 'Kurang Bersih' ? 'bg-warning text-dark' : 'bg-danger') }}">
                                    {{ $chk->status_kebersihan }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $chk->ada_tumpukan_sampah ? 'bg-danger' : 'bg-success' }}">
                                    {{ $chk->ada_tumpukan_sampah ? 'Ada Tumpukan' : 'Nihil Tumpukan' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $chk->status_bau === 'Tidak Bau' ? 'bg-success' : ($chk->status_bau === 'Bau Ringan' ? 'bg-warning text-dark' : 'bg-danger') }}">
                                    {{ $chk->status_bau }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($chk->foto_bukti)
                                    <a href="{{ asset('storage/' . $chk->foto_bukti) }}" target="_blank" title="Buka Foto Bongkar Muatan">
                                        <img src="{{ asset('storage/' . $chk->foto_bukti) }}" class="rounded border shadow-sm" style="height: 38px; width: 38px; object-fit: cover;" alt="Bukti Foto">
                                    </a>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td class="fw-bold">{{ $chk->skor_kebersihan }}</td>
                            <td class="fw-bold">{{ $chk->skor_bau }}</td>
                            <td><small class="text-muted">{{ $chk->catatan ?? '-' }}</small></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">
                                Belum ada input checklist untuk periode ini. Silakan tambahkan melalui menu Input Harian KPI.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('tonaseChart').getContext('2d');
    
    const labels = {!! json_encode($dailyTonnage->pluck('tgl')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d M'))) !!};
    const dataTonase = {!! json_encode($dailyTonnage->pluck('tonase')) !!};
    const targetData = labels.map(() => 20);

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels.length > 0 ? labels : ['Belum ada ritase'],
            datasets: [
                {
                    label: 'Tonase Aktual (Ton)',
                    data: dataTonase.length > 0 ? dataTonase : [0],
                    backgroundColor: 'rgba(46, 184, 92, 0.75)',
                    borderColor: 'rgb(46, 184, 92)',
                    borderWidth: 1,
                    borderRadius: 4,
                },
                {
                    label: 'Target KPI (20 Ton/hari)',
                    data: targetData.length > 0 ? targetData : [20],
                    type: 'line',
                    borderColor: 'rgb(229, 83, 83)',
                    borderWidth: 2,
                    borderDash: [5, 5],
                    fill: false,
                    pointRadius: 3,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    title: { display: true, text: 'Ton' }
                }
            }
        }
    });
});
</script>
@endpush
@endsection
