@extends('layouts.admin')
@section('title', isset($hasilPilahan) ? 'Edit Hasil Pilahan' : 'Tambah Hasil Pilahan')

@section('content')
<div class="page-header">
    <div>
        <h1>{{ isset($hasilPilahan) ? 'Edit' : 'Tambah' }} Hasil Pilahan</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li><li class="breadcrumb-item"><a href="{{ route('admin.hasil-pilahan.index') }}">Hasil Pilahan</a></li><li class="breadcrumb-item active">{{ isset($hasilPilahan) ? 'Edit' : 'Tambah' }}</li></ol></nav>
    </div>
</div>
<div class="row"><div class="col-lg-8"><div class="card"><div class="card-body">
    <form method="POST" action="{{ isset($hasilPilahan) ? route('admin.hasil-pilahan.update', $hasilPilahan) : route('admin.hasil-pilahan.store') }}">
        @csrf @if(isset($hasilPilahan)) @method('PUT') @endif
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                <input type="date" name="tanggal" class="form-control @error('tanggal') is-invalid @enderror" value="{{ old('tanggal', isset($hasilPilahan) ? $hasilPilahan->tanggal?->format('Y-m-d') : date('Y-m-d')) }}" required>
                @error('tanggal') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Kategori <span class="text-danger">*</span></label>
                <select name="kategori" class="form-select @error('kategori') is-invalid @enderror" required>
                    <option value="">-- Pilih --</option>
                    @foreach(['Organik'=>'Organik','Anorganik'=>'Anorganik','B3'=>'B3 (Bahan Berbahaya & Beracun)','Residu'=>'Residu'] as $val => $label)
                        <option value="{{ $val }}" {{ old('kategori', $hasilPilahan->kategori ?? '') == $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @error('kategori') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-12">
                <label class="form-label">Jenis <span class="text-danger">*</span></label>
                <input type="text" name="jenis" class="form-control @error('jenis') is-invalid @enderror" placeholder="cth: Plastik, Kertas, Logam, Kompos" value="{{ old('jenis', $hasilPilahan->jenis ?? '') }}" required>
                @error('jenis') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Tonase (kg) <span class="text-danger">*</span></label>
                <input type="number" step="0.01" name="tonase" class="form-control @error('tonase') is-invalid @enderror" value="{{ old('tonase', $hasilPilahan->tonase ?? '') }}" required>
                @error('tonase') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Petugas <span class="text-danger">*</span></label>
                <input type="text" name="officer" class="form-control @error('officer') is-invalid @enderror" value="{{ old('officer', $hasilPilahan->officer ?? '') }}" required>
                @error('officer') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-12">
                <label class="form-label">Keterangan</label>
                <textarea name="keterangan" class="form-control" rows="3">{{ old('keterangan', $hasilPilahan->keterangan ?? '') }}</textarea>
            </div>
            <div class="col-12 d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="cil-save me-1"></i> {{ isset($hasilPilahan) ? 'Perbarui' : 'Simpan' }}</button>
                <a href="{{ route('admin.hasil-pilahan.index') }}" class="btn btn-outline-secondary">Kembali</a>
            </div>
        </div>
    </form>
</div></div></div></div>
@endsection
