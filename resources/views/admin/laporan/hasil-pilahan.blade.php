@extends('layouts.admin')
@section('title', 'Laporan Hasil Pilahan')

@section('content')


<div class="page-header d-print-none">
    <div><h1>Laporan Hasil Pilahan Sampah</h1></div>
    <div class="d-flex gap-2 align-items-center">
        <button type="button" class="btn btn-outline-primary shadow-sm" data-coreui-toggle="modal" data-coreui-target="#previewModal">
            <i class="cil-zoom-in me-1"></i> Preview & Cetak
        </button>
        <div class="btn-group shadow-sm">
            <a href="{{ route('admin.laporan-operasional.hasil-pilahan', array_merge(request()->all(), ['export' => 'pdf'])) }}" target="_blank" class="btn btn-danger" title="Export PDF">
                <i class="cil-file me-1"></i> PDF
            </a>
            <a href="{{ route('admin.laporan-operasional.hasil-pilahan', array_merge(request()->all(), ['export' => 'excel'])) }}" class="btn btn-success" title="Export Excel">
                <i class="cil-spreadsheet me-1"></i> Excel
            </a>
        </div>
    </div>
</div>

<div class="card mb-4"><div class="card-body py-3">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-auto"><label class="form-label mb-0 small text-body-secondary">Dari</label><input type="date" name="dari" class="form-control" value="{{ $dari }}"></div>
        <div class="col-auto"><label class="form-label mb-0 small text-body-secondary">Sampai</label><input type="date" name="sampai" class="form-control" value="{{ $sampai }}"></div>
        <div class="col-auto">
            <label class="form-label mb-0 small text-body-secondary">Kategori</label>
            <select name="kategori" class="form-select">
                <option value="">-- Semua --</option>
                @foreach(['Organik','Anorganik','B3','Residu'] as $c)<option value="{{ $c }}" {{ $kategori == $c ? 'selected' : '' }}>{{ $c }}</option>@endforeach
            </select>
        </div>
        <div class="col-auto"><button class="btn btn-primary" type="submit"><i class="cil-filter me-1"></i> Filter</button></div>
    </form>
</div></div>

{{-- Stock Summary Section --}}
<div class="card mb-4" id="printable-summary">
    <div class="card-header bg-light fw-bold">
        <i class="cil-bar-chart me-1"></i> Ringkasan Stok Pilahan
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Kategori</th>
                        <th>Jenis</th>
                        <th class="text-end">Total Pilahan</th>
                        <th class="text-end">Terjual</th>
                        <th class="text-end">Sisa Stok</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stokSummary as $stok)
                    <tr>
                        <td>
                            @php $catColors = ['Organik'=>'success','Anorganik'=>'info','B3'=>'danger','Residu'=>'warning']; @endphp
                            <span class="badge bg-{{ $catColors[$stok->kategori] ?? 'secondary' }}">{{ $stok->kategori }}</span>
                        </td>
                        <td class="fw-medium">{{ $stok->jenis }}</td>
                        <td class="text-end text-primary">{{ number_format($stok->total_pilahan, 2, ',', '.') }} kg</td>
                        <td class="text-end text-danger">{{ number_format($stok->total_terjual, 2, ',', '.') }} kg</td>
                        <td class="text-end fw-bold {{ $stok->sisa_stok > 0 ? 'text-success' : 'text-body-secondary' }}">{{ number_format($stok->sisa_stok, 2, ',', '.') }} kg</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-4 text-body-secondary">Data ringkasan stok belum tersedia untuk filter ini.</td></tr>
                    @endforelse
                </tbody>
                @if(count($stokSummary) > 0)
                <tfoot class="border-top border-2 fw-bold bg-light">
                    <tr>
                        <td colspan="2" class="text-end">TOTAL KESELURUHAN</td>
                        <td class="text-end text-primary">{{ number_format($summaryTotals->total_pilahan, 2, ',', '.') }} kg</td>
                        <td class="text-end text-danger">{{ number_format($summaryTotals->total_terjual, 2, ',', '.') }} kg</td>
                        <td class="text-end text-success">{{ number_format($summaryTotals->sisa_stok, 2, ',', '.') }} kg</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>

<h5 class="mb-3">Riwayat Log Harian</h5>

<div class="card" id="printable">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light"><tr><th>Tanggal</th><th>Kategori</th><th>Jenis</th><th>Petugas</th><th class="text-end">Tonase</th></tr></thead>
                <tbody>
                    @forelse($rows as $r)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($r->tanggal)->format('d M Y') }}</td>
                        <td>
                            @php $catColors = ['Organik'=>'success','Anorganik'=>'info','B3'=>'danger','Residu'=>'warning']; @endphp
                            <span class="badge bg-{{ $catColors[$r->kategori] ?? 'secondary' }}">{{ $r->kategori }}</span>
                        </td>
                        <td>{{ $r->jenis }}</td>
                        <td>{{ $r->officer }}</td>
                        <td class="text-end">{{ number_format($r->tonase, 2, ',', '.') }} kg</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-4 text-body-secondary">Belum ada data hasil pilahan.</td></tr>
                    @endforelse
                </tbody>
                <tfoot class="border-top border-2 fw-bold">
                    <tr><td colspan="4" class="text-end">TOTAL ({{ number_format($totals->total_rows ?? 0, 0, ',', '.') }} Catatan)</td><td class="text-end">{{ number_format($totals->total_tonase ?? 0, 2, ',', '.') }} kg</td></tr>
                </tfoot>
            </table>
        </div>
    </div>
