@extends('layouts.admin')
@section('title', isset($klien) ? 'Edit Klien' : 'Tambah Klien')

@section('content')
<div class="page-header">
    <div>
        <h1>{{ isset($klien) ? 'Edit Klien' : 'Tambah Klien' }}</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li><li class="breadcrumb-item"><a href="{{ route('admin.klien.index') }}">Klien</a></li><li class="breadcrumb-item active">{{ isset($klien) ? 'Edit' : 'Tambah' }}</li></ol></nav>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ isset($klien) ? route('admin.klien.update', $klien) : route('admin.klien.store') }}">
                    @csrf
                    @if(isset($klien)) @method('PUT') @endif
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Klien <span class="text-danger">*</span></label>
                            <input type="text" name="nama_klien" class="form-control @error('nama_klien') is-invalid @enderror" value="{{ old('nama_klien', $klien->nama_klien ?? '') }}" required>
                            @error('nama_klien') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jenis <span class="text-danger">*</span></label>
                            <select name="jenis" id="jenis_select" class="form-select @error('jenis') is-invalid @enderror" required>
                                <option value="">-- Pilih --</option>
                                @foreach(['DLH'=>'DLH','Swasta'=>'Swasta','Offtaker'=>'Offtaker','Internal'=>'Internal'] as $val => $label)
                                    <option value="{{ $val }}" {{ old('jenis', $klien->jenis ?? '') == $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('jenis') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6" id="tarif_container" style="display: {{ old('jenis', $klien->jenis ?? '') == 'Swasta' ? 'block' : 'none' }}">
                            <label class="form-label">Tarif Bulanan</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="tarif_bulanan" class="form-control" value="{{ old('tarif_bulanan', $klien->tarif_bulanan ?? '') }}" min="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kontak</label>
                            <input type="text" name="kontak" class="form-control" value="{{ old('kontak', $klien->kontak ?? '') }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Alamat</label>
                            <textarea name="alamat" class="form-control" rows="3">{{ old('alamat', $klien->alamat ?? '') }}</textarea>
                        </div>
                        <div class="col-12 d-flex gap-2">
                            <button type="submit" class="btn btn-primary"><i class="cil-save me-1"></i> {{ isset($klien) ? 'Perbarui' : 'Simpan' }}</button>
                            <a href="{{ route('admin.klien.index') }}" class="btn btn-outline-secondary">Kembali</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const jenisSelect = document.getElementById('jenis_select');
    const tarifContainer = document.getElementById('tarif_container');

    jenisSelect.addEventListener('change', function() {
        if (this.value === 'Swasta') {
            tarifContainer.style.display = 'block';
        } else {
            tarifContainer.style.display = 'none';
        }
    });
});
</script>
@endsection
