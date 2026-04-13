@extends('layouts.admin')
@section('title', 'Laporan Kehadiran Karyawan')

@section('content')
<div class="d-none d-print-block">
    <x-kop-surat />
</div>

<div class="page-header d-print-none">
    <div>
        <h1>Laporan Kehadiran Karyawan</h1>
    </div>
    <button class="btn btn-outline-secondary" onclick="window.print()">
        <i class="cil-print me-1"></i> Print
    </button>
</div>

<div class="card mb-4 d-print-none">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-auto">
                <label class="form-label mb-0 small text-body-secondary">Dari</label>
                <input type="date" name="dari" class="form-control" value="{{ $dari }}">
            </div>
            <div class="col-auto">
                <label class="form-label mb-0 small text-body-secondary">Sampai</label>
                <input type="date" name="sampai" class="form-control" value="{{ $sampai }}">
            </div>
            <div class="col-auto">
                <label class="form-label mb-0 small text-body-secondary">Karyawan</label>
                <select name="user_id" class="form-select">
                    <option value="">-- Semua Karyawan --</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ $userId == $u->id ? 'selected' : '' }}>
                            {{ $u->name }}
                        </option>
                    @endforeach
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

<div class="card" id="printable">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Tanggal</th>
                        <th>Nama Karyawan</th>
                        <th>Check In</th>
                        <th>Check Out</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $r)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($r->attendance_date)->format('d M Y') }}</td>
                        <td><strong>{{ $r->user->name ?? '-' }}</strong></td>
                        <td>{{ $r->check_in ? \Carbon\Carbon::parse($r->check_in)->format('H:i') : '-' }}</td>
                        <td>{{ $r->check_out ? \Carbon\Carbon::parse($r->check_out)->format('H:i') : '-' }}</td>
                        <td>
                            @php 
                                $statusColors = [
                                    'present' => 'success',
                                    'absent' => 'danger',
                                    'sick' => 'warning',
                                    'leave' => 'info'
                                ]; 
                                $statusLabels = [
                                    'present' => 'Hadir',
                                    'absent' => 'Alpa',
                                    'sick' => 'Sakit',
                                    'leave' => 'Izin'
                                ];
                            @endphp
                            <span class="badge bg-{{ $statusColors[$r->status] ?? 'secondary' }}">
                                {{ $statusLabels[$r->status] ?? $r->status }}
                            </span>
                        </td>
                        <td>{{ $r->notes ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-body-secondary">
                            Belum ada data kehadiran pada periode ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot class="border-top border-2 fw-bold">
                    <tr>
                        <td colspan="4" class="text-end">RINGKASAN ({{ number_format($totals->total_rows, 0, ',', '.') }} Record)</td>
                        <td colspan="2">
                            <div class="d-flex gap-3">
                                <span class="text-success">Hadir: {{ $totals->present }}</span>
                                <span class="text-danger">Alpa: {{ $totals->absent }}</span>
                                <span class="text-warning">Sakit: {{ $totals->sick }}</span>
                                <span class="text-info">Izin: {{ $totals->leave }}</span>
                            </div>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    @if($rows->hasPages()) 
        <div class="card-footer bg-white d-print-none">
            {{ $rows->links() }}
        </div> 
    @endif
</div>
@endsection
