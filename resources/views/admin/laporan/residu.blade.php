@extends('layouts.admin')
@section('title', 'Laporan Pengangkutan Residu')

@section('content')
<div class="page-header">
    <div>
        <h1>Laporan Pengangkutan Residu</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="#">Laporan Operasional</a></li>
                <li class="breadcrumb-item active">Pengangkutan Residu</li>
            </ol>
        </nav>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header bg-white py-3">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-bold">Dari Tanggal</label>
                <input type="date" name="dari" class="form-control" value="{{ $dari }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold">Sampai Tanggal</label>
                <input type="date" name="sampai" class="form-control" value="{{ $sampai }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="cil-filter me-1"></i> Filter
                </button>
            </div>
            <div class="col-md-4 text-end">
                <div class="btn-group w-100">
                    <a href="{{ route('admin.laporan-operasional.residu') }}" class="btn btn-outline-secondary">Reset</a>
                    <a href="{{ route('admin.laporan-operasional.residu', array_merge(request()->all(), ['export' => 'pdf'])) }}" target="_blank" class="btn btn-danger">
                        <i class="cil-print me-1"></i> PDF
                    </a>
                    <a href="{{ route('admin.laporan-operasional.residu', array_merge(request()->all(), ['export' => 'excel'])) }}" class="btn btn-success">
                        <i class="cil-spreadsheet me-1"></i> Excel
                    </a>
                </div>
            </div>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" width="50">No</th>
                        <th>No. Tiket</th>
                        <th>Tanggal</th>
                        <th>Armada</th>
                        <th class="text-end">Bruto (Kg)</th>
                        <th class="text-end">Tarra (Kg)</th>
                        <th class="text-end">Netto (Kg)</th>
                        <th class="text-end">Retribusi</th>
                        <th>Tujuan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $index => $row)
                    <tr>
                        <td class="text-center">{{ $rows->firstItem() + $index }}</td>
                        <td>{{ $row->nomor_tiket }}</td>
                        <td>{{ $row->tanggal->format('d/m/Y') }}</td>
                        <td>{{ $row->armada->plat_nomor }}</td>
                        <td class="text-end">{{ number_format($row->berat_bruto, 0, ',', '.') }}</td>
                        <td class="text-end">{{ number_format($row->berat_tarra, 0, ',', '.') }}</td>
                        <td class="text-end fw-bold">{{ number_format($row->berat_netto, 0, ',', '.') }}</td>
                        <td class="text-end">Rp {{ number_format($row->biaya_retribusi, 0, ',', '.') }}</td>
                        <td>{{ $row->tujuan }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4 text-muted">Tidak ada data untuk periode ini.</td>
                    </tr>
                    @endforelse
                </tbody>
                @if($rows->count() > 0)
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="6" class="text-end">TOTAL</td>
                        <td class="text-end text-primary">{{ number_format($totals->total_netto, 0, ',', '.') }}</td>
                        <td class="text-end text-danger">Rp {{ number_format($totals->total_biaya, 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
    @if($rows->hasPages())
    <div class="card-footer bg-white">
        {{ $rows->links() }}
    </div>
    @endif
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="small opacity-75">Total Netto Residu</div>
                <div class="fs-4 fw-bold">{{ number_format($totals->total_netto, 0, ',', '.') }} Kg</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <div class="small opacity-75">Total Biaya Tipping Fee</div>
                <div class="fs-4 fw-bold">Rp {{ number_format($totals->total_biaya, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="small opacity-75">Total Ritase</div>
                <div class="fs-4 fw-bold">{{ $totals->total_rows }} Trip</div>
            </div>
        </div>
    </div>
</div>
@endsection
