@extends('layouts.admin')
@section('title', 'Edit Perhitungan Upah')

@section('content')
<div class="page-header">
    <div>
        <h1>Edit Perhitungan Upah</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.hrd.wage-calculation.index') }}">Perhitungan Upah</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.hrd.wage-calculation.show', $wageCalculation) }}">Detail</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 fw-bold">
                <i class="cil-pencil me-1"></i> Form Penyesuaian Upah
            </div>
            <div class="card-body">
                <div class="alert alert-info small">
                    <i class="cil-info me-1"></i> 
                    Gunakan form ini untuk melakukan penyesuaian manual pada total upah atau lembur. 
                    <strong>Catatan:</strong> Jika Anda melakukan hitung ulang otomatis, perubahan manual ini mungkin akan tertimpa.
                </div>

                <form action="{{ route('admin.hrd.wage-calculation.update', $wageCalculation) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label text-body-secondary small fw-bold">Karyawan</label>
                        <input type="text" class="form-control bg-light" value="{{ $wageCalculation->user->name }}" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-body-secondary small fw-bold">Periode</label>
                        <input type="text" class="form-control bg-light" value="{{ $wageCalculation->week_start->format('d/m/Y') }} - {{ $wageCalculation->week_end->format('d/m/Y') }}" readonly>
                    </div>

                    <hr class="my-4">

                    <div class="mb-3">
                        <label for="total_wage" class="form-label fw-bold">Total Upah Pokok / Borongan (Rp) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="total_wage" id="total_wage" class="form-control @error('total_wage') is-invalid @enderror" value="{{ old('total_wage', $wageCalculation->total_wage) }}" required>
                            @error('total_wage') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="overtime_pay" class="form-label fw-bold">Uang Lembur (Rp) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="overtime_pay" id="overtime_pay" class="form-control @error('overtime_pay') is-invalid @enderror" value="{{ old('overtime_pay', $wageCalculation->overtime_pay) }}" required>
                            @error('overtime_pay') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label fw-bold">Catatan Penyesuaian</label>
                        <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="3" placeholder="Alasan penyesuaian manual...">{{ old('notes', $wageCalculation->notes) }}</textarea>
                        @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <a href="{{ route('admin.hrd.wage-calculation.show', $wageCalculation) }}" class="btn btn-outline-secondary">
                            <i class="cil-arrow-left me-1"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="cil-save me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white py-3 fw-bold">
                <i class="cil-list me-1"></i> Data Terkini (Referensi)
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td class="text-body-secondary" style="width: 150px;">Total Output</td>
                        <td class="fw-bold">: {{ number_format($wageCalculation->total_quantity, 2, ',', '.') }} kg</td>
                    </tr>
                    <tr>
                        <td class="text-body-secondary">Status Saat Ini</td>
                        <td>: 
                            <span class="badge bg-{{ $wageCalculation->status === 'pending' ? 'warning' : ($wageCalculation->status === 'approved' ? 'info' : 'success') }}">
                                {{ ucfirst($wageCalculation->status) }}
                            </span>
                        </td>
                    </tr>
                </table>

                @if($wageCalculation->details)
                <div class="mt-4">
                    <p class="small fw-bold text-body-secondary mb-2 uppercase">Rincian Perhitungan Sistem:</p>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered small">
                            <thead class="table-light">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Kategori</th>
                                    <th class="text-end">Qty (kg)</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($wageCalculation->details as $item)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($item['date'])->format('d/m/Y') }}</td>
                                    <td>{{ $item['category'] }}</td>
                                    <td class="text-end">{{ number_format($item['quantity_paid'], 2, ',', '.') }}</td>
                                    <td class="text-end">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
