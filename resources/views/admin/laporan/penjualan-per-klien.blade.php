@extends('layouts.admin')
@section('title', 'Laporan Penjualan Per Klien')

@section('content')

<div class="page-header d-print-none">
    <div>
        <h1>Laporan Penjualan Per Klien</h1>
        <p class="text-secondary small mb-0">Frekuensi dihitung per hari transaksi unik per klien.</p>
    </div>
    <div class="d-flex gap-2 align-items-center">
        <button type="button" class="btn btn-outline-primary shadow-sm" data-coreui-toggle="modal" data-coreui-target="#previewModal">
            <i class="cil-zoom-in me-1"></i> Preview & Cetak
        </button>
        <div class="btn-group shadow-sm">
            <a href="{{ route('admin.laporan-operasional.penjualan-per-klien', array_merge(request()->all(), ['export' => 'pdf'])) }}" target="_blank" class="btn btn-danger" title="Export PDF">
                <i class="cil-file me-1"></i> PDF
            </a>
        </div>
    </div>
</div>

<div class="card mb-4 d-print-none">
    <div class="card-body py-3">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label small text-body-secondary">Dari Tanggal</label>
                <input type="date" name="dari" class="form-control" value="{{ $dari }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small text-body-secondary">Sampai Tanggal</label>
                <input type="date" name="sampai" class="form-control" value="{{ $sampai }}">
            </div>
            <div class="col-md-4">
                <label class="form-label small text-body-secondary">Filter Klien</label>
                <select name="klien_id" class="form-select select-tom">
                    <option value="">Semua Klien</option>
                    @foreach($kliens as $k)
                        <option value="{{ $k->id }}" {{ $klienId == $k->id ? 'selected' : '' }}>{{ $k->nama_klien }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100" type="submit">
                    <i class="cil-filter me-1"></i> Filter
                </button>
            </div>
        </form>
    </div>
</div>

<div class="space-y-4">
    @forelse($reports as $report)
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-between align-items-center">
            <div>
                <h5 class="mb-0 fw-bold text-primary">{{ $report->klien->nama_klien }}</h5>
                <span class="text-secondary small">{{ $report->klien->alamat }}</span>
            </div>
            <div class="text-end">
                <div class="badge bg-info-gradient text-dark p-2 px-3 shadow-sm" style="background: linear-gradient(45deg, #e0f2fe, #bae6fd);">
                    <span class="small uppercase text-secondary">Frekuensi Kunjungan:</span>
                    <strong class="fs-5 ms-1">{{ $report->frequency }}x</strong>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3" style="width: 120px;">Tanggal</th>
                            <th>Produk / Kategori</th>
                            <th class="text-end">Berat (kg)</th>
                            <th class="text-end">Harga Satuan</th>
                            <th class="text-end pe-3">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($report->items as $item)
                        <tr>
                            <td class="ps-3 text-secondary">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                            <td class="fw-medium">{{ $item->jenis_produk }}</td>
                            <td class="text-end">{{ number_format($item->berat_kg, 2, ',', '.') }} kg</td>
                            <td class="text-end text-secondary">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                            <td class="text-end pe-3 fw-bold">Rp {{ number_format($item->total_harga, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light border-top border-2">
                        <tr class="fw-bold">
                            <td colspan="2" class="ps-3 text-end text-uppercase small text-secondary">Total ({{ $report->items->count() }} Item)</td>
                            <td class="text-end">{{ number_format($report->total_berat, 2, ',', '.') }} kg</td>
                            <td></td>
                            <td class="text-end pe-3 text-primary fs-6">Rp {{ number_format($report->total_nominal, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    @empty
    <div class="card card-body text-center py-5 border-dashed">
        <i class="cil-mood-bad fs-1 text-secondary mb-3"></i>
        <p class="text-secondary mb-0">Tidak ada data penjualan ditemukan untuk periode ini.</p>
    </div>
    @endforelse
</div>

<!-- Modal Preview -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header d-print-none">
                <h5 class="modal-title" id="previewModalLabel">Preview Laporan Penjualan Per Klien</h5>
                <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-light p-0">
                <div id="printArea" class="bg-white p-5 shadow-sm mx-auto" style="max-width: 21cm; min-height: 29.7cm;">
                    <x-kop-surat />
                    
                    <div class="text-center mb-4">
                        <h4 class="fw-bold text-uppercase mb-1">LAPORAN PENJUALAN PER KLIEN</h4>
                        <p class="text-secondary">Periode: {{ \Carbon\Carbon::parse($dari)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($sampai)->format('d/m/Y') }}</p>
                    </div>

                    @foreach($reports as $report)
                    <div class="mb-5" style="page-break-inside: avoid;">
                        <div class="d-flex justify-content-between border-bottom border-2 border-dark mb-2 pb-1">
                            <div>
                                <h5 class="fw-bold mb-0">{{ $report->klien->nama_klien }}</h5>
                                <p class="small text-secondary mb-0">{{ $report->klien->alamat }}</p>
                            </div>
                            <div class="text-end">
                                <span class="small text-uppercase">Frekuensi:</span>
                                <span class="fw-bold">{{ $report->frequency }}x</span>
                            </div>
                        </div>

                        <table class="table table-bordered border-dark table-sm mb-0">
                            <thead class="table-light border-dark">
                                <tr>
                                    <th class="text-center" style="width: 40px;">No</th>
                                    <th>Tanggal</th>
                                    <th>Jenis Produk</th>
                                    <th class="text-end">Berat (kg)</th>
                                    <th class="text-end">Harga Satuan</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($report->items as $index => $item)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                                    <td>{{ $item->jenis_produk }}</td>
                                    <td class="text-end">{{ number_format($item->berat_kg, 2, ',', '.') }}</td>
                                    <td class="text-end">{{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                                    <td class="text-end fw-bold">{{ number_format($item->total_harga, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="fw-bold">
                                <tr>
                                    <td colspan="3" class="text-end uppercase small">Total Per Klien</td>
                                    <td class="text-end">{{ number_format($report->total_berat, 2, ',', '.') }} kg</td>
                                    <td></td>
                                    <td class="text-end">Rp {{ number_format($report->total_nominal, 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    @endforeach

                    <div class="row mt-5">
                        <div class="col-8"></div>
                        <div class="col-4 text-center">
                            <p class="mb-5">Dicetak pada: {{ now()->format('d/m/Y H:i') }}</p>
                            <div class="mt-5">
                                <p class="fw-bold mb-0">( ____________________ )</p>
                                <p class="text-secondary small">Admin Penjualan</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer d-print-none">
                <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="window.print()">
                    <i class="cil-print me-1"></i> Cetak Sekarang
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .bg-info-gradient { background: #f0f9ff; }
    .space-y-4 > * + * { margin-top: 1.5rem; }
    
    @media print {
        body { overflow: visible !important; height: auto !important; background: white !important; }
        .sidebar, .header, .mobile-bottom-nav, .modal-backdrop, .breadcrumb, .page-header, .card, form, .no-print, .d-print-none {
            display: none !important;
        }
        .wrapper { padding: 0 !important; margin: 0 !important; }
        .body { padding: 0 !important; margin: 0 !important; }
        .container-fluid { padding: 0 !important; margin: 0 !important; }
        .modal { display: block !important; position: static !important; width: 100% !important; background: white !important; overflow: visible !important; height: auto !important; }
        .modal-dialog { max-width: 100% !important; width: 100% !important; margin: 0 !important; padding: 0 !important; overflow: visible !important; height: auto !important; }
        .modal-content, .modal-body { display: block !important; border: none !important; box-shadow: none !important; padding: 0 !important; background: white !important; }
        #printArea { padding: 0 !important; margin: 0 !important; max-width: 100% !important; box-shadow: none !important; }
    }
</style>
@endpush
