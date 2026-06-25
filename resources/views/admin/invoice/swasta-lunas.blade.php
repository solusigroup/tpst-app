@extends('layouts.admin')
@section('title', 'Klien Swasta Lunas')

@section('content')
<div class="page-header mb-4">
    <div>
        <h1>Klien Swasta Lunas</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.invoice.index') }}">Invoice</a></li>
                <li class="breadcrumb-item active">Klien Swasta Lunas</li>
            </ol>
        </nav>
    </div>
</div>

{{-- Summary Cards --}}
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card border-start border-4 border-success h-100 shadow-sm">
            <div class="card-body py-3">
                <div class="text-muted small text-uppercase fw-semibold mb-1">Total Klien Swasta Lunas</div>
                <div class="fs-3 fw-bold text-success">{{ number_format($totalPaidClients) }}</div>
                <small class="text-muted">Klien swasta dengan minimal 1 invoice lunas</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-start border-4 border-info h-100 shadow-sm">
            <div class="card-body py-3">
                <div class="text-muted small text-uppercase fw-semibold mb-1">Total Invoice Lunas</div>
                <div class="fs-3 fw-bold text-info">{{ number_format($totalPaidInvoices) }}</div>
                <small class="text-muted">Jumlah invoice dengan status Paid</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-start border-4 border-primary h-100 shadow-sm">
            <div class="card-body py-3">
                <div class="text-muted small text-uppercase fw-semibold mb-1">Total Dana Diterima</div>
                <div class="fs-3 fw-bold text-primary">Rp {{ number_format($totalCollected, 0, ',', '.') }}</div>
                <small class="text-muted">Akumulasi penerimaan pembayaran lunas</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-start border-4 border-danger h-100 shadow-sm">
            <div class="card-body py-3">
                <div class="text-muted small text-uppercase fw-semibold mb-1">Total Piutang Swasta Aktif</div>
                <div class="fs-3 fw-bold text-danger">Rp {{ number_format($totalOutstanding, 0, ',', '.') }}</div>
                <small class="text-muted">Total invoice Swasta status Sent (Piutang)</small>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-white py-3 border-bottom-0">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            {{-- Tabs --}}
            <ul class="nav nav-tabs card-header-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link fw-semibold {{ $tab === 'clients' ? 'active text-primary border-bottom border-primary border-3' : 'text-secondary' }}" 
                       href="{{ route('admin.invoice.swasta-lunas', ['tab' => 'clients', 'search' => request('search')]) }}">
                        <i class="nav-icon cil-people me-1"></i> Daftar Klien ({{ number_format($totalPaidClients) }})
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-semibold {{ $tab === 'invoices' ? 'active text-primary border-bottom border-primary border-3' : 'text-secondary' }}" 
                       href="{{ route('admin.invoice.swasta-lunas', ['tab' => 'invoices', 'search' => request('search')]) }}">
                        <i class="nav-icon cil-description me-1"></i> Daftar Invoice Lunas ({{ number_format($totalPaidInvoices) }})
                    </a>
                </li>
            </ul>

            {{-- Search Form --}}
            <form method="GET" class="d-flex align-items-center gap-2">
                <input type="hidden" name="tab" value="{{ $tab }}">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" 
                           placeholder="{{ $tab === 'clients' ? 'Cari nama klien...' : 'Cari nomor invoice / klien...' }}" 
                           value="{{ request('search') }}">
                    <button class="btn btn-outline-primary" type="submit">
                        <i class="nav-icon cil-search"></i> Cari
                    </button>
                </div>
                @if(request()->filled('search'))
                    <a href="{{ route('admin.invoice.swasta-lunas', ['tab' => $tab]) }}" class="btn btn-outline-secondary">Reset</a>
                @endif
            </form>
        </div>
    </div>

    <div class="card-body p-0">
        @if($tab === 'clients')
            {{-- Tab Daftar Klien --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Nama Klien</th>
                            <th>Kontak</th>
                            <th>Alamat</th>
                            <th class="text-center">Invoice Lunas</th>
                            <th class="text-end">Total Pembayaran Lunas</th>
                            <th class="text-end pe-4">Sisa Piutang Aktif</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($clients as $item)
                        <tr>
                            <td class="ps-4">
                                <a href="{{ route('admin.klien.show', $item) }}" class="text-decoration-none fw-bold text-dark">
                                    {{ $item->nama_klien }}
                                </a>
                            </td>
                            <td>{{ $item->kontak ?? '-' }}</td>
                            <td><span class="text-truncate d-inline-block" style="max-width: 250px;">{{ $item->alamat ?? '-' }}</span></td>
                            <td class="text-center"><span class="badge bg-success text-white px-2 py-1">{{ $item->invoices_count }}</span></td>
                            <td class="text-end fw-semibold text-success">Rp {{ number_format($item->invoices_sum_total_tagihan, 0, ',', '.') }}</td>
                            <td class="text-end pe-4 fw-bold">
                                @if($item->outstanding_piutang > 0)
                                    <span class="text-danger" title="Ada tagihan belum dibayar (status Sent)">
                                        Rp {{ number_format($item->outstanding_piutang, 0, ',', '.') }}
                                        <i class="nav-icon cil-warning text-warning ms-1" style="font-size: 0.85rem;"></i>
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.klien.show', $item) }}" class="btn btn-sm btn-outline-primary" title="Lihat Detail Klien">
                                    <i class="nav-icon cil-user"></i> Detail
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Tidak ada data klien swasta lunas yang ditemukan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($clients->hasPages())
                <div class="card-footer bg-white border-top-0 py-3">
                    {{ $clients->links() }}
                </div>
            @endif
        @else
            {{-- Tab Daftar Invoice --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">No. Invoice</th>
                            <th>Klien</th>
                            <th>Periode</th>
                            <th class="text-end">Total Tagihan</th>
                            <th class="text-end">Uang Muka</th>
                            <th class="text-end">Sisa Tagihan</th>
                            <th>Tgl Invoice</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoices as $item)
                        <tr onclick="window.location='{{ route('admin.invoice.show', $item) }}'" style="cursor: pointer;">
                            <td class="ps-4"><strong>{{ $item->nomor_invoice ?? '-' }}</strong></td>
                            <td>
                                <a href="{{ route('admin.klien.show', $item->klien) }}" class="text-decoration-none fw-semibold text-dark" onclick="event.stopPropagation()">
                                    {{ $item->klien->nama_klien ?? '-' }}
                                </a>
                            </td>
                            <td>{{ $item->periode_bulan }}/{{ $item->periode_tahun }}</td>
                            <td class="text-end">Rp {{ number_format($item->total_tagihan, 0, ',', '.') }}</td>
                            <td class="text-end text-danger">Rp {{ number_format($item->uang_muka, 0, ',', '.') }}</td>
                            <td class="text-end fw-bold text-success">Rp {{ number_format($item->total_tagihan - $item->uang_muka, 0, ',', '.') }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal_invoice)->format('d/m/Y') }}</td>
                            <td class="text-end pe-4" onclick="event.stopPropagation()">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('invoices.print', $item) }}" target="_blank" class="btn btn-outline-success" title="Cetak"><i class="nav-icon cil-print"></i> Cetak</a>
                                    <a href="{{ route('admin.invoice.show', $item) }}" class="btn btn-outline-primary" title="Detail"><i class="nav-icon cil-zoom-in"></i> Detail</a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">Tidak ada data invoice lunas yang ditemukan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($invoices->hasPages())
                <div class="card-footer bg-white border-top-0 py-3">
                    {{ $invoices->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
