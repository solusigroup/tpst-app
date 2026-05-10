@extends('layouts.admin')
@section('title', 'Rekap Ritase per Tanggal & Jenis Klien')

@section('content')

<div class="page-header d-print-none">
    <div><h1>Rekap Ritase</h1></div>
    <div class="d-flex gap-2 align-items-center">
        <button type="button" class="btn btn-outline-primary shadow-sm" data-coreui-toggle="modal" data-coreui-target="#previewModal">
            <i class="cil-zoom-in me-1"></i> Preview & Cetak
        </button>
        <div class="btn-group shadow-sm">
            <a href="{{ route('admin.laporan-operasional.rekap-ritase', ['dari' => $dari, 'sampai' => $sampai, 'jenis_klien' => $jenisKlien, 'klien_id' => $klienId, 'is_approved' => $isApproved, 'export' => 'pdf']) }}" target="_blank" class="btn btn-danger" title="Export PDF">
                <i class="cil-file me-1"></i> PDF
            </a>
            <a href="{{ route('admin.laporan-operasional.rekap-ritase', ['dari' => $dari, 'sampai' => $sampai, 'jenis_klien' => $jenisKlien, 'klien_id' => $klienId, 'is_approved' => $isApproved, 'export' => 'excel']) }}" class="btn btn-success" title="Export Excel">
                <i class="cil-spreadsheet me-1"></i> Excel
            </a>
        </div>
    </div>
</div>

{{-- Filter --}}
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
            <label class="form-label mb-0 small text-body-secondary">Approval</label>
            <select name="is_approved" class="form-select">
                <option value="">-- Semua --</option>
                <option value="1" {{ $isApproved === '1' ? 'selected' : '' }}>Approved</option>
                <option value="0" {{ $isApproved === '0' ? 'selected' : '' }}>Not Approved</option>
            </select>
        </div>
        <div class="col-auto"><button class="btn btn-primary" type="submit"><i class="cil-filter me-1"></i> Filter</button></div>
    </form>
</div></div>

