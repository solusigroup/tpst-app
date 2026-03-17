@extends('layouts.admin')
@section('title', 'Edit Kehadiran')

@section('content')
<div class="page-header">
    <div>
        <h1>Edit Kehadiran</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li><li class="breadcrumb-item"><a href="{{ route('admin.hrd.attendance.index') }}">Kehadiran</a></li><li class="breadcrumb-item active">Edit</li></ol></nav>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.hrd.attendance.update', $attendance) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Karyawan</label>
                    <input type="text" class="form-control bg-light" value="{{ $attendance->user->name }}" readonly>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Tanggal</label>
                    <input type="text" class="form-control bg-light" value="{{ \Carbon\Carbon::parse($attendance->attendance_date)->format('d/m/Y') }}" readonly>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Jam Masuk</label>
                    <input type="time" name="check_in" class="form-control @error('check_in') is-invalid @enderror" value="{{ old('check_in', $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('H:i') : '') }}">
                    @error('check_in')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">Jam Keluar</label>
                    <input type="time" name="check_out" class="form-control @error('check_out') is-invalid @enderror" value="{{ old('check_out', $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('H:i') : '') }}">
                    @error('check_out')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                        <option value="present" {{ old('status', $attendance->status) == 'present' ? 'selected' : '' }}>Hadir</option>
                        <option value="absent" {{ old('status', $attendance->status) == 'absent' ? 'selected' : '' }}>Mangkir</option>
                        <option value="sick" {{ old('status', $attendance->status) == 'sick' ? 'selected' : '' }}>Sakit</option>
                        <option value="leave" {{ old('status', $attendance->status) == 'leave' ? 'selected' : '' }}>Izin</option>
                    </select>
                    @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-12">
                    <label class="form-label">Catatan</label>
                    <textarea name="notes" rows="3" class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $attendance->notes) }}</textarea>
                    @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            
            <div class="mt-4">
                <button type="submit" class="btn btn-primary"><i class="cil-save me-1"></i> Perbarui</button>
                <a href="{{ route('admin.hrd.attendance.index') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
