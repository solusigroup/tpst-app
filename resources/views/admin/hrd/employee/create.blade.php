@extends('layouts.admin')

@section('title', 'Tambah Karyawan')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-header">
                <strong>Tambah Karyawan</strong>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.hrd.employee.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jabatan</label>
                            <input type="text" name="position" class="form-control @error('position') is-invalid @enderror" value="{{ old('position') }}">
                            @error('position') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Username (Untuk Login) <span class="text-danger">*</span></label>
                            <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username') }}" required>
                            @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">No. KTP</label>
                            <input type="text" name="ktp_number" class="form-control @error('ktp_number') is-invalid @enderror" value="{{ old('ktp_number') }}">
                            @error('ktp_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tipe Gaji</label>
                            <select name="salary_type" class="form-select @error('salary_type') is-invalid @enderror">
                                <option value="">-- Pilih Tipe --</option>
                                <option value="bulanan" {{ old('salary_type') == 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                                <option value="borongan" {{ old('salary_type') == 'borongan' ? 'selected' : '' }}>Borongan</option>
                            </select>
                            @error('salary_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="3">{{ old('address') }}</textarea>
                        @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Foto Profil (Gunakan Kamera / Galeri)</label>
                        <input type="file" name="photo" class="form-control @error('photo') is-invalid @enderror" accept="image/*" capture="environment">
                        <small class="text-muted">Format gambar: jpg, png, jpeg. Maks 2MB.</small>
                        @error('photo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">Simpan Karyawan</button>
                        <a href="{{ route('admin.hrd.employee.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
