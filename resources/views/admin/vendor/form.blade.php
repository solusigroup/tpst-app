@extends('layouts.admin')
@section('title', isset($vendor) ? 'Edit Vendor' : 'Tambah Vendor')

@section('content')
<div class="page-header">
    <div>
        <h1>{{ isset($vendor) ? 'Edit Vendor' : 'Tambah Vendor' }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.vendor.index') }}">Vendor</a></li>
                <li class="breadcrumb-item active">{{ isset($vendor) ? 'Edit' : 'Tambah' }}</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0">{{ isset($vendor) ? 'Form Edit Vendor' : 'Form Tambah Vendor' }}</h5>
            </div>
            <div class="card-body">
                <form action="{{ isset($vendor) ? route('admin.vendor.update', $vendor) : route('admin.vendor.store') }}" method="POST">
                    @csrf
                    @if(isset($vendor))
                        @method('PUT')
                    @endif

                    <div class="mb-3">
                        <label for="nama_vendor" class="form-label">Nama Vendor <span class="text-danger">*</span></label>
                        <input type="text" name="nama_vendor" id="nama_vendor" class="form-control @error('nama_vendor') is-invalid @enderror" value="{{ old('nama_vendor', $vendor->nama_vendor ?? '') }}" required>
                        @error('nama_vendor')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="kontak" class="form-label">Kontak</label>
                        <input type="text" name="kontak" id="kontak" class="form-control @error('kontak') is-invalid @enderror" value="{{ old('kontak', $vendor->kontak ?? '') }}">
                        @error('kontak')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="alamat" class="form-label">Alamat</label>
                        <textarea name="alamat" id="alamat" class="form-control @error('alamat') is-invalid @enderror" rows="3">{{ old('alamat', $vendor->alamat ?? '') }}</textarea>
                        @error('alamat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.vendor.index') }}" class="btn btn-outline-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="cil-save me-1"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
