@extends('layouts.admin')
@section('title', 'Laporan Ritase')

@section('content')


<div class="page-header d-print-none">
    <div><h1>Laporan Ritase</h1></div>
    <div class="d-flex gap-2 align-items-center">
        <button type="button" class="btn btn-outline-primary shadow-sm" data-coreui-toggle="modal" data-coreui-target="#previewModal">
            <i class="cil-zoom-in me-1"></i> Preview & Cetak
        </button>
        <div class="btn-group shadow-sm">
            <a href="{{ route('admin.laporan-operasional.ritase', ['dari' => $dari, 'sampai' => $sampai, 'jenis_klien' => $jenisKlien, 'klien_id' => $klienId, 'jenis_armada' => $jenisArmada, 'status' => $status, 'is_approved' => $isApproved, 'export' => 'pdf']) }}" target="_blank" class="btn btn-danger" title="Export PDF">
                <i class="cil-file me-1"></i> PDF
            </a>
            <a href="{{ route('admin.laporan-operasional.ritase', ['dari' => $dari, 'sampai' => $sampai, 'jenis_klien' => $jenisKlien, 'klien_id' => $klienId, 'jenis_armada' => $jenisArmada, 'status' => $status, 'is_approved' => $isApproved, 'export' => 'excel']) }}" class="btn btn-success" title="Export Excel">
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
            <label class="form-label mb-0 small text-body-secondary">Jenis Klien</label>
            <select name="jenis_klien" class="form-select">
                <option value="">-- Semua Jenis --</option>
                @foreach(['DLH', 'Swasta', 'Offtaker', 'Internal'] as $jk)
                    <option value="{{ $jk }}" {{ $jenisKlien == $jk ? 'selected' : '' }}>{{ $jk }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-auto">
            <label class="form-label mb-0 small text-body-secondary">Klien</label>
            <select name="klien_id" class="form-select">
                <option value="">-- Semua Klien --</option>
                @foreach($kliens as $k)<option value="{{ $k->id }}" {{ $klienId == $k->id ? 'selected' : '' }}>{{ $k->nama_klien }} ({{ $k->jenis }})</option>@endforeach
            </select>
        </div>
        <div class="col-auto">
            <label class="form-label mb-0 small text-body-secondary">Jenis Armada</label>
            <select name="jenis_armada" class="form-select">
                <option value="">-- Semua Jenis --</option>
                @foreach(['Dump Truk', 'Pick Up', 'Tossa', 'Gerobak', 'Lainnya'] as $ja)
                    <option value="{{ $ja }}" {{ $jenisArmada == $ja ? 'selected' : '' }}>{{ $ja }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-auto">
            <label class="form-label mb-0 small text-body-secondary">Status</label>
            <select name="status" class="form-select">
                <option value="">-- Semua --</option>
                @foreach(['masuk','timbang','keluar','selesai'] as $s)<option value="{{ $s }}" {{ $status == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>@endforeach
            </select>
        </div>
        <div class="col-auto">
            <label class="form-label mb-0 small text-body-secondary">Approval</label>
            <select name="is_approved" class="form-select">
                <option value="">-- Semua --</option>
                <option value="1" {{ $isApproved === '1' ? 'selected' : '' }}>Approved</option>
                <option value="0" {{ $isApproved === '0' ? 'selected' : '' }}>Not Approved</option>
            </select>
        </div>
                <div class="col-auto">
            <label class="form-label mb-0 small text-body-secondary">Urutan Tanggal</label>
            <select name="sort_date" class="form-select">
                <option value="desc" {{ (isset($sortDate) && $sortDate == 'desc') ? 'selected' : '' }}>Terbaru (Descending)</option>
                <option value="asc" {{ (isset($sortDate) && $sortDate == 'asc') ? 'selected' : '' }}>Terlama (Ascending)</option>
            </select>
        </div>
<div class="col-auto"><button class="btn btn-primary" type="submit"><i class="cil-filter me-1"></i> Filter</button></div>
    </form>
</div></div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header bg-light"><strong>Rekap Jenis Armada</strong></div>
            <div class="card-body p-0">
                <table class="table table-sm table-hover mb-0">
                    <thead class="small table-light">
                        <tr>
                            <th>Jenis Armada</th>
                            <th class="text-center">Ritase</th>
                            <th class="text-end">Tonase (kg)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rekapJenis as $rj)
                        <tr>
                            <td>{{ $rj->jenis_armada ?? 'N/A' }}</td>
                            <td class="text-center">{{ number_format($rj->total_ritase, 0, ',', '.') }}</td>
                            <td class="text-end">{{ number_format($rj->total_netto, 2, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="fw-bold table-light">
                        <tr>
                            <td>TOTAL</td>
                            <td class="text-center">{{ number_format($rekapJenis->sum('total_ritase'), 0, ',', '.') }}</td>
                            <td class="text-end">{{ number_format($rekapJenis->sum('total_netto'), 2, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<ul class="nav nav-tabs mb-3 d-print-none" id="reportTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="table-tab" data-coreui-toggle="tab" data-coreui-target="#table-tab-pane" type="button" role="tab" aria-controls="table-tab-pane" aria-selected="true">
            <i class="cil-list me-1"></i> Daftar Transaksi
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="pivot-tab" data-coreui-toggle="tab" data-coreui-target="#pivot-tab-pane" type="button" role="tab" aria-controls="pivot-tab-pane" aria-selected="false">
            <i class="cil-grid me-1"></i> Analisis Pivot
        </button>
    </li>
</ul>

<div class="tab-content" id="reportTabsContent">
    <!-- Tab 1: Standard Table -->
    <div class="tab-pane fade show active" id="table-tab-pane" role="tabpanel" aria-labelledby="table-tab" tabindex="0">
        <div class="card" id="printable">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light"><tr><th>Tanggal</th><th>No Tiket</th><th>Armada</th><th>Jenis Armada</th><th>Klien</th><th>Jenis Klien</th><th class="text-end">Bruto</th><th class="text-end">Tarra</th><th class="text-end">Berat Netto</th><th class="text-end">Biaya Tipping</th><th>Status Tiket</th><th>Approve</th><th>Status Invoice</th></tr></thead>
                        <tbody>
                            @forelse($rows as $r)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($r->waktu_masuk)->translatedFormat('d M Y') }}</td>
                                <td><strong>{{ $r->nomor_tiket }}</strong></td>
                                <td>{{ $r->armada->plat_nomor ?? '-' }}</td>
                                <td>{{ $r->armada->jenis_armada ?? '-' }}</td>
                                <td>{{ $r->klien->nama_klien ?? '-' }}</td>
                                <td>
                                    @php
                                        $jenisColors = [
                                            'DLH' => 'info',
                                            'Swasta' => 'primary',
                                            'Offtaker' => 'success',
                                            'Internal' => 'secondary'
                                        ];
                                        $color = $jenisColors[$r->klien->jenis] ?? 'light';
                                    @endphp
                                    <span class="badge bg-{{ $color }}">{{ $r->klien->jenis ?? '-' }}</span>
                                </td>
                                <td class="text-end">{{ number_format($r->berat_bruto, 2, ',', '.') }} kg</td>
                                <td class="text-end">{{ number_format($r->berat_tarra, 2, ',', '.') }} kg</td>
                                <td class="text-end">{{ number_format($r->berat_netto, 2, ',', '.') }} kg</td>
                                <td class="text-end">Rp {{ number_format($r->biaya_tipping, 0, ',', '.') }}</td>
                                <td>
                                    @php $statusColors = ['masuk'=>'warning','timbang'=>'info','keluar'=>'primary','selesai'=>'success']; @endphp
                                    <span class="badge bg-{{ $statusColors[$r->status] ?? 'secondary' }}">{{ ucfirst($r->status) }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $r->is_approved ? 'success' : 'danger' }}">
                                        {{ $r->is_approved ? 'Yes' : 'No' }}
                                    </span>
                                </td>
                                <td>
                                    @php $invoiceColors = ['Draft'=>'secondary','Sent'=>'info','Paid'=>'success','Canceled'=>'danger']; @endphp
                                    <span class="badge bg-{{ $invoiceColors[$r->status_invoice] ?? 'secondary' }}">{{ $r->status_invoice ?? 'Unbilled' }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="13" class="text-center py-4 text-body-secondary">Belum ada data ritase.</td></tr>
                            @endforelse
                        </tbody>
                        <tfoot class="border-top border-2 fw-bold">
                            <tr>
                                <td colspan="6" class="text-end">TOTAL ({{ number_format($totals->total_rows ?? 0, 0, ',', '.') }} Ritase)</td>
                                <td class="text-end">{{ number_format($totals->total_bruto ?? 0, 2, ',', '.') }} kg</td>
                                <td class="text-end">{{ number_format($totals->total_tarra ?? 0, 2, ',', '.') }} kg</td>
                                <td class="text-end">{{ number_format($totals->total_netto ?? 0, 2, ',', '.') }} kg</td>
                                <td class="text-end">Rp {{ number_format($totals->total_tipping ?? 0, 0, ',', '.') }}</td>
                                <td colspan="3"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            @if($rows->hasPages()) <div class="card-footer bg-white">{{ $rows->links() }}</div> @endif
        </div>
    </div>
    
    <!-- Tab 2: Pivot Table Analysis -->
    <div class="tab-pane fade" id="pivot-tab-pane" role="tabpanel" aria-labelledby="pivot-tab" tabindex="0">
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3 align-items-end mb-3">
                    <div class="col-md-3">
                        <label class="form-label small text-body-secondary fw-semibold">Baris (Rows)</label>
                        <select id="pivot-row" class="form-select">
                            <option value="nama_klien" selected>Klien</option>
                            <option value="jenis_klien">Jenis Klien</option>
                            <option value="jenis_armada">Jenis Armada</option>
                            <option value="plat_nomor">Plat Nomor</option>
                            <option value="status">Status Tiket</option>
                            <option value="tanggal">Tanggal (Hari)</option>
                            <option value="bulan">Tanggal (Bulan)</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small text-body-secondary fw-semibold">Kolom (Columns)</label>
                        <select id="pivot-col" class="form-select">
                            <option value="" selected>-- Tidak Ada (1 Dimensi) --</option>
                            <option value="jenis_klien">Jenis Klien</option>
                            <option value="jenis_armada">Jenis Armada</option>
                            <option value="status">Status Tiket</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small text-body-secondary fw-semibold">Nilai (Values)</label>
                        <select id="pivot-value" class="form-select">
                            <option value="count" selected>Total Ritase (Jumlah)</option>
                            <option value="berat_netto">Total Netto (Tonase kg)</option>
                            <option value="berat_netto_avg">Rata-rata Netto (kg)</option>
                            <option value="biaya_tipping">Total Biaya Tipping (Rp)</option>
                        </select>
                    </div>
                    <div class="col-md-3 text-md-end text-start">
                        <button type="button" class="btn btn-success text-white shadow-sm w-100 w-md-auto" id="btn-export-pivot">
                            <i class="cil-spreadsheet me-1"></i> Export Pivot ke Excel
                        </button>
                    </div>
                </div>
                
                <div class="table-responsive mt-4">
                    <table class="table table-bordered table-hover align-middle mb-0" id="pivot-table">
                        <!-- Will be dynamically populated by JS -->
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@php
    $pivotJsonData = $allRowsForPrint->map(function($r) {
        return [
            'tanggal'       => \Carbon\Carbon::parse($r->waktu_masuk)->format('d/m/Y'),
            'bulan'         => \Carbon\Carbon::parse($r->waktu_masuk)->format('F Y'),
            'plat_nomor'    => $r->armada->plat_nomor ?? '-',
            'jenis_armada'  => $r->armada->jenis_armada ?? 'Lainnya',
            'nama_klien'    => $r->klien->nama_klien ?? '-',
            'jenis_klien'   => $r->klien->jenis ?? '-',
            'berat_bruto'   => (float)$r->berat_bruto,
            'berat_tarra'   => (float)$r->berat_tarra,
            'berat_netto'   => (float)$r->berat_netto,
            'biaya_tipping' => (float)$r->biaya_tipping,
            'status'        => ucfirst($r->status),
        ];
    });
@endphp
<script id="ritase-pivot-data" type="application/json">
    @json($pivotJsonData)
</script>

<!-- Modal Preview -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header d-print-none">
                <h5 class="modal-title" id="previewModalLabel">Preview Laporan Ritase</h5>
                <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-light p-0">
                <div id="printArea" class="bg-white p-5 shadow-sm mx-auto" style="max-width: 21cm; min-height: 29.7cm;">
                    <x-kop-surat />
                    
                    <div class="text-center mb-4">
                        <h4 class="fw-bold text-uppercase mb-1">LAPORAN RITASE</h4>
                        <p class="text-secondary">Periode: {{ \Carbon\Carbon::parse($dari)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($sampai)->format('d/m/Y') }}</p>
                    </div>

                    @if(isset($rekapJenis) && count($rekapJenis) > 0)
                    <div class="mb-4" style="width: 50%;">
                        <h6 class="fw-bold mb-2">Rekap Jenis Armada</h6>
                        <table class="table table-bordered table-sm border-dark">
                            <thead class="table-light border-dark">
                                <tr>
                                    <th>Jenis Armada</th>
                                    <th class="text-center">Ritase</th>
                                    <th class="text-end">Tonase (kg)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rekapJenis as $rj)
                                <tr>
                                    <td>{{ $rj->jenis_armada ?? 'N/A' }}</td>
                                    <td class="text-center">{{ number_format($rj->total_ritase, 0, ',', '.') }}</td>
                                    <td class="text-end">{{ number_format($rj->total_netto, 2, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="fw-bold">
                                <tr>
                                    <td>TOTAL</td>
                                    <td class="text-center">{{ number_format($rekapJenis->sum('total_ritase'), 0, ',', '.') }}</td>
                                    <td class="text-end">{{ number_format($rekapJenis->sum('total_netto'), 2, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    @endif

                    <table class="table table-bordered border-dark table-sm">
                        <thead class="table-light border-dark">
                            <tr>
                                <th class="text-center" style="width: 40px;">No</th>
                                <th>Tanggal</th>
                                <th>No Tiket</th>
                                <th>Armada</th>
                                <th>Jenis Armada</th>
                                <th>Klien</th>
                                <th>Jenis Klien</th>
                                <th class="text-end">Bruto</th>
                                <th class="text-end">Tarra</th>
                                <th class="text-end">Netto (kg)</th>
                                <th class="text-end">Tipping</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($allRowsForPrint as $index => $r)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>{{ \Carbon\Carbon::parse($r->waktu_masuk)->format('d/m/Y') }}</td>
                                <td>{{ $r->nomor_tiket }}</td>
                                <td>{{ $r->armada->plat_nomor ?? '-' }}</td>
                                <td>{{ $r->armada->jenis_armada ?? '-' }}</td>
                                <td>{{ $r->klien->nama_klien ?? '-' }}</td>
                                <td>{{ $r->klien->jenis ?? '-' }}</td>
                                <td class="text-end">{{ number_format($r->berat_bruto, 2, ',', '.') }}</td>
                                <td class="text-end">{{ number_format($r->berat_tarra, 2, ',', '.') }}</td>
                                <td class="text-end">{{ number_format($r->berat_netto, 2, ',', '.') }}</td>
                                <td class="text-end">{{ number_format($r->biaya_tipping, 0, ',', '.') }}</td>
                                <td>{{ ucfirst($r->status) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="fw-bold">
                            <tr class="table-light border-dark">
                                <td colspan="7" class="text-end">TOTAL KESELURUHAN</td>
                                <td class="text-end">{{ number_format($totals->total_bruto ?? 0, 2, ',', '.') }}</td>
                                <td class="text-end">{{ number_format($totals->total_tarra ?? 0, 2, ',', '.') }}</td>
                                <td class="text-end">{{ number_format($totals->total_netto ?? 0, 2, ',', '.') }}</td>
                                <td class="text-end">Rp {{ number_format($totals->total_tipping ?? 0, 0, ',', '.') }}</td>
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
@endsection

@push('styles')
<style>
    @media print {
        body { 
            overflow: visible !important; 
            height: auto !important; 
            background: white !important;
        }
        .sidebar, .header, .mobile-bottom-nav, .modal-backdrop, .breadcrumb, .page-header, .card, form, .no-print, .d-print-none {
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const rawData = JSON.parse(document.getElementById('ritase-pivot-data').textContent);
    
    const rowSelect = document.getElementById('pivot-row');
    const colSelect = document.getElementById('pivot-col');
    const valSelect = document.getElementById('pivot-value');
    const pivotTable = document.getElementById('pivot-table');
    const btnExport = document.getElementById('btn-export-pivot');
    
    const labels = {
        'nama_klien': 'Klien',
        'jenis_klien': 'Jenis Klien',
        'jenis_armada': 'Jenis Armada',
        'plat_nomor': 'Plat Nomor',
        'status': 'Status Tiket',
        'tanggal': 'Tanggal',
        'bulan': 'Bulan'
    };

    function formatValue(val, type) {
        if (type === 'count') {
            return new Intl.NumberFormat('id-ID', { maximumFractionDigits: 0 }).format(val);
        } else if (type === 'biaya_tipping') {
            return 'Rp ' + new Intl.NumberFormat('id-ID', { maximumFractionDigits: 0 }).format(val);
        } else {
            return new Intl.NumberFormat('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(val) + ' kg';
        }
    }

    function renderPivot() {
        const rowField = rowSelect.value;
        const colField = colSelect.value;
        const metric = valSelect.value;
        
        const rowKeys = [...new Set(rawData.map(r => r[rowField] || '-'))].sort();
        const colKeys = colField ? [...new Set(rawData.map(r => r[colField] || '-'))].sort() : [];
        
        const pivotData = {};
        rowKeys.forEach(r => {
            pivotData[r] = {};
            if (colField) {
                colKeys.forEach(c => {
                    pivotData[r][c] = { sum: 0, count: 0 };
                });
            } else {
                pivotData[r]['_total'] = { sum: 0, count: 0 };
            }
        });
        
        const colTotals = {};
        if (colField) {
            colKeys.forEach(c => {
                colTotals[c] = { sum: 0, count: 0 };
            });
        }
        const grandTotal = { sum: 0, count: 0 };
        
        rawData.forEach(r => {
            const rowVal = r[rowField] || '-';
            const colVal = colField ? (r[colField] || '-') : '_total';
            const value = metric === 'count' ? 1 : (r[metric] || 0);
            
            if (!pivotData[rowVal][colVal]) {
                pivotData[rowVal][colVal] = { sum: 0, count: 0 };
            }
            pivotData[rowVal][colVal].sum += value;
            pivotData[rowVal][colVal].count += 1;
            
            if (colField) {
                if (!colTotals[colVal]) {
                    colTotals[colVal] = { sum: 0, count: 0 };
                }
                colTotals[colVal].sum += value;
                colTotals[colVal].count += 1;
            }
            
            grandTotal.sum += value;
            grandTotal.count += 1;
        });

        function getAggregatedVal(cellData) {
            if (!cellData) return 0;
            if (metric === 'count') {
                return cellData.sum;
            } else if (metric === 'berat_netto_avg') {
                return cellData.count > 0 ? (cellData.sum / cellData.count) : 0;
            } else {
                return cellData.sum;
            }
        }

        let html = '';
        
        html += '<thead class="table-light">';
        html += '<tr>';
        html += `<th>${labels[rowField]}</th>`;
        if (colField) {
            colKeys.forEach(c => {
                html += `<th class="text-end">${c}</th>`;
            });
            html += '<th class="text-end fw-bold">Total</th>';
        } else {
            const valHeader = valSelect.options[valSelect.selectedIndex].text;
            html += `<th class="text-end">${valHeader}</th>`;
        }
        html += '</tr>';
        html += '</thead>';
        
        html += '<tbody>';
        rowKeys.forEach(r => {
            html += '<tr>';
            html += `<td><strong>${r}</strong></td>`;
            
            if (colField) {
                let rowSum = 0;
                let rowCount = 0;
                
                colKeys.forEach(c => {
                    const cellVal = getAggregatedVal(pivotData[r][c]);
                    rowSum += pivotData[r][c].sum;
                    rowCount += pivotData[r][c].count;
                    
                    html += `<td class="text-end">${formatValue(cellVal, metric)}</td>`;
                });
                
                let rowTotalVal = 0;
                if (metric === 'count') {
                    rowTotalVal = rowSum;
                } else if (metric === 'berat_netto_avg') {
                    rowTotalVal = rowCount > 0 ? (rowSum / rowCount) : 0;
                } else {
                    rowTotalVal = rowSum;
                }
                html += `<td class="text-end fw-bold table-light">${formatValue(rowTotalVal, metric)}</td>`;
            } else {
                const cellVal = getAggregatedVal(pivotData[r]['_total']);
                html += `<td class="text-end">${formatValue(cellVal, metric)}</td>`;
            }
            html += '</tr>';
        });
        html += '</tbody>';
        
        html += '<tfoot class="table-light fw-bold border-top border-2">';
        html += '<tr>';
        html += '<td>TOTAL KESELURUHAN</td>';
        
        if (colField) {
            colKeys.forEach(c => {
                const colTotalVal = getAggregatedVal(colTotals[c]);
                html += `<td class="text-end">${formatValue(colTotalVal, metric)}</td>`;
            });
            
            const grandTotalVal = getAggregatedVal(grandTotal);
            html += `<td class="text-end fw-bold">${formatValue(grandTotalVal, metric)}</td>`;
        } else {
            const grandTotalVal = getAggregatedVal(grandTotal);
            html += `<td class="text-end">${formatValue(grandTotalVal, metric)}</td>`;
        }
        
        html += '</tr>';
        html += '</tfoot>';
        
        pivotTable.innerHTML = html;
    }
    
    rowSelect.addEventListener('change', renderPivot);
    colSelect.addEventListener('change', renderPivot);
    valSelect.addEventListener('change', renderPivot);
    
    renderPivot();

    btnExport.addEventListener('click', function() {
        const fromDate = document.querySelector('input[name="dari"]').value;
        const toDate = document.querySelector('input[name="sampai"]').value;
        const rowField = rowSelect.value;
        const colField = colSelect.value;
        const metricLabel = valSelect.options[valSelect.selectedIndex].text;
        
        let htmlExcel = `
        <html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
        <head>
            <meta http-equiv="content-type" content="application/vnd.ms-excel; charset=UTF-8">
            <style>
                table { border-collapse: collapse; }
                th, td { border: 0.5pt solid #ccc; padding: 5px; font-family: Arial, sans-serif; }
                .text-end { text-align: right; }
                .fw-bold { font-weight: bold; }
                .bg-light { background-color: #f2f2f2; }
            </style>
        </head>
        <body>
            <h3>Analisis Pivot Laporan Ritase</h3>
            <p>Periode: ${fromDate} s.d. ${toDate}</p>
            <p><strong>Baris:</strong> ${labels[rowField]} | <strong>Kolom:</strong> ${colField ? labels[colField] : '-'} | <strong>Metrik:</strong> ${metricLabel}</p>
            <table>
                ${pivotTable.innerHTML}
            </table>
        </body>
        </html>
        `;
        
        const blob = new Blob([htmlExcel], { type: 'application/vnd.ms-excel' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `Pivot_Laporan_Ritase_${new Date().toISOString().slice(0,10)}.xls`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    });
});
</script>
@endpush
