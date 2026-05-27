@extends('layouts.admin')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-header d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h4 mb-1">Rekap Invoice Per Jenis Klien</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="#">Laporan Operasional</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Invoice Per Jenis Klien</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3">
                <form method="GET" action="{{ route('admin.laporan-operasional.invoice.per-jenis') }}" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label small text-muted mb-1">Dari Tanggal</label>
                        <input type="date" name="dari" class="form-control form-control-sm" value="{{ $dari }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small text-muted mb-1">Sampai Tanggal</label>
                        <input type="date" name="sampai" class="form-control form-control-sm" value="{{ $sampai }}">
                    </div>
                    <div class="col-md-4">
                        <button class="btn btn-sm btn-outline-primary" type="submit"><i class="cil-search me-1"></i> Filter</button>
                        <a href="{{ route('admin.laporan-operasional.invoice.per-jenis') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                    </div>
                </form>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">No</th>
                                <th>Jenis Klien</th>
                                <th class="text-center">Jumlah Invoice</th>
                                <th class="text-end">Total Tagihan (Rp)</th>
                                <th class="text-end">Total Dibayar (Rp)</th>
                                <th class="text-end pe-4">Sisa Tagihan (Rp)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($query as $index => $row)
                                <tr>
                                    <td class="ps-4">{{ $index + 1 }}</td>
                                    <td class="fw-medium">{{ $row->jenis_tarif ?? 'Tanpa Jenis' }}</td>
                                    <td class="text-center"><span class="badge bg-secondary">{{ $row->jumlah_invoice }}</span></td>
                                    <td class="text-end text-primary fw-medium">{{ number_format($row->total_tagihan, 0, ',', '.') }}</td>
                                    <td class="text-end text-success fw-medium">{{ number_format($row->total_dibayar, 0, ',', '.') }}</td>
                                    <td class="text-end text-danger fw-medium pe-4">{{ number_format($row->sisa_tagihan, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="cil-inbox fs-1 d-block mb-2 text-black-50"></i>
                                        Tidak ada data invoice pada periode ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($query->count() > 0)
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td colspan="2" class="text-end ps-4">TOTAL</td>
                                <td class="text-center">{{ number_format($totalInvoice, 0, ',', '.') }}</td>
                                <td class="text-end text-primary">{{ number_format($totalTagihan, 0, ',', '.') }}</td>
                                <td class="text-end text-success">{{ number_format($totalDibayar, 0, ',', '.') }}</td>
                                <td class="text-end text-danger pe-4">{{ number_format($totalSisa, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