@if($rows->hasPages()) <div class="card-footer bg-white">{{ $rows->links() }}</div> @endif
</div>

<!-- Modal Preview -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header d-print-none">
                <h5 class="modal-title" id="previewModalLabel">Preview Laporan Hasil Pilahan</h5>
                <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-light p-0">
                <div id="printArea" class="bg-white p-5 shadow-sm mx-auto" style="max-width: 21cm; min-height: 29.7cm;">
                    <x-kop-surat />
                    
                    <div class="text-center mb-4">
                        <h4 class="fw-bold text-uppercase mb-1">LAPORAN HASIL PILAHAN SAMPAH</h4>
                        <p class="text-secondary">Periode: {{ \Carbon\Carbon::parse($dari)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($sampai)->format('d/m/Y') }}</p>
                    </div>

                    <h6 class="fw-bold mb-2">Ringkasan Stok Pilahan</h6>
                    <table class="table table-bordered border-dark table-sm mb-4">
                        <thead class="table-light border-dark">
                            <tr>
                                <th>Kategori</th>
                                <th>Jenis</th>
                                <th class="text-end">Total Pilahan</th>
                                <th class="text-end">Terjual</th>
                                <th class="text-end">Sisa Stok</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stokSummary as $stok)
                            <tr>
                                <td>{{ $stok->kategori }}</td>
                                <td>{{ $stok->jenis }}</td>
                                <td class="text-end">{{ number_format($stok->total_pilahan, 2, ',', '.') }} kg</td>
                                <td class="text-end">{{ number_format($stok->total_terjual, 2, ',', '.') }} kg</td>
                                <td class="text-end fw-bold">{{ number_format($stok->sisa_stok, 2, ',', '.') }} kg</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="fw-bold table-light border-dark">
                            <tr>
                                <td colspan="2" class="text-end">TOTAL</td>
                                <td class="text-end">{{ number_format($summaryTotals->total_pilahan, 2, ',', '.') }} kg</td>
                                <td class="text-end">{{ number_format($summaryTotals->total_terjual, 2, ',', '.') }} kg</td>
                                <td class="text-end">{{ number_format($summaryTotals->sisa_stok, 2, ',', '.') }} kg</td>
                            </tr>
                        </tfoot>
                    </table>

                    <h6 class="fw-bold mb-2">Riwayat Log Harian</h6>
                    <table class="table table-bordered border-dark table-sm">
                        <thead class="table-light border-dark">
                            <tr>
                                <th class="text-center" style="width: 40px;">No</th>
                                <th>Tanggal</th>
                                <th>Kategori</th>
                                <th>Jenis</th>
                                <th>Petugas</th>
                                <th class="text-end">Tonase</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php 
                                $allRowsForPrint = \App\Models\HasilPilahan::query()
                                    ->when($dari, fn($q)=>$q->whereDate('tanggal','>=',$dari))
                                    ->when($sampai, fn($q)=>$q->whereDate('tanggal','<=',$sampai))
                                    ->when($kategori, fn($q)=>$q->where('kategori',$kategori))
                                    ->orderByDesc('tanggal')
                                    ->get(); 
                            @endphp
                            @foreach($allRowsForPrint as $index => $r)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>{{ \Carbon\Carbon::parse($r->tanggal)->format('d/m/Y') }}</td>
                                <td>{{ $r->kategori }}</td>
                                <td>{{ $r->jenis }}</td>
                                <td>{{ $r->officer }}</td>
                                <td class="text-end">{{ number_format($r->tonase, 2, ',', '.') }} kg</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="fw-bold table-light border-dark">
                            <tr>
                                <td colspan="5" class="text-end">TOTAL TONASE</td>
                                <td class="text-end">{{ number_format($totals->total_tonase ?? 0, 2, ',', '.') }} kg</td>
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
        body * { visibility: hidden; overflow: visible !important; }
        #printArea, #printArea * { visibility: visible; }
        #printArea {
            position: absolute; left: 0; top: 0; width: 100%;
            padding: 0 !important; margin: 0 !important;
        }
        .modal, .modal-backdrop, .sidebar, .header, .mobile-bottom-nav { display: none !important; }
        .modal-dialog, .modal-content, .modal-body {
            display: block !important; border: none !important;
            box-shadow: none !important; padding: 0 !important;
            margin: 0 !important; overflow: visible !important;
        }
    }
</style>
@endpush
