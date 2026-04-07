@extends('layouts.admin')

@section('title', 'Edit Mesin')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1>Edit Mesin</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.machines.index') }}">Mesin</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit</li>
            </ol>
        </nav>
    </div>
</div>

<div class="card col-md-6">
    <div class="card-body">
        <form action="{{ route('admin.machines.update', $machine->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-3">
                <label for="nomor_mesin" class="form-label">Kode / Nomor Mesin <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('nomor_mesin') is-invalid @enderror" id="nomor_mesin" name="nomor_mesin" value="{{ old('nomor_mesin', $machine->nomor_mesin) }}" required>
                @error('nomor_mesin')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="nama_mesin" class="form-label">Nama Lengkap Mesin <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('nama_mesin') is-invalid @enderror" id="nama_mesin" name="nama_mesin" value="{{ old('nama_mesin', $machine->nama_mesin) }}" required>
                @error('nama_mesin')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('admin.machines.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection
