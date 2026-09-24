@extends('layouts.admin')

@section('title', 'Edit Hari Libur - TPST Performance Management')

@section('content')
<div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
    <div>
        <h1 class="h3 mb-1 text-primary fw-bold">
            <i class="cil-calendar me-2"></i>Edit Hari Libur
        </h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item">Performance (KPI)</li>
                <li class="breadcrumb-item"><a href="{{ route('admin.kpi.holidays.index') }}">Hari Libur</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <div class="col-md-7 col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0 text-dark fw-bold">Form Edit Hari Libur</h5>
                <small class="text-muted">Perubahan tanggal atau nama hari libur akan langsung berpengaruh pada perhitungan KPI.</small>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.kpi.holidays.update', $holiday) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="tanggal" class="form-label fw-semibold">Tanggal Libur <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('tanggal') is-invalid @enderror" id="tanggal" name="tanggal" value="{{ old('tanggal', is_string($holiday->tanggal) ? $holiday->tanggal : $holiday->tanggal->format('Y-m-d')) }}" required>
                        @error('tanggal')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="nama" class="form-label fw-semibold">Nama Hari Libur / Keterangan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama" value="{{ old('nama', $holiday->nama) }}" placeholder="Contoh: Hari Raya Idul Fitri 1447 H, Tahun Baru, Cuti Bersama" required>
                        @error('nama')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4 pt-2 border-top">
                        <a href="{{ route('admin.kpi.holidays.index') }}" class="btn btn-secondary">
                            <i class="cil-arrow-left me-1"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="cil-save me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
