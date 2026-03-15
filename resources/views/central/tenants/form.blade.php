@extends('layouts.admin')
@section('title', isset($tenant) ? 'Edit Tenant' : 'Tambah Tenant')

@section('content')
<div class="page-header">
    <div>
        <h1>{{ isset($tenant) ? 'Edit Tenant' : 'Tambah Tenant Baru' }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('central.dashboard') }}">Central</a></li>
                <li class="breadcrumb-item"><a href="{{ route('central.tenants.index') }}">Tenants</a></li>
                <li class="breadcrumb-item active">{{ isset($tenant) ? 'Edit' : 'Tambah' }}</li>
            </ol>
        </nav>
    </div>
</div>

<form action="{{ isset($tenant) ? route('central.tenants.update', $tenant->id) : route('central.tenants.store') }}" method="POST">
    @csrf
    @if(isset($tenant)) @method('PUT') @endif

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-light"><h6 class="mb-0">Data Tenant</h6><small class="text-body-secondary">Informasi dasar tenant</small></div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Tenant <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $tenant->name ?? '') }}" required placeholder="PT Sampah Jaya">
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Domain <span class="text-danger">*</span></label>
                        <input type="text" name="domain" class="form-control @error('domain') is-invalid @enderror" value="{{ old('domain', $tenant->domain ?? '') }}" required placeholder="sampahjaya.test">
                        <div class="form-text">Domain unik untuk tenant ini</div>
                        @error('domain')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        @if(!isset($tenant))
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-light"><h6 class="mb-0">Admin Tenant</h6><small class="text-body-secondary">Buat admin user untuk tenant baru ini</small></div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Admin <span class="text-danger">*</span></label>
                        <input type="text" name="admin_name" class="form-control @error('admin_name') is-invalid @enderror" value="{{ old('admin_name') }}" required placeholder="Admin TPST">
                        @error('admin_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Username Admin</label>
                        <input type="text" name="admin_username" class="form-control @error('admin_username') is-invalid @enderror" value="{{ old('admin_username') }}" placeholder="admin_tpst">
                        @error('admin_username')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email Admin <span class="text-danger">*</span></label>
                        <input type="email" name="admin_email" class="form-control @error('admin_email') is-invalid @enderror" value="{{ old('admin_email') }}" required placeholder="admin@sampahjaya.test">
                        @error('admin_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password Admin <span class="text-danger">*</span></label>
                        <input type="password" name="admin_password" class="form-control @error('admin_password') is-invalid @enderror" required placeholder="Minimal 8 karakter">
                        @error('admin_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="mb-4">
        <button type="submit" class="btn btn-primary"><i class="cil-save me-1"></i> Simpan Data</button>
        <a href="{{ route('central.tenants.index') }}" class="btn btn-light ms-2">Batal</a>
    </div>
</form>
@endsection
