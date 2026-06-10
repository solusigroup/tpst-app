@extends('layouts.admin')
@section('title', isset($coa) ? 'Edit COA' : 'Tambah COA')

@section('content')
<div class="page-header">
    <div>
        <h1>{{ isset($coa) ? 'Edit' : 'Tambah' }} Chart of Account</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li><li class="breadcrumb-item"><a href="{{ route('admin.coa.index') }}">COA</a></li><li class="breadcrumb-item active">{{ isset($coa) ? 'Edit' : 'Tambah' }}</li></ol></nav>
    </div>
</div>
<div class="row"><div class="col-lg-8"><div class="card"><div class="card-body">
    <form method="POST" action="{{ isset($coa) ? route('admin.coa.update', $coa) : route('admin.coa.store') }}">
        @csrf @if(isset($coa)) @method('PUT') @endif
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Kode Akun <span class="text-danger">*</span></label>
                <input type="text" name="kode_akun" class="form-control @error('kode_akun') is-invalid @enderror" value="{{ old('kode_akun', $coa->kode_akun ?? '') }}" required>
                @error('kode_akun') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Nama Akun <span class="text-danger">*</span></label>
                <input type="text" name="nama_akun" class="form-control @error('nama_akun') is-invalid @enderror" value="{{ old('nama_akun', $coa->nama_akun ?? '') }}" required>
                @error('nama_akun') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Tipe <span class="text-danger">*</span></label>
                <select name="tipe" id="tipe" class="form-select no-search @error('tipe') is-invalid @enderror" required onchange="updateKlasifikasi()">
                    <option value="">-- Pilih --</option>
                    @foreach(['Asset','Liability','Equity','Revenue','Expense'] as $t)
                        <option value="{{ $t }}" {{ old('tipe', $coa->tipe ?? '') == $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
                @error('tipe') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Klasifikasi <span class="text-danger">*</span></label>
                <select name="klasifikasi" id="klasifikasi" class="form-select no-search @error('klasifikasi') is-invalid @enderror" required>
                    <option value="">-- Pilih Tipe dulu --</option>
                </select>
                @error('klasifikasi') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6 d-none" id="kategori_buku_pembantu_container">
                <label class="form-label">Kategori Buku Pembantu</label>
                <select name="kategori_buku_pembantu" id="kategori_buku_pembantu" class="form-select no-search @error('kategori_buku_pembantu') is-invalid @enderror">
                    <option value="">-- Bukan Akun Pembantu Khusus --</option>
                    <option value="piutang_dlh" {{ old('kategori_buku_pembantu', $coa->kategori_buku_pembantu ?? '') == 'piutang_dlh' ? 'selected' : '' }}>Piutang Dinas Lingkungan Hidup (DLH)</option>
                    <option value="piutang_swasta" {{ old('kategori_buku_pembantu', $coa->kategori_buku_pembantu ?? '') == 'piutang_swasta' ? 'selected' : '' }}>Piutang Swasta / Komersial</option>
                    <option value="piutang_offtaker" {{ old('kategori_buku_pembantu', $coa->kategori_buku_pembantu ?? '') == 'piutang_offtaker' ? 'selected' : '' }}>Piutang Offtaker</option>
                    <option value="utang_usaha" {{ old('kategori_buku_pembantu', $coa->kategori_buku_pembantu ?? '') == 'utang_usaha' ? 'selected' : '' }}>Utang Usaha (AP)</option>
                </select>
                @error('kategori_buku_pembantu') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-12 d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="cil-save me-1"></i> {{ isset($coa) ? 'Perbarui' : 'Simpan' }}</button>
                <a href="{{ route('admin.coa.index') }}" class="btn btn-outline-secondary">Kembali</a>
            </div>
        </div>
    </form>
</div></div></div></div>
@endsection

@push('scripts')
<script>
const klasifikasiMap = {
    'Asset': {'Aset Lancar':'Aset Lancar','Aset Tidak Lancar':'Aset Tidak Lancar'},
    'Liability': {'Liabilitas Jangka Pendek':'Liabilitas Jangka Pendek','Liabilitas Jangka Panjang':'Liabilitas Jangka Panjang'},
    'Equity': {'Ekuitas':'Ekuitas'},
    'Revenue': {'Pendapatan':'Pendapatan'},
    'Expense': {'Beban':'Beban'},
};
function updateKlasifikasi() {
    const tipe = document.getElementById('tipe').value;
    const sel = document.getElementById('klasifikasi');
    sel.innerHTML = '<option value="">-- Pilih --</option>';
    if (klasifikasiMap[tipe]) {
        Object.entries(klasifikasiMap[tipe]).forEach(([k,v]) => {
            const opt = document.createElement('option');
            opt.value = k; opt.textContent = v;
            sel.appendChild(opt);
        });
    }
    // Restore old value
    const oldVal = '{{ old("klasifikasi", $coa->klasifikasi ?? "") }}';
    if (oldVal) sel.value = oldVal;

    // Show/hide kategori_buku_pembantu container
    const container = document.getElementById('kategori_buku_pembantu_container');
    const select = document.getElementById('kategori_buku_pembantu');
    if (tipe === 'Asset' || tipe === 'Liability') {
        container.classList.remove('d-none');
    } else {
        container.classList.add('d-none');
        select.value = '';
    }
}
document.addEventListener('DOMContentLoaded', updateKlasifikasi);
</script>
@endpush
