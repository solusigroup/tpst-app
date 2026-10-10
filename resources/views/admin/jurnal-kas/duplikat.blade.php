@extends('layouts.admin')
@section('title', 'Pemeriksaan Double Jurnal Kas')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <div class="d-flex align-items-center gap-3">
            <h1 class="h3 fw-bold mb-0">Pemeriksaan Double Jurnal Kas</h1>
            @php $isNegatif = ($saldoKas ?? 0) < 0; @endphp
            <div class="badge {{ $isNegatif ? 'bg-danger bg-opacity-10 text-danger border border-danger' : 'bg-success bg-opacity-10 text-success border border-success' }} border-opacity-25 px-3 py-2 fs-6 rounded-pill shadow-sm">
                <i class="cil-wallet me-1"></i> Saldo Kas: Rp {{ number_format($saldoKas ?? 0, 0, ',', '.') }}
            </div>
        </div>
        <nav aria-label="breadcrumb" class="mt-1">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.jurnal-kas.index') }}">Jurnal Kas</a></li>
                <li class="breadcrumb-item active" aria-current="page">Pemeriksaan Duplikat</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.jurnal-kas.index') }}" class="btn btn-outline-secondary">
            <i class="cil-arrow-left me-1"></i> Kembali ke Jurnal Kas
        </a>
        <a href="{{ route('admin.jurnal-kas.duplikat') }}" class="btn btn-primary">
            <i class="cil-reload me-1"></i> Refresh Deteksi
        </a>
    </div>
</div>

{{-- KPI Summary Cards --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100 {{ ($duplicateStats['group_count'] ?? 0) > 0 ? 'border-start border-warning border-4' : 'border-start border-success border-4' }}">
            <div class="card-body p-3">
                <div class="text-body-secondary small text-uppercase fw-semibold mb-1">Kelompok Duplikat</div>
                <div class="fs-4 fw-bold {{ ($duplicateStats['group_count'] ?? 0) > 0 ? 'text-warning' : 'text-success' }}">
                    {{ number_format($duplicateStats['group_count'] ?? 0, 0, ',', '.') }} <span class="fs-6 fw-normal text-muted">kelompok</span>
                </div>
                <div class="small text-muted mt-1">Kombinasi data persis sama</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100 {{ ($duplicateStats['total_items_count'] ?? 0) > 0 ? 'border-start border-danger border-4' : 'border-start border-success border-4' }}">
            <div class="card-body p-3">
                <div class="text-body-secondary small text-uppercase fw-semibold mb-1">Total Transaksi Kembar</div>
                <div class="fs-4 fw-bold {{ ($duplicateStats['total_items_count'] ?? 0) > 0 ? 'text-danger' : 'text-success' }}">
                    {{ number_format($duplicateStats['total_items_count'] ?? 0, 0, ',', '.') }} <span class="fs-6 fw-normal text-muted">transaksi</span>
                </div>
                <div class="small text-muted mt-1">Total baris yang terindikasi</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100 border-start border-info border-4">
            <div class="card-body p-3">
                <div class="text-body-secondary small text-uppercase fw-semibold mb-1">Total Nilai Tercatat</div>
                <div class="fs-4 fw-bold text-info">
                    Rp {{ number_format($duplicateStats['total_nominal'] ?? 0, 0, ',', '.') }}
                </div>
                <div class="small text-muted mt-1">Akumulasi seluruh transaksi kembar</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100 {{ ($duplicateStats['potensi_redundansi_nominal'] ?? 0) > 0 ? 'border-start border-danger border-4' : 'border-start border-secondary border-4' }}">
            <div class="card-body p-3">
                <div class="text-body-secondary small text-uppercase fw-semibold mb-1">Potensi Nilai Redundan</div>
                <div class="fs-4 fw-bold {{ ($duplicateStats['potensi_redundansi_nominal'] ?? 0) > 0 ? 'text-danger' : 'text-secondary' }}">
                    Rp {{ number_format($duplicateStats['potensi_redundansi_nominal'] ?? 0, 0, ',', '.') }}
                </div>
                <div class="small text-muted mt-1">Selisih nominal berpotensi ganda</div>
            </div>
        </div>
    </div>
</div>

{{-- Filter Card --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('admin.jurnal-kas.duplikat') }}" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">Dari Tanggal</label>
                <input type="date" name="dari" class="form-control form-control-sm" value="{{ request('dari') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">Sampai Tanggal</label>
                <input type="date" name="sampai" class="form-control form-control-sm" value="{{ request('sampai') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">Jenis</label>
                <select name="jenis" class="form-select form-select-sm">
                    <option value="">Semua Jenis</option>
                    <option value="masuk" {{ request('jenis') == 'masuk' ? 'selected' : '' }}>Kas Masuk</option>
                    <option value="keluar" {{ request('jenis') == 'keluar' ? 'selected' : '' }}>Kas Keluar</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">Cari Deskripsi</label>
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Ketik kata kunci deskripsi..." value="{{ request('search') }}">
            </div>
            <div class="col-md-1 d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-outline-primary w-100" title="Terapkan Filter">
                    <i class="cil-search"></i>
                </button>
                @if(request()->hasAny(['dari', 'sampai', 'jenis', 'search']))
                    <a href="{{ route('admin.jurnal-kas.duplikat') }}" class="btn btn-sm btn-outline-secondary" title="Reset Filter">
                        <i class="cil-x"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- Notification Banner --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-success shadow-sm" role="alert">
        <i class="cil-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-coreui-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-danger shadow-sm" role="alert">
        <i class="cil-warning me-1"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-coreui-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- Petunjuk --}}
