@extends('layouts.admin')
@section('title', 'Audit Check - Tracing')

@section('content')
<div class="page-header">
    <div>
        <h1>Audit & Diagnostic Check</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.tracing.index') }}">Tracing</a></li>
                <li class="breadcrumb-item active">Audit Check</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.tracing.index') }}" class="btn btn-outline-secondary"><i class="cil-arrow-left me-1"></i> Kembali</a>
        <form method="POST" action="{{ route('admin.tracing.sync') }}" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-success" onclick="return confirm('Jalankan sinkronisasi otomatis untuk memperbaiki semua data yang tidak sinkron?')">
                <i class="cil-sync me-1"></i> Sinkronisasi & Perbaiki Otomatis
            </button>
        </form>
    </div>
</div>

{{-- Summary --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="card border-start border-start-4 border-start-danger h-100">
            <div class="card-body py-3">
                <div class="text-body-secondary small text-uppercase fw-semibold mb-1">Kritis</div>
                <div class="fs-4 fw-bold text-danger">{{ collect($issues)->where('severity', 'danger')->count() }}</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card border-start border-start-4 border-start-warning h-100">
            <div class="card-body py-3">
                <div class="text-body-secondary small text-uppercase fw-semibold mb-1">Peringatan</div>
                <div class="fs-4 fw-bold text-warning">{{ collect($issues)->where('severity', 'warning')->count() }}</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card border-start border-start-4 border-start-info h-100">
            <div class="card-body py-3">
                <div class="text-body-secondary small text-uppercase fw-semibold mb-1">Informasi</div>
                <div class="fs-4 fw-bold text-info">{{ collect($issues)->where('severity', 'info')->count() }}</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card border-start border-start-4 {{ count($issues) === 0 ? 'border-start-success' : 'border-start-secondary' }} h-100">
            <div class="card-body py-3">
                <div class="text-body-secondary small text-uppercase fw-semibold mb-1">Total Temuan</div>
                <div class="fs-4 fw-bold {{ count($issues) === 0 ? 'text-success' : '' }}">{{ count($issues) }}</div>
            </div>
        </div>
    </div>
</div>

@if(count($issues) === 0)
<div class="card">
    <div class="card-body text-center py-5">
        <i class="cil-check-circle text-success" style="font-size: 3rem;"></i>
        <h4 class="mt-3 text-success">Semua data terverifikasi</h4>
        <p class="text-body-secondary">Tidak ditemukan ketidaksesuaian antara Invoice, Jurnal, dan Buku Pembantu.</p>
    </div>
</div>
@else
<div class="card">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="cil-list me-2"></i> Daftar Temuan ({{ count($issues) }})</h5>
    </div>
    <div class="card-body p-0">
        <div class="list-group list-group-flush">
            @foreach($issues as $issue)
            <div class="list-group-item d-flex align-items-start gap-3 py-3">
                <div>
                    @if($issue['severity'] === 'danger')
                        <span class="badge bg-danger rounded-circle p-2"><i class="cil-warning"></i></span>
                    @elseif($issue['severity'] === 'warning')
                        <span class="badge bg-warning text-dark rounded-circle p-2"><i class="cil-warning"></i></span>
                    @else
                        <span class="badge bg-info rounded-circle p-2"><i class="cil-info"></i></span>
                    @endif
                </div>
                <div class="flex-grow-1">
                    <div class="fw-semibold small">
                        @if($issue['type'] === 'missing_journal') Jurnal Hilang
                        @elseif($issue['type'] === 'missing_buku_pembantu') Buku Pembantu Hilang
                        @elseif($issue['type'] === 'uninvoiced_penjualan') Penjualan Belum Di-invoice
                        @elseif($issue['type'] === 'amount_mismatch') Selisih Nominal
                        @elseif($issue['type'] === 'status_mismatch') Status Tidak Sinkron
                        @else {{ ucfirst(str_replace('_', ' ', $issue['type'])) }}
                        @endif
                    </div>
                    <div class="text-body-secondary small">{{ $issue['message'] }}</div>
                </div>
                <div>
                    <a href="{{ $issue['link'] }}" class="btn btn-sm btn-outline-primary"><i class="cil-external-link me-1"></i> Lihat</a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif
@endsection
