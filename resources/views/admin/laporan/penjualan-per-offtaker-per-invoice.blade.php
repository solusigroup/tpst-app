@extends('layouts.admin')
@section('title', 'Laporan Penjualan Hasil Pilahan Per Offtaker Per Invoice')

@section('content')
<div class="page-header d-print-none mb-4">
    <div>
        <h1 class="h3 mb-1">Laporan Penjualan Hasil Pilahan Per Offtaker Per Invoice</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="#">Laporan Operasional</a></li>
                <li class="breadcrumb-item active" aria-current="page">Penjualan Offtaker Per Invoice</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2 align-items-center flex-wrap">
        <button type="button" class="btn btn-outline-primary shadow-sm" data-coreui-toggle="modal" data-coreui-target="#previewModal">
            <i class="cil-zoom-in me-1"></i> Preview & Cetak
        </button>
        <div class="btn-group shadow-sm">
            <a href="{{ route('admin.laporan-operasional.penjualan.per-offtaker-per-invoice', array_merge(request()->all(), ['export' => 'pdf'])) }}" target="_blank" class="btn btn-danger" title="Export PDF">
                <i class="cil-file me-1"></i> PDF
            </a>
            <a href="{{ route('admin.laporan-operasional.penjualan.per-offtaker-per-invoice', array_merge(request()->all(), ['export' => 'excel'])) }}" class="btn btn-success text-white" title="Export Excel">
                <i class="cil-spreadsheet me-1"></i> Excel
            </a>
        </div>
    </div>
</div>

{{-- KPI Summary Cards --}}
<div class="row g-3 mb-4 d-print-none">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card stat-card stat-primary shadow-sm h-100">
            <div class="card-body d-flex align-items-center justify-content-between p-3">
                <div>
                    <span class="text-body-secondary small fw-semibold text-uppercase">Total Omzet</span>
                    <h4 class="mb-0 fw-bold text-primary mt-1">Rp {{ number_format($summary->total_omzet, 0, ',', '.') }}</h4>
                    <small class="text-muted">{{ $summary->total_invoice_count }} Invoice Terbit</small>
                </div>
                <div class="stat-icon bg-primary-light">
                    <i class="cil-money"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card stat-card stat-info shadow-sm h-100">
            <div class="card-body d-flex align-items-center justify-content-between p-3">
                <div>
                    <span class="text-body-secondary small fw-semibold text-uppercase">Total Berat Terjual</span>
                    <h4 class="mb-0 fw-bold text-info mt-1">{{ number_format($summary->total_berat_ton, 2, ',', '.') }} Ton</h4>
                    <small class="text-muted">{{ number_format($summary->total_berat_kg, 2, ',', '.') }} Kg</small>
                </div>
                <div class="stat-icon bg-info-light">
                    <i class="cil-layers"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card stat-card stat-success shadow-sm h-100">
            <div class="card-body d-flex align-items-center justify-content-between p-3">
                <div>
                    <span class="text-body-secondary small fw-semibold text-uppercase">Total Terbayar / DP</span>
                    <h4 class="mb-0 fw-bold text-success mt-1">Rp {{ number_format($summary->total_uang_muka, 0, ',', '.') }}</h4>
                    <small class="text-success"><i class="cil-check-circle me-1"></i>{{ $summary->total_invoice_paid }} Invoice Lunas</small>
                </div>
                <div class="stat-icon bg-success-light">
                    <i class="cil-check"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card stat-card stat-danger shadow-sm h-100">
            <div class="card-body d-flex align-items-center justify-content-between p-3">
                <div>
                    <span class="text-body-secondary small fw-semibold text-uppercase">Sisa Piutang</span>
                    <h4 class="mb-0 fw-bold text-danger mt-1">Rp {{ number_format($summary->total_sisa, 0, ',', '.') }}</h4>
                    <small class="text-danger"><i class="cil-warning me-1"></i>{{ $summary->total_invoice_unpaid }} Belum Lunas</small>
                </div>
                <div class="stat-icon bg-danger-light">
                    <i class="cil-clock"></i>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Filter Card --}}
