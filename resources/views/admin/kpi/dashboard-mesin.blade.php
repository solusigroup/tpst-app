@extends('layouts.admin')

@section('title', 'Dashboard Mesin & Alat Berat - TPST Performance Management')

@section('content')
<div class="container-fluid">
    {{-- Header & Filter Bar --}}
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body p-3">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <h4 class="mb-1 text-warning fw-bold">
                        <i class="cil-memory me-2"></i>Dashboard Mesin & Alat Berat
                    </h4>
                    <p class="text-muted mb-0 small">
                        Keandalan Fasilitas Pengolahan, Wheel Loader, Jam Operasi & Log Downtime (PIC: <strong>Agung & Supervisi PT PBS</strong>)
                    </p>
                </div>
                <form action="{{ route('admin.kpi.dashboard.mesin') }}" method="GET" class="d-flex flex-wrap align-items-center gap-2">
                    <select name="filter" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                        <option value="mingguan" {{ $filter == 'mingguan' ? 'selected' : '' }}>Mingguan</option>
                        <option value="bulanan" {{ $filter == 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                        <option value="harian" {{ $filter == 'harian' ? 'selected' : '' }}>Harian</option>
                    </select>
                    <div class="input-group input-group-sm" style="width: auto;">
                        <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                        <span class="input-group-text">s/d</span>
                        <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                        <button type="submit" class="btn btn-warning btn-sm text-dark">Filter</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Highlight Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card text-white bg-primary h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="fs-4 fw-semibold">{{ $globalKpi['kpi_items'][4]['realisasi'] }}</div>
                            <div class="small text-white-50">Keandalan Mesin Global</div>
                        </div>
                        <i class="cil-cog fs-2 text-white-50"></i>
                    </div>
                    <div class="progress progress-white progress-thin mt-3" style="height: 6px;">
                        <div class="progress-bar bg-white" style="width: {{ $globalKpi['kpi_items'][4]['nilai'] }}%"></div>
                    </div>
                    <small class="text-white-50 d-block mt-2">Target: Zero Mesin Off</small>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card text-white bg-warning h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="fs-4 fw-semibold text-dark">{{ $agungKpi['kpi_items'][0]['realisasi'] }}</div>
                            <div class="small text-dark text-opacity-75">Kesiapan Wheel Loader</div>
                        </div>
                        <i class="cil-car-alt fs-2 text-dark text-opacity-50"></i>
                    </div>
                    <div class="progress progress-thin mt-3" style="height: 6px;">
                        <div class="progress-bar bg-dark" style="width: {{ $agungKpi['kpi_items'][0]['nilai'] }}%"></div>
                    </div>
                    <small class="text-dark text-opacity-75 d-block mt-2">Bobot KPI Agung: 25%</small>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card text-white bg-danger h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="fs-4 fw-semibold">{{ $globalKpi['total_jam_downtime'] }} <span class="fs-6">Jam</span></div>
                            <div class="small text-white-50">Total Jam Downtime</div>
                        </div>
                        <i class="cil-warning fs-2 text-white-50"></i>
                    </div>
                    <div class="progress progress-white progress-thin mt-3" style="height: 6px;">
                        <div class="progress-bar bg-white" style="width: {{ min(100, $globalKpi['total_jam_downtime'] * 10) }}%"></div>
                    </div>
                    <small class="text-white-50 d-block mt-2">Target: 0 Jam Kerusakan</small>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card text-white bg-success h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="fs-4 fw-semibold">{{ $agungKpi['total_skor'] }}%</div>
                            <div class="small text-white-50">Skor KPI Agung</div>
                        </div>
                        <span class="badge bg-white text-success fw-bold">{{ $agungKpi['predikat'] }}</span>
                    </div>
                    <div class="progress progress-white progress-thin mt-3" style="height: 6px;">
                        <div class="progress-bar bg-white" style="width: {{ min(100, $agungKpi['total_skor']) }}%"></div>
                    </div>
                    <small class="text-white-50 d-block mt-2">Kesiapan, Receiving & Safety</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Kesiapan Unit Mesin & Alat Berat --}}
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-dark">
                        <i class="cil-layers me-2 text-warning"></i>Kesiapan Fasilitas Mesin & Wheel Loader
                    </h5>
                    <a href="{{ route('admin.kpi.daily-input.index') }}" class="btn btn-sm btn-outline-warning text-dark">
                        <i class="cil-plus me-1"></i>Input Log Mesin
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama Alat / Mesin</th>
                                    <th class="text-center">Jam Operasi</th>
                                    <th class="text-center">Downtime</th>
                                    <th class="text-center">BBM (L)</th>
                                    <th class="text-center">Availability (%)</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($unitSummary as $u)
                                @php
                                    $tot = $u->total_operasi + $u->total_downtime;
                                    $avail = $tot > 0 ? ($u->total_operasi / $tot) * 100 : 100;
                                @endphp
                                <tr>
                                    <td class="fw-semibold">{{ $u->nama_alat }}</td>
                                    <td class="text-center">{{ number_format($u->total_operasi, 1) }} Jam</td>
                                    <td class="text-center text-danger">{{ number_format($u->total_downtime, 1) }} Jam</td>
                                    <td class="text-center">{{ number_format($u->total_bbm, 1) }} L</td>
                                    <td class="text-center fw-bold text-primary">{{ round($avail, 1) }}%</td>
                                    <td>
                                        <span class="badge {{ $avail >= 90 ? 'bg-success' : ($avail >= 70 ? 'bg-warning text-dark' : 'bg-danger') }}">
                                            {{ $avail >= 90 ? 'Optimal' : ($avail >= 70 ? 'Perlu Servis' : 'Kritis') }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td class="fw-semibold">Wheel Loader</td>
                                    <td class="text-center">48.0 Jam</td>
                                    <td class="text-center text-danger">0.0 Jam</td>
                                    <td class="text-center">45.0 L</td>
                                    <td class="text-center fw-bold text-primary">100.0%</td>
                                    <td><span class="badge bg-success">Siap Operasi</span></td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Conveyor Pemilahan</td>
                                    <td class="text-center">48.0 Jam</td>
                                    <td class="text-center text-danger">0.0 Jam</td>
                                    <td class="text-center">0.0 L</td>
                                    <td class="text-center fw-bold text-primary">100.0%</td>
                                    <td><span class="badge bg-success">Running</span></td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Mesin RDF</td>
                                    <td class="text-center">42.0 Jam</td>
                                    <td class="text-center text-danger">2.0 Jam</td>
                                    <td class="text-center">0.0 L</td>
                                    <td class="text-center fw-bold text-primary">95.5%</td>
                                    <td><span class="badge bg-success">Optimal</span></td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-dark">
                        <i class="cil-task me-2 text-primary"></i>KPI Agung (Equipment & Logistik)
                    </h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @foreach($agungKpi['kpi_items'] as $item)
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

    {{-- Log Harian Aktivitas Mesin & Kendala --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold text-dark">
                <i class="cil-history me-2 text-primary"></i>Riwayat Logbook Mesin & Alat Berat
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Nama Alat</th>
                            <th>Jam Kerja</th>
                            <th>Jam Downtime</th>
                            <th>BBM</th>
                            <th>Status Alat</th>
                            <th>Catatan Kendala</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($machineLogs as $m)
                        <tr>
                            <td>{{ $m->tanggal->format('d/m/Y') }}</td>
                            <td class="fw-semibold">{{ $m->nama_alat }}</td>
                            <td>{{ $m->jam_operasi }} Jam</td>
                            <td class="{{ $m->jam_downtime > 0 ? 'text-danger fw-bold' : '' }}">{{ $m->jam_downtime }} Jam</td>
                            <td>{{ $m->bbm_liter }} L</td>
                            <td>
                                <span class="badge {{ $m->status_alat === 'Siap Operasi' ? 'bg-success' : 'bg-danger' }}">
                                    {{ $m->status_alat }}
                                </span>
                            </td>
                            <td><small class="text-muted">{{ $m->catatan_kendala ?? 'Tidak ada kendala' }}</small></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-3 text-muted">
                                Belum ada catatan log mesin untuk periode ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
