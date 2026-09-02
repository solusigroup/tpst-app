@extends('layouts.admin')
@section('title', isset($invoice) ? 'Edit Invoice' : 'Buat Invoice')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div><h1>{{ isset($invoice) ? 'Edit' : 'Buat' }} Invoice</h1></div>
    <div>
        <a href="{{ route('admin.invoice.index') }}" class="btn btn-outline-secondary">
            <i class="cil-arrow-left me-1"></i> Kembali
        </a>
    </div>
</div>
<div class="row"><div class="col-lg-8"><div class="card"><div class="card-body">
    <form method="POST" action="{{ isset($invoice) ? route('admin.invoice.update', $invoice) : route('admin.invoice.store') }}">
        @csrf @if(isset($invoice)) @method('PUT') @endif
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Klien <span class="text-danger">*</span></label>
                <select name="klien_id" class="form-select @error('klien_id') is-invalid @enderror" required>
                    <option value="">-- Pilih --</option>
                    @foreach($kliens as $k)
                        <option value="{{ $k->id }}" 
                                data-jenis-tarif="{{ $k->jenis_tarif }}" 
                                data-besaran-tarif="{{ $k->besaran_tarif }}"
                                {{ old('klien_id', $invoice->klien_id ?? '') == $k->id ? 'selected' : '' }}>
                            {{ $k->nama_klien }}
                        </option>
                    @endforeach
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
            <div class="col-md-4">
                <label class="form-label">Total Tagihan (Rp) <span class="text-danger">*</span></label>
                <input type="number" id="total_tagihan" name="total_tagihan" class="form-control @error('total_tagihan') is-invalid @enderror" value="{{ old('total_tagihan', $invoice->total_tagihan ?? '0') }}" required>
                @error('total_tagihan') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Uang Muka / DP (Rp)</label>
                <input type="number" id="uang_muka" name="uang_muka" class="form-control @error('uang_muka') is-invalid @enderror" value="{{ old('uang_muka', $invoice->uang_muka ?? '0') }}" required>
                @error('uang_muka') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Sisa Tagihan (Rp)</label>
                <input type="number" id="sisa_tagihan" class="form-control" value="{{ ($invoice->total_tagihan ?? 0) - ($invoice->uang_muka ?? 0) }}" readonly>
            </div>
            <div class="col-12">
                <small class="text-muted" id="tariff-info"></small><br>
                <small class="text-muted">Total Tagihan dan Uang Muka dihitung otomatis berdasarkan item yang dipilih @if($kliens->where('jenis_tarif', 'Bulanan')->count() > 0) dan tarif bulanan (jika ada)@endif.</small>
            </div>
            <div class="col-md-6">
                <label class="form-label">Status <span class="text-danger">*</span></label>
                <select name="status" class="form-select" required>
                    @foreach(['Draft','Sent','Paid','Canceled'] as $s)<option value="{{ $s }}" {{ old('status', $invoice->status ?? 'Draft') == $s ? 'selected' : '' }}>{{ $s }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Akun Pembayaran (Kas/Bank) <span class="text-danger">*</span></label>
                <select name="coa_pembayaran_id" class="form-select @error('coa_pembayaran_id') is-invalid @enderror" required>
                    @foreach($coas as $c)
                        <option value="{{ $c->id }}" {{ old('coa_pembayaran_id', $invoice->coa_pembayaran_id ?? '') == $c->id ? 'selected' : ( !old('coa_pembayaran_id') && !isset($invoice) && $c->kode_akun === '1130' ? 'selected' : '' ) }}>
                            {{ $c->kode_akun }} - {{ $c->nama_akun }}
                        </option>
                    @endforeach
                </select>
                @error('coa_pembayaran_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-12 mt-4">
                <h5 class="border-bottom pb-2">Item Tertagih</h5>
                <div class="d-flex align-items-center gap-3 mb-2">
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" id="show-all-unbilled" role="switch">
                        <label class="form-check-label text-muted" for="show-all-unbilled">
                            <small>Tampilkan semua item unbilled (semua periode)</small>
                        </label>
                    </div>
                </div>
                <div id="period-filter-info" class="mb-2" style="font-size: 0.8rem;"></div>
                <div id="loading-items" class="text-muted" style="display: none;">Memuat data...</div>
                <div id="no-items" class="text-muted" style="display: none;">Pilih Klien untuk melihat item yang belum ditagihkan.</div>
                
                <div id="ritase-container" class="mb-3" style="display: none;">
                    <div class="d-flex justify-content-between align-items-center">
                        <strong>Ritase (Tipping Fee)</strong>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="select-all-ritase">
                            <label class="form-check-label" for="select-all-ritase"><small>Pilih Semua</small></label>
                        </div>
                    </div>
                    <div class="mt-2" id="ritase-list"></div>
                </div>

                <div id="penjualan-container" class="mb-3" style="display: none;">
                    <div class="d-flex justify-content-between align-items-center">
                        <strong>Penjualan (Hasil Pilahan)</strong>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="select-all-penjualan">
                            <label class="form-check-label" for="select-all-penjualan"><small>Pilih Semua</small></label>
                        </div>
                    </div>
                    <div class="mt-2" id="penjualan-list"></div>
                </div>
            </div>
            <div class="col-12 mt-3">
                <label class="form-label">Deskripsi Layanan (Opsional)</label>
                <textarea name="deskripsi_layanan" class="form-control" rows="2" placeholder="Gunakan untuk menimpa deskripsi otomatis pada saat cetak invoice">{{ old('deskripsi_layanan', $invoice->deskripsi_layanan ?? '') }}</textarea>
                <small class="text-muted">Jika diisi, teks ini akan muncul sebagai rincian utama di PDF. Jika dikosongkan, PDf akan merincikan otomatis berdasarkan rekapan Ritase/Penjualan.</small>
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const klienSelect = document.querySelector('select[name="klien_id"]');
    const loadingDiv = document.getElementById('loading-items');
    const noItemsDiv = document.getElementById('no-items');
    const ritaseContainer = document.getElementById('ritase-container');
    const ritaseList = document.getElementById('ritase-list');
    const penjualanContainer = document.getElementById('penjualan-container');
    const penjualanList = document.getElementById('penjualan-list');
    const totalTagihanInput = document.getElementById('total_tagihan');
    const uangMukaInput = document.getElementById('uang_muka');
    const sisaTagihanInput = document.getElementById('sisa_tagihan');
    
    // Check if we are editing an invoice
    const invoiceId = "{{ isset($invoice) ? $invoice->id : '' }}";
    let isInitialLoad = true;

    function formatRupiah(number) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
    }

    const selectAllRitase = document.getElementById('select-all-ritase');
    const selectAllPenjualan = document.getElementById('select-all-penjualan');

    function calculateTotal() {
        let total = 0;
        let dp = 0;
        
        // Sum from checkboxes
        document.querySelectorAll('.item-checkbox:checked').forEach(cb => {
            total += parseFloat(cb.dataset.price || 0);
            dp += parseFloat(cb.dataset.dp || 0);
        });

        // Add monthly fee if client is Bulanan
        const selectedOption = klienSelect.options[klienSelect.selectedIndex];
        const tariffInfo = document.getElementById('tariff-info');
        if (selectedOption && selectedOption.value) {
            const jenisTarif = selectedOption.dataset.jenisTarif;
            const besaranTarif = parseFloat(selectedOption.dataset.besaranTarif || 0);
            
            tariffInfo.textContent = `Tarif Klien: ${jenisTarif} - Rp ${besaranTarif.toLocaleString('id-ID')}`;
            
            if (jenisTarif === 'Bulanan') {
                total += besaranTarif;
            }
        } else {
            tariffInfo.textContent = '';
        }

        totalTagihanInput.value = total;
        uangMukaInput.value = dp;
        calculateBalance();
        updateSelectAllState();
    }

    function calculateBalance() {
        const total = parseFloat(totalTagihanInput.value || 0);
        const dp = parseFloat(uangMukaInput.value || 0);
        sisaTagihanInput.value = total - dp;
    }

    function updateSelectAllState() {
        const ritaseCheckboxes = document.querySelectorAll('input[name="selected_ritase[]"]');
        if (ritaseCheckboxes.length > 0) {
            selectAllRitase.checked = Array.from(ritaseCheckboxes).every(cb => cb.checked);
        }

        const penjualanCheckboxes = document.querySelectorAll('input[name="selected_penjualan[]"]');
        if (penjualanCheckboxes.length > 0) {
            selectAllPenjualan.checked = Array.from(penjualanCheckboxes).every(cb => cb.checked);
        }
    }

    selectAllRitase.addEventListener('change', function() {
        document.querySelectorAll('input[name="selected_ritase[]"]').forEach(cb => {
            cb.checked = this.checked;
        });
        calculateTotal();
    });

    selectAllPenjualan.addEventListener('change', function() {
        document.querySelectorAll('input[name="selected_penjualan[]"]').forEach(cb => {
            cb.checked = this.checked;
        });
        calculateTotal();
    });

    uangMukaInput.addEventListener('input', calculateBalance);
    totalTagihanInput.addEventListener('input', calculateBalance);

    const periodeBulanSelect = document.querySelector('select[name="periode_bulan"]');
    const periodeTahunSelect = document.querySelector('select[name="periode_tahun"]');
    const showAllToggle = document.getElementById('show-all-unbilled');
    const periodFilterInfo = document.getElementById('period-filter-info');

    const bulanNames = {
        '01':'Januari','02':'Februari','03':'Maret','04':'April','05':'Mei','06':'Juni',
        '07':'Juli','08':'Agustus','09':'September','10':'Oktober','11':'November','12':'Desember'
    };

    function updatePeriodInfo() {
        const bulan = periodeBulanSelect.value;
        const tahun = periodeTahunSelect.value;
        const showAll = showAllToggle.checked;

        if (showAll) {
            periodFilterInfo.innerHTML = '<span class="badge bg-info text-dark"><i class="cil-layers me-1"></i> Menampilkan semua item unbilled dari semua periode</span>';
        } else if (bulan && tahun) {
            periodFilterInfo.innerHTML = `<span class="badge bg-primary"><i class="cil-calendar me-1"></i> Filter aktif: ${bulanNames[bulan] || bulan} ${tahun}</span>`;
        } else {
            periodFilterInfo.innerHTML = '';
        }
    }

    function fetchItems() {
        const klienId = klienSelect.value;
        if (!klienId) {
            ritaseContainer.style.display = 'none';
            penjualanContainer.style.display = 'none';
            noItemsDiv.style.display = 'block';
            periodFilterInfo.innerHTML = '';
            return;
        }

        const periodeBulan = periodeBulanSelect.value;
        const periodeTahun = periodeTahunSelect.value;
        const showAll = showAllToggle.checked;

        loadingDiv.style.display = 'block';
        noItemsDiv.style.display = 'none';
        ritaseList.innerHTML = '';
        penjualanList.innerHTML = '';
        
        if (!invoiceId || !isInitialLoad) {
            totalTagihanInput.value = 0; // reset calculated total until fetched
        }
        
        // Reset select all checkboxes
        if (selectAllRitase) selectAllRitase.checked = false;
        if (selectAllPenjualan) selectAllPenjualan.checked = false;

        let url = `{{ route('admin.invoice-items.pending') }}?klien_id=${klienId}`;
        if (invoiceId) url += `&invoice_id=${invoiceId}`;
        url += `&periode_bulan=${periodeBulan}&periode_tahun=${periodeTahun}`;
        if (showAll) url += `&show_all=1`;

        updatePeriodInfo();

        fetch(url)
            .then(res => res.json())
            .then(data => {
                loadingDiv.style.display = 'none';
                
                let hasItems = false;

                function escapeHtml(text) {
                    if (!text) return '';
                    const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
                    return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
                }

                // Handle Ritase
                if (data.ritase && data.ritase.length > 0) {
                    hasItems = true;
                    ritaseContainer.style.display = 'block';
                    data.ritase.forEach(item => {
                        const checked = item.selected ? 'checked' : '';
                        const outOfPeriod = (!item.in_period && showAll) ? '<span class="badge bg-warning text-dark ms-1" style="font-size:0.65rem;">Beda Periode</span>' : '';
                        ritaseList.innerHTML += `
                            <div class="form-check">
                                <input class="form-check-input item-checkbox" type="checkbox" name="selected_ritase[]" value="${item.id}" id="ritase_${item.id}" data-price="${item.price}" ${checked}>
                                <label class="form-check-label" for="ritase_${item.id}">
                                    ${escapeHtml(item.label)} ${outOfPeriod}
                                </label>
                            </div>
                        `;
                    });
                } else {
                    ritaseContainer.style.display = 'none';
                }

                // Handle Penjualan
                if (data.penjualan && data.penjualan.length > 0) {
                    hasItems = true;
                    penjualanContainer.style.display = 'block';
                    data.penjualan.forEach(item => {
                        const checked = item.selected ? 'checked' : '';
                        const outOfPeriod = (!item.in_period && showAll) ? '<span class="badge bg-warning text-dark ms-1" style="font-size:0.65rem;">Beda Periode</span>' : '';
                        penjualanList.innerHTML += `
                            <div class="form-check">
                                <input class="form-check-input item-checkbox" type="checkbox" name="selected_penjualan[]" value="${item.id}" id="penjualan_${item.id}" data-price="${item.price}" data-dp="${item.dp}" ${checked}>
                                <label class="form-check-label" for="penjualan_${item.id}">
                                    ${escapeHtml(item.label)} ${outOfPeriod}
                                </label>
                            </div>
                        `;
                    });
                } else {
                    penjualanContainer.style.display = 'none';
                }

                if (!hasItems) {
                    noItemsDiv.style.display = 'block';
                    if (showAll) {
                        noItemsDiv.textContent = 'Tidak ada tagihan tertunda untuk klien ini.';
                    } else {
                        noItemsDiv.innerHTML = 'Tidak ada item pada periode <strong>' + (bulanNames[periodeBulan] || periodeBulan) + ' ' + periodeTahun + '</strong>. Coba aktifkan toggle "Tampilkan semua item unbilled" untuk melihat item dari periode lain.';
                    }
                } else {
                    // Attach change event listeners to checkboxes for live re-calculation
                    document.querySelectorAll('.item-checkbox').forEach(cb => {
                        cb.addEventListener('change', calculateTotal);
                    });
                    
                    // Initial calculation for pre-selected items
                    if (!invoiceId || !isInitialLoad) {
                        calculateTotal();
                    } else {
                        updateSelectAllState();
                    }
                }
                
                isInitialLoad = false;
            })
            .catch(err => {
                console.error('Error fetching items:', err);
                loadingDiv.style.display = 'none';
                noItemsDiv.style.display = 'block';
                noItemsDiv.textContent = 'Gagal memuat data. Silakan coba lagi.';
            });
    }

    klienSelect.addEventListener('change', fetchItems);
    periodeBulanSelect.addEventListener('change', fetchItems);
    periodeTahunSelect.addEventListener('change', fetchItems);
    showAllToggle.addEventListener('change', fetchItems);
    
    // Trigger automatically on page load to fetch existing items if Klien is pre-filled
    if (klienSelect.value) {
        fetchItems();
    }
});
</script>
@endpush
