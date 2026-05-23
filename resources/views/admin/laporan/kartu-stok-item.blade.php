@extends('layouts.admin')
@section('title', 'Kartu Stok Item')

@section('content')

<div class="page-header d-print-none">
    <div>
        <h1>Kartu Stok Item</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="#">Laporan Operasional</a></li>
                <li class="breadcrumb-item active">Kartu Stok Item</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2 align-items-center">
        @if($jenisItem)
        <button type="button" class="btn btn-outline-primary shadow-sm" data-coreui-toggle="modal" data-coreui-target="#previewModal">
            <i class="cil-zoom-in me-1"></i> Preview & Cetak
        </button>
        <div class="btn-group shadow-sm">
            <a href="{{ route('admin.laporan-operasional.kartu-stok-item', array_merge(request()->all(), ['export' => 'pdf'])) }}" target="_blank" class="btn btn-danger" title="Export PDF">
                <i class="cil-file me-1"></i> PDF
            </a>
            <a href="{{ route('admin.laporan-operasional.kartu-stok-item', array_merge(request()->all(), ['export' => 'excel'])) }}" class="btn btn-success" title="Export Excel">
                <i class="cil-spreadsheet me-1"></i> Excel
            </a>
        </div>
        @endif
    </div>
</div>

<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-auto">
                <label class="form-label mb-0 small text-body-secondary">Dari Tanggal</label>
                <input type="date" name="dari" class="form-control" value="{{ $dari }}">
            </div>
            <div class="col-auto">
                <label class="form-label mb-0 small text-body-secondary">Sampai Tanggal</label>
                <input type="date" name="sampai" class="form-control" value="{{ $sampai }}">
            </div>
            <div class="col-auto">
                <label class="form-label mb-0 small text-body-secondary">Pilih Jenis Barang <span class="text-danger">*</span></label>
                <select name="jenis" class="form-select" required>
                    <option value="">-- Pilih Jenis Barang --</option>
                    @foreach($semuaJenis as $jenis)
                        <option value="{{ $jenis }}" {{ $jenisItem == $jenis ? 'selected' : '' }}>{{ $jenis }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <button class="btn btn-primary" type="submit"><i class="cil-filter me-1"></i> Tampilkan</button>
            </div>
        </form>
    </div>
</div>

@if(!$jenisItem)
<div class="alert alert-info d-flex align-items-center" role="alert">
    <i class="cil-info me-3 fs-3"></i>
    <div>
        Silakan pilih **Jenis Barang** pada filter di atas dan klik **Tampilkan** untuk melihat pergerakan (histori) stok item tersebut secara kronologis.
    </div>
</div>
@else
<div class="card shadow-sm border-0 mb-4" id="printable">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold text-primary">Kartu Stok: {{ $jenisItem }}</h5>
        <span class="badge bg-light text-dark border">Periode: {{ \Carbon\Carbon::parse($dari)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($sampai)->format('d/m/Y') }}</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 50px;">No</th>
                        <th style="width: 120px;">Tanggal</th>
                        <th style="width: 120px;">Jenis Mutasi</th>
                        <th>Keterangan</th>
                        <th class="text-end" style="width: 150px;">Masuk (Kg)</th>
                        <th class="text-end" style="width: 150px;">Keluar (Kg)</th>
                        <th class="text-end" style="width: 150px;">Saldo (Kg)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="table-secondary">
                        <td colspan="4" class="text-end fw-bold">SALDO AWAL SEBELUM {{ \Carbon\Carbon::parse($dari)->format('d/m/Y') }}</td>
                        <td></td>
                        <td></td>
                        <td class="text-end fw-bold font-monospace">{{ number_format($saldoAwal, 2, ',', '.') }}</td>
                    </tr>
                    
                    @php 
                        $runningBalance = $saldoAwal; 
                        $totalMasuk = 0;
                        $totalKeluar = 0;
                    @endphp
                    
                    @forelse($mutasi as $index => $row)
                        @php 
                            $masuk = $row->jumlah_masuk;
                            $keluar = $row->jumlah_keluar;
                            $runningBalance = $runningBalance + $masuk - $keluar;
                            
                            $totalMasuk += $masuk;
                            $totalKeluar += $keluar;
                        @endphp
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}</td>
                            <td>
                                @if($row->tipe === 'Masuk')
                                    <span class="badge bg-success">Produksi (Masuk)</span>
                                @else
                                    <span class="badge bg-danger">Penjualan (Keluar)</span>
                                @endif
                            </td>
                            <td>{{ $row->keterangan }}</td>
                            <td class="text-end text-success font-monospace">{{ $masuk > 0 ? number_format($masuk, 2, ',', '.') : '-' }}</td>
                            <td class="text-end text-danger font-monospace">{{ $keluar > 0 ? number_format($keluar, 2, ',', '.') : '-' }}</td>
                            <td class="text-end fw-bold font-monospace">{{ number_format($runningBalance, 2, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Tidak ada pergerakan stok pada periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="table-light fw-bold border-top-2">
                    <tr>
                        <td colspan="4" class="text-end">TOTAL MUTASI PERIODE INI</td>
                        <td class="text-end text-success font-monospace">{{ number_format($totalMasuk, 2, ',', '.') }}</td>
                        <td class="text-end text-danger font-monospace">{{ number_format($totalKeluar, 2, ',', '.') }}</td>
                        <td class="text-end font-monospace">{{ number_format($runningBalance, 2, ',', '.') }}</td>
                    </tr>
                    <tr class="table-primary">
                        <td colspan="4" class="text-end">SALDO AKHIR (SISA STOK)</td>
                        <td colspan="2"></td>
                        <td class="text-end fs-5 font-monospace">{{ number_format($runningBalance, 2, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<!-- Modal Preview -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header d-print-none">
                <h5 class="modal-title" id="previewModalLabel">Preview Laporan Kartu Stok Item</h5>
                <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-light p-0">
                <div id="printArea" class="bg-white p-5 shadow-sm mx-auto" style="max-width: 21cm; min-height: 29.7cm;">
                    <x-kop-surat />
                    
                    <div class="text-center mb-4">
                        <h4 class="fw-bold text-uppercase mb-1">KARTU STOK BARANG (HISTORI ITEM)</h4>
                        <p class="text-secondary mb-1">Jenis Barang: <strong>{{ $jenisItem }}</strong></p>
                        <p class="text-secondary small mb-1">Periode: {{ \Carbon\Carbon::parse($dari)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($sampai)->format('d/m/Y') }}</p>
                    </div>

                    <table class="table table-bordered border-dark table-sm mt-4">
                        <thead class="table-light border-dark">
                            <tr>
                                <th class="text-center" style="width: 40px;">No</th>
                                <th class="text-center">Tanggal</th>
                                <th class="text-center">Tipe</th>
                                <th>Keterangan</th>
                                <th class="text-center">Masuk (Kg)</th>
                                <th class="text-center">Keluar (Kg)</th>
                                <th class="text-center">Saldo (Kg)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="table-secondary border-dark">
                                <td colspan="4" class="text-end fw-bold">SALDO AWAL SEBELUM {{ \Carbon\Carbon::parse($dari)->format('d/m/Y') }}</td>
                                <td></td>
                                <td></td>
                                <td class="text-end fw-bold">{{ number_format($saldoAwal, 2, ',', '.') }}</td>
                            </tr>
                            
                            @php 
                                $printRunningBalance = $saldoAwal; 
                                $printTotalMasuk = 0;
                                $printTotalKeluar = 0;
                            @endphp
                            
                            @foreach($mutasi as $index => $row)
                                @php 
                                    $masuk = $row->jumlah_masuk;
                                    $keluar = $row->jumlah_keluar;
                                    $printRunningBalance = $printRunningBalance + $masuk - $keluar;
                                    
                                    $printTotalMasuk += $masuk;
                                    $printTotalKeluar += $keluar;
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td class="text-center">{{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}</td>
                                    <td class="text-center">{{ $row->tipe }}</td>
                                    <td>{{ $row->keterangan }}</td>
                                    <td class="text-end">{{ $masuk > 0 ? number_format($masuk, 2, ',', '.') : '-' }}</td>
                                    <td class="text-end">{{ $keluar > 0 ? number_format($keluar, 2, ',', '.') : '-' }}</td>
                                    <td class="text-end fw-bold">{{ number_format($printRunningBalance, 2, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light fw-bold border-dark">
                            <tr>
                                <td colspan="4" class="text-end">TOTAL MUTASI</td>
                                <td class="text-end">{{ number_format($printTotalMasuk, 2, ',', '.') }}</td>
                                <td class="text-end">{{ number_format($printTotalKeluar, 2, ',', '.') }}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-end">SALDO AKHIR TANGGAL {{ \Carbon\Carbon::parse($sampai)->format('d/m/Y') }}</td>
                                <td colspan="2"></td>
                                <td class="text-end">{{ number_format($printRunningBalance, 2, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>

                    <div class="row mt-5">
                        <div class="col-8"></div>
                        <div class="col-4 text-center">
                            <p class="mb-5">Dicetak pada: {{ now()->format('d/m/Y H:i') }}</p>
                            <div class="mt-5">
                                <p class="fw-bold mb-0">( ____________________ )</p>
                                <p class="text-secondary small">&nbsp;</p>
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
@endif

@endsection

@push('styles')
<style>
    .font-monospace {
        font-family: 'SFMono-Regular', Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace !important;
    }
    @media print {
        body { 
            overflow: visible !important; 
            height: auto !important; 
            background: white !important;
        }
        .sidebar, .header, .mobile-bottom-nav, .modal-backdrop, .breadcrumb, .page-header, .card, form, .no-print, .d-print-none, .alert {
            display: none !important;
        }
        .wrapper { padding: 0 !important; margin: 0 !important; }
        .body { padding: 0 !important; margin: 0 !important; }
        .container-fluid { padding: 0 !important; margin: 0 !important; }
        .modal {
            display: block !important;
            position: static !important;
            left: 0 !important;
            top: 0 !important;
            width: 100% !important;
            opacity: 1 !important;
            visibility: visible !important;
            background: white !important;
            overflow: visible !important;
            height: auto !important;
        }
        .modal-dialog {
            max-width: 100% !important;
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            overflow: visible !important;
            height: auto !important;
        }
        .modal-content, .modal-body {
            display: block !important;
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
            background: white !important;
            visibility: visible !important;
            opacity: 1 !important;
            overflow: visible !important;
            height: auto !important;
            max-height: none !important;
        }
        #printArea {
            visibility: visible !important;
            opacity: 1 !important;
            display: block !important;
            padding: 0 !important;
            margin: 0 !important;
            max-width: 100% !important;
            min-height: auto !important;
            box-shadow: none !important;
        }
        #printArea * {
            visibility: visible !important;
            opacity: 1 !important;
        }
    }
</style>
@endpush
