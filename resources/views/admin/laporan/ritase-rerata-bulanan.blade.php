@extends('layouts.admin')

@section('title', 'Laporan Ritase Rerata Harian Per Bulan')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Ritase Rerata Harian Per Bulan</h1>
    </div>
    <div>
        <a href="{{ route('admin.laporan-operasional.ritase-rerata-bulanan', array_merge(request()->all(), ['export' => 'pdf'])) }}" target="_blank" class="btn btn-danger">
            <i class="cil-file text-white"></i> Export PDF
        </a>
        <a href="{{ route('admin.laporan-operasional.ritase-rerata-bulanan', array_merge(request()->all(), ['export' => 'excel'])) }}" class="btn btn-success">
            <i class="cil-spreadsheet text-white"></i> Export Excel
        </a>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header bg-white py-3">
        <h6 class="m-0 font-weight-bold text-primary">Filter Laporan</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.laporan-operasional.ritase-rerata-bulanan') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-2">
                <label for="tahun" class="form-label">Tahun</label>
                <select name="tahun" id="tahun" class="form-select" onchange="this.form.submit()">
                    @for($i = date('Y'); $i >= 2020; $i--)
                        <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                <label for="jenis_klien" class="form-label">Jenis Klien</label>
                <select name="jenis_klien" id="jenis_klien" class="form-select" onchange="this.form.submit()">
                    <option value="">Semua Jenis</option>
                    @foreach(['DLH', 'Swasta', 'Offtaker', 'Internal'] as $jk)
                        <option value="{{ $jk }}" {{ $jenisKlien == $jk ? 'selected' : '' }}>{{ $jk }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="klien_id" class="form-label">Klien</label>
                <select name="klien_id" id="klien_id" class="form-select ts-select">
                    <option value="">Semua Klien</option>
                    @foreach($kliens as $k)
                        <option value="{{ $k->id }}" {{ $klienId == $k->id ? 'selected' : '' }}>
                            {{ $k->nama_klien }} ({{ $k->jenis }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="is_approved" class="form-label">Approval</label>
                <select name="is_approved" id="is_approved" class="form-select">
                    <option value="">Semua</option>
                    <option value="1" {{ $isApproved === '1' ? 'selected' : '' }}>Approved</option>
                    <option value="0" {{ $isApproved === '0' ? 'selected' : '' }}>Not Approved</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="cil-filter"></i> Filter
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Summary Cards --}}
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card border-start border-start-4 border-info h-100 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="text-body-secondary small fw-semibold text-uppercase">Total Ritase Setahun</div>
                    <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center" style="width:32px;height:32px;">
                        <i class="cil-truck"></i>
                    </div>
                </div>
                <div class="fs-4 fw-bold">{{ number_format($grandTotalRitase, 0, ',', '.') }} <span class="small fw-normal text-body-secondary">ritase</span></div>
                <div class="small text-body-secondary mt-1">
                    Aktif: <span class="fw-semibold">{{ number_format($totalActiveDays, 0, ',', '.') }}</span> hari
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="card border-start border-start-4 border-success h-100 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="text-body-secondary small fw-semibold text-uppercase">Total Tonase Setahun</div>
                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width:32px;height:32px;">
                        <i class="cil-speedometer"></i>
                    </div>
                </div>
                <div class="fs-4 fw-bold">{{ number_format($grandTotalNetto / 1000, 2, ',', '.') }} <span class="small fw-normal text-body-secondary">ton</span></div>
                <div class="small text-body-secondary mt-1">
                    <span class="fw-semibold">{{ number_format($grandTotalNetto, 2, ',', '.') }}</span> kg netto
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card border-start border-start-4 border-warning h-100 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="text-body-secondary small fw-semibold text-uppercase">Rerata Ritase / Hari</div>
                    <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center" style="width:32px;height:32px;">
                        <i class="cil-chart"></i>
                    </div>
                </div>
                <div class="fs-4 fw-bold">{{ number_format($overallRerataRitaseKalender, 2, ',', '.') }} <span class="small fw-normal text-body-secondary">/hari (Kalender)</span></div>
                <div class="small text-body-secondary mt-1">
                    Aktif: <span class="fw-semibold">{{ number_format($overallRerataRitaseAktif, 2, ',', '.') }}</span> /hari
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card border-start border-start-4 border-danger h-100 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="text-body-secondary small fw-semibold text-uppercase">Rerata Tonase / Hari</div>
                    <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center" style="width:32px;height:32px;">
                        <i class="cil-weight"></i>
                    </div>
                </div>
                <div class="fs-4 fw-bold">{{ number_format($overallRerataNettoKalender / 1000, 2, ',', '.') }} <span class="small fw-normal text-body-secondary">ton/hari (Kalender)</span></div>
                <div class="small text-body-secondary mt-1">
                    Aktif: <span class="fw-semibold">{{ number_format($overallRerataNettoAktif / 1000, 2, ',', '.') }}</span> ton/hari
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center bg-white">
        <h6 class="m-0 font-weight-bold text-primary">Data Rekap Ritase Rerata Harian Per Bulan - Tahun {{ $tahun }}</h6>
        <div class="text-body-secondary small">
            @if($klien) Filter Klien: <strong>{{ $klien->nama_klien }}</strong> | @endif Filter Jenis: <strong>{{ $jenisKlien ?: 'Semua' }}</strong>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover align-middle" width="100%" cellspacing="0">
                <thead class="table-light text-center">
                    <tr>
                        <th rowspan="2" class="align-middle">Bulan</th>
                        <th colspan="3" class="table-info">Ritase</th>
                        <th colspan="3" class="table-success">Tonase / Netto (kg)</th>
                        <th rowspan="2" class="align-middle">Hari Kalender</th>
                        <th rowspan="2" class="align-middle">Hari Aktif</th>
                    </tr>
                    <tr>
                        <th>Total</th>
                        <th>Rerata/Hari (Kalender)</th>
                        <th>Rerata/Hari (Aktif)</th>
                        <th>Total</th>
                        <th>Rerata/Hari (Kalender)</th>
                        <th>Rerata/Hari (Aktif)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reportData as $row)
                    <tr>
                        <td class="fw-bold">{{ $row->nama_bulan }}</td>
                        <td class="text-center">{{ number_format($row->total_ritase, 0, ',', '.') }}</td>
                        <td class="text-center table-info fw-bold">{{ number_format($row->rerata_ritase_kalender, 2, ',', '.') }}</td>
                        <td class="text-center table-info">{{ number_format($row->rerata_ritase_aktif, 2, ',', '.') }}</td>
                        <td class="text-end">{{ number_format($row->total_netto, 2, ',', '.') }} kg</td>
                        <td class="text-end table-success fw-bold">{{ number_format($row->rerata_netto_kalender, 2, ',', '.') }} kg</td>
                        <td class="text-end table-success">{{ number_format($row->rerata_netto_aktif, 2, ',', '.') }} kg</td>
                        <td class="text-center">{{ $row->calendar_days }}</td>
                        <td class="text-center">{{ $row->active_days }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td class="text-center">TOTAL / RERATA</td>
                        <td class="text-center">{{ number_format($grandTotalRitase, 0, ',', '.') }}</td>
                        <td class="text-center table-info">{{ number_format($overallRerataRitaseKalender, 2, ',', '.') }}</td>
                        <td class="text-center table-info">{{ number_format($overallRerataRitaseAktif, 2, ',', '.') }}</td>
                        <td class="text-end">{{ number_format($grandTotalNetto, 2, ',', '.') }} kg</td>
                        <td class="text-end table-success">{{ number_format($overallRerataNettoKalender, 2, ',', '.') }} kg</td>
                        <td class="text-end table-success">{{ number_format($overallRerataNettoAktif, 2, ',', '.') }} kg</td>
                        <td class="text-center">{{ $totalCalendarDays }}</td>
                        <td class="text-center">{{ $totalActiveDays }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
