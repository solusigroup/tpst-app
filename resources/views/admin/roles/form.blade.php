@extends('layouts.admin')

@section('title', isset($role) ? 'Edit Role' : 'Tambah Role')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="page-header">
            <div>
                <h1>{{ isset($role) ? 'Edit Role' : 'Tambah Role Baru' }}</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">Role & Izin</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ isset($role) ? 'Edit' : 'Tambah' }}</li>
                    </ol>
                </nav>
            </div>
            <div>
                <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
                    <i class="cil-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ isset($role) ? route('admin.roles.update', $role) : route('admin.roles.store') }}" method="POST">
                    @csrf
                    @if(isset($role)) @method('PUT') @endif

                    <div class="mb-4">
                        <label for="name" class="form-label text-primary fw-bold">Nama Akses (Role Name)</label>
                        <input type="text" class="form-control form-control-lg @error('name') is-invalid @enderror" id="name" name="name" 
                            value="{{ old('name', $role->name ?? '') }}" 
                            placeholder="Contoh: mandor, kasir, logistik" required>
                        <div class="form-text">Gunakan huruf kecil tanpa spasi (bisa pakai underscore _). Contoh: <code>supervisor_lapangan</code>.</div>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr>
                    <h5 class="mb-3">Pengaturan Izin (Permissions)</h5>
                    <p class="text-muted small mb-4">Pilih modul/fitur apa saja yang (CAN) diakses oleh Role ini. Jika tidak dicentang maka mereka (CANNOT) melihat/mengubah data tersebut.</p>

                    <div class="row g-4">
                        @foreach($permissions as $group => $perms)
                            <div class="col-md-4 col-sm-6">
                                <div class="card h-100 bg-light border-0 shadow-sm">
                                    <div class="card-header bg-transparent border-bottom border-secondary text-uppercase fw-bold text-secondary">
                                        Modul: {{ str_replace('_', ' ', $group) }}
                                    </div>
                                    <div class="card-body p-3">
                                        @foreach($perms as $perm)
                                            <div class="form-check form-switch mb-2">
                                                <input class="form-check-input" type="checkbox" role="switch" 
                                                    id="perm_{{ $perm->id }}" name="permissions[]" value="{{ $perm->name }}"
                                                    {{ (isset($rolePermissions) && in_array($perm->name, $rolePermissions)) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="perm_{{ $perm->id }}">
                                                    {{ str_replace('_', ' ', $perm->name) }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                        <a href="{{ route('admin.roles.index') }}" class="btn btn-light me-2">Batal</a>
                        <button type="submit" class="btn btn-primary text-white">
                            <i class="cil-save me-1"></i> Simpan Role
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
