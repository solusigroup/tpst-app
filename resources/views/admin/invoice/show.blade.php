@extends('layouts.admin')
@section('title', 'Detail Invoice - ' . $invoice->nomor_invoice)

@section('content')
<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Detail Invoice</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.invoice.index') }}">Invoice</a></li>
                <li class="breadcrumb-item active">{{ $invoice->nomor_invoice }}</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('invoices.print', $invoice) }}" target="_blank" class="btn btn-success">
            <i class="cil-print me-1"></i> Cetak
        </a>
        <a href="{{ route('admin.invoice.edit', $invoice) }}" class="btn btn-warning">
            <i class="cil-pencil me-1"></i> Edit
        </a>
        <a href="{{ route('admin.invoice.index') }}" class="btn btn-outline-secondary">Kembali</a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- Main Invoice Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 fw-bold text-primary">
                    <i class="cil-description me-2"></i>Informasi Invoice
                </h5>
                @php
                    $statusColors = [
                        'Paid' => 'success',
                        'Sent' => 'info',
                        'Draft' => 'warning',
                        'Canceled' => 'danger'
                    ];
                    $badgeColor = $statusColors[$invoice->status] ?? 'secondary';
                @endphp
                <span class="badge bg-{{ $badgeColor }} px-3 py-2 fs-6">
                    {{ $invoice->status }}
                </span>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="text-muted d-block small text-uppercase fw-bold mb-1">Klien</label>
                        <h5 class="fw-bold mb-3 text-dark">{{ $invoice->klien->nama_klien ?? '-' }}</h5>
                        
                        <label class="text-muted d-block small text-uppercase fw-bold mb-1">Periode</label>
                        <p class="mb-3 text-dark">{{ $invoice->periode_bulan }} / {{ $invoice->periode_tahun }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted d-block small text-uppercase fw-bold mb-1">Tanggal Invoice</label>
                        <p class="mb-3 text-dark">{{ \Carbon\Carbon::parse($invoice->tanggal_invoice)->format('d F Y') }}</p>
                        
                        <label class="text-muted d-block small text-uppercase fw-bold mb-1">Jatuh Tempo</label>
                        <p class="mb-3 text-dark">{{ \Carbon\Carbon::parse($invoice->tanggal_jatuh_tempo)->format('d F Y') }}</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <label class="text-muted d-block small text-uppercase fw-bold mb-1">Keterangan</label>
                        <p class="mb-0 text-dark italic font-monospace bg-light p-2 rounded border-start border-4 border-info">
                            {{ $invoice->keterangan ?? 'Tidak ada keterangan.' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Linked Items -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 fw-bold text-primary">
                    <i class="cil-list me-2"></i>Rincian Tagihan
                </h5>
                @if($invoice->klien && ($invoice->klien->nama_klien === 'Dinas Lingkungan Hidup' || $invoice->klien->jenis === 'DLH'))
                    <span class="badge bg-info text-white">Konsolidasi DLH</span>
                @endif
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-4">Item Transaksi</th>
                                <th>Klien Asal</th>
                                <th>Kategori</th>
                                <th class="text-end pe-4">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoice->ritase as $ritase)
                            <tr>
                                <td class="ps-4">
                                    <span class="d-block fw-bold">Ritase - {{ $ritase->armada->plat_nomor ?? ($ritase->armada->nomor_polisi ?? '-') }}</span>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($ritase->waktu_masuk)->format('d/m/Y H:i') }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">{{ $ritase->klien->nama_klien ?? '-' }} ({{ $ritase->klien->jenis ?? '-' }})</span>
                                </td>
                                <td>Tipping Fee</td>
                                <td class="text-end pe-4 text-dark font-monospace">Rp {{ number_format($ritase->biaya_tipping, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach

                            @foreach($invoice->penjualan as $penjualan)
                            <tr>
                                <td class="ps-4">
                                    <span class="d-block fw-bold">Penjualan - {{ $penjualan->jenis_produk }}</span>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($penjualan->tanggal)->format('d/m/Y') }} ({{ number_format($penjualan->berat_kg, 2) }} kg)</small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">{{ $penjualan->klien->nama_klien ?? '-' }} ({{ $penjualan->klien->jenis ?? '-' }})</span>
                                </td>
                                <td>Penjualan Produk</td>
                                <td class="text-end pe-4 text-dark font-monospace">Rp {{ number_format($penjualan->total_harga, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                            
                            @if($invoice->ritase->isEmpty() && $invoice->penjualan->isEmpty())
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted small">Tidak ada rincian item transaksi.</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar Cards -->
    <div class="col-lg-4">
        <!-- Totals Card -->
        <div class="card border-0 shadow-sm mb-4 bg-primary text-white overflow-hidden position-relative" style="background: linear-gradient(135deg, #321fdb 0%, #1f1498 100%);">
            <div class="card-body py-4 z-index-1 position-relative">
                <p class="text-white-50 text-uppercase fw-bold small mb-2">Total Tagihan</p>
                <h2 class="fw-bold mb-4 font-monospace">Rp {{ number_format($invoice->total_tagihan, 0, ',', '.') }}</h2>
                
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-white-50">Uang Muka / DP</span>
                    <span class="font-monospace">Rp {{ number_format($invoice->uang_muka, 0, ',', '.') }}</span>
                </div>
                <hr class="bg-white-50 my-2">
                <div class="d-flex justify-content-between fw-bold pt-1">
                    <span>Sisa Pelunasan</span>
                    <span class="font-monospace fs-5">Rp {{ number_format($invoice->total_tagihan - $invoice->uang_muka, 0, ',', '.') }}</span>
                </div>
            </div>
            <!-- Subtle background circle for premium look -->
            <div class="position-absolute end-0 bottom-0 mb-n4 me-n4 bg-white opacity-10 rounded-circle" style="width: 150px; height: 150px;"></div>
        </div>

        <!-- Action Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="card-title mb-0 fw-bold">Aksi Cepat</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.jurnal.create', ['ref_type' => urlencode('App\Models\Invoice'), 'ref_id' => $invoice->id]) }}" class="btn btn-light text-start border d-flex justify-content-between align-items-center">
                        <span><i class="cil-book me-2"></i>Buat Jurnal Ledger</span>
                        <i class="cil-chevron-right small text-muted"></i>
                    </a>
                    <button class="btn btn-light text-start border d-flex justify-content-between align-items-center" onclick="window.print()">
                        <span><i class="cil-external-link me-2"></i>Ekspor ke PDF</span>
                        <i class="cil-chevron-right small text-muted"></i>
                    </button>
                </div>
                <hr>
                <div class="mt-3">
                    <small class="text-muted d-block mb-1">Dibuat pada</small>
                    <p class="mb-0 text-dark small">{{ $invoice->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .font-monospace {
        font-family: 'SFMono-Regular', Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace !important;
    }
    .z-index-1 { z-index: 1; }
    .opacity-10 { opacity: 0.1; }
    .italic { font-style: italic; }
</style>
@endsection
