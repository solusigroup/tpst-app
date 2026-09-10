@extends('layouts.admin')

@section('title', 'Detail Evaluasi KPI & Verifikasi PT PBS')

@section('content')
<div class="container-fluid">
    {{-- Top Action & Status Bar --}}
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body p-3">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <a href="{{ route('admin.kpi.evaluasi.index') }}" class="btn btn-outline-secondary btn-sm mb-2">
                        <i class="cil-arrow-left me-1"></i> Kembali ke Daftar Evaluasi
                    </a>
                    <h4 class="mb-1 text-primary fw-bold">
                        Dokumen Evaluasi Kinerja (KPI)
                    </h4>
                    <p class="text-muted mb-0 small">
                        Periode: <strong>{{ $kpiEvaluation->periode_mulai->format('d/m/Y') }}</strong> s/d <strong>{{ $kpiEvaluation->periode_selesai->format('d/m/Y') }}</strong>
                        ({{ strtoupper($kpiEvaluation->periode_tipe) }}) | Target: <strong>{{ strtoupper(str_replace('_', ' ', $kpiEvaluation->jabatan)) }}</strong>
                    </p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    @if($kpiEvaluation->status === 'draft')
                        <form action="{{ route('admin.kpi.evaluasi.submit', $kpiEvaluation) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="cil-send me-1"></i> Ajukan ke Supervisi PT PBS
                            </button>
                        </form>
                    @elseif($kpiEvaluation->status === 'approved')
                        <a href="{{ route('admin.kpi.evaluasi.export-pdf', $kpiEvaluation) }}" class="btn btn-danger btn-sm">
                            <i class="cil-print me-1"></i> Cetak Dokumen PDF Resmi
                        </a>
                    @endif
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

    {{-- Score Header Summary --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-center py-3 h-100">
                <div class="card-body">
                    <span class="text-muted small text-uppercase fw-bold">Skor Akhir KPI</span>
                    <div class="display-5 fw-bold text-primary my-2">{{ $kpiEvaluation->total_skor }}%</div>
                    <span class="badge {{ $kpiEvaluation->total_skor >= 100 ? 'bg-success' : 'bg-info' }} px-3 py-2 fs-6">
                        {{ $kpiEvaluation->predikat }}
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm py-3 h-100">
                <div class="card-body">
                    <span class="text-muted small text-uppercase fw-bold">Status Persetujuan</span>
                    <div class="mt-2 mb-3">
                        @if($kpiEvaluation->status === 'approved')
                            <span class="badge bg-success fs-6"><i class="cil-check-circle me-1"></i> Disetujui Penuh (Approved)</span>
                        @elseif($kpiEvaluation->status === 'submitted')
                            <span class="badge bg-warning text-dark fs-6"><i class="cil-clock me-1"></i> Menunggu Review Supervisi PBS</span>
                        @elseif($kpiEvaluation->status === 'revision')
                            <span class="badge bg-danger fs-6"><i class="cil-reload me-1"></i> Perlu Revisi / Catatan Perbaikan</span>
                        @else
                            <span class="badge bg-secondary fs-6">Draft Internal</span>
                        @endif
                    </div>
                    <div class="small text-muted">
                        <div><strong>Lembaga Supervisi:</strong> {{ $kpiEvaluation->supervisor_institution }}</div>
                        @if($kpiEvaluation->submitted_at)
                            <div>Diajukan: {{ $kpiEvaluation->submitted_at->format('d/m/Y H:i') }}</div>
                        @endif
                        @if($kpiEvaluation->approved_at)
                            <div>Disetujui: {{ $kpiEvaluation->approved_at->format('d/m/Y H:i') }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm py-3 h-100">
                <div class="card-body">
                    <span class="text-muted small text-uppercase fw-bold">Pihak Terkait</span>
                    <div class="mt-2 small">
                        <div class="mb-1"><strong>Operator TPST:</strong> PT Tata Bumi Adilimbah</div>
                        <div class="mb-1"><strong>Site Manager:</strong> Budi Sucahyo</div>
                        <div class="mb-1"><strong>Supervisi:</strong> PT Pinastika Bhakti Semesta</div>
                        <div><strong>Klien Wilayah:</strong> DLH Kabupaten Lamongan</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel Rincian Skor Indikator KPI --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold text-dark">
                <i class="cil-list me-2 text-primary"></i>Rincian Matriks Penilaian Indikator
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Indikator KPI</th>
                            <th>Target Standar</th>
                            <th>Realisasi Lapangan</th>
                            <th class="text-center">Nilai (%)</th>
                            <th class="text-center">Bobot</th>
                            <th class="text-end">Skor Terbobot</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!empty($kpiEvaluation->rincian_kpi))
                            @foreach($kpiEvaluation->rincian_kpi as $idx => $row)
                            <tr>
                                <td>{{ $idx + 1 }}</td>
                                <td class="fw-semibold">{{ $row['nama'] }}</td>
                                <td><span class="badge bg-light text-dark border">{{ $row['target'] }}</span></td>
                                <td><span class="small">{{ $row['realisasi'] }}</span></td>
                                <td class="text-center">
                                    <span class="badge {{ $row['nilai'] >= 100 ? 'bg-success' : ($row['nilai'] >= 80 ? 'bg-info' : 'bg-warning text-dark') }}">
                                        {{ $row['nilai'] }}%
                                    </span>
                                </td>
                                <td class="text-center text-muted">{{ $row['bobot'] }}%</td>
                                <td class="text-end fw-bold text-primary">{{ $row['skor'] }}</td>
                            </tr>
                            @endforeach
                        @endif
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="5" class="text-end">Total Skor Tertimbang:</th>
                            <th class="text-center">100%</th>
                            <th class="text-end text-primary fs-5">{{ $kpiEvaluation->total_skor }}%</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    {{-- Panel Verifikasi & Approval Supervisi PT PBS --}}
    <div class="card border-0 shadow-sm border-top border-4 border-primary mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold text-dark">
                <i class="cil-shield-alt me-2 text-primary"></i>Lembar Verifikasi Supervisi Teknis (PT Pinastika Bhakti Semesta)
            </h5>
        </div>
        <div class="card-body">
            @if($kpiEvaluation->status === 'approved')
                <div class="alert alert-success">
                    <h6 class="fw-bold mb-1"><i class="cil-check-circle me-1"></i> Telah Diverifikasi & Disetujui</h6>
                    <p class="mb-1 small">
                        Dokumen evaluasi ini telah disetujui resmi oleh: <strong>{{ $kpiEvaluation->approved_by_name }}</strong> 
                        ({{ $kpiEvaluation->supervisor_institution }}) pada tanggal {{ $kpiEvaluation->approved_at->format('d F Y H:i') }}.
                    </p>
                    @if($kpiEvaluation->supervisor_notes)
                        <div class="mt-2 p-2 bg-white rounded border small">
                            <strong>Catatan Supervisi:</strong> {{ $kpiEvaluation->supervisor_notes }}
                        </div>
                    @endif
                </div>
            @else
                <form action="{{ route('admin.kpi.evaluasi.approve', $kpiEvaluation) }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Petugas Supervisi (PT PBS) <span class="text-danger">*</span></label>
                            <input type="text" name="supervisor_name" class="form-control" required value="{{ auth()->user()->name }}" placeholder="Contoh: Tim Supervisi PT Pinastika Bhakti Semesta">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tindakan / Keputusan Supervisi <span class="text-danger">*</span></label>
                            <select name="action" class="form-select" required>
                                <option value="approve" selected>Setujui Dokumen (Approve KPI)</option>
                                <option value="revision">Kembalikan untuk Revisi / Penjelasan Data</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Catatan & Rekomendasi Supervisi Teknis</label>
                            <textarea name="supervisor_notes" class="form-control" rows="3" placeholder="Tuliskan catatan teknis operasional, rekomendasi perbaikan alat/mesin, atau tanggapan terkait pencapaian..."></textarea>
                        </div>
                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="cil-check me-1"></i> Proses Verifikasi Supervisi
                            </button>
                        </div>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
