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
    <div class="d-flex gap-2 align-items-center">
        <button type="button" class="btn btn-outline-primary shadow-sm" data-coreui-toggle="modal" data-coreui-target="#previewModal">
            <i class="cil-zoom-in me-1"></i> Preview & Cetak
        </button>
        <div class="btn-group shadow-sm">
            <a href="{{ route('admin.laporan-operasional.residu', array_merge(request()->all(), ['export' => 'pdf'])) }}" target="_blank" class="btn btn-danger" title="Export PDF">
                <i class="cil-file me-1"></i> PDF
            </a>
            <a href="{{ route('admin.laporan-operasional.residu', array_merge(request()->all(), ['export' => 'excel'])) }}" class="btn btn-success" title="Export Excel">
                <i class="cil-spreadsheet me-1"></i> Excel
            </a>
        </div>
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
            <div class="col-md-auto">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="cil-filter me-1"></i> Filter
                </button>
            </div>
            <div class="col-md-auto">
                <a href="{{ route('admin.laporan-operasional.residu') }}" class="btn btn-outline-secondary">Reset</a>
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

    </div>
</div>

<!-- Modal Preview -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header d-print-none">
                <h5 class="modal-title" id="previewModalLabel">Preview Laporan Pengangkutan Residu</h5>
                <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-light p-0">
                <div id="printArea" class="bg-white p-5 shadow-sm mx-auto" style="max-width: 21cm; min-height: 29.7cm;">
                    <x-kop-surat />
                    
                    <div class="text-center mb-4">
                        <h4 class="fw-bold text-uppercase mb-1">LAPORAN PENGANGKUTAN RESIDU</h4>
                        <p class="text-secondary">Periode: {{ \Carbon\Carbon::parse($dari)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($sampai)->format('d/m/Y') }}</p>
                    </div>

                    <table class="table table-bordered border-dark table-sm">
                        <thead class="table-light border-dark">
                            <tr>
                                <th class="text-center" style="width: 40px;">No</th>
                                <th>No. Tiket</th>
                                <th>Tanggal</th>
                                <th>Armada</th>
                                <th class="text-end">Bruto</th>
                                <th class="text-end">Tarra</th>
                                <th class="text-end">Netto</th>
                                <th class="text-end">Retribusi</th>
                                <th>Tujuan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php 
                                $allRowsForPrint = \App\Models\PengangkutanResidu::with('armada')
                                    ->when($dari, fn($q)=>$q->whereDate('tanggal','>=',$dari))
                                    ->when($sampai, fn($q)=>$q->whereDate('tanggal','<=',$sampai))
                                    ->orderByDesc('tanggal')
                                    ->get(); 
                            @endphp
                            @foreach($allRowsForPrint as $index => $row)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>{{ $row->nomor_tiket }}</td>
                                <td>{{ $row->tanggal->format('d/m/Y') }}</td>
                                <td>{{ $row->armada->plat_nomor }}</td>
                                <td class="text-end">{{ number_format($row->berat_bruto, 0, ',', '.') }}</td>
                                <td class="text-end">{{ number_format($row->berat_tarra, 0, ',', '.') }}</td>
                                <td class="text-end fw-bold">{{ number_format($row->berat_netto, 0, ',', '.') }}</td>
                                <td class="text-end">Rp {{ number_format($row->biaya_retribusi, 0, ',', '.') }}</td>
                                <td>{{ $row->tujuan }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="fw-bold">
                            <tr class="table-light border-dark">
                                <td colspan="6" class="text-end">TOTAL</td>
                                <td class="text-end">{{ number_format($totals->total_netto, 0, ',', '.') }} Kg</td>
                                <td class="text-end">Rp {{ number_format($totals->total_biaya, 0, ',', '.') }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>

                    <div class="row mt-5">
                        <div class="col-8"></div>
                        <div class="col-4 text-center">
                            <p class="mb-5">Dicetak pada: {{ now()->format('d/m/Y H:i') }}</p>
                            <div class="mt-5">
                                <p class="fw-bold mb-0">( ____________________ )</p>
                                <p class="text-secondary small">Admin Operasional</p>
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
    @media print {
        /* Hide everything by default */
        body * {
            visibility: hidden;
            overflow: visible !important;
        }
        /* Only show the printArea and its ancestors (to keep it in DOM structure) */
        #printArea, #printArea * {
            visibility: visible;
        }
        /* Position printArea at the very top of the printed page */
        #printArea {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            padding: 0 !important;
            margin: 0 !important;
        }
        /* Ensure the modal and its content are visible, but hide UI chrome */
        .modal-backdrop, .sidebar, .header, .mobile-bottom-nav, .modal-header, .modal-footer {
            display: none !important;
        }
        .modal {
            padding: 0 !important;
            margin: 0 !important;
            display: block !important;
            position: static;
            overflow: visible !important;
        }
        .modal-dialog {
            max-width: 100% !important;
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            display: block !important;
        }
        .modal-content, .modal-body {
            display: block !important;
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
            margin: 0 !important;
            background: transparent !important;
        }
    }
</style>
@endpush