<div class="card mb-4 shadow-sm border-0 d-print-none">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('admin.laporan-operasional.penjualan.per-offtaker-per-invoice') }}" class="row g-3 align-items-end">
            <div class="col-md-2">
                <label class="form-label small text-body-secondary fw-semibold mb-1">Dari Tanggal</label>
                <input type="date" name="dari" class="form-control form-control-sm" value="{{ $dari }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small text-body-secondary fw-semibold mb-1">Sampai Tanggal</label>
                <input type="date" name="sampai" class="form-control form-control-sm" value="{{ $sampai }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small text-body-secondary fw-semibold mb-1">Offtaker (Pembeli)</label>
                <select name="klien_id" class="form-select form-select-sm">
                    <option value="">-- Semua Offtaker --</option>
                    @foreach($kliens as $k)
                        <option value="{{ $k->id }}" {{ $klienId == $k->id ? 'selected' : '' }}>{{ $k->nama_klien }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small text-body-secondary fw-semibold mb-1">Status Invoice</label>
                <select name="status_invoice" class="form-select form-select-sm">
                    <option value="">-- Semua Status --</option>
                    <option value="Paid" {{ $statusInvoice === 'Paid' ? 'selected' : '' }}>Lunas (Paid)</option>
                    <option value="Sent" {{ $statusInvoice === 'Sent' ? 'selected' : '' }}>Terkirim (Sent)</option>
                    <option value="Draft" {{ $statusInvoice === 'Draft' ? 'selected' : '' }}>Draft</option>
                    <option value="uninvoiced" {{ $statusInvoice === 'uninvoiced' ? 'selected' : '' }}>Belum Di-invoice</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small text-body-secondary fw-semibold mb-1">Jenis Produk</label>
                <select name="jenis_produk" class="form-select form-select-sm">
                    <option value="">-- Semua Produk --</option>
                    @foreach($produkList as $prod)
                        <option value="{{ $prod }}" {{ $jenisProduk === $prod ? 'selected' : '' }}>{{ $prod }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-1 d-flex gap-1">
                <button class="btn btn-sm btn-primary w-100" type="submit" title="Filter">
                    <i class="cil-filter me-1"></i> Cari
                </button>
                <a href="{{ route('admin.laporan-operasional.penjualan.per-offtaker-per-invoice') }}" class="btn btn-sm btn-outline-secondary" title="Reset Filter">
                    <i class="cil-reload"></i>
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Main Data Reports per Offtaker --}}
<div class="space-y-4">
    @forelse($reports as $report)
    <div class="card mb-4 shadow-sm border-0">
        {{-- Offtaker Header Banner --}}
        <div class="card-header bg-body py-3 d-flex flex-wrap justify-content-between align-items-center border-bottom">
            <div class="d-flex align-items-center gap-3">
                <div class="avatar avatar-lg bg-primary text-white d-flex align-items-center justify-content-center fw-bold fs-5" style="width:48px;height:48px;border-radius:0.75rem;">
                    {{ strtoupper(substr($report->klien->nama_klien, 0, 2)) }}
                </div>
                <div>
                    <h5 class="mb-1 fw-bold text-primary">{{ $report->klien->nama_klien }}</h5>
                    <div class="text-body-secondary small d-flex gap-3 flex-wrap">
                        @if($report->klien->kontak)
                            <span><i class="cil-phone me-1"></i>{{ $report->klien->kontak }}</span>
                        @endif
                        @if($report->klien->alamat)
                            <span><i class="cil-location-pin me-1"></i>{{ $report->klien->alamat }}</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2 mt-2 mt-md-0 flex-wrap">
                <div class="badge bg-light text-body border p-2 px-3 text-end">
                    <span class="d-block small text-muted text-uppercase">Total Berat</span>
                    <strong class="fs-6 text-dark">{{ number_format($report->total_berat, 2, ',', '.') }} kg</strong>
                </div>
                <div class="badge bg-light text-body border p-2 px-3 text-end">
                    <span class="d-block small text-muted text-uppercase">Total Omzet</span>
                    <strong class="fs-6 text-primary">Rp {{ number_format($report->total_nominal, 0, ',', '.') }}</strong>
                </div>
                <div class="badge bg-light text-body border p-2 px-3 text-end">
                    <span class="d-block small text-muted text-uppercase">Sisa Piutang</span>
                    <strong class="fs-6 text-danger">Rp {{ number_format($report->total_sisa, 0, ',', '.') }}</strong>
                </div>
            </div>
        </div>

        <div class="card-body p-3">
            @foreach($report->invoices as $invData)
            <div class="card mb-3 border {{ $invData->is_uninvoiced ? 'border-warning' : 'border-light-subtle' }} shadow-none">
                {{-- Invoice Header --}}
                <div class="card-header {{ $invData->is_uninvoiced ? 'bg-warning-subtle text-dark' : 'bg-light-subtle' }} py-2 px-3 d-flex flex-wrap justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        @if($invData->is_uninvoiced)
                            <span class="badge bg-warning text-dark px-2 py-1"><i class="cil-clock me-1"></i> Belum Di-invoice (Draft Pending)</span>
                            <span class="small text-muted">Penjualan tercatat tapi belum dimasukkan ke dalam invoice resmi</span>
                        @else
                            <a href="{{ route('admin.invoice.show', $invData->invoice->id) }}" class="fw-bold text-decoration-none text-primary d-flex align-items-center" target="_blank" title="Buka Detail Invoice">
                                <i class="cil-description me-1"></i> {{ $invData->invoice->nomor_invoice }}
                                <i class="cil-external-link ms-1 small"></i>
                            </a>
                            <span class="text-muted small">| Tgl Invoice: <strong>{{ \Carbon\Carbon::parse($invData->invoice->tanggal_invoice)->format('d/m/Y') }}</strong></span>
                            @if($invData->invoice->tanggal_jatuh_tempo)
                                <span class="text-muted small">| Jatuh Tempo: <strong>{{ \Carbon\Carbon::parse($invData->invoice->tanggal_jatuh_tempo)->format('d/m/Y') }}</strong></span>
                            @endif

                            @if($invData->invoice->status === 'Paid')
                                <span class="badge bg-success text-white ms-2 px-2 py-1"><i class="cil-check-circle me-1"></i> Lunas</span>
                            @elseif($invData->invoice->status === 'Sent')
                                <span class="badge bg-primary text-white ms-2 px-2 py-1"><i class="cil-send me-1"></i> Terkirim</span>
                            @elseif($invData->invoice->status === 'Draft')
                                <span class="badge bg-secondary text-white ms-2 px-2 py-1">Draft</span>
                            @elseif($invData->invoice->status === 'Canceled')
                                <span class="badge bg-danger text-white ms-2 px-2 py-1">Batal</span>
                            @endif
                        @endif
                    </div>
                    <div class="small fw-semibold text-end">
                        <span class="text-muted">Subtotal Tagihan:</span>
                        <span class="text-primary fs-6 ms-1">Rp {{ number_format($invData->total_nominal, 0, ',', '.') }}</span>
                        @if($invData->total_uang_muka > 0)
                            <span class="text-success ms-2">(DP: Rp {{ number_format($invData->total_uang_muka, 0, ',', '.') }})</span>
                        @endif
                        @if($invData->sisa_tagihan > 0)
                            <span class="text-danger ms-2">Sisa: Rp {{ number_format($invData->sisa_tagihan, 0, ',', '.') }}</span>
                        @endif
                    </div>
                </div>

                {{-- Penjualan Items Table inside Invoice --}}
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3" style="width: 45px;">No</th>
                                    <th style="width: 120px;">Tgl Jual</th>
                                    <th>Komoditas / Hasil Pilahan</th>
                                    <th class="text-end" style="width: 120px;">Berat (Kg)</th>
                                    <th class="text-end" style="width: 130px;">Harga Satuan</th>
                                    <th class="text-end" style="width: 140px;">Subtotal (Rp)</th>
                                    <th class="text-end pe-3" style="width: 130px;">Uang Muka / DP</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invData->items as $idx => $item)
                                <tr>
                                    <td class="ps-3 text-muted">{{ $idx + 1 }}</td>
                                    <td class="text-secondary">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                                    <td>
                                        <span class="fw-semibold">{{ $item->jenis_produk }}</span>
                                        @if($item->wasteCategory)
                                            <span class="badge bg-light text-dark border ms-1 small">{{ $item->wasteCategory->name }}</span>
                                        @endif
                                    </td>
                                    <td class="text-end fw-medium">{{ number_format($item->berat_kg, 2, ',', '.') }} kg</td>
                                    <td class="text-end text-muted">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                                    <td class="text-end fw-bold text-dark">Rp {{ number_format($item->total_harga, 0, ',', '.') }}</td>
                                    <td class="text-end pe-3 text-success">
                                        @if($item->jumlah_bayar > 0)
                                            Rp {{ number_format($item->jumlah_bayar, 0, ',', '.') }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light border-top">
                                <tr class="fw-bold small">
                                    <td colspan="3" class="ps-3 text-end text-uppercase text-muted">Total {{ $invData->is_uninvoiced ? 'Item Belum Di-invoice' : 'Invoice ' . ($invData->invoice->nomor_invoice ?? '') }} ({{ $invData->items->count() }} item):</td>
                                    <td class="text-end text-dark">{{ number_format($invData->total_berat, 2, ',', '.') }} kg</td>
                                    <td></td>
                                    <td class="text-end text-primary">Rp {{ number_format($invData->total_nominal, 0, ',', '.') }}</td>
                                    <td class="text-end pe-3 text-success">Rp {{ number_format($invData->total_uang_muka, 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Offtaker Footer Subtotal --}}
        <div class="card-footer bg-light-subtle py-2 px-3 d-flex justify-content-between align-items-center border-top">
            <span class="small fw-semibold text-muted text-uppercase">Ringkasan Total Offtaker {{ $report->klien->nama_klien }}:</span>
            <div class="d-flex gap-4 fw-bold small">
                <span>Berat: <span class="text-dark">{{ number_format($report->total_berat, 2, ',', '.') }} kg</span></span>
                <span>Omzet: <span class="text-primary">Rp {{ number_format($report->total_nominal, 0, ',', '.') }}</span></span>
                <span>Terbayar: <span class="text-success">Rp {{ number_format($report->total_uang_muka, 0, ',', '.') }}</span></span>
                <span>Sisa: <span class="text-danger">Rp {{ number_format($report->total_sisa, 0, ',', '.') }}</span></span>
            </div>
        </div>
    </div>
    @empty
    <div class="card shadow-sm border-0 py-5 text-center">
        <div class="card-body">
            <i class="cil-description text-muted" style="font-size: 3rem;"></i>
            <h5 class="mt-3 fw-bold text-secondary">Tidak Ada Data Penjualan Hasil Pilahan</h5>
            <p class="text-muted small">Tidak ditemukan data penjualan hasil pilahan untuk Offtaker pada periode dan kriteria filter terpilih.</p>
            <a href="{{ route('admin.laporan-operasional.penjualan.per-offtaker-per-invoice') }}" class="btn btn-sm btn-outline-primary mt-2">
                <i class="cil-reload me-1"></i> Reset Filter
            </a>
        </div>
    </div>
    @endforelse
</div>

{{-- Modal Preview & Cetak Laporan --}}
<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header d-print-none py-3">
                <h5 class="modal-title fw-bold" id="previewModalLabel"><i class="cil-print me-2"></i>Preview Cetak Laporan Penjualan Offtaker Per Invoice</h5>
                <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="printArea">
                {{-- Kop Surat TPST --}}
                <x-kop-surat />

                <div class="text-center my-3 pb-2 border-bottom">
                    <h4 class="fw-bold text-uppercase mb-1">LAPORAN PENJUALAN HASIL PILAHAN PER OFFTAKER PER INVOICE</h4>
                    <p class="text-secondary small mb-0">Periode: {{ \Carbon\Carbon::parse($dari)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($sampai)->format('d/m/Y') }}</p>
                </div>

                {{-- Preview Table --}}
                <div class="table-responsive my-3">
                    <table class="table table-bordered table-sm align-middle" style="font-size: 9pt;">
                        <thead class="table-light text-center">
                            <tr>
                                <th style="width: 30px;">No</th>
                                <th>Offtaker / Invoice / Item Pilahan</th>
                                <th style="width: 85px;">Tgl Transaksi</th>
                                <th style="width: 90px;" class="text-end">Berat (kg)</th>
                                <th style="width: 100px;" class="text-end">Harga Satuan</th>
                                <th style="width: 110px;" class="text-end">Subtotal (Rp)</th>
                                <th style="width: 105px;" class="text-end">Terbayar (Rp)</th>
                                <th style="width: 85px;" class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $globalNo = 1; @endphp
                            @foreach($reports as $rep)
                                <tr class="table-secondary fw-bold">
                                    <td class="text-center">{{ $globalNo++ }}</td>
                                    <td colspan="7">
                                        OFFTAKER: {{ strtoupper($rep->klien->nama_klien) }} 
                                        @if($rep->klien->alamat) <span class="fw-normal text-muted">({{ $rep->klien->alamat }})</span> @endif
                                    </td>
                                </tr>
                                @foreach($rep->invoices as $inv)
                                    <tr class="table-light fw-semibold">
                                        <td></td>
                                        <td colspan="4" class="ps-3">
                                            @if($inv->is_uninvoiced)
                                                <span class="text-warning">● Item Belum Ter-invoice (Draft Pending)</span>
                                            @else
                                                <span class="text-primary">● INVOICE: {{ $inv->invoice->nomor_invoice }}</span> 
                                                (Tgl: {{ \Carbon\Carbon::parse($inv->invoice->tanggal_invoice)->format('d/m/Y') }})
                                            @endif
                                        </td>
                                        <td class="text-end text-primary">Rp {{ number_format($inv->total_nominal, 0, ',', '.') }}</td>
                                        <td class="text-end text-success">Rp {{ number_format($inv->total_uang_muka, 0, ',', '.') }}</td>
                                        <td class="text-center">
                                            @if($inv->is_uninvoiced)
                                                <span class="badge bg-warning text-dark">Pending</span>
                                            @else
                                                <span class="badge bg-{{ $inv->invoice->status === 'Paid' ? 'success' : ($inv->invoice->status === 'Sent' ? 'primary' : 'secondary') }}">
                                                    {{ $inv->invoice->status }}
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                    @foreach($inv->items as $subIdx => $it)
                                    <tr>
                                        <td></td>
                                        <td class="ps-4 text-muted">↳ {{ $it->jenis_produk }}</td>
                                        <td class="text-center text-muted">{{ \Carbon\Carbon::parse($it->tanggal)->format('d/m/Y') }}</td>
                                        <td class="text-end">{{ number_format($it->berat_kg, 2, ',', '.') }}</td>
                                        <td class="text-end">Rp {{ number_format($it->harga_satuan, 0, ',', '.') }}</td>
                                        <td class="text-end">Rp {{ number_format($it->total_harga, 0, ',', '.') }}</td>
                                        <td class="text-end text-muted">{{ $it->jumlah_bayar > 0 ? 'Rp ' . number_format($it->jumlah_bayar, 0, ',', '.') : '-' }}</td>
                                        <td></td>
                                    </tr>
                                    @endforeach
                                @endforeach
                                <tr class="fw-bold" style="background-color: #f8fafc;">
                                    <td colspan="3" class="text-end text-uppercase">Subtotal Offtaker {{ $rep->klien->nama_klien }}:</td>
                                    <td class="text-end">{{ number_format($rep->total_berat, 2, ',', '.') }} kg</td>
                                    <td></td>
                                    <td class="text-end text-primary">Rp {{ number_format($rep->total_nominal, 0, ',', '.') }}</td>
                                    <td class="text-end text-success">Rp {{ number_format($rep->total_uang_muka, 0, ',', '.') }}</td>
                                    <td></td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-dark fw-bold">
                            <tr>
                                <td colspan="3" class="text-end text-uppercase">GRAND TOTAL KESELURUHAN:</td>
                                <td class="text-end">{{ number_format($summary->total_berat_kg, 2, ',', '.') }} kg</td>
                                <td></td>
                                <td class="text-end">Rp {{ number_format($summary->total_omzet, 0, ',', '.') }}</td>
                                <td class="text-end">Rp {{ number_format($summary->total_uang_muka, 0, ',', '.') }}</td>
                                <td class="text-center">Sisa: Rp {{ number_format($summary->total_sisa, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                {{-- Signatures / Pengesahan --}}
                <div class="row mt-5 pt-3">
                    <div class="col-8"></div>
                    <div class="col-4 text-center">
                        <p class="mb-5">Lamongan, {{ now()->translatedFormat('d F Y') }}<br>Dicetak Oleh,</p>
                        <div class="mt-5">
                            <p class="fw-bold mb-0"><u>{{ auth()->user()->name ?? 'Administrator' }}</u></p>
                            <p class="text-secondary small">Bagian Operasional / Kasir Penjualan</p>
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
