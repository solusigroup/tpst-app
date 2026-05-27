@extends('layouts.admin')
@section('title', 'Detail Vendor: ' . $vendor->nama_vendor)

@section('content')
<div class="page-header d-print-none flex-wrap d-flex justify-content-between align-items-center">
    <div>
        <h1>{{ $vendor->nama_vendor }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.vendor.index') }}">Vendor</a></li>
                <li class="breadcrumb-item active">Detail</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.vendor.index') }}" class="btn btn-outline-secondary">
            <i class="cil-arrow-left me-1"></i> Kembali
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-header bg-white">
                <h5 class="mb-0">Informasi Vendor</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless table-sm mb-0">
                    <tr>
                        <td class="text-body-secondary w-25">Nama</td>
                        <td><strong>{{ $vendor->nama_vendor }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-body-secondary">Kontak</td>
                        <td>{{ $vendor->kontak ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-body-secondary">Alamat</td>
                        <td>{{ $vendor->alamat ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-body-secondary">Terdaftar</td>
                        <td>{{ $vendor->created_at?->format('d/m/Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-8 mb-4">
        <div class="card h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Riwayat Hutang / Transaksi</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Tanggal</th>
                                <th>No. Ref</th>
                                <th>Keterangan</th>
                                <th class="text-end">Total Hutang</th>
                                <th class="text-end">Terbayar</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($hutang as $h)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($h->tanggal)->format('d/m/Y') }}</td>
                                <td>{{ $h->jurnalHeader->nomor_referensi ?? '-' }}</td>
                                <td style="max-width: 200px; white-space: normal;">{{ $h->keterangan }}</td>
                                <td class="text-end fw-bold text-danger">Rp {{ number_format($h->jumlah, 0, ',', '.') }}</td>
                                <td class="text-end text-success">Rp {{ number_format($h->terbayar, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    @if($h->status == 'lunas')
                                        <span class="badge bg-success">Lunas</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-body-secondary">Tidak ada riwayat hutang / transaksi.</td>
                            </tr>
                            @endforelse
                        </tbody>
                        @if($hutang->count() > 0)
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="3" class="text-end">Total Kumulatif:</th>
                                <th class="text-end text-danger fw-bold">Rp {{ number_format($hutang->sum('jumlah'), 0, ',', '.') }}</th>
                                <th class="text-end text-success fw-bold">Rp {{ number_format($hutang->sum('terbayar'), 0, ',', '.') }}</th>
                                <th class="text-center text-primary fw-bold">
                                    Sisa: Rp {{ number_format($hutang->sum('jumlah') - $hutang->sum('terbayar'), 0, ',', '.') }}
                                </th>
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
