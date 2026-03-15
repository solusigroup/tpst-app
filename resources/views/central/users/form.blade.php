@extends('layouts.admin')
@section('title', isset($user) ? 'Edit User' : 'Tambah User')

@section('content')
<div class="page-header">
    <div>
        <h1>{{ isset($user) ? 'Edit User' : 'Tambah User' }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('central.dashboard') }}">Central</a></li>
                <li class="breadcrumb-item"><a href="{{ route('central.users.index') }}">Users</a></li>
                <li class="breadcrumb-item active">{{ isset($user) ? 'Edit' : 'Tambah' }}</li>
            </ol>
        </nav>
    </div>
</div>

<form action="{{ isset($user) ? route('central.users.update', $user->id) : route('central.users.store') }}" method="POST">
    @csrf
    @if(isset($user)) @method('PUT') @endif

    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tenant <span class="text-danger">*</span></label>
                    <select name="tenant_id" class="form-select @error('tenant_id') is-invalid @enderror" required>
                        <option value="">-- Pilih Tenant --</option>
                        @foreach($tenants as $t)
                            <option value="{{ $t->id }}" {{ old('tenant_id', $user->tenant_id ?? '') == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                        @endforeach
                    </select>
                    @error('tenant_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nama <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name ?? '') }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username', $user->username ?? '') }}">
                    @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email ?? '') }}" required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Password {!! isset($user) ? '<small class="text-body-secondary">(Kosongkan jika tidak ingin diubah)</small>' : '<span class="text-danger">*</span>' !!}</label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" {{ isset($user) ? '' : 'required' }}>
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Role <span class="text-danger">*</span></label>
                    <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                        @foreach(['admin' => 'Admin', 'timbangan' => 'Timbangan', 'keuangan' => 'Keuangan'] as $val => $label)
                            <option value="{{ $val }}" {{ old('role', $user->role ?? '') == $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mb-3 mt-4">
                <div class="form-check form-switch form-switch-lg">
                    <input class="form-check-input" type="checkbox" role="switch" name="is_super_admin" value="1" id="isSuperAdmin" {{ old('is_super_admin', $user->is_super_admin ?? false) ? 'checked' : '' }}>
                    <label class="form-check-label ms-2" for="isSuperAdmin">
                        <span class="d-block fw-bold text-danger">Super Admin (Central Panel Access)</span>
                        <small class="text-body-secondary">Berikan akses untuk semua fitur tenant dan panel central</small>
                    </label>
                </div>
            </div>
        </div>
        <div class="card-footer bg-light">
            <button type="submit" class="btn btn-primary"><i class="cil-save me-1"></i> Simpan Data</button>
            <a href="{{ route('central.users.index') }}" class="btn btn-outline-secondary ms-2">Batal</a>
        </div>
    </div>
</form>
@endsection
