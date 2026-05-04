@extends('layouts.admin')
@section('title', 'Catat Pengangkutan Residu')

@section('content')
<div class="page-header">
    <div>
        <h1>Catat Pengangkutan Residu</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.pengangkutan-residu.index') }}">Pengangkutan Residu</a></li>
                <li class="breadcrumb-item active">Tambah</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.pengangkutan-residu.store') }}" method="POST">
                    @csrf
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Armada / Truk <span class="text-danger">*</span></label>
                            <select name="armada_id" class="form-select @error('armada_id') is-invalid @enderror" required>
                                <option value="">Pilih Armada...</option>
                                @foreach($armadas as $armada)
                                    <option value="{{ $armada->id }}" data-berat-kosong="{{ $armada->berat_kosong }}" {{ old('armada_id') == $armada->id ? 'selected' : '' }}>
                                        {{ $armada->plat_nomor }} - {{ $armada->nama_armada }}
                                    </option>
                                @endforeach
                            </select>
                            @error('armada_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal" class="form-control @error('tanggal') is-invalid @enderror" value="{{ old('tanggal', date('Y-m-d')) }}" required>
                            @error('tanggal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Waktu Keluar TPST</label>
                            <input type="time" name="waktu_keluar" class="form-control @error('waktu_keluar') is-invalid @enderror" value="{{ old('waktu_keluar', date('H:i')) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Waktu Kembali / Masuk</label>
                            <input type="time" name="waktu_masuk" class="form-control @error('waktu_masuk') is-invalid @enderror" value="{{ old('waktu_masuk') }}">
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label">Berat Bruto (Kg) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="berat_bruto" id="berat_bruto" class="form-control @error('berat_bruto') is-invalid @enderror" value="{{ old('berat_bruto', 0) }}" required>
                            <small class="text-muted">Berat saat keluar TPST (Penuh)</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Berat Tarra (Kg) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="berat_tarra" id="berat_tarra" class="form-control @error('berat_tarra') is-invalid @enderror" value="{{ old('berat_tarra', 0) }}" required>
                            <small class="text-muted">Berat truk kosong</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Berat Netto (Kg)</label>
                            <input type="number" id="berat_netto" class="form-control bg-light" value="0" readonly>
                            <small class="text-muted">Hasil Pengurangan</small>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Tujuan Pembuangan</label>
                        <input type="text" name="tujuan" class="form-control" value="{{ old('tujuan', 'TPA Tambakrigadung') }}">
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="3">{{ old('keterangan') }}</textarea>
                    </div>

                    <div class="alert alert-info d-flex align-items-center mb-4">
                        <i class="cil-info me-2 fs-5"></i>
                        <div>
                            Sistem akan secara otomatis mencatat <strong>Biaya Retribusi Rp 30.000</strong> dan membuat jurnal pengeluaran (Utang Biaya TPA).
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.pengangkutan-residu.index') }}" class="btn btn-outline-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary px-4">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const brutoInput = document.getElementById('berat_bruto');
    const tarraInput = document.getElementById('berat_tarra');
    const nettoInput = document.getElementById('berat_netto');
    const armadaSelect = document.querySelector('select[name="armada_id"]');

    function calculateNetto() {
        const bruto = parseFloat(brutoInput.value) || 0;
        const tarra = parseFloat(tarraInput.value) || 0;
        nettoInput.value = (bruto - tarra).toFixed(2);
    }

    // Auto-fill tarra from armada berat_kosong
    function onArmadaChange() {
        const selectedOption = armadaSelect.options[armadaSelect.selectedIndex];
        if (selectedOption && selectedOption.dataset.beratKosong) {
            tarraInput.value = selectedOption.dataset.beratKosong;
            calculateNetto();
        }
    }

    brutoInput.addEventListener('input', calculateNetto);
    tarraInput.addEventListener('input', calculateNetto);

    // Support both TomSelect and vanilla select
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            if (armadaSelect.tomselect) {
                const allOptions = [];
                Array.from(armadaSelect.querySelectorAll('option')).forEach(opt => {
                    if (opt.value) {
                        allOptions.push({ value: opt.value, beratKosong: opt.dataset.beratKosong });
                    }
                });
                armadaSelect.tomselect.on('change', function(value) {
                    const selected = allOptions.find(a => a.value == value);
                    if (selected && selected.beratKosong) {
                        tarraInput.value = selected.beratKosong;
                        calculateNetto();
                    }
                });
            } else {
                armadaSelect.addEventListener('change', onArmadaChange);
            }
        }, 300);
    });
</script>
@endpush
@endsection
