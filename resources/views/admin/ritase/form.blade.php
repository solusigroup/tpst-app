@extends('layouts.admin')
@section('title', isset($ritase) ? 'Edit Ritase' : 'Tambah Ritase')

@section('content')
<div class="page-header">
    <div>
        <h1>{{ isset($ritase) ? 'Edit Ritase' : 'Tambah Ritase' }}</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li><li class="breadcrumb-item"><a href="{{ route('admin.ritase.index') }}">Ritase</a></li><li class="breadcrumb-item active">{{ isset($ritase) ? 'Edit' : 'Tambah' }}</li></ol></nav>
    </div>
</div>

<form method="POST" action="{{ isset($ritase) ? route('admin.ritase.update', $ritase) : route('admin.ritase.store') }}" enctype="multipart/form-data">
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
                        <label class="form-label">Asal Sampah</label>
                        <input type="text" name="jenis_sampah" class="form-control" value="{{ old('jenis_sampah', $ritase->jenis_sampah ?? '') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Biaya Tipping (Rp)</label>
                        <input type="number" name="biaya_tipping" id="biaya_tipping" class="form-control" value="{{ old('biaya_tipping', $ritase->biaya_tipping ?? '') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            @foreach(['masuk'=>'Masuk','timbang'=>'Timbang','keluar'=>'Keluar','selesai'=>'Selesai'] as $val => $label)
                                <option value="{{ $val }}" {{ old('status', $ritase->status ?? 'masuk') == $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tiket (Manual)</label>
                        <input type="text" name="tiket" class="form-control" value="{{ old('tiket', $ritase->tiket ?? '') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Foto Timbangan Bruto (Maks 2MB)</label>
                        <div class="d-flex gap-2 mb-2">
                            <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('foto_tiket_bruto').click()">
                                <i class="cil-camera me-1"></i> Ambil Foto / Pilih File
                            </button>
                        </div>
                        <input type="file" name="foto_tiket_bruto" id="foto_tiket_bruto" class="form-control d-none @error('foto_tiket_bruto') is-invalid @enderror" accept="image/*" capture="environment" onchange="previewImageBruto(this)">
                        <div id="file-name-display-bruto" class="small text-muted mb-2"></div>
                        @error('foto_tiket_bruto') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        
                        <div id="image-preview-bruto" class="mt-2 text-center border p-2 rounded {{ (isset($ritase) && $ritase->foto_tiket_bruto) ? '' : 'd-none' }}">
                            @if(isset($ritase) && $ritase->foto_tiket_bruto)
                                <a href="{{ asset('storage/' . $ritase->foto_tiket_bruto) }}" target="_blank" id="preview-link-bruto">
                                    <img src="{{ asset('storage/' . $ritase->foto_tiket_bruto) }}" id="preview-img-bruto" class="img-fluid rounded" style="max-height: 200px;">
                                </a>
                            @else
                                <a href="#" target="_blank" id="preview-link-bruto">
                                    <img src="" id="preview-img-bruto" class="img-fluid rounded" style="max-height: 200px;">
                                </a>
                            @endif
                            <p class="small text-muted mt-1 mb-0">Preview foto timbangan bruto</p>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Foto Timbangan Tarra (Maks 2MB)</label>
                        <div class="d-flex gap-2 mb-2">
                            <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('foto_tiket_tarra').click()">
                                <i class="cil-camera me-1"></i> Ambil Foto / Pilih File
                            </button>
                        </div>
                        <input type="file" name="foto_tiket_tarra" id="foto_tiket_tarra" class="form-control d-none @error('foto_tiket_tarra') is-invalid @enderror" accept="image/*" capture="environment" onchange="previewImageTarra(this)">
                        <div id="file-name-display-tarra" class="small text-muted mb-2"></div>
                        @error('foto_tiket_tarra') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        
                        <div id="image-preview-tarra" class="mt-2 text-center border p-2 rounded {{ (isset($ritase) && $ritase->foto_tiket_tarra) ? '' : 'd-none' }}">
                            @if(isset($ritase) && $ritase->foto_tiket_tarra)
                                <a href="{{ asset('storage/' . $ritase->foto_tiket_tarra) }}" target="_blank" id="preview-link-tarra">
                                    <img src="{{ asset('storage/' . $ritase->foto_tiket_tarra) }}" id="preview-img-tarra" class="img-fluid rounded" style="max-height: 200px;">
                                </a>
                            @else
                                <a href="#" target="_blank" id="preview-link-tarra">
                                    <img src="" id="preview-img-tarra" class="img-fluid rounded" style="max-height: 200px;">
                                </a>
                            @endif
                            <p class="small text-muted mt-1 mb-0">Preview foto timbangan tarra</p>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Foto Tiket Umum</label>
                        <div class="d-flex gap-2 mb-2">
                            <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('foto_tiket').click()">
                                <i class="cil-camera me-1"></i> Ambil Foto / Pilih File
                            </button>
                        </div>
                        <input type="file" name="foto_tiket" id="foto_tiket" class="form-control d-none @error('foto_tiket') is-invalid @enderror" accept="image/*" capture="environment" onchange="previewImage(this)">
                        <div id="file-name-display" class="small text-muted mb-2"></div>
                        @error('foto_tiket') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        
                        <div id="image-preview" class="mt-2 text-center border p-2 rounded {{ (isset($ritase) && $ritase->foto_tiket) ? '' : 'd-none' }}">
                            @if(isset($ritase) && $ritase->foto_tiket)
                                <a href="{{ asset('storage/' . $ritase->foto_tiket) }}" target="_blank" id="preview-link">
                                    <img src="{{ asset('storage/' . $ritase->foto_tiket) }}" id="preview-img" class="img-fluid rounded" style="max-height: 200px;">
                                </a>
                            @else
                                <a href="#" target="_blank" id="preview-link">
                                    <img src="" id="preview-img" class="img-fluid rounded" style="max-height: 200px;">
                                </a>
                            @endif
                            <p class="small text-muted mt-1 mb-0">Preview foto tiket</p>
                        </div>
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
    const netto = bruto - tarra;
    document.getElementById('berat_netto').value = netto.toFixed(2);
    
    // Auto-calculate tipping fee: netto * 80
    const tippingInput = document.getElementById('biaya_tipping');
    // Only auto-fill if empty or is a new record
    @if(!isset($ritase))
        tippingInput.value = Math.round(netto * 80);
    @endif
}

function previewImage(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const reader = new FileReader();
        
        document.getElementById('file-name-display').textContent = 'File terpilih: ' + file.name;
        
        reader.onload = function(e) {
            document.getElementById('image-preview').classList.remove('d-none');
            document.getElementById('preview-img').src = e.target.result;
            document.getElementById('preview-link').href = e.target.result;
        }
        
        reader.readAsDataURL(file);
    }
}

function previewImageBruto(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const reader = new FileReader();
        
        document.getElementById('file-name-display-bruto').textContent = 'File terpilih: ' + file.name;
        
        reader.onload = function(e) {
            document.getElementById('image-preview-bruto').classList.remove('d-none');
            document.getElementById('preview-img-bruto').src = e.target.result;
            document.getElementById('preview-link-bruto').href = e.target.result;
        }
        
        reader.readAsDataURL(file);
    }
}

function previewImageTarra(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const reader = new FileReader();
        
        document.getElementById('file-name-display-tarra').textContent = 'File terpilih: ' + file.name;
        
        reader.onload = function(e) {
            document.getElementById('image-preview-tarra').classList.remove('d-none');
            document.getElementById('preview-img-tarra').src = e.target.result;
            document.getElementById('preview-link-tarra').href = e.target.result;
        }
        
        reader.readAsDataURL(file);
    }
}
</script>
@endpush