<div class="alert alert-info border-info bg-info bg-opacity-10 d-flex align-items-start gap-2 mb-4 rounded-3 shadow-sm">
    <i class="cil-info fs-5 text-info mt-1 flex-shrink-0"></i>
    <div class="small">
        <strong>Kriteria Indikasi Double Jurnal Kas:</strong> Sistem mengidentifikasi transaksi ganda berdasarkan kesamaan <strong>Tanggal Transaksi</strong>, <strong>Nilai Nominal</strong>, dan <strong>Deskripsi/Keterangan</strong>.
        <br>
        Jika salah satu transaksi merupakan entri ganda yang tidak disengaja (misal: dobel klik submit atau duplikasi import), Anda dapat <strong>menghapus</strong> entri redundan tersebut. Penghapusan akan otomatis membatalkan jurnal akuntansi terkait dan memperbarui saldo kas.
    </div>
</div>

{{-- Duplicate Groups Listing --}}
@if($grouped->isEmpty())
    <div class="card border-0 shadow-sm text-center py-5">
        <div class="card-body py-4">
            <div class="mb-3">
                <i class="cil-check-circle text-success" style="font-size: 4rem;"></i>
            </div>
            <h4 class="fw-bold text-dark">Tidak Ditemukan Double Jurnal Kas</h4>
            <p class="text-muted mx-auto mb-4" style="max-width: 500px;">
                Semua data transaksi jurnal kas Anda rapi dan valid. Tidak ada transaksi yang memiliki kesamaan Tanggal, Nominal, dan Deskripsi secara bersamaan.
            </p>
            <a href="{{ route('admin.jurnal-kas.index') }}" class="btn btn-outline-primary">
                <i class="cil-arrow-left me-1"></i> Kembali ke Daftar Jurnal Kas
            </a>
        </div>
    </div>
