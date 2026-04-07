@extends('layouts.admin')

@section('title', 'Isi Logbook Mesin')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1>Isi Logbook Mesin</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.machine-logs.index') }}">Logbook Mesin</a></li>
                <li class="breadcrumb-item active" aria-current="page">Isi Baru</li>
            </ol>
        </nav>
    </div>
</div>

<div class="card col-md-8">
    <div class="card-body">
        <form action="{{ route('admin.machine-logs.store') }}" method="POST">
            @csrf
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="machine_id" class="form-label">Mesin <span class="text-danger">*</span></label>
                    <select class="form-select @error('machine_id') is-invalid @enderror" id="machine_id" name="machine_id" required>
                        <option value="">Pilih Mesin...</option>
                        @foreach($machines as $machine)
                            <option value="{{ $machine->id }}" {{ old('machine_id') == $machine->id ? 'selected' : '' }}>
                                {{ $machine->nomor_mesin }} - {{ $machine->nama_mesin }}
                            </option>
                        @endforeach
                    </select>
                    @error('machine_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="waktu_cek" class="form-label">Waktu Pengecekan <span class="text-danger">*</span></label>
                    <select class="form-select @error('waktu_cek') is-invalid @enderror" id="waktu_cek" name="waktu_cek" required>
                        <option value="">Pilih Waktu...</option>
                        <option value="Engine On" {{ old('waktu_cek') == 'Engine On' ? 'selected' : '' }}>Engine On (Pagi / Mulai Operasi)</option>
                        <option value="Engine Off" {{ old('waktu_cek') == 'Engine Off' ? 'selected' : '' }}>Engine Off (Sore / Selesai Operasi)</option>
                    </select>
                    @error('waktu_cek')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label d-block">Status Lampu Menara <span class="text-danger">*</span></label>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-check p-3 border rounded h-100 shadow-sm" style="background-color: rgba(16, 185, 129, 0.05); border-left: 4px solid #10b981 !important;">
                            <input class="form-check-input ms-1" type="radio" name="status_lampu" id="statusHijau" value="Hijau" {{ old('status_lampu') == 'Hijau' ? 'checked' : '' }} required>
                            <label class="form-check-label ms-2 d-block w-100" for="statusHijau" style="cursor:pointer">
                                <strong>🟢 Hijau (Normal Operation)</strong><br>
                                <small class="text-muted">Mesin mencapai cycle time normal. Siap/Sedang beroperasi.</small>
                            </label>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-check p-3 border rounded h-100 shadow-sm" style="background-color: rgba(245, 158, 11, 0.05); border-left: 4px solid #f59e0b !important;">
                            <input class="form-check-input ms-1" type="radio" name="status_lampu" id="statusKuning" value="Kuning" {{ old('status_lampu') == 'Kuning' ? 'checked' : '' }}>
                            <label class="form-check-label ms-2 d-block w-100" for="statusKuning" style="cursor:pointer">
                                <strong>🟡 Kuning (Attention Required)</strong><br>
                                <small class="text-muted">Mesin hidup tapi ada isu non-teknis (misal stok tipis/QC).</small>
                            </label>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-check p-3 border rounded h-100 shadow-sm" style="background-color: rgba(6, 182, 212, 0.05); border-left: 4px solid #06b6d4 !important;">
                            <input class="form-check-input ms-1" type="radio" name="status_lampu" id="statusBiru" value="Biru" {{ old('status_lampu') == 'Biru' ? 'checked' : '' }}>
                            <label class="form-check-label ms-2 d-block w-100" for="statusBiru" style="cursor:pointer">
                                <strong>🔵 Biru (Under Maintenance)</strong><br>
                                <small class="text-muted">Sedang diperbaiki teknisi / Preventive maintenance.</small>
                            </label>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-check p-3 border rounded h-100 shadow-sm" style="background-color: rgba(239, 68, 68, 0.05); border-left: 4px solid #ef4444 !important;">
                            <input class="form-check-input ms-1" type="radio" name="status_lampu" id="statusMerah" value="Merah" {{ old('status_lampu') == 'Merah' ? 'checked' : '' }}>
                            <label class="form-check-label ms-2 d-block w-100" for="statusMerah" style="cursor:pointer">
                                <strong class="text-danger">🔴 Merah (Emergency / Breakdown)</strong><br>
                                <small class="text-muted">Kegagalan kritis/emergency stop. Akan kirim WA otomatis ke Teknisi.</small>
                            </label>
                        </div>
                    </div>
                </div>
                @error('status_lampu')
                    <div class="text-danger mt-2 small">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="keterangan" class="form-label">Keterangan Tambahan / RCA</label>
                <textarea class="form-control @error('keterangan') is-invalid @enderror" id="keterangan" name="keterangan" rows="3" placeholder="Isi catatan khusus jika status Kuning, Biru, atau Merah">{{ old('keterangan') }}</textarea>
                @error('keterangan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="text-muted">Wajib diisi jika terjadi breakdown (Merah) untuk analisis Root Cause (RCA).</small>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('admin.machine-logs.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary" id="btnSubmit">Simpan Logbook</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('input[name="status_lampu"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            if (this.value === 'Merah') {
                document.getElementById('keterangan').required = true;
            } else {
                document.getElementById('keterangan').required = false;
            }
        });
    });
</script>
@endpush
