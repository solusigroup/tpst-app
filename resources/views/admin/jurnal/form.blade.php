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

    @if(isset($refType) && isset($refId))
        <input type="hidden" name="referensi_type" value="{{ $refType }}">
        <input type="hidden" name="referensi_id" value="{{ $refId }}">
    @endif

    <div class="row g-4">
        <div class="col-lg-8">
            {{-- Template Selector (only on create mode) --}}
            @if(!isset($jurnal) && isset($templates) && $templates->count() > 0)
            <div class="card mb-4 border-primary border-opacity-25">
                <div class="card-header bg-primary bg-opacity-10 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold text-primary"><i class="cil-layers me-1"></i> Template Jurnal Berulang</h6>
                </div>
                <div class="card-body py-3">
                    <div class="row g-2 align-items-end">
                        <div class="col">
                            <select id="template-select" class="form-select">
                                <option value="">-- Pilih Template --</option>
                                @foreach($templates as $tpl)
                                    <option value="{{ $tpl->id }}" data-details='@json($tpl->details)' data-deskripsi="{{ $tpl->deskripsi }}">
                                        {{ $tpl->nama }} @if($tpl->deskripsi) — {{ $tpl->deskripsi }} @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-primary" id="btn-apply-template">
                                <i class="cil-check me-1"></i> Terapkan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endif

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
                            <textarea name="deskripsi" class="form-control" rows="3">{{ old('deskripsi', $jurnal->deskripsi ?? ($defaultDeskripsi ?? '')) }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Bukti Transaksi</label>
                            <input type="file" name="bukti_transaksi" class="form-control" accept="image/*" data-compress>
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
                            <thead class="table-light">
                            <thead class="table-light">
                                <tr><th>Akun</th><th>Mitra (Opsional)</th><th style="width:180px;">Debit</th><th style="width:180px;">Kredit</th><th style="width:50px;"></th></tr>
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
                                        <td>
                                            <select name="details[{{ $i }}][contactable_type_id]" class="form-select form-select-sm">
                                                <option value="">-- Tanpa Mitra --</option>
                                                <optgroup label="Klien">
                                                    @foreach($kliens as $k)
                                                        <option value="App\Models\Klien:{{ $k->id }}" {{ ($detail->contactable_type === 'App\Models\Klien' && $detail->contactable_id == $k->id) ? 'selected' : '' }}>{{ $k->nama_klien }}</option>
                                                    @endforeach
                                                </optgroup>
                                                <optgroup label="Vendor">
                                                    @foreach($vendors as $v)
                                                        <option value="App\Models\Vendor:{{ $v->id }}" {{ ($detail->contactable_type === 'App\Models\Vendor' && $detail->contactable_id == $v->id) ? 'selected' : '' }}>{{ $v->nama_vendor }}</option>
                                                    @endforeach
                                                </optgroup>
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
                                        <td>
                                            <select name="details[0][contactable_type_id]" class="form-select form-select-sm">
                                                <option value="">-- Tanpa Mitra --</option>
                                                <optgroup label="Klien">@foreach($kliens as $k)<option value="App\Models\Klien:{{ $k->id }}">{{ $k->nama_klien }}</option>@endforeach</optgroup>
                                                <optgroup label="Vendor">@foreach($vendors as $v)<option value="App\Models\Vendor:{{ $v->id }}">{{ $v->nama_vendor }}</option>@endforeach</optgroup>
                                            </select>
                                        </td>
                                        <td><input type="number" name="details[0][debit]" class="form-control form-control-sm debit-input" value="{{ old('details.0.debit', rtrim(rtrim(number_format($defaultNominal ?? 0, 2, '.', ''), '0'), '.') ?: 0) }}" oninput="updateTotals()"></td>
                                        <td><input type="number" name="details[0][kredit]" class="form-control form-control-sm kredit-input" value="0" oninput="updateTotals()"></td>
                                        <td><button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('tr').remove();updateTotals()"><i class="cil-trash"></i></button></td>
                                    </tr>
                                    <tr>
                                        <td><select name="details[1][coa_id]" class="form-select form-select-sm" required><option value="">-- Pilih --</option>@foreach($coas as $c)<option value="{{ $c->id }}">{{ $c->kode_akun }} - {{ $c->nama_akun }}</option>@endforeach</select></td>
                                        <td>
                                            <select name="details[1][contactable_type_id]" class="form-select form-select-sm">
                                                <option value="">-- Tanpa Mitra --</option>
                                                <optgroup label="Klien">@foreach($kliens as $k)<option value="App\Models\Klien:{{ $k->id }}">{{ $k->nama_klien }}</option>@endforeach</optgroup>
                                                <optgroup label="Vendor">@foreach($vendors as $v)<option value="App\Models\Vendor:{{ $v->id }}">{{ $v->nama_vendor }}</option>@endforeach</optgroup>
                                            </select>
                                        </td>
                                        <td><input type="number" name="details[1][debit]" class="form-control form-control-sm debit-input" value="0" oninput="updateTotals()"></td>
                                        <td><input type="number" name="details[1][kredit]" class="form-control form-control-sm kredit-input" value="{{ old('details.1.kredit', rtrim(rtrim(number_format($defaultNominal ?? 0, 2, '.', ''), '0'), '.') ?: 0) }}" oninput="updateTotals()"></td>
                                        <td><button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('tr').remove();updateTotals()"><i class="cil-trash"></i></button></td>
                                    </tr>
                                @endif
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <td class="fw-bold text-end" colspan="2">Total</td>
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

            {{-- Save as Template (only on create mode) --}}
            @if(!isset($jurnal))
            <div class="card mt-3">
                <div class="card-header bg-white"><h6 class="mb-0 fw-semibold"><i class="cil-layers me-1"></i> Simpan sebagai Template</h6></div>
                <div class="card-body">
                    <p class="small text-muted mb-3">Simpan pola COA Debit/Kredit saat ini agar bisa digunakan lagi nanti.</p>
                    <div class="mb-2">
                        <input type="text" id="template-nama" class="form-control form-control-sm" placeholder="Nama template, misal: Bayar Listrik">
                    </div>
                    <div class="mb-2">
                        <input type="text" id="template-deskripsi" class="form-control form-control-sm" placeholder="Deskripsi singkat (opsional)">
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-success w-100" id="btn-save-template">
                        <i class="cil-save me-1"></i> Simpan Template
                    </button>
                </div>
            </div>

            {{-- Manage Templates --}}
            @if(isset($templates) && $templates->count() > 0)
            <div class="card mt-3">
                <div class="card-header bg-white"><h6 class="mb-0 fw-semibold">Daftar Template</h6></div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @foreach($templates as $tpl)
                        <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                            <div>
                                <strong class="small">{{ $tpl->nama }}</strong>
                                @if($tpl->deskripsi)<br><span class="text-muted small">{{ $tpl->deskripsi }}</span>@endif
                            </div>
                            <form method="POST" action="{{ route('admin.jurnal-template.destroy', $tpl) }}" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus template ini?')" title="Hapus">
                                    <i class="cil-trash"></i>
                                </button>
                            </form>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif
            @endif
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
let rowIndex = {{ isset($jurnal) ? $jurnal->jurnalDetails->count() : 2 }};

const coaOptions = `<option value="">-- Pilih --</option>@foreach($coas as $c)<option value="{{ $c->id }}">{{ $c->kode_akun }} - {{ $c->nama_akun }}</option>@endforeach`;
const mitraOptions = `<option value="">-- Tanpa Mitra --</option><optgroup label="Klien">@foreach($kliens as $k)<option value="App\\Models\\Klien:{{ $k->id }}">{{ $k->nama_klien }}</option>@endforeach</optgroup><optgroup label="Vendor">@foreach($vendors as $v)<option value="App\\Models\\Vendor:{{ $v->id }}">{{ $v->nama_vendor }}</option>@endforeach</optgroup>`;

function addRow(coaId = '', posisi = '') {
    const row = `<tr>
        <td><select name="details[${rowIndex}][coa_id]" class="form-select form-select-sm" required>${coaOptions}</select></td>
        <td><select name="details[${rowIndex}][contactable_type_id]" class="form-select form-select-sm">${mitraOptions}</select></td>
        <td><input type="number" name="details[${rowIndex}][debit]" class="form-control form-control-sm debit-input" value="0" oninput="updateTotals()"></td>
        <td><input type="number" name="details[${rowIndex}][kredit]" class="form-control form-control-sm kredit-input" value="0" oninput="updateTotals()"></td>
        <td><button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('tr').remove();updateTotals()"><i class="cil-trash"></i></button></td>
    </tr>`;
    document.getElementById('detail-body').insertAdjacentHTML('beforeend', row);

    if (coaId) {
        const lastRow = document.getElementById('detail-body').lastElementChild;
        lastRow.querySelector('select[name*="coa_id"]').value = coaId;
    }

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

document.addEventListener('DOMContentLoaded', function() {
    updateTotals();

    // Apply Template
    const btnApply = document.getElementById('btn-apply-template');
    if (btnApply) {
        btnApply.addEventListener('click', function() {
            const select = document.getElementById('template-select');
            const option = select.options[select.selectedIndex];
            if (!option || !option.value) {
                alert('Pilih template terlebih dahulu.');
                return;
            }

            const details = JSON.parse(option.dataset.details);
            const deskripsi = option.dataset.deskripsi || '';

            // Clear existing rows
            document.getElementById('detail-body').innerHTML = '';
            rowIndex = 0;

            // Fill deskripsi if empty
            const deskripsiField = document.querySelector('textarea[name="deskripsi"]');
            if (deskripsiField && !deskripsiField.value.trim()) {
                deskripsiField.value = deskripsi;
            }

            // Add rows from template
            details.forEach(function(d) {
                addRow(d.coa_id, d.posisi);
            });

            updateTotals();

            // Visual feedback
            select.closest('.card').classList.add('border-success');
            setTimeout(() => select.closest('.card').classList.remove('border-success'), 1500);
        });
    }

    // Save as Template
    const btnSave = document.getElementById('btn-save-template');
    if (btnSave) {
        btnSave.addEventListener('click', function() {
            const nama = document.getElementById('template-nama').value.trim();
            if (!nama) {
                alert('Nama template wajib diisi.');
                document.getElementById('template-nama').focus();
                return;
            }

            const rows = document.querySelectorAll('#detail-body tr');
            if (rows.length < 2) {
                alert('Minimal harus ada 2 baris detail jurnal.');
                return;
            }

            const templateDetails = [];
            let valid = true;
            rows.forEach(function(row) {
                const coaSelect = row.querySelector('select[name*="coa_id"]');
                const debitInput = row.querySelector('.debit-input');
                const kreditInput = row.querySelector('.kredit-input');

                if (!coaSelect || !coaSelect.value) {
                    valid = false;
                    return;
                }

                const debit = parseFloat(debitInput?.value) || 0;
                const kredit = parseFloat(kreditInput?.value) || 0;
                const posisi = debit > 0 ? 'debit' : 'kredit';

                templateDetails.push({
                    coa_id: coaSelect.value,
                    posisi: posisi
                });
            });

            if (!valid || templateDetails.length < 2) {
                alert('Pastikan semua baris detail memiliki Akun yang dipilih.');
                return;
            }

            // Submit via hidden form
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("admin.jurnal-template.store") }}';

            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';
            form.appendChild(csrf);

            const namaInput = document.createElement('input');
            namaInput.type = 'hidden';
            namaInput.name = 'nama';
            namaInput.value = nama;
            form.appendChild(namaInput);

            const deskripsiInput = document.createElement('input');
            deskripsiInput.type = 'hidden';
            deskripsiInput.name = 'deskripsi';
            deskripsiInput.value = document.getElementById('template-deskripsi').value.trim();
            form.appendChild(deskripsiInput);

            templateDetails.forEach(function(d, i) {
                const coaInput = document.createElement('input');
                coaInput.type = 'hidden';
                coaInput.name = `template_details[${i}][coa_id]`;
                coaInput.value = d.coa_id;
                form.appendChild(coaInput);

                const posisiInput = document.createElement('input');
                posisiInput.type = 'hidden';
                posisiInput.name = `template_details[${i}][posisi]`;
                posisiInput.value = d.posisi;
                form.appendChild(posisiInput);
            });

            document.body.appendChild(form);
            form.submit();
        });
    }
});
</script>
@endpush
