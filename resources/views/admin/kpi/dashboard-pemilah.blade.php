@extends('layouts.admin')

@section('title', 'Dashboard 12 Tenaga Pemilah - TPST Performance Management')

@section('content')
<div class="container-fluid">
    {{-- Header & Filter Bar --}}
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body p-3">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <h4 class="mb-1 text-success fw-bold">
                        <i class="cil-people me-2"></i>Dashboard Pemilah & Material Recovery
                    </h4>
                    <p class="text-muted mb-0 small">
                        Leaderboard Produktivitas <strong>12 Tenaga Pemilah</strong>, Target Sortir Mingguan & Nilai Ekonomi Recovery (Upah Rp 70.000/Hari)
                    </p>
                </div>
                <form action="{{ route('admin.kpi.dashboard.pemilah') }}" method="GET" class="d-flex flex-wrap align-items-center gap-2">
                    <select name="filter" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                        <option value="mingguan" {{ $filter == 'mingguan' ? 'selected' : '' }}>Mingguan (Target 50 kg/pekan)</option>
                        <option value="bulanan" {{ $filter == 'bulanan' ? 'selected' : '' }}>Bulanan (Rekapitulasi)</option>
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

    {{-- Highlight Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card text-white bg-primary h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="fs-4 fw-semibold">{{ count($leaderboard) }} Orang</div>
                            <div class="small text-white-50">Tenaga Pemilah Aktif</div>
                        </div>
                        <i class="cil-user fs-2 text-white-50"></i>
                    </div>
                    <small class="text-white-50 d-block mt-3">Target Standar: 12 Orang</small>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card text-white bg-success h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="fs-4 fw-semibold">{{ number_format($totalKgSemua, 1) }} kg</div>
                            <div class="small text-white-50">Total Material Terpilah</div>
                        </div>
                        <i class="cil-scale fs-2 text-white-50"></i>
                    </div>
                    <small class="text-white-50 d-block mt-3">Plastik, Kardus, Logam, dll.</small>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card text-white bg-info h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="fs-4 fw-semibold">Rp {{ number_format($totalNilaiSemua, 0, ',', '.') }}</div>
                            <div class="small text-white-50">Nilai Ekonomi Material</div>
                        </div>
                        <i class="cil-money fs-2 text-white-50"></i>
                    </div>
                    <small class="text-white-50 d-block mt-3">Valuasi Hasil Recovery</small>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card text-white bg-warning h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="fs-4 fw-semibold text-dark">
                                {{ count($leaderboard) > 0 ? $leaderboard[0]['nama'] : '-' }}
                            </div>
                            <div class="small text-dark text-opacity-75">Top Performer (Rank #1)</div>
                        </div>
                        <i class="cil-trophy fs-2 text-dark text-opacity-50"></i>
                    </div>
                    <small class="text-dark text-opacity-75 d-block mt-3">
                        Total Pilahan: {{ count($leaderboard) > 0 ? $leaderboard[0]['total_kg'] : 0 }} kg
                    </small>
                </div>
            </div>
        </div>
    </div>

    {{-- Leaderboard Table --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-dark">
                <i class="cil-award me-2 text-warning"></i>Leaderboard & Evaluasi Kinerja Tenaga Pemilah
            </h5>
            <span class="badge bg-light text-dark border">
                Bobot KPI: Pilahan (40%), Nilai Ekonomi (25%), Kualitas (15%), Kebersihan (10%), K3 (10%)
            </span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 70px;">Rank</th>
                            <th>Nama Tenaga Pemilah</th>
                            <th class="text-center">Hari Hadir</th>
                            <th class="text-center">Target (kg)</th>
                            <th class="text-center">Realisasi (kg)</th>
                            <th class="text-center">Pencapaian (%)</th>
                            <th class="text-end">Nilai Ekonomi (Rp)</th>
                            <th class="text-center">Predikat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leaderboard as $row)
                        <tr>
                            <td class="text-center">
                                @if($row['ranking'] == 1)
                                    <span class="badge bg-warning text-dark px-2 py-1"><i class="cil-trophy me-1"></i>#1</span>
                                @elseif($row['ranking'] == 2)
                                    <span class="badge bg-secondary px-2 py-1">#2</span>
                                @elseif($row['ranking'] == 3)
                                    <span class="badge bg-danger px-2 py-1">#3</span>
                                @else
                                    <span class="badge bg-light text-dark border">#{{ $row['ranking'] }}</span>
                                @endif
                            </td>
                            <td class="fw-semibold">
                                {{ $row['nama'] }}
                                @if(!empty($row['breakdown']))
                                    <div class="small text-muted" style="font-size: 0.75rem;">
                                        @foreach($row['breakdown'] as $cat => $kg)
                                            <span class="me-2">{{ $cat }}: {{ number_format($kg, 1) }}kg</span>
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                            <td class="text-center">{{ $row['hari_hadir'] }} hari</td>
                            <td class="text-center">{{ $row['target_kg'] }} kg</td>
                            <td class="text-center fw-bold">{{ $row['total_kg'] }} kg</td>
                            <td class="text-center">
                                <span class="badge {{ $row['achievement_pct'] >= 100 ? 'bg-success' : ($row['achievement_pct'] >= 75 ? 'bg-info' : 'bg-warning text-dark') }}">
                                    {{ $row['achievement_pct'] }}%
                                </span>
                            </td>
                            <td class="text-end fw-bold text-success">
                                Rp {{ number_format($row['nilai_ekonomi'], 0, ',', '.') }}
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $row['achievement_pct'] >= 100 ? 'bg-success' : 'bg-primary' }}">
                                    {{ $row['achievement_pct'] >= 100 ? 'Sangat Baik' : 'Baik' }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                Belum ada data output pemilah yang tercatat pada periode ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Rekap Produksi Hasil Pilahan & RDF Pabrik --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold text-dark">
                <i class="cil-layers me-2 text-primary"></i>Komposisi Kategori Material Recovery & Bal RDF
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Kategori Material</th>
                            <th class="text-center">Total Tonase (Ton)</th>
                            <th class="text-center">Jumlah Bal</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($hasilPilahanList as $hp)
                        <tr>
                            <td class="fw-semibold">{{ $hp->kategori }}</td>
                            <td class="text-center fw-bold">{{ number_format($hp->total_ton, 2) }} Ton</td>
                            <td class="text-center">{{ $hp->total_bal ?? '-' }} Bal</td>
                            <td><small class="text-muted">Siap jual ke offtaker</small></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-3 text-muted">
                                Belum ada rekapitulasi hasil pilahan untuk periode ini.
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
