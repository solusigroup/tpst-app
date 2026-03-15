@extends('layouts.admin')
@section('title', 'Pengaturan Perusahaan')

@section('content')
<div class="page-header"><div><h1>Pengaturan Perusahaan</h1></div></div>

<form method="POST" action="{{ route('admin.company-settings.update') }}">
    @csrf @method('PUT')
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-white"><h6 class="mb-0 fw-semibold">Identitas Perusahaan</h6><small class="text-body-secondary">Nama dan alamat resmi perusahaan</small></div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Perusahaan <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $tenant->name ?? '') }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat Lengkap</label>
                        <textarea name="address" class="form-control" rows="3">{{ old('address', $tenant->address ?? '') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email Perusahaan</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $tenant->email ?? '') }}">
                    </div>
                </div>
            </div>
            <div class="card mb-4">
                <div class="card-header bg-white"><h6 class="mb-0 fw-semibold">Informasi Rekening Bank</h6></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label">Nama Bank</label><input type="text" name="bank_name" class="form-control" value="{{ old('bank_name', $tenant->bank_name ?? '') }}"></div>
                        <div class="col-md-6"><label class="form-label">No. Rekening</label><input type="text" name="bank_account_number" class="form-control" value="{{ old('bank_account_number', $tenant->bank_account_number ?? '') }}"></div>
                        <div class="col-md-6"><label class="form-label">Nama Pemilik</label><input type="text" name="bank_account_name" class="form-control" value="{{ old('bank_account_name', $tenant->bank_account_name ?? '') }}"></div>
                    </div>
                </div>
            </div>
            <div class="card mb-4">
                <div class="card-header bg-white"><h6 class="mb-0 fw-semibold">Pejabat & Otorisasi</h6></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4"><label class="form-label">Nama Direktur</label><input type="text" name="director_name" class="form-control" value="{{ old('director_name', $tenant->director_name ?? '') }}"></div>
                        <div class="col-md-4"><label class="form-label">Nama Manajer</label><input type="text" name="manager_name" class="form-control" value="{{ old('manager_name', $tenant->manager_name ?? '') }}"></div>
                        <div class="col-md-4"><label class="form-label">Bag. Keuangan</label><input type="text" name="finance_name" class="form-control" value="{{ old('finance_name', $tenant->finance_name ?? '') }}"></div>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary"><i class="cil-save me-1"></i> Simpan Perubahan</button>
        </div>
    </div>
</form>
@endsection
