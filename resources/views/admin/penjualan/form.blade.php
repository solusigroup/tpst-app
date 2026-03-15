@extends('layouts.admin')
@section('title', isset($penjualan) ? 'Edit Penjualan' : 'Tambah Penjualan')

@section('content')
<div class="page-header">
    <div>
        <h1>{{ isset($penjualan) ? 'Edit' : 'Tambah' }} Penjualan</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li><li class="breadcrumb-item"><a href="{{ route('admin.penjualan.index') }}">Penjualan</a></li><li class="breadcrumb-item active">{{ isset($penjualan) ? 'Edit' : 'Tambah' }}</li></ol></nav>
    </div>
</div>
<div class="row"><div class="col-lg-8"><div class="card"><div class="card-body">
    <form method="POST" action="{{ isset($penjualan) ? route('admin.penjualan.update', $penjualan) : route('admin.penjualan.store') }}">
        @csrf @if(isset($penjualan)) @method('PUT') @endif
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Klien <span class="text-danger">*</span></label>
                <select name="klien_id" class="form-select @error('klien_id') is-invalid @enderror" required>
                    <option value="">-- Pilih --</option>
                    @foreach($kliens as $k)<option value="{{ $k->id }}" {{ old('klien_id', $penjualan->klien_id ?? '') == $k->id ? 'selected' : '' }}>{{ $k->nama_klien }}</option>@endforeach
                </select>
                @error('klien_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                <input type="date" name="tanggal" class="form-control @error('tanggal') is-invalid @enderror" value="{{ old('tanggal', isset($penjualan) ? \Carbon\Carbon::parse($penjualan->tanggal)->format('Y-m-d') : '') }}" required>
                @error('tanggal') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-12">
                <label class="form-label">Jenis Produk <span class="text-danger">*</span></label>
                <input type="text" name="jenis_produk" class="form-control @error('jenis_produk') is-invalid @enderror" value="{{ old('jenis_produk', $penjualan->jenis_produk ?? '') }}" required>
                @error('jenis_produk') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Berat (kg) <span class="text-danger">*</span></label>
                <input type="number" step="0.01" name="berat_kg" id="berat_kg" class="form-control @error('berat_kg') is-invalid @enderror" value="{{ old('berat_kg', $penjualan->berat_kg ?? '') }}" required oninput="calcTotal()">
                @error('berat_kg') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Harga Satuan (Rp) <span class="text-danger">*</span></label>
                <input type="number" name="harga_satuan" id="harga_satuan" class="form-control @error('harga_satuan') is-invalid @enderror" value="{{ old('harga_satuan', $penjualan->harga_satuan ?? '') }}" required oninput="calcTotal()">
                @error('harga_satuan') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Total Harga</label>
                <input type="text" id="total_harga_display" class="form-control bg-light" value="Rp {{ number_format(old('total_harga', $penjualan->total_harga ?? 0), 0, ',', '.') }}" readonly>
            </div>
            <div class="col-12 d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="cil-save me-1"></i> {{ isset($penjualan) ? 'Perbarui' : 'Simpan' }}</button>
                <a href="{{ route('admin.penjualan.index') }}" class="btn btn-outline-secondary">Kembali</a>
            </div>
        </div>
    </form>
</div></div></div></div>
@endsection

@push('scripts')
<script>
function calcTotal() {
    const berat = parseFloat(document.getElementById('berat_kg').value) || 0;
    const harga = parseFloat(document.getElementById('harga_satuan').value) || 0;
    const total = berat * harga;
    document.getElementById('total_harga_display').value = 'Rp ' + total.toLocaleString('id-ID');
}
</script>
@endpush
