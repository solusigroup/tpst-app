@extends('layouts.admin')
@section('title', isset($ritase) ? 'Edit Ritase' : 'Tambah Ritase')

@section('content')
<div class="page-header">
    <div>
        <h1>{{ isset($ritase) ? 'Edit Ritase' : 'Tambah Ritase' }}</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li><li class="breadcrumb-item"><a href="{{ route('admin.ritase.index') }}">Ritase</a></li><li class="breadcrumb-item active">{{ isset($ritase) ? 'Edit' : 'Tambah' }}</li></ol></nav>
    </div>
</div>

<form method="POST" action="{{ isset($ritase) ? route('admin.ritase.update', $ritase) : route('admin.ritase.store') }}" enctype="multipart/form-data">
    @csrf
    @if(isset($ritase)) @method('PUT') @endif

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-white"><h6 class="mb-0 fw-semibold">Informasi Ritase</h6></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Armada <span class="text-danger">*</span></label>
                            <select name="armada_id" class="form-select @error('armada_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Armada --</option>
                                @foreach($armadas as $a)
                                    <option value="{{ $a->id }}" data-berat-kosong="{{ $a->berat_kosong }}" data-klien-id="{{ $a->klien_id }}" {{ old('armada_id', $ritase->armada_id ?? '') == $a->id ? 'selected' : '' }}>{{ $a->plat_nomor }}</option>
                                @endforeach
                            </select>
                            @error('armada_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Klien <span class="text-danger">*</span></label>
                            <select name="klien_id" class="form-select @error('klien_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Klien --</option>
                                @foreach($kliens as $k)
                                    <option value="{{ $k->id }}" {{ old('klien_id', $ritase->klien_id ?? '') == $k->id ? 'selected' : '' }}>{{ $k->nama_klien }}</option>
                                @endforeach
                            </select>
                            @error('klien_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Waktu Masuk <span class="text-danger">*</span></label>
                            <input type="datetime-local" id="waktu_masuk" name="waktu_masuk" class="form-control @error('waktu_masuk') is-invalid @enderror" value="{{ old('waktu_masuk', isset($ritase) ? \Carbon\Carbon::parse($ritase->waktu_masuk)->format('Y-m-d\TH:i') : '') }}" required onchange="updateWaktuKeluar()">
                            @error('waktu_masuk') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Waktu Keluar</label>
                            <input type="datetime-local" id="waktu_keluar" name="waktu_keluar" class="form-control @error('waktu_keluar') is-invalid @enderror" value="{{ old('waktu_keluar', isset($ritase) && $ritase->waktu_keluar ? \Carbon\Carbon::parse($ritase->waktu_keluar)->format('Y-m-d\TH:i') : '') }}">
                            @error('waktu_keluar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header bg-white"><h6 class="mb-0 fw-semibold">Pengukuran Berat</h6></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Berat Bruto (kg) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="berat_bruto" id="berat_bruto" class="form-control @error('berat_bruto') is-invalid @enderror" value="{{ old('berat_bruto', $ritase->berat_bruto ?? '') }}" required oninput="calcNetto()">
                            @error('berat_bruto') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Berat Tarra (kg) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="berat_tarra" id="berat_tarra" class="form-control @error('berat_tarra') is-invalid @enderror" value="{{ old('berat_tarra', $ritase->berat_tarra ?? '') }}" required oninput="calcNetto()">
                            @error('berat_tarra') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Berat Netto (kg)</label>
                            <input type="number" step="0.01" id="berat_netto" class="form-control bg-light" value="{{ old('berat_netto', $ritase->berat_netto ?? '0') }}" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-white"><h6 class="mb-0 fw-semibold">Detail</h6></div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Asal Sampah <span class="text-danger">*</span></label>
                        <select name="jenis_sampah" id="jenis_sampah" class="form-select no-search @error('jenis_sampah') is-invalid @enderror" placeholder="Ketik atau pilih asal sampah..." required>
                            <option value="">-- Ketik atau pilih --</option>
                            @if(old('jenis_sampah', $ritase->jenis_sampah ?? ''))
                                <option value="{{ old('jenis_sampah', $ritase->jenis_sampah ?? '') }}" selected>{{ old('jenis_sampah', $ritase->jenis_sampah ?? '') }}</option>
                            @endif
                        </select>
                        @error('jenis_sampah') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div class="form-text">Pilih dari riwayat atau ketik baru. Otomatis tersimpan untuk klien ini.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Biaya Tipping (Rp)</label>
                        <input type="number" name="biaya_tipping" id="biaya_tipping" class="form-control" value="{{ old('biaya_tipping', $ritase->biaya_tipping ?? '') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            @foreach(['masuk'=>'Masuk','timbang'=>'Timbang','keluar'=>'Keluar','selesai'=>'Selesai'] as $val => $label)
                                <option value="{{ $val }}" {{ old('status', $ritase->status ?? 'selesai') == $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tiket (Manual)</label>
                        <input type="text" name="tiket" class="form-control" value="{{ old('tiket', $ritase->tiket ?? '') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Foto Timbangan Bruto (Maks 5MB)</label>
                        <div class="d-flex gap-2 mb-2">
                            <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('foto_tiket_bruto').click()">
                                <i class="cil-camera me-1"></i> Ambil Foto / Pilih File
                            </button>
                        </div>
                        <input type="file" name="foto_tiket_bruto" id="foto_tiket_bruto" class="form-control d-none @error('foto_tiket_bruto') is-invalid @enderror" accept="image/*" capture="environment" onchange="previewImageBruto(this)">
                        <div id="file-name-display-bruto" class="small text-muted mb-2"></div>
                        @error('foto_tiket_bruto') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        
                        <div id="image-preview-bruto" class="mt-2 text-center border p-2 rounded {{ (isset($ritase) && $ritase->foto_tiket_bruto) ? '' : 'd-none' }}">
                            @if(isset($ritase) && $ritase->foto_tiket_bruto)
                                <a href="{{ asset('storage/' . $ritase->foto_tiket_bruto) }}" target="_blank" id="preview-link-bruto">
                                    <img src="{{ asset('storage/' . $ritase->foto_tiket_bruto) }}" id="preview-img-bruto" class="img-fluid rounded" style="max-height: 200px;">
                                </a>
                            @else
                                <a href="#" target="_blank" id="preview-link-bruto">
                                    <img src="" id="preview-img-bruto" class="img-fluid rounded" style="max-height: 200px;">
                                </a>
                            @endif
                            <p class="small text-muted mt-1 mb-0">Preview foto timbangan bruto</p>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Foto Timbangan Tarra (Maks 5MB)</label>
                        <div class="d-flex gap-2 mb-2">
                            <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('foto_tiket_tarra').click()">
                                <i class="cil-camera me-1"></i> Ambil Foto / Pilih File
                            </button>
                        </div>
                        <input type="file" name="foto_tiket_tarra" id="foto_tiket_tarra" class="form-control d-none @error('foto_tiket_tarra') is-invalid @enderror" accept="image/*" capture="environment" onchange="previewImageTarra(this)">
                        <div id="file-name-display-tarra" class="small text-muted mb-2"></div>
                        @error('foto_tiket_tarra') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        
                        <div id="image-preview-tarra" class="mt-2 text-center border p-2 rounded {{ (isset($ritase) && $ritase->foto_tiket_tarra) ? '' : 'd-none' }}">
                            @if(isset($ritase) && $ritase->foto_tiket_tarra)
                                <a href="{{ asset('storage/' . $ritase->foto_tiket_tarra) }}" target="_blank" id="preview-link-tarra">
                                    <img src="{{ asset('storage/' . $ritase->foto_tiket_tarra) }}" id="preview-img-tarra" class="img-fluid rounded" style="max-height: 200px;">
                                </a>
                            @else
                                <a href="#" target="_blank" id="preview-link-tarra">
                                    <img src="" id="preview-img-tarra" class="img-fluid rounded" style="max-height: 200px;">
                                </a>
                            @endif
                            <p class="small text-muted mt-1 mb-0">Preview foto timbangan tarra</p>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Foto Tiket Umum</label>
                        <div class="d-flex gap-2 mb-2">
                            <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('foto_tiket').click()">
                                <i class="cil-camera me-1"></i> Ambil Foto / Pilih File
                            </button>
                        </div>
                        <input type="file" name="foto_tiket" id="foto_tiket" class="form-control d-none @error('foto_tiket') is-invalid @enderror" accept="image/*" capture="environment" onchange="previewImage(this)">
                        <div id="file-name-display" class="small text-muted mb-2"></div>
                        @error('foto_tiket') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        
                        <div id="image-preview" class="mt-2 text-center border p-2 rounded {{ (isset($ritase) && $ritase->foto_tiket) ? '' : 'd-none' }}">
                            @if(isset($ritase) && $ritase->foto_tiket)
                                <a href="{{ asset('storage/' . $ritase->foto_tiket) }}" target="_blank" id="preview-link">
                                    <img src="{{ asset('storage/' . $ritase->foto_tiket) }}" id="preview-img" class="img-fluid rounded" style="max-height: 200px;">
                                </a>
                            @else
                                <a href="#" target="_blank" id="preview-link">
                                    <img src="" id="preview-img" class="img-fluid rounded" style="max-height: 200px;">
                                </a>
                            @endif
                            <p class="small text-muted mt-1 mb-0">Preview foto tiket</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-body d-grid gap-2">
                    <button type="submit" class="btn btn-primary"><i class="cil-save me-1"></i> {{ isset($ritase) ? 'Perbarui' : 'Simpan' }}</button>
                    <a href="{{ route('admin.ritase.index') }}" class="btn btn-outline-secondary">Kembali</a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
function calcNetto() {
    const bruto = parseFloat(document.getElementById('berat_bruto').value) || 0;
    const tarra = parseFloat(document.getElementById('berat_tarra').value) || 0;
    const netto = bruto - tarra;
    document.getElementById('berat_netto').value = netto.toFixed(2);
    
    // Auto-calculate tipping fee: netto * 80
    const tippingInput = document.getElementById('biaya_tipping');
    // Only auto-fill if empty or is a new record
    @if(!isset($ritase))
        tippingInput.value = Math.round(netto * 80);
    @endif
}

function updateWaktuKeluar() {
    const waktuMasukInput = document.getElementById('waktu_masuk').value;
    if (waktuMasukInput) {
        const waktuMasuk = new Date(waktuMasukInput);
        // Add 15 minutes
        waktuMasuk.setMinutes(waktuMasuk.getMinutes() + 15);
        
        // Format to YYYY-MM-DDTHH:mm
        const year = waktuMasuk.getFullYear();
        const month = String(waktuMasuk.getMonth() + 1).padStart(2, '0');
        const day = String(waktuMasuk.getDate()).padStart(2, '0');
        const hours = String(waktuMasuk.getHours()).padStart(2, '0');
        const minutes = String(waktuMasuk.getMinutes()).padStart(2, '0');
        
        const formattedDate = `${year}-${month}-${day}T${hours}:${minutes}`;
        document.getElementById('waktu_keluar').value = formattedDate;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Beri jeda sejenak untuk memastikan plugin TomSelect dari layout telah dipasang penuh
    setTimeout(function() {
        const armadaSelect = document.querySelector('select[name="armada_id"]');
        const klienSelect = document.querySelector('select[name="klien_id"]');
        const jenisSampahSelect = document.getElementById('jenis_sampah');
        
        // ── Inisialisasi Tom Select untuk Asal Sampah (creatable) ──
        let tsJenisSampah = null;
        if (jenisSampahSelect && !jenisSampahSelect.tomselect) {
            tsJenisSampah = new TomSelect('#jenis_sampah', {
                create: true,
                createOnBlur: true,
                maxItems: 1,
                persist: false,
                placeholder: 'Ketik atau pilih asal sampah...',
                render: {
                    option_create: function(data, escape) {
                        return '<div class="create"><i class="cil-plus me-1"></i> Tambah: <strong>' + escape(data.input) + '</strong></div>';
                    },
                    no_results: function(data, escape) {
                        return '<div class="no-results">Tidak ditemukan — ketik untuk menambah baru</div>';
                    }
                }
            });
        } else if (jenisSampahSelect && jenisSampahSelect.tomselect) {
            tsJenisSampah = jenisSampahSelect.tomselect;
        }

        /**
         * Load asal sampah suggestions from server for a given klien_id.
         */
        function loadAsalSampah(klienId) {
            if (!tsJenisSampah || !klienId) return;
            
            const currentValue = tsJenisSampah.getValue();
            
            fetch('{{ route("admin.ritase.asal-sampah") }}?klien_id=' + klienId, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(items => {
                // Clear existing options except current value
                tsJenisSampah.clearOptions();
                tsJenisSampah.addOption({ value: '', text: '-- Ketik atau pilih --' });
                
                items.forEach(item => {
                    tsJenisSampah.addOption({ value: item, text: item });
                });
                
                // Restore value if it was set
                if (currentValue) {
                    // Make sure the current value exists as an option
                    tsJenisSampah.addOption({ value: currentValue, text: currentValue });
                    tsJenisSampah.setValue(currentValue, true);
                }
                
                tsJenisSampah.refreshOptions(false);
            })
            .catch(err => console.warn('Failed to load asal sampah:', err));
        }

        if (armadaSelect && klienSelect) {
            
            // Kumpulkan data semua opsi dari DOM asli
            const allArmadaData = [];
            Array.from(armadaSelect.querySelectorAll('option')).forEach(opt => {
                if (opt.value) {
                    allArmadaData.push({
                        value: opt.value,
                        text: opt.innerText,
                        klienId: opt.dataset.klienId,
                        beratKosong: opt.dataset.beratKosong
                    });
                }
            });

            // Apabila menggunakan TomSelect
            if (armadaSelect.tomselect && klienSelect.tomselect) {
                const tsArmada = armadaSelect.tomselect;
                const tsKlien = klienSelect.tomselect;

                tsArmada.on('change', function(value) {
                    const selected = allArmadaData.find(a => a.value == value);
                    if (selected) {
                        if (selected.beratKosong) {
                            const beratTarra = document.getElementById('berat_tarra');
                            beratTarra.value = selected.beratKosong;
                            calcNetto();
                        }
                        if (selected.klienId && tsKlien.getValue() != selected.klienId) {
                            tsKlien.setValue(selected.klienId, true);
                        }
                    }
                });

                // When klien changes, load asal sampah suggestions
                tsKlien.on('change', function(value) {
                    loadAsalSampah(value);
                });

                // Inisialisasi awal jika form adalah form Update
                if (tsKlien.getValue()) {
                    loadAsalSampah(tsKlien.getValue());
                }
                if (tsArmada.getValue()) {
                    tsArmada.trigger('change', tsArmada.getValue());
                }
                
            } else {
                // Fallback jika bukan TomSelect (VanillaJS)
                armadaSelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    if (selectedOption && selectedOption.dataset.beratKosong) {
                        const beratTarra = document.getElementById('berat_tarra');
                        beratTarra.value = selectedOption.dataset.beratKosong;
                        calcNetto();
                    }
                    if (selectedOption && selectedOption.value !== "") {
                        const klienId = selectedOption.dataset.klienId;
                        if (klienId) {
                            klienSelect.value = klienId;
                            loadAsalSampah(klienId);
                        }
                    }
                });

                klienSelect.addEventListener('change', function() {
                    loadAsalSampah(this.value);
                });
            }
        }
    }, 300); // 300ms tunda
});

function previewImage(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const reader = new FileReader();
        
        document.getElementById('file-name-display').textContent = 'File terpilih: ' + file.name;
        
        reader.onload = function(e) {
            document.getElementById('image-preview').classList.remove('d-none');
            document.getElementById('preview-img').src = e.target.result;
            document.getElementById('preview-link').href = e.target.result;
        }
        
        reader.readAsDataURL(file);
    }
}

function previewImageBruto(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const reader = new FileReader();
        
        document.getElementById('file-name-display-bruto').textContent = 'File terpilih: ' + file.name;
        
        reader.onload = function(e) {
            document.getElementById('image-preview-bruto').classList.remove('d-none');
            document.getElementById('preview-img-bruto').src = e.target.result;
            document.getElementById('preview-link-bruto').href = e.target.result;
        }
        
        reader.readAsDataURL(file);
    }
}

function previewImageTarra(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const reader = new FileReader();
        
        document.getElementById('file-name-display-tarra').textContent = 'File terpilih: ' + file.name;
        
        reader.onload = function(e) {
            document.getElementById('image-preview-tarra').classList.remove('d-none');
            document.getElementById('preview-img-tarra').src = e.target.result;
            document.getElementById('preview-link-tarra').href = e.target.result;
        }
        
        reader.readAsDataURL(file);
    }
}
</script>
@endpush
