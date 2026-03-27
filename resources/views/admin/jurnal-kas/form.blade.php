@extends('layouts.admin')
@section('title', isset($jurnalKas) ? 'Edit Jurnal Kas' : 'Tambah Jurnal Kas')

@section('content')
<div class="page-header">
    <div>
        <h1>{{ isset($jurnalKas) ? 'Edit' : 'Tambah' }} Jurnal Kas</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li><li class="breadcrumb-item"><a href="{{ route('admin.jurnal-kas.index') }}">Jurnal Kas</a></li><li class="breadcrumb-item active">{{ isset($jurnalKas) ? 'Edit' : 'Tambah' }}</li></ol></nav>
    </div>
</div>
<div class="row"><div class="col-lg-8"><div class="card"><div class="card-body">
    <form method="POST" action="{{ isset($jurnalKas) ? route('admin.jurnal-kas.update', $jurnalKas) : route('admin.jurnal-kas.store') }}" enctype="multipart/form-data">
        @csrf @if(isset($jurnalKas)) @method('PUT') @endif
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                <input type="date" name="tanggal" class="form-control @error('tanggal') is-invalid @enderror" value="{{ old('tanggal', isset($jurnalKas) ? \Carbon\Carbon::parse($jurnalKas->tanggal)->format('Y-m-d') : '') }}" required>
                @error('tanggal') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Jenis <span class="text-danger">*</span></label>
                <select name="jenis" class="form-select @error('jenis') is-invalid @enderror" required>
                    <option value="">-- Pilih --</option>
                    <option value="masuk" {{ old('jenis', ($jurnalKas->tipe ?? '') == 'Penerimaan' ? 'masuk' : '') == 'masuk' ? 'selected' : '' }}>Kas Masuk</option>
                    <option value="keluar" {{ old('jenis', ($jurnalKas->tipe ?? '') == 'Pengeluaran' ? 'keluar' : '') == 'keluar' ? 'selected' : '' }}>Kas Keluar</option>
                </select>
                @error('jenis') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Akun (COA) <span class="text-danger">*</span></label>
                <select name="coa_id" class="form-select @error('coa_id') is-invalid @enderror" required>
                    <option value="">-- Pilih --</option>
                    @foreach($coas as $c)<option value="{{ $c->id }}" {{ old('coa_id', $jurnalKas->coa_id ?? ($jurnalKas->coa_lawan_id ?? '')) == $c->id ? 'selected' : '' }}>{{ $c->kode_akun }} - {{ $c->nama_akun }}</option>@endforeach
                </select>
                @error('coa_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Mitra (Opsional)</label>
                <select name="contactable_type_id" class="form-select @error('contactable_type_id') is-invalid @enderror">
                    <option value="">-- Tanpa Mitra --</option>
                    <optgroup label="Klien">
                        @foreach($kliens as $k)
                            <option value="App\Models\Klien:{{ $k->id }}" {{ (isset($jurnalKas) && $jurnalKas->contactable_type === 'App\Models\Klien' && $jurnalKas->contactable_id == $k->id) ? 'selected' : '' }}>{{ $k->nama_klien }}</option>
                        @endforeach
                    </optgroup>
                    <optgroup label="Vendor">
                        @foreach($vendors as $v)
                            <option value="App\Models\Vendor:{{ $v->id }}" {{ (isset($jurnalKas) && $jurnalKas->contactable_type === 'App\Models\Vendor' && $jurnalKas->contactable_id == $v->id) ? 'selected' : '' }}>{{ $v->nama_vendor }}</option>
                        @endforeach
                    </optgroup>
                </select>
                @error('contactable_type_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Jumlah (Rp) <span class="text-danger">*</span></label>
                <input type="number" name="jumlah" class="form-control @error('jumlah') is-invalid @enderror" value="{{ old('jumlah', isset($jurnalKas) ? $jurnalKas->nominal : '') }}" required>
                @error('jumlah') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-12">
                <label class="form-label">Deskripsi</label>
                <textarea name="deskripsi" class="form-control" rows="3">{{ old('deskripsi', $jurnalKas->deskripsi ?? '') }}</textarea>
            </div>
            <div class="col-12">
                <label class="form-label">Bukti Transaksi <span class="text-danger">*</span></label>
                <input type="file" name="bukti_transaksi" class="form-control @error('bukti_transaksi') is-invalid @enderror" accept=".jpg,.jpeg,.png,.pdf" {{ isset($jurnalKas) && $jurnalKas->bukti_transaksi ? '' : 'required' }}>
                <div class="form-text">Format: JPG, PNG, PDF. Maks: 2MB. Bisa ambil dari Kamera Pustaka/Galeri.</div>
                @error('bukti_transaksi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                
                @if(isset($jurnalKas) && $jurnalKas->bukti_transaksi)
                    <div class="mt-2">
                        <a href="{{ Storage::url($jurnalKas->bukti_transaksi) }}" target="_blank" class="btn btn-sm btn-info text-white">
                            <i class="cil-external-link me-1"></i> Lihat Bukti Saat Ini
                        </a>
                        <div class="form-text text-warning mt-1">Mengunggah file baru akan menimpa file yang lama.</div>
                    </div>
                @endif
            </div>
            <div class="col-12 d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="cil-save me-1"></i> {{ isset($jurnalKas) ? 'Perbarui' : 'Simpan' }}</button>
                <a href="{{ route('admin.jurnal-kas.index') }}" class="btn btn-outline-secondary">Kembali</a>
            </div>
        </div>
    </form>
</div></div></div></div>
@endsection