@else
    <div class="d-flex flex-column gap-4">
        @php $groupIndex = 1; @endphp
        @foreach($grouped as $groupKey => $items)
            @php
                $firstItem = $items->first();
                $itemCount = $items->count();
                $nominal = $firstItem->nominal;
                $redundantNominal = ($itemCount - 1) * $nominal;
                $isPenerimaan = $firstItem->tipe === 'Penerimaan';
            @endphp
            <div class="card border-0 shadow-sm overflow-hidden">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="badge bg-dark rounded-pill px-3 py-2 fs-7">
                            Kelompok #{{ $groupIndex++ }}
                        </span>
                        <span class="badge bg-{{ $isPenerimaan ? 'success' : 'danger' }} px-3 py-2">
                            {{ $isPenerimaan ? 'Kas Masuk' : 'Kas Keluar' }}
                        </span>
                        <span class="fw-bold fs-6 text-dark">
                            <i class="cil-calendar me-1 text-muted"></i> {{ \Carbon\Carbon::parse($firstItem->tanggal)->format('d/m/Y') }}
                        </span>
                        <span class="text-muted">•</span>
                        <span class="fw-bold fs-6 {{ $isPenerimaan ? 'text-success' : 'text-danger' }}">
                            Rp {{ number_format($nominal, 0, ',', '.') }}
                        </span>
                        <span class="text-muted">•</span>
                        <span class="fst-italic text-dark bg-light px-2 py-1 rounded border">
                            "{{ $firstItem->deskripsi ?: '(Tanpa Deskripsi)' }}"
                        </span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1">
                            <i class="cil-copy me-1"></i> {{ $itemCount }} Transaksi Kembar
                        </span>
                        <span class="badge bg-warning bg-opacity-25 text-dark border border-warning border-opacity-50 px-2 py-1" title="Potensi nilai yang tercatat lebih dari satu kali">
                            Redundan: Rp {{ number_format($redundantNominal, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;" class="text-center">#</th>
                                    <th>No. Ref / ID</th>
                                    <th>Akun Kas</th>
                                    <th>Akun Lawan</th>
                                    <th>Mitra</th>
                                    <th>Waktu Input (Created)</th>
                                    <th>Status</th>
                                    <th>Bukti</th>
                                    <th class="text-end" style="min-width: 140px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $rowNumber = 1; @endphp
                                @foreach($items as $item)
                                    @php
                                        $header = $item->jurnalHeader;
                                        $refNo = $header ? $header->nomor_referensi : "ID-{$item->id}";
                                    @endphp
                                    <tr>
                                        <td class="text-center text-muted fw-semibold">{{ $rowNumber++ }}</td>
                                        <td>
                                            <span class="fw-bold text-dark">{{ $refNo }}</span>
                                            <div class="small text-muted">Jurnal Kas #{{ $item->id }}</div>
                                        </td>
                                        <td>
                                            <span class="small fw-semibold text-secondary">
                                                {{ $item->coaKas->kode_akun ?? '1101' }} - {{ $item->coaKas->nama_akun ?? 'Kas' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="fw-semibold text-dark">
                                                {{ $item->coaLawan ? ($item->coaLawan->kode_akun . ' - ' . $item->coaLawan->nama_akun) : '-' }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($item->contactable)
                                                <span class="badge bg-light text-dark border">
                                                    {{ class_basename($item->contactable_type) }}: {{ $item->contactable->nama_klien ?? $item->contactable->nama_vendor ?? '-' }}
                                                </span>
                                            @else
                                                <span class="text-muted small">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div>{{ $item->created_at ? $item->created_at->format('d/m/Y H:i:s') : '-' }}</div>
                                            <div class="small text-muted">{{ $item->created_at ? $item->created_at->diffForHumans() : '-' }}</div>
                                        </td>
                                        <td>
                                            @if($item->status == 'posted')
                                                <span class="badge bg-success">Posted</span>
                                            @else
                                                <span class="badge bg-warning text-dark">Unposted</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($item->bukti_transaksi)
                                                <a href="{{ Storage::url($item->bukti_transaksi) }}" target="_blank" class="badge bg-info text-decoration-none" title="Lihat Bukti">
                                                    <i class="cil-paperclip me-1"></i> Lihat
                                                </a>
                                            @else
                                                <span class="text-muted small">-</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('admin.jurnal-kas.edit', $item->id) }}" class="btn btn-outline-primary" title="Edit Transaksi Ini">
                                                    <i class="cil-pencil me-1"></i> Edit
                                                </a>
                                                <button type="button" class="btn btn-outline-danger" 
                                                        onclick="confirmDeleteDuplicate({{ $item->id }}, '{{ addslashes($refNo) }}', 'Rp {{ number_format($item->nominal, 0, ',', '.') }}', '{{ addslashes($item->deskripsi ?? '') }}')"
                                                        title="Hapus Entri Duplikat Ini">
                                                    <i class="cil-trash me-1"></i> Hapus
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif

{{-- Hidden Form for Safe Deletion --}}
<form id="deleteDuplicateForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
    <input type="hidden" name="redirect_to" value="duplikat">
</form>

{{-- Modal Konfirmasi Hapus Duplikat --}}
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-danger d-flex align-items-center gap-2" id="deleteConfirmModalLabel">
                    <i class="cil-warning fs-4"></i> Konfirmasi Hapus Transaksi Duplikat
                </h5>
                <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-3">
                <p class="mb-3">
                    Apakah Anda yakin ingin menghapus transaksi jurnal kas ini?
                </p>
                <div class="bg-light p-3 rounded border mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted small">No. Referensi:</span>
                        <strong class="text-dark" id="delModalRef">-</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted small">Nominal:</span>
                        <strong class="text-success" id="delModalNominal">-</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted small">Deskripsi:</span>
                        <span class="text-dark text-end fw-semibold" id="delModalDeskripsi" style="max-width: 250px;">-</span>
                    </div>
                </div>
                <div class="alert alert-warning small mb-0 py-2">
                    <i class="cil-info me-1"></i> Tindakan ini akan menghapus entri Jurnal Kas beserta Jurnal Akuntansi terkait secara otomatis.
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light border" data-coreui-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger text-white fw-semibold" id="btnConfirmDeleteAction">
                    <i class="cil-trash me-1"></i> Ya, Hapus Transaksi
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentDeleteId = null;
let deleteModalInstance = null;

document.addEventListener('DOMContentLoaded', function() {
    const modalEl = document.getElementById('deleteConfirmModal');
    if (modalEl) {
        if (typeof coreui !== 'undefined' && coreui.Modal) {
            deleteModalInstance = new coreui.Modal(modalEl);
        } else if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            deleteModalInstance = new bootstrap.Modal(modalEl);
        }
    }

    document.getElementById('btnConfirmDeleteAction').addEventListener('click', function() {
        if (!currentDeleteId) return;
        const form = document.getElementById('deleteDuplicateForm');
        form.action = "{{ url('admin/jurnal-kas') }}/" + currentDeleteId;
        form.submit();
    });
});

function confirmDeleteDuplicate(id, refNo, nominal, deskripsi) {
    currentDeleteId = id;
    document.getElementById('delModalRef').textContent = refNo;
    document.getElementById('delModalNominal').textContent = nominal;
    document.getElementById('delModalDeskripsi').textContent = deskripsi || '(Tanpa Deskripsi)';

    if (deleteModalInstance) {
        deleteModalInstance.show();
    } else {
        if (confirm('Yakin ingin menghapus transaksi jurnal kas ' + refNo + ' (' + nominal + ')?')) {
            const form = document.getElementById('deleteDuplicateForm');
            form.action = "{{ url('admin/jurnal-kas') }}/" + id;
            form.submit();
        }
    }
}
</script>
@endpush
@endsection
