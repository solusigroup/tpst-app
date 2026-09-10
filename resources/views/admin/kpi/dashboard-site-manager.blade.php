@extends('layouts.admin')

@section('title', 'Dashboard Site Manager - TPST Performance Management')

@section('content')
<div class="container-fluid">
    {{-- Header & Filter Bar --}}
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body p-3">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <h4 class="mb-1 text-primary fw-bold">
                        <i class="cil-speedometer me-2"></i>Dashboard Site Manager
                    </h4>
                    <p class="text-muted mb-0 small">
                        <strong>Budi Sucahyo</strong> (Site Manager TPST Megilan Lamongan - PT Tata Bumi Adilimbah)
                    </p>
                </div>
                <form action="{{ route('admin.kpi.dashboard.site-manager') }}" method="GET" class="d-flex flex-wrap align-items-center gap-2">
                    <select name="filter" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                        <option value="mingguan" {{ $filter == 'mingguan' ? 'selected' : '' }}>Mingguan (Minggu Ini)</option>
                        <option value="bulanan" {{ $filter == 'bulanan' ? 'selected' : '' }}>Bulanan (Bulan Berjalan)</option>
                        <option value="harian" {{ $filter == 'harian' ? 'selected' : '' }}>Harian (Hari Ini)</option>
                    </select>
                    <div class="input-group input-group-sm" style="width: auto;">
                        <span class="input-group-text bg-white"><i class="cil-calendar"></i></span>
                        <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                        <span class="input-group-text">s/d</span>
                        <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                        <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Top Score Highlights --}}
    <div class="row g-3 mb-4">
        {{-- Total Skor KPI Budi --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card text-white bg-primary h-100 border-0 shadow-sm">
                <div class="card-body pb-0 d-flex justify-content-between align-items-start">
                    <div>
                        <div class="fs-4 fw-semibold">{{ $budiKpi['total_skor'] }}%</div>
                        <div class="small text-white-50">Skor KPI Site Manager</div>
                    </div>
                    <span class="badge bg-white text-primary fw-bold">{{ $budiKpi['predikat'] }}</span>
                </div>
                <div class="card-body pt-2">
                    <div class="progress progress-white progress-thin mt-2" style="height: 6px;">
                        <div class="progress-bar bg-white" style="width: {{ min(100, $budiKpi['total_skor']) }}%"></div>
                    </div>
                    <div class="d-flex justify-content-between small text-white-50 mt-1">
                        <span>Target: 100%</span>
                        <span>Bobot: 100%</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Skor KPI Global TPST --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card text-white bg-success h-100 border-0 shadow-sm">
                <div class="card-body pb-0 d-flex justify-content-between align-items-start">
                    <div>
                        <div class="fs-4 fw-semibold">{{ $globalKpi['total_skor'] }}%</div>
                        <div class="small text-white-50">Skor KPI Global TPST</div>
                    </div>
                    <span class="badge bg-white text-success fw-bold">{{ $globalKpi['predikat'] }}</span>
                </div>
                <div class="card-body pt-2">
                    <div class="progress progress-white progress-thin mt-2" style="height: 6px;">
                        <div class="progress-bar bg-white" style="width: {{ min(100, $globalKpi['total_skor']) }}%"></div>
                    </div>
                    <div class="d-flex justify-content-between small text-white-50 mt-1">
                        <span>Kontribusi Bobot: 40%</span>
                        <span>Level: Global</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tonase Rata-rata Harian --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card text-white bg-info h-100 border-0 shadow-sm">
                <div class="card-body pb-0 d-flex justify-content-between align-items-start">
                    <div>
                        <div class="fs-4 fw-semibold">{{ $globalKpi['rata_tonase_harian'] }} <span class="fs-6">Ton/hari</span></div>
                        <div class="small text-white-50">Realisasi Tonase Masuk</div>
                    </div>
                    <i class="cil-truck fs-2 text-white-50"></i>
                </div>
                <div class="card-body pt-2">
                    <div class="progress progress-white progress-thin mt-2" style="height: 6px;">
                        <div class="progress-bar bg-white" style="width: {{ min(100, ($globalKpi['rata_tonase_harian'] / 20) * 100) }}%"></div>
                    </div>
                    <div class="d-flex justify-content-between small text-white-50 mt-1">
                        <span>Target: 20 Ton/hari</span>
                        <span>Total: {{ $globalKpi['total_tonase'] }} Ton</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tipping Fee Swasta --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card text-white bg-warning h-100 border-0 shadow-sm">
                <div class="card-body pb-0 d-flex justify-content-between align-items-start">
                    <div>
                        <div class="fs-5 fw-semibold text-dark">Rp {{ number_format($globalKpi['realisasi_tipping'], 0, ',', '.') }}</div>
                        <div class="small text-dark text-opacity-75">Tipping Fee Swasta</div>
                    </div>
                    <i class="cil-dollar fs-2 text-dark text-opacity-50"></i>
                </div>
                <div class="card-body pt-2">
                    <div class="progress progress-thin mt-2" style="height: 6px;">
                        <div class="progress-bar bg-dark" style="width: {{ min(100, ($globalKpi['realisasi_tipping'] / 20000000) * 100) }}%"></div>
                    </div>
                    <div class="d-flex justify-content-between small text-dark text-opacity-75 mt-1">
                        <span>Target: Rp 20 Jt/bln</span>
                        <span>Status: Optimal</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        {{-- Tabel Rincian KPI Budi Sucahyo --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-dark">
                        <i class="cil-user me-2 text-primary"></i>Rincian Penilaian KPI - Budi Sucahyo
                    </h5>
                    <span class="badge bg-primary">Skor: {{ $budiKpi['total_skor'] }}%</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Indikator KPI</th>
                                    <th>Target</th>
                                    <th>Realisasi Aktual</th>
                                    <th class="text-center">Nilai (%)</th>
                                    <th class="text-center">Bobot</th>
                                    <th class="text-end">Skor Akhir</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($budiKpi['kpi_items'] as $item)
                                <tr>
                                    <td class="fw-semibold">{{ $item['nama'] }}</td>
                                    <td><span class="badge bg-light text-dark border">{{ $item['target'] }}</span></td>
                                    <td><span class="small">{{ $item['realisasi'] }}</span></td>
                                    <td class="text-center">
                                        <span class="badge {{ $item['nilai'] >= 100 ? 'bg-success' : ($item['nilai'] >= 80 ? 'bg-info' : 'bg-warning') }}">
                                            {{ $item['nilai'] }}%
                                        </span>
                                    </td>
                                    <td class="text-center text-muted small">{{ $item['bobot'] }}%</td>
                                    <td class="text-end fw-bold text-primary">{{ $item['skor'] }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="4" class="text-end">Total Skor Tertimbang:</th>
                                    <th class="text-center">100%</th>
                                    <th class="text-end text-primary fs-5">{{ $budiKpi['total_skor'] }}%</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Skor Tim Pengurus Harian & Supervisi PBS --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-dark">
                        <i class="cil-people me-2 text-primary"></i>Kinerja 3 Pengurus Harian
                    </h5>
                </div>
                <div class="card-body">
                    {{-- Nita --}}
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-semibold">Nita Khoirunisa</span>
                            <span class="badge bg-info">{{ $nitaKpi['predikat'] }} ({{ $nitaKpi['total_skor'] }}%)</span>
                        </div>
                        <small class="text-muted d-block mb-1">Admin, Data & Commercial</small>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-info" style="width: {{ min(100, $nitaKpi['total_skor']) }}%"></div>
                        </div>
                    </div>
                    <hr class="my-2">
                    {{-- Agung --}}
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-semibold">Agung</span>
                            <span class="badge bg-warning text-dark">{{ $agungKpi['predikat'] }} ({{ $agungKpi['total_skor'] }}%)</span>
                        </div>
                        <small class="text-muted d-block mb-1">Equipment, Logistik & Receiving</small>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-warning" style="width: {{ min(100, $agungKpi['total_skor']) }}%"></div>
                        </div>
                    </div>
                    <hr class="my-2">
                    {{-- Ana --}}
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-semibold">Ana Maria</span>
                            <span class="badge bg-success">{{ $anaKpi['predikat'] }} ({{ $anaKpi['total_skor'] }}%)</span>
                        </div>
                        <small class="text-muted d-block mb-1">Operasional & Quality Control</small>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-success" style="width: {{ min(100, $anaKpi['total_skor']) }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bukti Foto Fisik Sisi Depan TPST --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-dark">
                        <i class="cil-camera me-1 text-success"></i>Bukti Foto Sisi Depan TPST
                    </h6>
                    @if($latestProof)
                        <span class="badge bg-light text-dark border">{{ $latestProof->tanggal->format('d/m/Y') }}</span>
                    @endif
                </div>
                <div class="card-body p-3 text-center">
                    @if($latestProof && $latestProof->foto_bukti)
                        <a href="{{ asset('storage/' . $latestProof->foto_bukti) }}" target="_blank">
                            <img src="{{ asset('storage/' . $latestProof->foto_bukti) }}" class="img-fluid rounded border shadow-sm mb-2" style="max-height: 180px; object-fit: cover; width: 100%;" alt="Foto Sisi Depan TPST">
                        </a>
                        <div class="small text-muted text-start">
                            <div><strong>Lokasi:</strong> {{ $latestProof->area }}</div>
                            <div><strong>Status:</strong> <span class="badge bg-success">{{ $latestProof->status_kebersihan }}</span> ({{ $latestProof->ada_tumpukan_sampah ? 'Ada Tumpukan' : 'Nihil Tumpukan' }})</div>
                        </div>
                    @else
                        <div class="py-4 text-muted small">
                            <i class="cil-camera fs-2 d-block mb-1 text-secondary opacity-50"></i>
                            Belum ada foto bukti fisik yang diunggah.
                        </div>
                    @endif
                </div>
            </div>

            {{-- Kartu Status Supervisi PT PBS --}}
            <div class="card border-0 shadow-sm border-start border-4 border-primary">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <i class="cil-shield-alt text-primary fs-3 me-2"></i>
                        <div>
                            <h6 class="mb-0 fw-bold">Supervisi PT Pinastika Bhakti Semesta</h6>
                            <small class="text-muted">Badan Supervisi & Verifikasi Teknis</small>
                        </div>
                    </div>
                    <p class="small text-secondary mb-3">
                        Kinerja periodik ini diverifikasi dan disahkan oleh Supervisi PT PBS sebelum diajukan dalam laporan resmi ke DLH Lamongan.
                    </p>
                    <a href="{{ route('admin.kpi.evaluasi.index') }}" class="btn btn-outline-primary btn-sm w-100">
                        <i class="cil-file me-1"></i>Buka Halaman Evaluasi & Approval
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Keluhan Stakeholder Terkini --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-dark">
                <i class="cil-speech me-2 text-danger"></i>Log Stakeholder & Zero Complain Tracking
            </h5>
            <a href="{{ route('admin.kpi.daily-input.index') }}" class="btn btn-sm btn-outline-danger">
                <i class="cil-plus me-1"></i>Input Keluhan
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Stakeholder</th>
                            <th>Pelapor</th>
                            <th>Isi Keluhan</th>
                            <th>Urgensi</th>
                            <th>Status</th>
                            <th>Tindakan Penanganan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($complaints as $c)
                        <tr>
                            <td>{{ $c->tanggal->format('d/m/Y') }}</td>
                            <td><span class="badge bg-secondary">{{ $c->stakeholder_type }}</span></td>
                            <td>{{ $c->nama_pelapor ?? '-' }}</td>
                            <td>{{ $c->isi_keluhan }}</td>
                            <td>
                                <span class="badge {{ $c->tingkat_urgensi === 'Tinggi' ? 'bg-danger' : ($c->tingkat_urgensi === 'Sedang' ? 'bg-warning text-dark' : 'bg-info') }}">
                                    {{ $c->tingkat_urgensi }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $c->status_penanganan === 'Resolved' ? 'bg-success' : 'bg-danger' }}">
                                    {{ $c->status_penanganan }}
                                </span>
                            </td>
                            <td>{{ $c->tindakan_perbaikan ?? 'Belum ada tindakan' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-3 text-muted">
                                <i class="cil-check text-success me-1"></i> Tidak ada keluhan stakeholder dalam periode ini (Target Zero Complain Tercapai).
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
