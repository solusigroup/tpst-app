@extends('layouts.admin')
@section('title', 'Pengangkutan Residu')

@section('content')
<div class="page-header">
    <div>
        <h1>Pengangkutan Residu</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Pengangkutan Residu</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <form id="bulk-pembayaran-form" action="{{ route('admin.pengangkutan-residu.bulk-pembayaran') }}" method="POST" class="d-none">
            @csrf
            <input type="hidden" name="residu_ids" id="bulk-residu-ids">
        </form>
        <button type="button" id="btn-bulk-pembayaran" class="btn btn-success" style="display:none;" onclick="submitBulkPembayaran()">
            <i class="cil-check-circle me-1"></i> Tandai Sudah Bayar (<span id="bulk-count">0</span>)
        </button>
        <a href="{{ route('admin.pengangkutan-residu.create') }}" class="btn btn-primary">
            <i class="cil-plus me-1"></i> Catat Residu
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-auto">
                <input type="text" name="search" class="form-control" placeholder="No. Tiket / Plat..." value="{{ request('search') }}">
            </div>
            <div class="col-auto">
                <select name="status_pembayaran" class="form-select" title="Status Pembayaran">
                    <option value="">-- Semua Pembayaran --</option>
                    <option value="Sudah" {{ request('status_pembayaran') == 'Sudah' ? 'selected' : '' }}>Sudah</option>
                    <option value="Belum" {{ request('status_pembayaran') == 'Belum' ? 'selected' : '' }}>Belum</option>
                </select>
            </div>
            <div class="col-auto">
                <button class="btn btn-outline-primary" type="submit">
                    <i class="cil-search me-1"></i> Cari
                </button>
            </div>
            @if(request()->hasAny(['search', 'status_pembayaran']))
                <div class="col-auto">
                    <a href="{{ route('admin.pengangkutan-residu.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            @endif
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 40px;"><input type="checkbox" id="checkAll" class="form-check-input"></th>
                        <th>No. Tiket</th>
                        <th>Tanggal</th>
                        <th>Armada</th>
                        <th>Netto (Kg)</th>
                        <th>Biaya</th>
                        <th>Pembayaran</th>
                        <th>Tujuan</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($entries as $item)
                    <tr>
                        <td>
                            @if($item->status_pembayaran !== 'Sudah')
                                <input type="checkbox" class="form-check-input residu-checkbox" value="{{ $item->id }}">
                            @endif
                        </td>
                        <td><strong>{{ $item->nomor_tiket }}</strong></td>
                        <td>{{ $item->tanggal->format('d/m/Y') }}</td>
                        <td>
                            <div>{{ $item->armada->plat_nomor }}</div>
                            <small class="text-body-secondary">{{ $item->armada->nama_armada }}</small>
                        </td>
                        <td class="fw-bold">{{ number_format($item->berat_netto, 0, ',', '.') }}</td>
                        <td class="text-danger fw-semibold">Rp {{ number_format($item->biaya_retribusi, 0, ',', '.') }}</td>
                        <td>
                            @if($item->status_pembayaran === 'Sudah')
                                <span class="badge bg-success">Sudah</span>
                            @else
                                <span class="badge bg-danger">Belum</span>
                            @endif
                        </td>
                        <td>{{ $item->tujuan }}</td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.pengangkutan-residu.show', $item) }}" class="btn btn-outline-info" title="Detail">
                                    <i class="cil-find-in-page"></i>
                                </a>
                                <a href="{{ route('admin.pengangkutan-residu.edit', $item) }}" class="btn btn-outline-warning" title="Edit">
                                    <i class="cil-pencil"></i>
                                </a>
                                <form action="{{ route('admin.pengangkutan-residu.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Hapus">
                                        <i class="cil-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4 text-body-secondary">Belum ada data pengangkutan residu.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($entries->hasPages())
    <div class="card-footer bg-white">
        {{ $entries->links() }}
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const checkAll = document.getElementById('checkAll');
    const checkboxes = document.querySelectorAll('.residu-checkbox');
    const btnBulkPembayaran = document.getElementById('btn-bulk-pembayaran');
    const bulkCount = document.getElementById('bulk-count');
    const bulkResiduIds = document.getElementById('bulk-residu-ids');
    const bulkForm = document.getElementById('bulk-pembayaran-form');

    function updateBulkButton() {
        const checked = Array.from(checkboxes).filter(cb => cb.checked);
        bulkCount.innerText = checked.length;
        if (checked.length > 0) {
            btnBulkPembayaran.style.display = 'inline-block';
        } else {
            btnBulkPembayaran.style.display = 'none';
        }
    }

    if (checkAll) {
        checkAll.addEventListener('change', function () {
            checkboxes.forEach(cb => cb.checked = checkAll.checked);
            updateBulkButton();
        });
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', function () {
            const allChecked = Array.from(checkboxes).every(c => c.checked);
            const someChecked = Array.from(checkboxes).some(c => c.checked);
            checkAll.checked = allChecked;
            checkAll.indeterminate = someChecked && !allChecked;
            updateBulkButton();
        });
    });

    window.submitBulkPembayaran = function() {
        const checked = Array.from(checkboxes).filter(cb => cb.checked).map(cb => cb.value);
        if (checked.length === 0) return;

        if (confirm(`Yakin ingin menandai ${checked.length} data sebagai sudah dibayar?`)) {
            bulkResiduIds.value = checked.join(',');
            bulkForm.submit();
        }
    }
});
</script>
@endpush
