@extends('layouts.admin')
@section('title', isset($jurnalKas) ? 'Edit Jurnal Kas' : 'Tambah Jurnal Kas')

@section('content')
<div class="page-header">
    <div>
        <h1>{{ isset($jurnalKas) ? 'Edit' : 'Tambah' }} Jurnal Kas</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li><li class="breadcrumb-item"><a href="{{ route('admin.jurnal-kas.index') }}">Jurnal Kas</a></li><li class="breadcrumb-item active">{{ isset($jurnalKas) ? 'Edit' : 'Tambah' }}</li></ol></nav>
    </div>
</div>

<div class="row" id="formContainer">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ isset($jurnalKas) ? route('admin.jurnal-kas.update', $jurnalKas) : route('admin.jurnal-kas.store') }}" enctype="multipart/form-data">
                    @csrf @if(isset($jurnalKas)) @method('PUT') @endif
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal" class="form-control @error('tanggal') is-invalid @enderror" value="{{ old('tanggal', isset($jurnalKas) ? \Carbon\Carbon::parse($jurnalKas->tanggal)->format('Y-m-d') : '') }}" required>
                            @error('tanggal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jenis <span class="text-danger">*</span></label>
                            <select name="jenis" id="jenisSelect" class="form-select @error('jenis') is-invalid @enderror" required>
                                <option value="">-- Pilih --</option>
                                <option value="masuk" {{ old('jenis', ($jurnalKas->tipe ?? '') == 'Penerimaan' ? 'masuk' : '') == 'masuk' ? 'selected' : '' }}>Kas Masuk</option>
                                <option value="keluar" {{ old('jenis', ($jurnalKas->tipe ?? '') == 'Pengeluaran' ? 'keluar' : '') == 'keluar' ? 'selected' : '' }}>Kas Keluar</option>
                            </select>
                            @error('jenis') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Akun (COA) <span class="text-danger">*</span></label>
                            <select name="coa_id" class="form-select @error('coa_id') is-invalid @enderror" required>
                                <option value="">-- Pilih --</option>
                                @foreach($coas as $c)<option value="{{ $c->id }}" {{ old('coa_id', $jurnalKas->coa_id ?? ($jurnalKas->coa_lawan_id ?? '')) == $c->id ? 'selected' : '' }}>{{ $c->kode_akun }} - {{ $c->nama_akun }}</option>@endforeach
                            </select>
                            @error('coa_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Mitra (Opsional)</label>
                            <select name="contactable_type_id" class="form-select @error('contactable_type_id') is-invalid @enderror">
                                <option value="">-- Tanpa Mitra --</option>
                                <optgroup label="Klien">
                                    @foreach($kliens as $k)
                                        <option value="App\Models\Klien:{{ $k->id }}" {{ (isset($jurnalKas) && $jurnalKas->contactable_type === 'App\Models\Klien' && $jurnalKas->contactable_id == $k->id) ? 'selected' : '' }}>{{ $k->nama_klien }}</option>
                                    @endforeach
                                </optgroup>
                                <optgroup label="Vendor">
                                    @foreach($vendors as $v)
                                        <option value="App\Models\Vendor:{{ $v->id }}" {{ (isset($jurnalKas) && $jurnalKas->contactable_type === 'App\Models\Vendor' && $jurnalKas->contactable_id == $v->id) ? 'selected' : '' }}>{{ $v->nama_vendor }}</option>
                                    @endforeach
                                </optgroup>
                            </select>
                            @error('contactable_type_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jumlah (Rp) <span class="text-danger">*</span></label>
                            <input type="number" name="jumlah" class="form-control @error('jumlah') is-invalid @enderror" value="{{ old('jumlah', isset($jurnalKas) ? $jurnalKas->nominal : '') }}" required>
                            @error('jumlah') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="deskripsi" class="form-control" rows="3">{{ old('deskripsi', $jurnalKas->deskripsi ?? '') }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Bukti Transaksi <span class="text-danger">*</span></label>
                            <input type="file" name="bukti_transaksi" class="form-control @error('bukti_transaksi') is-invalid @enderror" accept=".jpg,.jpeg,.png,.pdf" {{ isset($jurnalKas) && $jurnalKas->bukti_transaksi ? '' : 'required' }}>
                            <div class="form-text">Format: JPG, PNG, PDF. Maks: 2MB. Bisa ambil dari Kamera Pustaka/Galeri.</div>
                            @error('bukti_transaksi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            
                            @if(isset($jurnalKas) && $jurnalKas->bukti_transaksi)
                                <div class="mt-2">
                                    <a href="{{ Storage::url($jurnalKas->bukti_transaksi) }}" target="_blank" class="btn btn-sm btn-info text-white">
                                        <i class="cil-external-link me-1"></i> Lihat Bukti Saat Ini
                                    </a>
                                    <div class="form-text text-warning mt-1">Mengunggah file baru akan menimpa file yang lama.</div>
                                </div>
                            @endif
                        </div>
                        <div class="col-12 d-flex gap-2">
                            <button type="submit" class="btn btn-primary"><i class="cil-save me-1"></i> {{ isset($jurnalKas) ? 'Perbarui' : 'Simpan' }}</button>
                            <a href="{{ route('admin.jurnal-kas.index') }}" class="btn btn-outline-secondary">Kembali</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@if(!isset($jurnalKas))
<style>
.btn-select-type {
    transition: all 0.2s ease;
}
.btn-select-type:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
}
.btn-kas-masuk {
    background-color: #1a73e8;
    color: white;
}
.btn-kas-masuk:hover {
    background-color: #155dbb;
    color: white;
}
.btn-kas-keluar {
    background-color: #ea4335;
    color: white;
}
.btn-kas-keluar:hover {
    background-color: #c5221f;
    color: white;
}
</style>

<div class="modal fade" id="typeSelectionModal" data-coreui-backdrop="static" data-coreui-keyboard="false" tabindex="-1" aria-labelledby="typeSelectionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 1rem;">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" id="btnCancelModalTop" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center pt-0 pb-4 px-4">
                <h4 class="mb-4 fw-bold" style="color: #2c3e50;">Pilih Tipe Transaksi Kas</h4>
                <div class="row g-3 justify-content-center">
                    <div class="col-6">
                        <button type="button" class="btn btn-select-type btn-kas-masuk w-100 p-4 h-100 d-flex flex-column align-items-center justify-content-center border-0 shadow-sm" data-type="masuk" style="border-radius: 1rem;">
                            <i class="cil-data-transfer-down mb-3" style="font-size: 2.5rem;"></i>
                            <h5 class="fw-bold mb-1">Kas Masuk</h5>
                            <small style="opacity: 0.9; font-size: 0.8rem;">Penerimaan Dana</small>
                        </button>
                    </div>
                    <div class="col-6">
                        <button type="button" class="btn btn-select-type btn-kas-keluar w-100 p-4 h-100 d-flex flex-column align-items-center justify-content-center border-0 shadow-sm" data-type="keluar" style="border-radius: 1rem;">
                            <i class="cil-data-transfer-up mb-3" style="font-size: 2.5rem;"></i>
                            <h5 class="fw-bold mb-1">Kas Keluar</h5>
                            <small style="opacity: 0.9; font-size: 0.8rem;">Pengeluaran Dana</small>
                        </button>
                    </div>
                </div>
                <div class="mt-4 pt-2">
                    <button type="button" class="btn btn-light rounded-pill px-4 py-2 border shadow-sm" id="btnCancelModalBottom" style="font-weight: 500;">Batal</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const isEdit = {{ isset($jurnalKas) ? 'true' : 'false' }};
    const hasOldJenis = '{{ old('jenis') }}' !== '';
    const oldJenisVal = '{{ old('jenis') }}';
    const formContainer = document.getElementById('formContainer');
    const modalEl = document.getElementById('typeSelectionModal');
    const typeSelect = document.getElementById('jenisSelect');
    
    let bsModal;
    if (modalEl) {
        if (typeof coreui !== 'undefined' && coreui.Modal) {
            bsModal = new coreui.Modal(modalEl);
        } else if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            bsModal = new bootstrap.Modal(modalEl);
        }
    }

    // Function to strictly lock the jenis select (supports Tom Select)
    function lockJenisSelect(type) {
        if (!type) return;
        
        // Use TomSelect API if available, else fallback to native
        if (typeSelect.tomselect) {
            typeSelect.tomselect.setValue(type);
            typeSelect.tomselect.disable();
        } else {
            typeSelect.value = type;
            typeSelect.dispatchEvent(new Event('change'));
            typeSelect.disabled = true;
            typeSelect.style.backgroundColor = '#e9ecef';
            typeSelect.style.cursor = 'not-allowed';
        }

        // Add hidden input so the form still submits the required 'jenis' value
        let hiddenInput = document.getElementById('hiddenJenisInput');
        if (!hiddenInput) {
            hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'jenis';
            hiddenInput.id = 'hiddenJenisInput';
            typeSelect.parentNode.appendChild(hiddenInput);
        }
        hiddenInput.value = type;
    }

    if (!isEdit && !hasOldJenis) {
        // Hide form initially
        if (formContainer) formContainer.style.display = 'none';
        if (bsModal) bsModal.show();
    } else {
        // If there's an old value from validation error, or we are editing
        const valToLock = hasOldJenis ? oldJenisVal : typeSelect.value;
        if (valToLock !== '') {
            // Need a slight delay to let TomSelect initialize if it hasn't already
            setTimeout(() => lockJenisSelect(valToLock), 100);
        }
    }

    // Handle type selection from modal
    document.querySelectorAll('.btn-select-type').forEach(btn => {
        btn.addEventListener('click', function(e) {
            const type = this.getAttribute('data-type') || this.dataset.type;
            
            lockJenisSelect(type);
            
            if (bsModal) bsModal.hide();
            
            // Show the form
            if (formContainer) {
                formContainer.style.opacity = '0';
                formContainer.style.display = 'flex';
                setTimeout(() => {
                    formContainer.style.transition = 'opacity 0.3s ease-in';
                    formContainer.style.opacity = '1';
                }, 50);
            }
        });
    });

    // Handle cancel buttons
    const cancelHandler = function() {
        window.location.href = "{{ route('admin.jurnal-kas.index') }}";
    };
    
    const cancelTop = document.getElementById('btnCancelModalTop');
    const cancelBottom = document.getElementById('btnCancelModalBottom');
    
    if (cancelTop) cancelTop.addEventListener('click', cancelHandler);
    if (cancelBottom) cancelBottom.addEventListener('click', cancelHandler);
});
</script>
@else
<script>
document.addEventListener('DOMContentLoaded', function() {
    // In edit mode, strictly lock the type select as well to prevent changing type
    const typeSelect = document.getElementById('jenisSelect');
    
    function lockEditSelect() {
        if (typeSelect && typeSelect.value !== '') {
            if (typeSelect.tomselect) {
                typeSelect.tomselect.disable();
            } else {
                typeSelect.disabled = true;
                typeSelect.style.backgroundColor = '#e9ecef';
                typeSelect.style.cursor = 'not-allowed';
            }
            
            let hiddenInput = document.getElementById('hiddenJenisInput');
            if (!hiddenInput) {
                hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'jenis';
                hiddenInput.id = 'hiddenJenisInput';
                typeSelect.parentNode.appendChild(hiddenInput);
            }
            hiddenInput.value = typeSelect.value;
        }
    }
    
    // Slight delay to ensure TomSelect is ready
    setTimeout(lockEditSelect, 100);
});
</script>
@endif

@endsection
