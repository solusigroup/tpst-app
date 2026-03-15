@extends('layouts.admin')
@section('title', isset($armada) ? 'Edit Armada' : 'Tambah Armada')

@section('content')
<div class="page-header">
    <div>
        <h1>{{ isset($armada) ? 'Edit Armada' : 'Tambah Armada' }}</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li><li class="breadcrumb-item"><a href="{{ route('admin.armada.index') }}">Armada</a></li><li class="breadcrumb-item active">{{ isset($armada) ? 'Edit' : 'Tambah' }}</li></ol></nav>
    </div>
</div>

<div class="row"><div class="col-lg-8"><div class="card"><div class="card-body">
    <form method="POST" action="{{ isset($armada) ? route('admin.armada.update', $armada) : route('admin.armada.store') }}">
        @csrf @if(isset($armada)) @method('PUT') @endif
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Klien <span class="text-danger">*</span></label>
                <select name="klien_id" class="form-select @error('klien_id') is-invalid @enderror" required>
                    <option value="">-- Pilih --</option>
                    @foreach($kliens as $k)<option value="{{ $k->id }}" {{ old('klien_id', $armada->klien_id ?? '') == $k->id ? 'selected' : '' }}>{{ $k->nama_klien }}</option>@endforeach
                </select>
                @error('klien_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Plat Nomor <span class="text-danger">*</span></label>
                <input type="text" name="plat_nomor" class="form-control @error('plat_nomor') is-invalid @enderror" value="{{ old('plat_nomor', $armada->plat_nomor ?? '') }}" required>
                @error('plat_nomor') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Kapasitas Maksimal (kg) <span class="text-danger">*</span></label>
                <input type="number" name="kapasitas_maksimal" class="form-control @error('kapasitas_maksimal') is-invalid @enderror" value="{{ old('kapasitas_maksimal', $armada->kapasitas_maksimal ?? '') }}" required>
                @error('kapasitas_maksimal') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-12 d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="cil-save me-1"></i> {{ isset($armada) ? 'Perbarui' : 'Simpan' }}</button>
                <a href="{{ route('admin.armada.index') }}" class="btn btn-outline-secondary">Kembali</a>
            </div>
        </div>
    </form>
</div></div></div></div>
@endsection
