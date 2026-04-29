@extends('layouts.admin')
@section('title', 'Laporan Kehadiran Karyawan')

@section('content')


<div class="page-header d-print-none">
    <div><h1>Laporan Kehadiran Karyawan</h1></div>
    <div class="d-flex gap-2 align-items-center">
        <button type="button" class="btn btn-outline-primary shadow-sm" data-coreui-toggle="modal" data-coreui-target="#previewModal">
            <i class="cil-zoom-in me-1"></i> Preview & Cetak
        </button>
        <div class="btn-group shadow-sm">
            <a href="{{ route('admin.laporan-operasional.kehadiran', array_merge(request()->all(), ['export' => 'pdf'])) }}" target="_blank" class="btn btn-danger" title="Export PDF">
                <i class="cil-file me-1"></i> PDF
            </a>
            <a href="{{ route('admin.laporan-operasional.kehadiran', array_merge(request()->all(), ['export' => 'excel'])) }}" class="btn btn-success" title="Export Excel">
                <i class="cil-spreadsheet me-1"></i> Excel
            </a>
        </div>
    </div>
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

<!-- Modal Preview -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header d-print-none">
                <h5 class="modal-title" id="previewModalLabel">Preview Laporan Kehadiran</h5>
                <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-light p-0">
                <div id="printArea" class="bg-white p-5 shadow-sm mx-auto" style="max-width: 21cm; min-height: 29.7cm;">
                    <x-kop-surat />
                    
                    <div class="text-center mb-4">
                        <h4 class="fw-bold text-uppercase mb-1">LAPORAN KEHADIRAN KARYAWAN</h4>
                        <p class="text-secondary">Periode: {{ \Carbon\Carbon::parse($dari)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($sampai)->format('d/m/Y') }}</p>
                    </div>

                    <div class="mb-3">
                        <strong>Ringkasan Status:</strong><br>
                        Hadir: {{ $totals->present }} | Alpa: {{ $totals->absent }} | Sakit: {{ $totals->sick }} | Izin: {{ $totals->leave }} | Total: {{ $totals->total_rows }}
                    </div>

                    <table class="table table-bordered border-dark table-sm">
                        <thead class="table-light border-dark">
                            <tr>
                                <th class="text-center" style="width: 40px;">No</th>
                                <th>Tanggal</th>
                                <th>Nama Karyawan</th>
                                <th>Status</th>
                                <th>Masuk</th>
                                <th>Keluar</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php 
                                $allRowsForPrint = \App\Models\Attendance::with('user')
                                    ->when($dari, fn($q)=>$q->whereDate('attendance_date','>=',$dari))
                                    ->when($sampai, fn($q)=>$q->whereDate('attendance_date','<=',$sampai))
                                    ->when($userId, fn($q)=>$q->where('user_id',$userId))
                                    ->orderByDesc('attendance_date')
                                    ->get(); 
                                $statusLabels = [
                                    'present' => 'Hadir',
                                    'absent' => 'Alpa',
                                    'sick' => 'Sakit',
                                    'leave' => 'Izin'
                                ];
                            @endphp
                            @foreach($allRowsForPrint as $index => $r)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>{{ \Carbon\Carbon::parse($r->attendance_date)->format('d/m/Y') }}</td>
                                <td>{{ $r->user->name ?? '-' }}</td>
                                <td>{{ $statusLabels[$r->status] ?? $r->status }}</td>
                                <td>{{ $r->check_in ? \Carbon\Carbon::parse($r->check_in)->format('H:i') : '-' }}</td>
                                <td>{{ $r->check_out ? \Carbon\Carbon::parse($r->check_out)->format('H:i') : '-' }}</td>
                                <td>{{ $r->notes ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="row mt-5">
                        <div class="col-8"></div>
                        <div class="col-4 text-center">
                            <p class="mb-5">Dicetak pada: {{ now()->format('d/m/Y H:i') }}</p>
                            <div class="mt-5">
                                <p class="fw-bold mb-0">( ____________________ )</p>
                                <p class="text-secondary small">Admin Operasional</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer d-print-none">
                <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="window.print()">
                    <i class="cil-print me-1"></i> Cetak Sekarang
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    @media print {
        body { 
            overflow: visible !important; 
            height: auto !important; 
            background: white !important;
        }
        .sidebar, .header, .mobile-bottom-nav, .modal-backdrop, .breadcrumb, .page-header, .card, form, .no-print, .d-print-none {
            display: none !important;
        }
        .wrapper { padding: 0 !important; margin: 0 !important; }
        .body { padding: 0 !important; margin: 0 !important; }
        .container-fluid { padding: 0 !important; margin: 0 !important; }
        .modal {
            display: block !important;
            position: static !important;
            left: 0 !important;
            top: 0 !important;
            width: 100% !important;
            opacity: 1 !important;
            visibility: visible !important;
            background: white !important;
            overflow: visible !important;
            height: auto !important;
        }
        .modal-dialog {
            max-width: 100% !important;
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            overflow: visible !important;
            height: auto !important;
        }
        .modal-content, .modal-body {
            display: block !important;
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
            background: white !important;
            visibility: visible !important;
            opacity: 1 !important;
            overflow: visible !important;
            height: auto !important;
            max-height: none !important;
        }
        #printArea {
            visibility: visible !important;
            opacity: 1 !important;
            display: block !important;
            padding: 0 !important;
            margin: 0 !important;
            max-width: 100% !important;
            min-height: auto !important;
            box-shadow: none !important;
        }
        #printArea * {
            visibility: visible !important;
            opacity: 1 !important;
        }
    }
</style>
@endpush
