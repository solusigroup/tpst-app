@extends('layouts.admin')
@section('title', 'Edit Kategori Sampah')

@section('content')
<div class="page-header">
    <div>
        <h1>Edit Kategori Sampah</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li><li class="breadcrumb-item"><a href="{{ route('admin.hrd.waste-category.index') }}">Kategori Sampah</a></li><li class="breadcrumb-item active">Edit</li></ol></nav>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.hrd.waste-category.update', $wasteCategory) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $wasteCategory->name) }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Satuan Ukuran <span class="text-danger">*</span></label>
                    <input type="text" name="unit" class="form-control @error('unit') is-invalid @enderror" value="{{ old('unit', $wasteCategory->unit) }}" required>
                    @error('unit')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-12">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description', $wasteCategory->description) }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-12">
                    <div class="form-check form-switch mt-2">
                        <input type="hidden" name="is_active" value="0">
                        <input class="form-check-input" type="checkbox" role="switch" name="is_active" id="isActiveSwitch" value="1" {{ old('is_active', $wasteCategory->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="isActiveSwitch">Status Aktif</label>
                    </div>
                </div>
            </div>
            
            <div class="mt-4">
                <button type="submit" class="btn btn-primary"><i class="cil-save me-1"></i> Perbarui</button>
                <a href="{{ route('admin.hrd.waste-category.index') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
