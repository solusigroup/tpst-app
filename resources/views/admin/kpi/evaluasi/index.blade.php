@extends('layouts.admin')

@section('title', 'Evaluasi & Supervisi PT PBS - Performance Management')

@section('content')
<div class="container-fluid">
    {{-- Header & Generate Action --}}
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body p-3">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <h4 class="mb-1 text-primary fw-bold">
                        <i class="cil-task me-2"></i>Evaluasi KPI & Dokumen Supervisi PT PBS
                    </h4>
                    <p class="text-muted mb-0 small">
                        Rekapitulasi Pengukuran Mingguan & Bulanan Berjenjang (Operator: PT Tata Bumi Adilimbah | Supervisi: PT Pinastika Bhakti Semesta)
                    </p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#generateModal">
                        <i class="cil-plus me-1"></i> Generate Evaluasi Baru
                    </button>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="cil-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if(session('warning'))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="cil-warning me-1"></i> {{ session('warning') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Filter Tabs --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="btn-group btn-group-sm">
            <a href="{{ route('admin.kpi.evaluasi.index', ['periode_tipe' => 'mingguan']) }}" 
               class="btn {{ $tipe === 'mingguan' ? 'btn-primary' : 'btn-outline-primary' }}">
               Evaluasi Mingguan
            </a>
            <a href="{{ route('admin.kpi.evaluasi.index', ['periode_tipe' => 'bulanan']) }}" 
               class="btn {{ $tipe === 'bulanan' ? 'btn-primary' : 'btn-outline-primary' }}">
               Rekap Bulanan
            </a>
        </div>
    </div>

    {{-- Daftar Evaluasi --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Periode</th>
                            <th>Target Penilaian</th>
                            <th class="text-center">Total Skor</th>
                            <th class="text-center">Predikat</th>
                            <th>Status Approval</th>
                            <th>Supervisi PT PBS</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($evaluations as $index => $eval)
                        <tr>
                            <td>{{ $evaluations->firstItem() + $index }}</td>
                            <td>
                                <span class="fw-semibold">
                                    {{ $eval->periode_mulai->format('d/m/Y') }} s/d {{ $eval->periode_selesai->format('d/m/Y') }}
                                </span>
                                <div class="small text-muted text-uppercase">{{ $eval->periode_tipe }}</div>
                            </td>
                            <td>
                                @if($eval->target_tipe === 'global')
                                    <span class="badge bg-success">KPI Global TPST</span>
                                @else
                                    <span class="fw-semibold text-capitalize">{{ str_replace('_', ' ', $eval->jabatan) }}</span>
                                    <div class="small text-muted">{{ $eval->user ? $eval->user->name : '-' }}</div>
                                @endif
                            </td>
                            <td class="text-center fw-bold text-primary fs-6">{{ $eval->total_skor }}%</td>
                            <td class="text-center">
                                <span class="badge {{ $eval->total_skor >= 100 ? 'bg-success' : ($eval->total_skor >= 85 ? 'bg-info' : 'bg-warning text-dark') }}">
                                    {{ $eval->predikat }}
                                </span>
                            </td>
                            <td>
                                @if($eval->status === 'approved')
                                    <span class="badge bg-success"><i class="cil-check me-1"></i>Approved</span>
                                @elseif($eval->status === 'submitted')
                                    <span class="badge bg-warning text-dark"><i class="cil-clock me-1"></i>Menunggu Supervisi PBS</span>
                                @elseif($eval->status === 'revision')
                                    <span class="badge bg-danger"><i class="cil-reload me-1"></i>Perlu Revisi</span>
                                @else
                                    <span class="badge bg-secondary">Draft</span>
                                @endif
                            </td>
                            <td>
                                <small>
                                    @if($eval->status === 'approved')
                                        <strong>{{ $eval->approved_by_name ?? 'Supervisi PBS' }}</strong>
                                        <div class="text-muted" style="font-size: 0.75rem;">{{ $eval->approved_at ? $eval->approved_at->format('d/m/Y H:i') : '' }}</div>
                                    @else
                                        <span class="text-muted">PT PBS (Pending)</span>
                                    @endif
                                </small>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.kpi.evaluasi.show', $eval) }}" class="btn btn-outline-primary" title="Lihat Rincian & Verifikasi">
                                        <i class="cil-magnifying-glass"></i> Periksa
                                    </a>
                                    @if($eval->status === 'approved')
                                    <a href="{{ route('admin.kpi.evaluasi.export-pdf', $eval) }}" class="btn btn-outline-danger" title="Download PDF Resmi">
                                        <i class="cil-print"></i> PDF
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                Belum ada dokumen evaluasi KPI. Klik tombol <strong>"Generate Evaluasi Baru"</strong> untuk menarik data otomatis.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($evaluations->hasPages())
        <div class="card-footer bg-white">
            {{ $evaluations->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>

{{-- Modal Generate Evaluasi --}}
<div class="modal fade" id="generateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.kpi.evaluasi.generate') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Generate Dokumen Evaluasi KPI</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="small text-muted">
                        Sistem akan mengagregasi data operasional (tonase ritase, checklist kebersihan & bau, log mesin, invoice tipping fee, dan data presensi) secara otomatis.
                    </p>
                    <div class="mb-3">
                        <label class="form-label">Siklus Periode <span class="text-danger">*</span></label>
                        <select name="periode_tipe" class="form-select" required>
                            <option value="mingguan" selected>Mingguan (Evaluasi Pekanan)</option>
                            <option value="bulanan">Bulanan (Appraisal Rekap Bulanan)</option>
                        </select>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                            <input type="date" name="periode_mulai" class="form-control" value="{{ \Carbon\Carbon::today()->startOfWeek()->toDateString() }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Tanggal Selesai <span class="text-danger">*</span></label>
                            <input type="date" name="periode_selesai" class="form-control" value="{{ \Carbon\Carbon::today()->endOfWeek()->toDateString() }}" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Target Penilaian <span class="text-danger">*</span></label>
                        <select name="target_tipe" class="form-select" required>
                            <option value="global" selected>Level 1: KPI Global TPST Megilan (40% Weight)</option>
                            <option value="site_manager">Level 2: Budi Sucahyo (Site Manager)</option>
                            <option value="admin_commercial">Level 2: Nita Khoirunisa (Admin & Commercial)</option>
                            <option value="equipment_logistik">Level 2: Agung (Equipment, Logistik & Receiving)</option>
                            <option value="operasional_qc">Level 2: Ana Maria (Operasional & Quality Control)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Generate Draft Evaluasi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
