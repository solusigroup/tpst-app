@extends('layouts.admin')
@section('title', 'Rekap Kehadiran Karyawan')

@section('content')

<div class="page-header d-print-none">
    <div><h1>Rekap Kehadiran Karyawan</h1></div>
    <div class="d-flex gap-2 align-items-center">
        <div class="btn-group shadow-sm">
            <a href="{{ route('admin.laporan-operasional.kehadiran', array_merge(request()->all(), ['export' => 'pdf'])) }}" target="_blank" class="btn btn-danger" title="Export PDF">
                <i class="cil-file me-1"></i> PDF
            </a>
        </div>
    </div>
</div>

<div class="card mb-4 d-print-none">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-auto">
                <label class="form-label mb-0 small text-body-secondary">Bulan</label>
                <select name="month" class="form-select">
                    <option value="">-- Bebas --</option>
                    @foreach([
                        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
                        '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
                        '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                    ] as $m => $name)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <label class="form-label mb-0 small text-body-secondary">Tahun</label>
                <select name="year" class="form-select">
                    @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-auto">
                <div class="vr h-100 mx-2"></div>
            </div>
            <div class="col-auto">
                <label class="form-label mb-0 small text-body-secondary">Dari</label>
                <input type="date" name="dari" class="form-control" value="{{ $dari }}">
            </div>
            <div class="col-auto">
                <label class="form-label mb-0 small text-body-secondary">Sampai</label>
                <input type="date" name="sampai" class="form-control" value="{{ $sampai }}">
            </div>
            <div class="col-auto">
                <label class="form-label mb-0 small text-body-secondary">Tipe Gaji</label>
                <select name="salary_type" class="form-select">
                    <option value="">-- Semua --</option>
                    <option value="harian" {{ $salaryType == 'harian' ? 'selected' : '' }}>Harian</option>
                    <option value="bulanan" {{ $salaryType == 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                    <option value="borongan" {{ $salaryType == 'borongan' ? 'selected' : '' }}>Borongan</option>
                </select>
            </div>
            <div class="col-auto">
                <label class="form-label mb-0 small text-body-secondary">Karyawan</label>
                <select name="user_id" class="form-select">
                    <option value="">-- Semua --</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ $userId == $u->id ? 'selected' : '' }}>
                            {{ $u->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <label class="form-label mb-0 small text-body-secondary">Tampilan</label>
                <select name="mode" class="form-select fw-bold text-primary">
                    <option value="detail" {{ $mode == 'detail' ? 'selected' : '' }}>DETAIL HARIAN</option>
                    <option value="rekap" {{ $mode == 'rekap' ? 'selected' : '' }}>REKAP BULANAN</option>
                </select>
            </div>
            <div class="col-auto">
                <button class="btn btn-primary" type="submit">
                    <i class="cil-filter me-1"></i> Filter
                </button>
                <a href="{{ route('admin.laporan-operasional.kehadiran') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="50">No</th>
                        <th>Nama Karyawan</th>
                        <th>Tipe Gaji</th>
                        <th class="text-center">Hadir (H)</th>
                        <th class="text-center">Sakit (S)</th>
                        <th class="text-center">Izin (I)</th>
                        <th class="text-center">Alpa (A)</th>
                        <th class="text-center">Total Hari</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rekapData as $index => $emp)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <strong>{{ $emp->name }}</strong><br>
                            <small class="text-muted">{{ $emp->position ?? '-' }}</small>
                        </td>
                        <td><span class="text-capitalize">{{ $emp->salary_type ?? '-' }}</span></td>
                        <td class="text-center fw-bold text-success">{{ $emp->present_count }}</td>
                        <td class="text-center text-warning">{{ $emp->sick_count }}</td>
                        <td class="text-center text-info">{{ $emp->leave_count }}</td>
                        <td class="text-center text-danger">{{ $emp->absent_count }}</td>
                        <td class="text-center bg-light fw-bold">{{ $emp->total_days }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-body-secondary">
                            Belum ada data kehadiran pada periode ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
