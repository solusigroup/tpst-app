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
                        <option value="{{ $val }}" {{ old('kategori', $hasilPilahan->kategori ?? 'Anorganik') == $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @error('kategori') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-12">
                <label class="form-label">Jenis (Waste Category) <span class="text-danger">*</span></label>
                <select name="waste_category_id" class="form-select @error('waste_category_id') is-invalid @enderror" required>
                    <option value="">-- Pilih Jenis --</option>
                    @foreach($wasteCategories as $cat)
                        <option value="{{ $cat->id }}" {{ old('waste_category_id', $hasilPilahan->waste_category_id ?? '') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
                @error('waste_category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                <small class="text-muted">Jenis ini akan otomatis ditautkan ke modul Employee Output (HRD)</small>
            </div>
            <div class="col-md-4">
                <label class="form-label">Tonase (kg) <span class="text-danger">*</span></label>
                <input type="number" step="0.01" name="tonase" class="form-control @error('tonase') is-invalid @enderror" value="{{ old('tonase', $hasilPilahan->tonase ?? '') }}" required>
                @error('tonase') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Jml Bal</label>
                <input type="number" name="jml_bal" class="form-control @error('jml_bal') is-invalid @enderror" value="{{ old('jml_bal', $hasilPilahan->jml_bal ?? '') }}" placeholder="Kosongkan jika tidak ada">
                @error('jml_bal') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Petugas <span class="text-danger">*</span></label>
                <select name="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                    <option value="">-- Pilih Petugas --</option>
                    @foreach($petugas as $p)
                        <option value="{{ $p->id }}" {{ old('user_id', $hasilPilahan->user_id ?? '') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
                @error('user_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                <small class="text-muted">Data output karyawan ini akan otomatis ter-update di modul HRD</small>
            </div>
            <div class="col-12">
                <label class="form-label">Keterangan</label>
                <textarea name="keterangan" class="form-control" rows="3">{{ old('keterangan', $hasilPilahan->keterangan ?? '') }}</textarea>
            </div>

            {{-- Info box integrasi --}}
            <div class="col-12">
                <div class="alert alert-info mb-0">
                    <i class="cil-sync me-1"></i>
                    <strong>Integrasi Otomatis:</strong> Data hasil pilahan ini akan otomatis di-sinkronkan ke modul <strong>Employee Output (HRD)</strong>.
                    Total tonase per petugas per jenis per hari akan diagregasikan menjadi satu record output karyawan.
                </div>
            </div>

            <div class="col-12 d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="cil-save me-1"></i> {{ isset($hasilPilahan) ? 'Perbarui' : 'Simpan' }}</button>
                <a href="{{ route('admin.hasil-pilahan.index') }}" class="btn btn-outline-secondary">Kembali</a>
            </div>
        </div>
    </form>
</div></div></div></div>
@endsection
