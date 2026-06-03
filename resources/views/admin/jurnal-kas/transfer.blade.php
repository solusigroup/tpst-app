@extends('layouts.admin')
@section('title', 'Transfer Kas / Bank')

@section('content')
<div class="page-header">
    <div>
        <h1><i class="cil-transfer me-2"></i>Transfer Kas / Bank</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.jurnal-kas.index') }}">Jurnal Kas</a></li>
                <li class="breadcrumb-item active">Transfer</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card shadow-sm">
            <div class="card-header bg-primary bg-opacity-10 border-bottom-0">
                <h6 class="mb-0 fw-semibold text-primary"><i class="cil-swap-horizontal me-1"></i> Transfer Antar Rekening Kas / Bank</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.transfer-kas.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        {{-- Tanggal --}}
                        <div class="col-md-6">
                            <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal" class="form-control @error('tanggal') is-invalid @enderror" value="{{ old('tanggal', date('Y-m-d')) }}" required>
                            @error('tanggal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6"></div>

                        {{-- Dari Akun --}}
                        <div class="col-md-6">
                            <label class="form-label">Dari Rekening <span class="text-danger">*</span></label>
                            <select name="dari_coa_id" id="dari_coa_id" class="form-select @error('dari_coa_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Sumber --</option>
                                @foreach($kasBank as $c)
                                    <option value="{{ $c->id }}" data-saldo="{{ $c->saldo }}" {{ old('dari_coa_id') == $c->id ? 'selected' : '' }}>
                                        {{ $c->kode_akun }} - {{ $c->nama_akun }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text" id="saldo-dari">
                                <span class="text-muted">Pilih rekening sumber untuk melihat saldo.</span>
                            </div>
                            @error('dari_coa_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Ke Akun --}}
                        <div class="col-md-6">
                            <label class="form-label">Ke Rekening <span class="text-danger">*</span></label>
                            <select name="ke_coa_id" id="ke_coa_id" class="form-select @error('ke_coa_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Tujuan --</option>
                                @foreach($kasBank as $c)
                                    <option value="{{ $c->id }}" {{ old('ke_coa_id') == $c->id ? 'selected' : '' }}>
                                        {{ $c->kode_akun }} - {{ $c->nama_akun }}
                                    </option>
                                @endforeach
                            </select>
                            @error('ke_coa_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Arrow indicator --}}
                        <div class="col-12 text-center py-1">
                            <div class="d-inline-flex align-items-center gap-3 px-4 py-2 rounded-pill bg-light border">
                                <span id="label-dari" class="fw-semibold text-muted">Sumber</span>
                                <i class="cil-arrow-right text-primary" style="font-size: 1.3rem;"></i>
                                <span id="label-ke" class="fw-semibold text-muted">Tujuan</span>
                            </div>
                        </div>

                        {{-- Jumlah Transfer --}}
                        <div class="col-md-6">
                            <label class="form-label">Jumlah Transfer (Rp) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="jumlah" id="jumlah" class="form-control @error('jumlah') is-invalid @enderror" value="{{ old('jumlah') }}" min="1" required>
                            </div>
                            @error('jumlah') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Biaya Admin --}}
                        <div class="col-md-6">
                            <label class="form-label">Biaya Admin Bank (Rp)</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="biaya_admin" id="biaya_admin" class="form-control @error('biaya_admin') is-invalid @enderror" value="{{ old('biaya_admin', 0) }}" min="0">
                            </div>
                            <div class="form-text">Otomatis dicatat ke COA {{ $coaBiayaAdmin->kode_akun ?? '8102' }} - {{ $coaBiayaAdmin->nama_akun ?? 'Biaya Admin Bank' }}</div>
                            @error('biaya_admin') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Ringkasan Jurnal --}}
                        <div class="col-12">
                            <div class="alert alert-info bg-info bg-opacity-10 border-info border-opacity-25 mb-0" id="ringkasan-jurnal" style="display: none;">
                                <h6 class="alert-heading fw-bold mb-2"><i class="cil-spreadsheet me-1"></i> Preview Jurnal yang Akan Dibuat</h6>
                                <table class="table table-sm table-borderless mb-0 small">
                                    <tbody id="ringkasan-body"></tbody>
                                    <tfoot>
                                        <tr class="fw-bold border-top">
                                            <td>TOTAL</td>
                                            <td class="text-end" id="ringkasan-total-debit">-</td>
                                            <td class="text-end" id="ringkasan-total-kredit">-</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        {{-- Deskripsi --}}
                        <div class="col-12">
                            <label class="form-label">Keterangan</label>
                            <textarea name="deskripsi" class="form-control" rows="2" placeholder="Misal: Tarik tunai untuk operasional harian">{{ old('deskripsi') }}</textarea>
                        </div>

                        {{-- Bukti --}}
                        <div class="col-12">
                            <label class="form-label">Bukti Transfer</label>
                            <input type="file" name="bukti_transaksi" class="form-control @error('bukti_transaksi') is-invalid @enderror" accept="image/*" data-compress capture="environment">
                            <div class="form-text">Foto struk / bukti transfer (opsional). Otomatis dikompresi.</div>
                            @error('bukti_transaksi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Buttons --}}
                        <div class="col-12 d-flex gap-2 mt-2">
                            <button type="submit" class="btn btn-primary" id="btn-submit">
                                <i class="cil-transfer me-1"></i> Transfer Sekarang
                            </button>
                            <a href="{{ route('admin.jurnal-kas.index') }}" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dariSelect = document.getElementById('dari_coa_id');
    const keSelect = document.getElementById('ke_coa_id');
    const jumlahInput = document.getElementById('jumlah');
    const biayaInput = document.getElementById('biaya_admin');
    const saldoDariDiv = document.getElementById('saldo-dari');
    const labelDari = document.getElementById('label-dari');
    const labelKe = document.getElementById('label-ke');
    const ringkasan = document.getElementById('ringkasan-jurnal');
    const ringkasanBody = document.getElementById('ringkasan-body');

    function formatRp(n) {
        return 'Rp ' + Number(n).toLocaleString('id-ID');
    }

    function updateLabels() {
        const dariOpt = dariSelect.options[dariSelect.selectedIndex];
        const keOpt = keSelect.options[keSelect.selectedIndex];
        labelDari.textContent = dariOpt && dariOpt.value ? dariOpt.textContent.trim() : 'Sumber';
        labelKe.textContent = keOpt && keOpt.value ? keOpt.textContent.trim() : 'Tujuan';
    }

    function updateSaldo() {
        const opt = dariSelect.options[dariSelect.selectedIndex];
        if (opt && opt.value) {
            const saldo = parseFloat(opt.dataset.saldo) || 0;
            saldoDariDiv.innerHTML = `Saldo tersedia: <strong class="text-${saldo > 0 ? 'success' : 'danger'}">${formatRp(saldo)}</strong>`;
        } else {
            saldoDariDiv.innerHTML = '<span class="text-muted">Pilih rekening sumber untuk melihat saldo.</span>';
        }
    }

    function updatePreview() {
        const jumlah = parseFloat(jumlahInput.value) || 0;
        const biaya = parseFloat(biayaInput.value) || 0;
        const dariOpt = dariSelect.options[dariSelect.selectedIndex];
        const keOpt = keSelect.options[keSelect.selectedIndex];

        if (jumlah <= 0 || !dariOpt?.value || !keOpt?.value) {
            ringkasan.style.display = 'none';
            return;
        }

        const totalKredit = jumlah + biaya;
        let html = '';

        // Debit: Tujuan
        html += `<tr>
            <td><span class="badge bg-success me-1">D</span> ${keOpt.textContent.trim()}</td>
            <td class="text-end">${formatRp(jumlah)}</td>
            <td class="text-end">-</td>
        </tr>`;

        // Debit: Biaya Admin (if > 0)
        if (biaya > 0) {
            html += `<tr>
                <td><span class="badge bg-success me-1">D</span> {{ ($coaBiayaAdmin->kode_akun ?? '8102') . ' - ' . ($coaBiayaAdmin->nama_akun ?? 'Biaya Admin Bank') }}</td>
                <td class="text-end">${formatRp(biaya)}</td>
                <td class="text-end">-</td>
            </tr>`;
        }

        // Kredit: Sumber
        html += `<tr>
            <td><span class="badge bg-danger me-1">K</span> ${dariOpt.textContent.trim()}</td>
            <td class="text-end">-</td>
            <td class="text-end">${formatRp(totalKredit)}</td>
        </tr>`;

        ringkasanBody.innerHTML = html;
        document.getElementById('ringkasan-total-debit').textContent = formatRp(totalKredit);
        document.getElementById('ringkasan-total-kredit').textContent = formatRp(totalKredit);
        ringkasan.style.display = 'block';
    }

    dariSelect.addEventListener('change', function() { updateSaldo(); updateLabels(); updatePreview(); });
    keSelect.addEventListener('change', function() { updateLabels(); updatePreview(); });
    jumlahInput.addEventListener('input', updatePreview);
    biayaInput.addEventListener('input', updatePreview);

    updateSaldo();
    updateLabels();
    updatePreview();
});
</script>
@endpush
@endsection
