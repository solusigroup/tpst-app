@extends('layouts.admin')
@section('title', 'Tambah Kehadiran')

@section('content')
<div class="page-header">
    <div>
        <h1>Tambah Kehadiran</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li><li class="breadcrumb-item"><a href="{{ route('admin.hrd.attendance.index') }}">Kehadiran</a></li><li class="breadcrumb-item active">Tambah</li></ol></nav>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.hrd.attendance.store') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Karyawan <span class="text-danger">*</span></label>
                    <select name="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                        <option value="">-- Pilih Karyawan --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                        @endforeach
                    </select>
                    @error('user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                    <input type="date" name="attendance_date" class="form-control @error('attendance_date') is-invalid @enderror" value="{{ old('attendance_date', date('Y-m-d')) }}" required>
                    @error('attendance_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">Jam Masuk</label>
                    <input type="time" name="check_in" class="form-control @error('check_in') is-invalid @enderror" value="{{ old('check_in') }}">
                    @error('check_in')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">Jam Keluar</label>
                    <input type="time" name="check_out" class="form-control @error('check_out') is-invalid @enderror" value="{{ old('check_out') }}">
                    @error('check_out')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                        <option value="present" {{ old('status') == 'present' ? 'selected' : '' }}>Hadir</option>
                        <option value="absent" {{ old('status') == 'absent' ? 'selected' : '' }}>Mangkir</option>
                        <option value="sick" {{ old('status') == 'sick' ? 'selected' : '' }}>Sakit</option>
                        <option value="leave" {{ old('status') == 'leave' ? 'selected' : '' }}>Izin</option>
                    </select>
                    @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-12">
                    <label class="form-label">Catatan</label>
                    <textarea name="notes" rows="3" class="form-control @error('notes') is-invalid @enderror">{{ old('notes') }}</textarea>
                    @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            
            <div class="mt-4">
                <button type="submit" class="btn btn-primary"><i class="cil-save me-1"></i> Simpan</button>
                <a href="{{ route('admin.hrd.attendance.index') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
