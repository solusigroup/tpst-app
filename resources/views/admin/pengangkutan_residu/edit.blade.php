@extends('layouts.admin')
@section('title', 'Edit Pengangkutan Residu')

@section('content')
<div class="page-header">
    <div>
        <h1>Edit Pengangkutan Residu</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.pengangkutan-residu.index') }}">Pengangkutan Residu</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.pengangkutan-residu.update', $item) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">No. Tiket</label>
                            <input type="text" class="form-control bg-light" value="{{ $item->nomor_tiket }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal" class="form-control @error('tanggal') is-invalid @enderror" value="{{ old('tanggal', $item->tanggal->format('Y-m-d')) }}" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Armada / Truk <span class="text-danger">*</span></label>
                            <select name="armada_id" class="form-select @error('armada_id') is-invalid @enderror" required>
                                @foreach($armadas as $armada)
                                    <option value="{{ $armada->id }}" {{ old('armada_id', $item->armada_id) == $armada->id ? 'selected' : '' }}>
                                        {{ $armada->nomor_plat }} - {{ $armada->nama_armada }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tujuan Pembuangan</label>
                            <input type="text" name="tujuan" class="form-control" value="{{ old('tujuan', $item->tujuan) }}">
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Waktu Keluar TPST</label>
                            <input type="time" name="waktu_keluar" class="form-control" value="{{ old('waktu_keluar', $item->waktu_keluar) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Waktu Kembali / Masuk</label>
                            <input type="time" name="waktu_masuk" class="form-control" value="{{ old('waktu_masuk', $item->waktu_masuk) }}">
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label">Berat Bruto (Kg) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="berat_bruto" id="berat_bruto" class="form-control @error('berat_bruto') is-invalid @enderror" value="{{ old('berat_bruto', $item->berat_bruto) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Berat Tarra (Kg) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="berat_tarra" id="berat_tarra" class="form-control @error('berat_tarra') is-invalid @enderror" value="{{ old('berat_tarra', $item->berat_tarra) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Berat Netto (Kg)</label>
                            <input type="number" id="berat_netto" class="form-control bg-light" value="{{ $item->berat_netto }}" readonly>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="3">{{ old('keterangan', $item->keterangan) }}</textarea>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.pengangkutan-residu.index') }}" class="btn btn-outline-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary px-4">Simpan Perubahan</button>
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

    function calculateNetto() {
        const bruto = parseFloat(brutoInput.value) || 0;
        const tarra = parseFloat(tarraInput.value) || 0;
        nettoInput.value = (bruto - tarra).toFixed(2);
    }

    brutoInput.addEventListener('input', calculateNetto);
    tarraInput.addEventListener('input', calculateNetto);
</script>
@endpush
@endsection
