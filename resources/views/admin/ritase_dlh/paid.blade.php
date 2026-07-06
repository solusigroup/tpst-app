@extends('layouts.admin')
@section('title', 'Ritase DLH - Dibayar')

@section('content')
<div class="page-header">
    <div>
        <h1 class="d-flex align-items-center gap-2">
            <i class="cil-dollar text-info"></i>
            Ritase DLH — Dibayar (Paid)
        </h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Ritase DLH (Dibayar)</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.ritase-dlh.export-excel', array_merge(request()->all(), ['type' => 'paid'])) }}" class="btn btn-success" target="_blank">
            <i class="cil-cloud-download me-1"></i> Ekspor Excel
        </a>
        <a href="{{ route('admin.ritase-dlh.export-pdf', array_merge(request()->all(), ['type' => 'paid'])) }}" class="btn btn-danger" target="_blank">
            <i class="cil-print me-1"></i> Cetak PDF
        </a>
    </div>
</div>

{{-- Filter --}}
<div class="card mb-3">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-auto">
                <label class="form-label mb-0 small text-body-secondary">Dari</label>
                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
            </div>
            <div class="col-auto">
                <label class="form-label mb-0 small text-body-secondary">Sampai</label>
                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
            </div>
            <div class="col-auto">
                <label class="form-label mb-0 small text-body-secondary">Cari Tiket / Klien</label>
                <input type="text" name="search" class="form-control" placeholder="Nomor tiket / nama klien..." value="{{ request('search') }}">
            </div>
            <div class="col-auto">
                <label class="form-label mb-0 small text-body-secondary">Armada</label>
                <select name="armada_id" class="form-select">
                    <option value="">-- Semua Armada --</option>
                    @foreach($armadas as $arm)
                        <option value="{{ $arm->id }}" {{ request('armada_id') == $arm->id ? 'selected' : '' }}>{{ $arm->plat_nomor }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <button class="btn btn-primary" type="submit"><i class="cil-search me-1"></i> Filter</button>
            </div>
            @if(request()->hasAny(['search', 'start_date', 'end_date', 'armada_id']))
                <div class="col-auto">
                    <a href="{{ route('admin.ritase-dlh.paid') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            @endif
        </form>
    </div>
</div>

{{-- Summary Cards --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-body-secondary small mb-1">Total Ritase Dibayar</div>
                        <div class="fs-3 fw-bold text-dark">{{ number_format($totalCount, 0, ',', '.') }}</div>
                    </div>
                    <div class="p-2 bg-info bg-opacity-10 rounded-3">
                        <i class="cil-dollar fs-4 text-info"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-body-secondary small mb-1">Total Berat Netto</div>
                        <div class="fs-4 fw-bold text-primary">{{ number_format($totalBeratNetto, 2, ',', '.') }} kg</div>
                        <div class="small text-body-secondary">≈ {{ number_format($totalBeratNetto / 1000, 2, ',', '.') }} ton</div>
                    </div>
                    <div class="p-2 bg-primary bg-opacity-10 rounded-3">
                        <i class="cil-weight fs-4 text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-body-secondary small mb-1">Total Telah Dibayar (Rp 80.000/ton)</div>
                        <div class="fs-4 fw-bold text-success">Rp {{ number_format($totalBiayaTipping, 0, ',', '.') }}</div>
                    </div>
                    <div class="p-2 bg-success bg-opacity-10 rounded-3">
                        <i class="cil-check-circle fs-4 text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Tabel Data --}}
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <strong><i class="cil-dollar text-info me-2"></i>Daftar Ritase DLH — Dibayar (Lunas)</strong>
        <span class="badge bg-info fs-6">{{ number_format($totalCount, 0, ',', '.') }} Ritase</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 small">
                <thead class="table-light">
                    <tr>
                        <th width="3%">No</th>
                        <th>No. Tiket</th>
                        <th>Tanggal</th>
                        <th>Klien (Asal Sampah)</th>
                        <th>Armada</th>
                        <th>Asal Sampah</th>
                        <th class="text-end">Bruto (kg)</th>
                        <th class="text-end">Tarra (kg)</th>
                        <th class="text-end">Netto (kg)</th>
                        <th class="text-end">Netto (ton)</th>
                        <th class="text-end">Biaya Tipping</th>
                        <th class="text-center">Invoice</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ritase as $index => $item)
                    <tr>
                        <td class="text-center">{{ $ritase->firstItem() + $index }}</td>
                        <td><strong>{{ $item->nomor_tiket ?? '-' }}</strong></td>
                        <td>{{ $item->waktu_masuk ? \Carbon\Carbon::parse($item->waktu_masuk)->format('d/m/Y H:i') : '-' }}</td>
                        <td>{{ $item->klien->nama_klien ?? '-' }}</td>
                        <td>{{ $item->armada->plat_nomor ?? '-' }}</td>
                        <td>{{ $item->jenis_sampah ?? '-' }}</td>
                        <td class="text-end">{{ number_format($item->berat_bruto, 2, ',', '.') }}</td>
                        <td class="text-end">{{ number_format($item->berat_tarra, 2, ',', '.') }}</td>
                        <td class="text-end fw-semibold">{{ number_format($item->berat_netto, 2, ',', '.') }}</td>
                        <td class="text-end">{{ number_format($item->berat_netto / 1000, 4, ',', '.') }}</td>
                        <td class="text-end fw-bold text-success">Rp {{ number_format($item->biaya_tipping, 0, ',', '.') }}</td>
                        <td class="text-center">
                            @if($item->invoice_id)
                                <a href="{{ route('admin.invoice.show', $item->invoice_id) }}" class="badge bg-success text-decoration-none">
                                    <i class="cil-check-circle me-1"></i>Lunas #{{ $item->invoice_id }}
                                </a>
                            @else
                                <span class="badge bg-secondary">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('admin.ritase.show', $item) }}" class="btn btn-sm btn-outline-info" title="Lihat Detail">
                                <i class="cil-magnifying-glass"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="13" class="text-center py-5 text-body-secondary">
                            <i class="cil-dollar fs-2 d-block mb-2 text-info"></i>
                            Tidak ada data ritase DLH yang sudah dibayar pada filter ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($ritase->count() > 0)
                <tfoot class="border-top border-2 fw-bold table-light">
                    <tr>
                        <td colspan="8" class="text-end">TOTAL (halaman ini)</td>
                        <td class="text-end">{{ number_format($ritase->sum('berat_netto'), 2, ',', '.') }} kg</td>
                        <td class="text-end">{{ number_format($ritase->sum('berat_netto') / 1000, 4, ',', '.') }} ton</td>
                        <td class="text-end text-success">Rp {{ number_format($ritase->sum('biaya_tipping'), 0, ',', '.') }}</td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
    @if($ritase->hasPages())
    <div class="card-footer bg-white">{{ $ritase->links() }}</div>
    @endif
</div>
@endsection
