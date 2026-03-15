@extends('layouts.admin')
@section('title', isset($invoice) ? 'Edit Invoice' : 'Buat Invoice')

@section('content')
<div class="page-header"><div><h1>{{ isset($invoice) ? 'Edit' : 'Buat' }} Invoice</h1></div></div>
<div class="row"><div class="col-lg-8"><div class="card"><div class="card-body">
    <form method="POST" action="{{ isset($invoice) ? route('admin.invoice.update', $invoice) : route('admin.invoice.store') }}">
        @csrf @if(isset($invoice)) @method('PUT') @endif
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Klien <span class="text-danger">*</span></label>
                <select name="klien_id" class="form-select @error('klien_id') is-invalid @enderror" required>
                    <option value="">-- Pilih --</option>
                    @foreach($kliens as $k)<option value="{{ $k->id }}" {{ old('klien_id', $invoice->klien_id ?? '') == $k->id ? 'selected' : '' }}>{{ $k->nama_klien }}</option>@endforeach
                </select>@error('klien_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-3">
                <label class="form-label">Bulan <span class="text-danger">*</span></label>
                <select name="periode_bulan" class="form-select" required>
                    @foreach(['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'] as $val => $label)
                        <option value="{{ $val }}" {{ old('periode_bulan', $invoice->periode_bulan ?? '') == $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Tahun <span class="text-danger">*</span></label>
                <select name="periode_tahun" class="form-select" required>
                    @for($y = date('Y') - 2; $y <= date('Y') + 1; $y++)
                        <option value="{{ $y }}" {{ old('periode_tahun', $invoice->periode_tahun ?? '') == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Tgl Invoice <span class="text-danger">*</span></label>
                <input type="date" name="tanggal_invoice" class="form-control @error('tanggal_invoice') is-invalid @enderror" value="{{ old('tanggal_invoice', isset($invoice) ? \Carbon\Carbon::parse($invoice->tanggal_invoice)->format('Y-m-d') : date('Y-m-d')) }}" required>
                @error('tanggal_invoice') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Jatuh Tempo <span class="text-danger">*</span></label>
                <input type="date" name="tanggal_jatuh_tempo" class="form-control" value="{{ old('tanggal_jatuh_tempo', isset($invoice) ? \Carbon\Carbon::parse($invoice->tanggal_jatuh_tempo)->format('Y-m-d') : date('Y-m-d', strtotime('+14 days'))) }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Total Tagihan (Rp) <span class="text-danger">*</span></label>
                <input type="number" name="total_tagihan" class="form-control @error('total_tagihan') is-invalid @enderror" value="{{ old('total_tagihan', $invoice->total_tagihan ?? '') }}" required>
                @error('total_tagihan') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Status <span class="text-danger">*</span></label>
                <select name="status" class="form-select" required>
                    @foreach(['Draft','Sent','Paid','Canceled'] as $s)<option value="{{ $s }}" {{ old('status', $invoice->status ?? 'Draft') == $s ? 'selected' : '' }}>{{ $s }}</option>@endforeach
                </select>
            </div>
            <div class="col-12">
                <label class="form-label">Keterangan</label>
                <textarea name="keterangan" class="form-control" rows="3">{{ old('keterangan', $invoice->keterangan ?? '') }}</textarea>
            </div>
            <div class="col-12 d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="cil-save me-1"></i> {{ isset($invoice) ? 'Perbarui' : 'Simpan' }}</button>
                <a href="{{ route('admin.invoice.index') }}" class="btn btn-outline-secondary">Kembali</a>
            </div>
        </div>
    </form>
</div></div></div></div>
@endsection
