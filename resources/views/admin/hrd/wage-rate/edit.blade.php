@extends('layouts.admin')
@section('title', 'Edit Tarif Upah')

@section('content')
<div class="page-header">
    <div>
        <h1>Edit Tarif Upah</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li><li class="breadcrumb-item"><a href="{{ route('admin.hrd.wage-rate.index') }}">Tarif Upah</a></li><li class="breadcrumb-item active">Edit</li></ol></nav>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.hrd.wage-rate.update', $wageRate) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Kategori Sampah <span class="text-danger">*</span></label>
                    <select name="waste_category_id" class="form-select @error('waste_category_id') is-invalid @enderror" required>
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('waste_category_id', $wageRate->waste_category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }} (per {{ $cat->unit }})</option>
                        @endforeach
                    </select>
                    @error('waste_category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Tarif (Rp) <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" name="rate_per_unit" class="form-control @error('rate_per_unit') is-invalid @enderror" value="{{ old('rate_per_unit', (float)$wageRate->rate_per_unit) }}" required>
                    @error('rate_per_unit')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Tanggal Berlaku <span class="text-danger">*</span></label>
                    <input type="date" name="effective_date" class="form-control @error('effective_date') is-invalid @enderror" value="{{ old('effective_date', \Carbon\Carbon::parse($wageRate->effective_date)->format('Y-m-d')) }}" required>
                    @error('effective_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Tanggal Berakhir (Opsional)</label>
                    <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror" value="{{ old('end_date', $wageRate->end_date ? \Carbon\Carbon::parse($wageRate->end_date)->format('Y-m-d') : '') }}">
                    @error('end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-12">
                    <div class="form-check form-switch mt-2">
                        <input type="hidden" name="is_active" value="0">
                        <input class="form-check-input" type="checkbox" role="switch" name="is_active" id="isActiveSwitch" value="1" {{ old('is_active', $wageRate->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="isActiveSwitch">Status Aktif</label>
                    </div>
                </div>
            </div>
            
            <div class="mt-4">
                <button type="submit" class="btn btn-primary"><i class="cil-save me-1"></i> Perbarui</button>
                <a href="{{ route('admin.hrd.wage-rate.index') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
