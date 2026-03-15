@extends('layouts.admin')
@section('title', isset($jurnal) ? 'Edit Jurnal' : 'Tambah Jurnal')

@section('content')
<div class="page-header">
    <div>
        <h1>{{ isset($jurnal) ? 'Edit' : 'Tambah' }} Jurnal</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li><li class="breadcrumb-item"><a href="{{ route('admin.jurnal.index') }}">Jurnal</a></li><li class="breadcrumb-item active">{{ isset($jurnal) ? 'Edit' : 'Tambah' }}</li></ol></nav>
    </div>
</div>

<form method="POST" action="{{ isset($jurnal) ? route('admin.jurnal.update', $jurnal) : route('admin.jurnal.store') }}" enctype="multipart/form-data">
    @csrf @if(isset($jurnal)) @method('PUT') @endif

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-white"><h6 class="mb-0 fw-semibold">Informasi Jurnal</h6></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal" class="form-control @error('tanggal') is-invalid @enderror" value="{{ old('tanggal', isset($jurnal) ? \Carbon\Carbon::parse($jurnal->tanggal)->format('Y-m-d') : '') }}" required>
                            @error('tanggal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">No. Referensi</label>
                            <input type="text" class="form-control bg-light" value="{{ $jurnal->nomor_referensi ?? 'Otomatis' }}" readonly>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="deskripsi" class="form-control" rows="3">{{ old('deskripsi', $jurnal->deskripsi ?? '') }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Bukti Transaksi</label>
                            <input type="file" name="bukti_transaksi" class="form-control" accept="image/*">
                            @if(isset($jurnal) && $jurnal->bukti_transaksi)
                                <img src="{{ asset('storage/' . $jurnal->bukti_transaksi) }}" class="mt-2 rounded" style="max-height:100px;" alt="bukti">
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold">Detail Jurnal</h6>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="addRow()"><i class="cil-plus me-1"></i> Tambah Baris</button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0" id="detail-table">
                            <thead class="bg-light">
                                <tr><th>Akun</th><th style="width:180px;">Debit</th><th style="width:180px;">Kredit</th><th style="width:50px;"></th></tr>
                            </thead>
                            <tbody id="detail-body">
                                @if(isset($jurnal) && $jurnal->jurnalDetails->count())
                                    @foreach($jurnal->jurnalDetails as $i => $detail)
                                    <tr>
                                        <td>
                                            <select name="details[{{ $i }}][coa_id]" class="form-select form-select-sm" required>
                                                <option value="">-- Pilih --</option>
                                                @foreach($coas as $c)<option value="{{ $c->id }}" {{ $detail->coa_id == $c->id ? 'selected' : '' }}>{{ $c->kode_akun }} - {{ $c->nama_akun }}</option>@endforeach
                                            </select>
                                        </td>
                                        <td><input type="number" name="details[{{ $i }}][debit]" class="form-control form-control-sm debit-input" value="{{ $detail->debit }}" oninput="updateTotals()"></td>
                                        <td><input type="number" name="details[{{ $i }}][kredit]" class="form-control form-control-sm kredit-input" value="{{ $detail->kredit }}" oninput="updateTotals()"></td>
                                        <td><button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('tr').remove();updateTotals()"><i class="cil-trash"></i></button></td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td><select name="details[0][coa_id]" class="form-select form-select-sm" required><option value="">-- Pilih --</option>@foreach($coas as $c)<option value="{{ $c->id }}">{{ $c->kode_akun }} - {{ $c->nama_akun }}</option>@endforeach</select></td>
                                        <td><input type="number" name="details[0][debit]" class="form-control form-control-sm debit-input" value="0" oninput="updateTotals()"></td>
                                        <td><input type="number" name="details[0][kredit]" class="form-control form-control-sm kredit-input" value="0" oninput="updateTotals()"></td>
                                        <td><button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('tr').remove();updateTotals()"><i class="cil-trash"></i></button></td>
                                    </tr>
                                    <tr>
                                        <td><select name="details[1][coa_id]" class="form-select form-select-sm" required><option value="">-- Pilih --</option>@foreach($coas as $c)<option value="{{ $c->id }}">{{ $c->kode_akun }} - {{ $c->nama_akun }}</option>@endforeach</select></td>
                                        <td><input type="number" name="details[1][debit]" class="form-control form-control-sm debit-input" value="0" oninput="updateTotals()"></td>
                                        <td><input type="number" name="details[1][kredit]" class="form-control form-control-sm kredit-input" value="0" oninput="updateTotals()"></td>
                                        <td><button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('tr').remove();updateTotals()"><i class="cil-trash"></i></button></td>
                                    </tr>
                                @endif
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <td class="fw-bold text-end">Total</td>
                                    <td class="fw-bold" id="total-debit">0</td>
                                    <td class="fw-bold" id="total-kredit">0</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div id="balance-alert" class="alert alert-danger m-3 d-none">
                        <i class="cil-warning me-1"></i> Total debit dan kredit harus seimbang!
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-body d-grid gap-2">
                    <button type="submit" class="btn btn-primary"><i class="cil-save me-1"></i> {{ isset($jurnal) ? 'Perbarui' : 'Simpan' }}</button>
                    <a href="{{ route('admin.jurnal.index') }}" class="btn btn-outline-secondary">Kembali</a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
let rowIndex = {{ isset($jurnal) ? $jurnal->jurnalDetails->count() : 2 }};

function addRow() {
    const coaOptions = `<option value="">-- Pilih --</option>@foreach($coas as $c)<option value="{{ $c->id }}">{{ $c->kode_akun }} - {{ $c->nama_akun }}</option>@endforeach`;
    const row = `<tr>
        <td><select name="details[${rowIndex}][coa_id]" class="form-select form-select-sm" required>${coaOptions}</select></td>
        <td><input type="number" name="details[${rowIndex}][debit]" class="form-control form-control-sm debit-input" value="0" oninput="updateTotals()"></td>
        <td><input type="number" name="details[${rowIndex}][kredit]" class="form-control form-control-sm kredit-input" value="0" oninput="updateTotals()"></td>
        <td><button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('tr').remove();updateTotals()"><i class="cil-trash"></i></button></td>
    </tr>`;
    document.getElementById('detail-body').insertAdjacentHTML('beforeend', row);
    rowIndex++;
}

function updateTotals() {
    let totalDebit = 0, totalKredit = 0;
    document.querySelectorAll('.debit-input').forEach(el => totalDebit += parseFloat(el.value) || 0);
    document.querySelectorAll('.kredit-input').forEach(el => totalKredit += parseFloat(el.value) || 0);
    document.getElementById('total-debit').textContent = totalDebit.toLocaleString('id-ID');
    document.getElementById('total-kredit').textContent = totalKredit.toLocaleString('id-ID');
    document.getElementById('balance-alert').classList.toggle('d-none', Math.abs(totalDebit - totalKredit) < 0.01);
}

document.addEventListener('DOMContentLoaded', updateTotals);
</script>
@endpush
