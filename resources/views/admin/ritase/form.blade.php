@extends('layouts.admin')
@section('title', isset($ritase) ? 'Edit Ritase' : 'Tambah Ritase')

@section('content')
<div class="page-header">
    <div>
        <h1>{{ isset($ritase) ? 'Edit Ritase' : 'Tambah Ritase' }}</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li><li class="breadcrumb-item"><a href="{{ route('admin.ritase.index') }}">Ritase</a></li><li class="breadcrumb-item active">{{ isset($ritase) ? 'Edit' : 'Tambah' }}</li></ol></nav>
    </div>
</div>

<form method="POST" action="{{ isset($ritase) ? route('admin.ritase.update', $ritase) : route('admin.ritase.store') }}">
    @csrf
    @if(isset($ritase)) @method('PUT') @endif

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-white"><h6 class="mb-0 fw-semibold">Informasi Ritase</h6></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Armada <span class="text-danger">*</span></label>
                            <select name="armada_id" class="form-select @error('armada_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Armada --</option>
                                @foreach($armadas as $a)
                                    <option value="{{ $a->id }}" {{ old('armada_id', $ritase->armada_id ?? '') == $a->id ? 'selected' : '' }}>{{ $a->plat_nomor }}</option>
                                @endforeach
                            </select>
                            @error('armada_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Klien <span class="text-danger">*</span></label>
                            <select name="klien_id" class="form-select @error('klien_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Klien --</option>
                                @foreach($kliens as $k)
                                    <option value="{{ $k->id }}" {{ old('klien_id', $ritase->klien_id ?? '') == $k->id ? 'selected' : '' }}>{{ $k->nama_klien }}</option>
                                @endforeach
                            </select>
                            @error('klien_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Waktu Masuk <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="waktu_masuk" class="form-control @error('waktu_masuk') is-invalid @enderror" value="{{ old('waktu_masuk', isset($ritase) ? \Carbon\Carbon::parse($ritase->waktu_masuk)->format('Y-m-d\TH:i') : '') }}" required>
                            @error('waktu_masuk') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Waktu Keluar</label>
                            <input type="datetime-local" name="waktu_keluar" class="form-control @error('waktu_keluar') is-invalid @enderror" value="{{ old('waktu_keluar', isset($ritase) && $ritase->waktu_keluar ? \Carbon\Carbon::parse($ritase->waktu_keluar)->format('Y-m-d\TH:i') : '') }}">
                            @error('waktu_keluar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header bg-white"><h6 class="mb-0 fw-semibold">Pengukuran Berat</h6></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Berat Bruto (kg) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="berat_bruto" id="berat_bruto" class="form-control @error('berat_bruto') is-invalid @enderror" value="{{ old('berat_bruto', $ritase->berat_bruto ?? '') }}" required oninput="calcNetto()">
                            @error('berat_bruto') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Berat Tarra (kg) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="berat_tarra" id="berat_tarra" class="form-control @error('berat_tarra') is-invalid @enderror" value="{{ old('berat_tarra', $ritase->berat_tarra ?? '') }}" required oninput="calcNetto()">
                            @error('berat_tarra') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Berat Netto (kg)</label>
                            <input type="number" step="0.01" id="berat_netto" class="form-control bg-light" value="{{ old('berat_netto', $ritase->berat_netto ?? '0') }}" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-white"><h6 class="mb-0 fw-semibold">Detail</h6></div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Jenis Sampah</label>
                        <input type="text" name="jenis_sampah" class="form-control" value="{{ old('jenis_sampah', $ritase->jenis_sampah ?? '') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Biaya Tipping (Rp)</label>
                        <input type="number" name="biaya_tipping" class="form-control" value="{{ old('biaya_tipping', $ritase->biaya_tipping ?? '') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            @foreach(['masuk'=>'Masuk','timbang'=>'Timbang','keluar'=>'Keluar','selesai'=>'Selesai'] as $val => $label)
                                <option value="{{ $val }}" {{ old('status', $ritase->status ?? 'masuk') == $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-body d-grid gap-2">
                    <button type="submit" class="btn btn-primary"><i class="cil-save me-1"></i> {{ isset($ritase) ? 'Perbarui' : 'Simpan' }}</button>
                    <a href="{{ route('admin.ritase.index') }}" class="btn btn-outline-secondary">Kembali</a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
function calcNetto() {
    const bruto = parseFloat(document.getElementById('berat_bruto').value) || 0;
    const tarra = parseFloat(document.getElementById('berat_tarra').value) || 0;
    document.getElementById('berat_netto').value = (bruto - tarra).toFixed(2);
}
</script>
@endpush
