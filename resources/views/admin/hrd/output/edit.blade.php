@extends('layouts.admin')
@section('title', 'Edit Output Pemilah')

@section('content')
<div class="page-header">
    <div>
        <h1>Edit Output Pemilah</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li><li class="breadcrumb-item"><a href="{{ route('admin.hrd.output.index') }}">Output Pemilah</a></li><li class="breadcrumb-item active">Edit</li></ol></nav>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.hrd.output.update', $output) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Karyawan</label>
                    <input type="text" class="form-control bg-light" value="{{ $output->user->name }}" readonly>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                    <input type="date" name="output_date" class="form-control @error('output_date') is-invalid @enderror" value="{{ old('output_date', \Carbon\Carbon::parse($output->output_date)->format('Y-m-d')) }}" required>
                    @error('output_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">Kategori Sampah <span class="text-danger">*</span></label>
                    <select name="waste_category_id" id="categorySelect" class="form-select @error('waste_category_id') is-invalid @enderror" required onchange="updateUnit()">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" data-unit="{{ $cat->unit }}" {{ old('waste_category_id', $output->waste_category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    @error('waste_category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">Jumlah <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="number" step="0.01" name="quantity" class="form-control @error('quantity') is-invalid @enderror" value="{{ old('quantity', (float)$output->quantity) }}" required>
                        <span class="input-group-text unit-display">{{ $output->unit }}</span>
                        @error('quantity')<div class="invalid-feedback" style="display:block">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Satuan <span class="text-danger">*</span></label>
                    <input type="text" name="unit" id="unitInput" class="form-control @error('unit') is-invalid @enderror" value="{{ old('unit', $output->unit) }}" required readonly>
                    @error('unit')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-12">
                    <label class="form-label">Catatan</label>
                    <textarea name="notes" rows="3" class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $output->notes) }}</textarea>
                    @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            
            <div class="mt-4">
                <button type="submit" class="btn btn-primary"><i class="cil-save me-1"></i> Perbarui</button>
                <a href="{{ route('admin.hrd.output.index') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function updateUnit() {
        const select = document.getElementById('categorySelect');
        const selectedOption = select.options[select.selectedIndex];
        const unit = selectedOption.getAttribute('data-unit');
        
        if(unit) {
            document.getElementById('unitInput').value = unit;
            document.querySelectorAll('.unit-display').forEach(el => el.textContent = unit);
        }
    }
</script>
@endpush
@endsection
