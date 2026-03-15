@extends('layouts.admin')
@section('title', isset($user) ? 'Edit User' : 'Tambah User')

@section('content')
<div class="page-header"><div><h1>{{ isset($user) ? 'Edit' : 'Tambah' }} User</h1></div></div>
<div class="row"><div class="col-lg-8">
    <form method="POST" action="{{ isset($user) ? route('admin.users.update', $user) : route('admin.users.store') }}">
        @csrf @if(isset($user)) @method('PUT') @endif
        <div class="card mb-4">
            <div class="card-header bg-white"><h6 class="mb-0 fw-semibold">User Information</h6></div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name ?? '') }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Username <span class="text-danger">*</span></label>
                        <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username', $user->username ?? '') }}" required>
                        @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email ?? '') }}" required>
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>
        <div class="card mb-4">
            <div class="card-header bg-white"><h6 class="mb-0 fw-semibold">Security & Role</h6></div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Password {{ isset($user) ? '(kosongkan jika tidak diubah)' : '' }} @unless(isset($user))<span class="text-danger">*</span>@endunless</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" {{ isset($user) ? '' : 'required' }}>
                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tenant</label>
                        <select name="tenant_id" class="form-select">
                            <option value="">-- Superuser (no tenant) --</option>
                            @foreach($tenants as $t)<option value="{{ $t->id }}" {{ old('tenant_id', $user->tenant_id ?? '') == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>@endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Role <span class="text-danger">*</span></label>
                        <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                            <option value="">-- Pilih Role --</option>
                            @foreach($roles as $r)
                                <option value="{{ $r->name }}" {{ old('role', $user->role ?? '') == $r->name ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $r->name)) }}
                                </option>
                            @endforeach
                        </select>
                        @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary"><i class="cil-save me-1"></i> {{ isset($user) ? 'Perbarui' : 'Simpan' }}</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Kembali</a>
        </div>
    </form>
</div></div>
@endsection