{{-- Summary Cards per Jenis Klien --}}
<div class="row g-3 mb-4">
    @php
        $jenisColors = [
            'DLH' => ['bg' => 'bg-info', 'border' => 'border-info', 'icon' => 'cil-building'],
            'Swasta' => ['bg' => 'bg-primary', 'border' => 'border-primary', 'icon' => 'cil-briefcase'],
            'Offtaker' => ['bg' => 'bg-success', 'border' => 'border-success', 'icon' => 'cil-people'],
            'Internal' => ['bg' => 'bg-secondary', 'border' => 'border-secondary', 'icon' => 'cil-home'],
        ];
    @endphp
    @foreach($rekapPerJenis as $rj)
    @php $style = $jenisColors[$rj->jenis] ?? ['bg' => 'bg-dark', 'border' => 'border-dark', 'icon' => 'cil-tag']; @endphp
    <div class="col-xl-3 col-md-6">
        <div class="card border-start border-start-4 {{ $style['border'] }} h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="text-body-secondary small fw-semibold text-uppercase">{{ $rj->jenis }}</div>
                    <div class="{{ $style['bg'] }} text-white rounded-circle d-flex align-items-center justify-content-center" style="width:32px;height:32px;">
                        <i class="{{ $style['icon'] }}"></i>
                    </div>
                </div>
                <div class="fs-4 fw-bold">{{ number_format($rj->total_ritase, 0, ',', '.') }} <span class="small fw-normal text-body-secondary">ritase</span></div>
                <div class="small text-body-secondary mt-1">
                    <span class="fw-semibold">{{ number_format($rj->total_netto, 2, ',', '.') }}</span> kg netto
                </div>
                <div class="small text-body-secondary">
                    Rp <span class="fw-semibold">{{ number_format($rj->total_tipping, 0, ',', '.') }}</span> tipping
                </div>
            </div>
        </div>
    </div>
    @endforeach
    {{-- Grand Total Card --}}
    <div class="col-xl-3 col-md-6">
        <div class="card border-start border-start-4 border-warning h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="text-body-secondary small fw-semibold text-uppercase">TOTAL KESELURUHAN</div>
                    <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center" style="width:32px;height:32px;">
                        <i class="cil-chart-pie"></i>
                    </div>
                </div>
                <div class="fs-4 fw-bold">{{ number_format($grandTotals->total_ritase ?? 0, 0, ',', '.') }} <span class="small fw-normal text-body-secondary">ritase</span></div>
                <div class="small text-body-secondary mt-1">
                    <span class="fw-semibold">{{ number_format($grandTotals->total_netto ?? 0, 2, ',', '.') }}</span> kg netto
                </div>
                <div class="small text-body-secondary">
                    Rp <span class="fw-semibold">{{ number_format($grandTotals->total_tipping ?? 0, 0, ',', '.') }}</span> tipping
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Pivot Table: Rekap per Tanggal per Jenis Klien --}}
<div class="card mb-4">
    <div class="card-header bg-light d-flex align-items-center justify-content-between">
        <strong><i class="cil-calendar me-2"></i>Rekap Harian per Jenis Klien</strong>
        <span class="badge bg-primary">{{ $pivotData->count() }} hari</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-sm align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th rowspan="2" class="align-middle text-center" style="min-width:110px;">Tanggal</th>
                        @foreach($jenisTypes as $jt)
                        <th colspan="3" class="text-center border-start">{{ $jt }}</th>
                        @endforeach
                        <th colspan="3" class="text-center border-start bg-warning bg-opacity-10">Total Harian</th>
                    </tr>
                    <tr>
                        @foreach($jenisTypes as $jt)
                        <th class="text-center border-start small">Ritase</th>
                        <th class="text-end small">Netto (kg)</th>
                        <th class="text-end small">Tipping (Rp)</th>
                        @endforeach
                        <th class="text-center border-start small">Ritase</th>
                        <th class="text-end small">Netto (kg)</th>
                        <th class="text-end small">Tipping (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pivotData as $row)
                    <tr>
                        <td class="fw-semibold text-center">{{ \Carbon\Carbon::parse($row['tanggal'])->format('d M Y') }}</td>
                        @foreach($jenisTypes as $jt)
                        @php $cell = $row['jenis'][$jt] ?? null; @endphp
                        <td class="text-center border-start">{{ $cell ? number_format($cell['total_ritase'], 0, ',', '.') : '-' }}</td>
                        <td class="text-end">{{ $cell ? number_format($cell['total_netto'], 2, ',', '.') : '-' }}</td>
                        <td class="text-end">{{ $cell ? number_format($cell['total_tipping'], 0, ',', '.') : '-' }}</td>
                        @endforeach
                        <td class="text-center border-start fw-bold">{{ number_format($row['total_ritase'], 0, ',', '.') }}</td>
                        <td class="text-end fw-bold">{{ number_format($row['total_netto'], 2, ',', '.') }}</td>
                        <td class="text-end fw-bold">{{ number_format($row['total_tipping'], 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="{{ 1 + (count($jenisTypes) * 3) + 3 }}" class="text-center py-4 text-body-secondary">Tidak ada data ritase pada periode ini.</td></tr>
                    @endforelse
                </tbody>
                @if($pivotData->count() > 0)
                <tfoot class="border-top border-2 fw-bold table-light">
                    <tr>
                        <td class="text-center">TOTAL</td>
                        @foreach($jenisTypes as $jt)
                        @php
                            $jtRekap = $rekapPerJenis->firstWhere('jenis', $jt);
                        @endphp
                        <td class="text-center border-start">{{ $jtRekap ? number_format($jtRekap->total_ritase, 0, ',', '.') : '-' }}</td>
                        <td class="text-end">{{ $jtRekap ? number_format($jtRekap->total_netto, 2, ',', '.') : '-' }}</td>
                        <td class="text-end">{{ $jtRekap ? number_format($jtRekap->total_tipping, 0, ',', '.') : '-' }}</td>
                        @endforeach
                        <td class="text-center border-start">{{ number_format($grandTotals->total_ritase ?? 0, 0, ',', '.') }}</td>
                        <td class="text-end">{{ number_format($grandTotals->total_netto ?? 0, 2, ',', '.') }}</td>
                        <td class="text-end">{{ number_format($grandTotals->total_tipping ?? 0, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>

{{-- Rekap per Klien Detail --}}
<div class="card mb-4">
    <div class="card-header bg-light d-flex align-items-center justify-content-between">
        <strong><i class="cil-people me-2"></i>Detail Rekap per Klien</strong>
        <span class="badge bg-primary">{{ $rekapPerKlien->count() }} klien</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-sm align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Nama Klien</th>
                        <th>Jenis</th>
                        <th class="text-center">Total Ritase</th>
                        <th class="text-end">Berat Netto (kg)</th>
                        <th class="text-end">Biaya Tipping (Rp)</th>
                        <th class="text-end">Rata-rata Netto/Ritase</th>
                    </tr>
                </thead>
                <tbody>
                    @php $currentJenis = null; @endphp
                    @foreach($rekapPerKlien as $index => $rk)
                        @if($currentJenis !== $rk->jenis)
                        @php $currentJenis = $rk->jenis; @endphp
                        <tr class="table-light">
                            <td colspan="7" class="fw-bold small text-uppercase">
                                @php $jColor = $jenisColors[$rk->jenis] ?? ['bg' => 'bg-dark']; @endphp
                                <span class="badge {{ $jColor['bg'] }} me-1">{{ $rk->jenis }}</span>
                            </td>
                        </tr>
                        @endif
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td class="fw-semibold">{{ $rk->nama_klien }}</td>
                            <td>
                                @php $color = $jenisColors[$rk->jenis]['bg'] ?? 'bg-dark'; @endphp
                                <span class="badge {{ $color }}">{{ $rk->jenis }}</span>
                            </td>
                            <td class="text-center">{{ number_format($rk->total_ritase, 0, ',', '.') }}</td>
                            <td class="text-end">{{ number_format($rk->total_netto, 2, ',', '.') }}</td>
                            <td class="text-end">{{ number_format($rk->total_tipping, 0, ',', '.') }}</td>
                            <td class="text-end text-body-secondary">{{ $rk->total_ritase > 0 ? number_format($rk->total_netto / $rk->total_ritase, 2, ',', '.') : '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
                @if($rekapPerKlien->count() > 0)
                <tfoot class="border-top border-2 fw-bold table-light">
                    <tr>
                        <td colspan="3" class="text-end">TOTAL KESELURUHAN</td>
                        <td class="text-center">{{ number_format($grandTotals->total_ritase ?? 0, 0, ',', '.') }}</td>
                        <td class="text-end">{{ number_format($grandTotals->total_netto ?? 0, 2, ',', '.') }}</td>
                        <td class="text-end">{{ number_format($grandTotals->total_tipping ?? 0, 0, ',', '.') }}</td>
                        <td class="text-end text-body-secondary">{{ ($grandTotals->total_ritase ?? 0) > 0 ? number_format(($grandTotals->total_netto ?? 0) / $grandTotals->total_ritase, 2, ',', '.') : '-' }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>

<!-- Modal Preview -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header d-print-none">
                <h5 class="modal-title" id="previewModalLabel">Preview Rekap Ritase</h5>
                <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-light p-0">
                <div id="printArea" class="bg-white p-5 shadow-sm mx-auto" style="max-width: 29.7cm; min-height: 21cm;">
                    <x-kop-surat />

                    <div class="text-center mb-4">
                        <h4 class="fw-bold text-uppercase mb-1">REKAP RITASE PER TANGGAL & JENIS KLIEN</h4>
                        <p class="text-secondary mb-0">Periode: {{ \Carbon\Carbon::parse($dari)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($sampai)->format('d/m/Y') }}</p>
                        @if($isApproved !== null && $isApproved !== '')
                            <p class="text-secondary small">Status Approval: {{ $isApproved == 1 ? 'Approved' : 'Not Approved' }}</p>
                        @endif
                    </div>

                    {{-- Summary Per Jenis --}}
                    <h6 class="fw-bold mb-2">Ringkasan per Jenis Klien</h6>
                    <table class="table table-bordered table-sm border-dark" style="width: 60%; margin-bottom: 20px;">
                        <thead class="table-light border-dark">
                            <tr>
                                <th>Jenis Klien</th>
                                <th class="text-center">Total Ritase</th>
                                <th class="text-end">Netto (kg)</th>
                                <th class="text-end">Tipping (Rp)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rekapPerJenis as $rj)
                            <tr>
                                <td>{{ $rj->jenis }}</td>
                                <td class="text-center">{{ number_format($rj->total_ritase, 0, ',', '.') }}</td>
                                <td class="text-end">{{ number_format($rj->total_netto, 2, ',', '.') }}</td>
                                <td class="text-end">{{ number_format($rj->total_tipping, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="fw-bold">
                            <tr>
                                <td>TOTAL</td>
                                <td class="text-center">{{ number_format($grandTotals->total_ritase ?? 0, 0, ',', '.') }}</td>
                                <td class="text-end">{{ number_format($grandTotals->total_netto ?? 0, 2, ',', '.') }}</td>
                                <td class="text-end">{{ number_format($grandTotals->total_tipping ?? 0, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>

                    {{-- Pivot Harian --}}
                    <h6 class="fw-bold mb-2">Rekap Harian</h6>
                    <table class="table table-bordered border-dark table-sm" style="font-size: 10px;">
                        <thead class="table-light border-dark">
                            <tr>
                                <th rowspan="2" class="align-middle text-center">Tanggal</th>
                                @foreach($jenisTypes as $jt)
                                <th colspan="3" class="text-center border-start">{{ $jt }}</th>
                                @endforeach
                                <th colspan="3" class="text-center border-start">Total</th>
                            </tr>
                            <tr>
                                @foreach($jenisTypes as $jt)
                                <th class="text-center border-start">Rit</th>
                                <th class="text-end">Netto</th>
                                <th class="text-end">Tipping</th>
                                @endforeach
                                <th class="text-center border-start">Rit</th>
                                <th class="text-end">Netto</th>
                                <th class="text-end">Tipping</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pivotData as $row)
                            <tr>
                                <td class="text-center">{{ \Carbon\Carbon::parse($row['tanggal'])->format('d/m/Y') }}</td>
                                @foreach($jenisTypes as $jt)
                                @php $cell = $row['jenis'][$jt] ?? null; @endphp
                                <td class="text-center border-start">{{ $cell ? number_format($cell['total_ritase'], 0, ',', '.') : '-' }}</td>
                                <td class="text-end">{{ $cell ? number_format($cell['total_netto'], 2, ',', '.') : '-' }}</td>
                                <td class="text-end">{{ $cell ? number_format($cell['total_tipping'], 0, ',', '.') : '-' }}</td>
                                @endforeach
                                <td class="text-center border-start fw-bold">{{ number_format($row['total_ritase'], 0, ',', '.') }}</td>
                                <td class="text-end fw-bold">{{ number_format($row['total_netto'], 2, ',', '.') }}</td>
                                <td class="text-end fw-bold">{{ number_format($row['total_tipping'], 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="fw-bold border-dark">
                            <tr class="table-light">
                                <td class="text-center">TOTAL</td>
                                @foreach($jenisTypes as $jt)
                                @php $jtRekap = $rekapPerJenis->firstWhere('jenis', $jt); @endphp
                                <td class="text-center border-start">{{ $jtRekap ? number_format($jtRekap->total_ritase, 0, ',', '.') : '-' }}</td>
                                <td class="text-end">{{ $jtRekap ? number_format($jtRekap->total_netto, 2, ',', '.') : '-' }}</td>
                                <td class="text-end">{{ $jtRekap ? number_format($jtRekap->total_tipping, 0, ',', '.') : '-' }}</td>
                                @endforeach
                                <td class="text-center border-start">{{ number_format($grandTotals->total_ritase ?? 0, 0, ',', '.') }}</td>
                                <td class="text-end">{{ number_format($grandTotals->total_netto ?? 0, 2, ',', '.') }}</td>
                                <td class="text-end">{{ number_format($grandTotals->total_tipping ?? 0, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>

                    {{-- Per Klien Detail --}}
                    <h6 class="fw-bold mb-2 mt-4">Detail per Klien</h6>
                    <table class="table table-bordered border-dark table-sm">
                        <thead class="table-light border-dark">
                            <tr>
                                <th class="text-center" style="width: 40px;">No</th>
                                <th>Nama Klien</th>
                                <th>Jenis</th>
                                <th class="text-center">Ritase</th>
                                <th class="text-end">Netto (kg)</th>
                                <th class="text-end">Tipping (Rp)</th>
                                <th class="text-end">Avg Netto/Rit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rekapPerKlien as $index => $rk)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>{{ $rk->nama_klien }}</td>
                                <td>{{ $rk->jenis }}</td>
                                <td class="text-center">{{ number_format($rk->total_ritase, 0, ',', '.') }}</td>
                                <td class="text-end">{{ number_format($rk->total_netto, 2, ',', '.') }}</td>
                                <td class="text-end">{{ number_format($rk->total_tipping, 0, ',', '.') }}</td>
                                <td class="text-end">{{ $rk->total_ritase > 0 ? number_format($rk->total_netto / $rk->total_ritase, 2, ',', '.') : '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="fw-bold">
                            <tr class="table-light border-dark">
                                <td colspan="3" class="text-end">TOTAL</td>
                                <td class="text-center">{{ number_format($grandTotals->total_ritase ?? 0, 0, ',', '.') }}</td>
                                <td class="text-end">{{ number_format($grandTotals->total_netto ?? 0, 2, ',', '.') }}</td>
                                <td class="text-end">{{ number_format($grandTotals->total_tipping ?? 0, 0, ',', '.') }}</td>
                                <td class="text-end">{{ ($grandTotals->total_ritase ?? 0) > 0 ? number_format(($grandTotals->total_netto ?? 0) / $grandTotals->total_ritase, 2, ',', '.') : '-' }}</td>
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
        @page {
            size: landscape;
        }
    }
</style>
@endpush
