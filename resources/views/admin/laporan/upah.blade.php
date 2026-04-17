@extends('layouts.admin')
@section('title', 'Laporan Perhitungan Upah Karyawan')

@section('content')

<div class="page-header d-print-none">
    <div><h1>Laporan Perhitungan Upah Karyawan</h1></div>
    <div class="d-flex gap-2 align-items-center">
        <button type="button" class="btn btn-outline-primary shadow-sm" data-coreui-toggle="modal" data-coreui-target="#previewModal">
            <i class="cil-zoom-in me-1"></i> Preview & Cetak
        </button>
        <div class="btn-group shadow-sm">
            <a href="{{ route('admin.laporan-operasional.upah', array_merge(request()->all(), ['export' => 'pdf'])) }}" target="_blank" class="btn btn-danger" title="Export PDF">
                <i class="cil-file me-1"></i> PDF
            </a>
            <a href="{{ route('admin.laporan-operasional.upah', array_merge(request()->all(), ['export' => 'excel'])) }}" class="btn btn-success" title="Export Excel">
                <i class="cil-spreadsheet me-1"></i> Excel
            </a>
        </div>
    </div>
</div>

<div class="card mb-4 d-print-none">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-auto">
                <label class="form-label mb-0 small text-body-secondary">Bulan</label>
                <select name="month" class="form-select">
                    <option value="">-- Bebas --</option>
                    @foreach([
                        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
                        '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
                        '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                    ] as $m => $name)
                        <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <label class="form-label mb-0 small text-body-secondary">Tahun</label>
                <select name="year" class="form-select">
                    @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                        <option value="{{ $y }}" {{ request('year', date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-auto">
                <div class="vr h-100 mx-2"></div>
            </div>
            <div class="col-auto">
                <label class="form-label mb-0 small text-body-secondary">Dari</label>
                <input type="date" name="dari" class="form-control" value="{{ $dari }}">
            </div>
            <div class="col-auto">
                <label class="form-label mb-0 small text-body-secondary">Sampai</label>
                <input type="date" name="sampai" class="form-control" value="{{ $sampai }}">
            </div>
            <div class="col-auto">
                <label class="form-label mb-0 small text-body-secondary">Skema Upah</label>
                <select name="skema_upah" class="form-select">
                    <option value="">-- Semua --</option>
                    <option value="harian" {{ request('skema_upah') == 'harian' ? 'selected' : '' }}>Harian</option>
                    <option value="bulanan" {{ request('skema_upah') == 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                    <option value="borongan" {{ request('skema_upah') == 'borongan' ? 'selected' : '' }}>Borongan</option>
                </select>
            </div>
            <div class="col-auto">
                <button class="btn btn-primary" type="submit">
                    <i class="cil-filter me-1"></i> Filter
                </button>
                <a href="{{ route('admin.laporan-operasional.upah') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="row mb-4 d-print-none">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm bg-primary text-white">
            <div class="card-body">
                <div class="text-white-50 small mb-1">Total Upah (Gross)</div>
                <div class="fs-4 fw-bold">Rp {{ number_format($totals->total_wage, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm bg-success text-white">
            <div class="card-body">
                <div class="text-white-50 small mb-1">Sudah Dibayar</div>
                <div class="fs-4 fw-bold">Rp {{ number_format($totals->total_paid, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm bg-danger text-white">
            <div class="card-body">
                <div class="text-white-50 small mb-1">Belum Dibayar</div>
                <div class="fs-4 fw-bold">Rp {{ number_format($totals->total_unpaid, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>
</div>

<div class="card" id="printable">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Periode</th>
                        <th>Karyawan</th>
                        <th>Skema</th>
                        <th class="text-end">Output</th>
                        <th class="text-end">Total Upah</th>
                        <th class="text-end">Sdh Dibayar</th>
                        <th class="text-end">Belum Dibayar</th>
                        <th class="text-center">Status</th>
                        <th>Tgl Bayar</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $r)
                    <tr>
                        <td>
                            <div class="small text-body-secondary">{{ \Carbon\Carbon::parse($r->week_start)->format('d/m/Y') }}</div>
                            <div class="small text-body-secondary">{{ \Carbon\Carbon::parse($r->week_end)->format('d/m/Y') }}</div>
                        </td>
                        <td>
                            <strong>{{ $r->user->name ?? '-' }}</strong>
                            <div class="small text-body-secondary">{{ $r->user->position ?? '-' }}</div>
                        </td>
                        <td><span class="text-capitalize">{{ $r->user->salary_type ?? '-' }}</span></td>
                        <td class="text-end">{{ number_format($r->total_quantity, 2, ',', '.') }}</td>
                        <td class="text-end fw-bold">Rp {{ number_format($r->total_wage, 0, ',', '.') }}</td>
                        <td class="text-end text-success">
                            {{ $r->status === 'paid' ? 'Rp ' . number_format($r->total_wage, 0, ',', '.') : '-' }}
                        </td>
                        <td class="text-end text-danger">
                            {{ $r->status !== 'paid' ? 'Rp ' . number_format($r->total_wage, 0, ',', '.') : '-' }}
                        </td>
                        <td class="text-center">
                            @php 
                                $statusColors = [
                                    'pending' => 'warning',
                                    'approved' => 'info',
                                    'paid' => 'success'
                                ]; 
                                $statusLabels = [
                                    'pending' => 'Pending',
                                    'approved' => 'Disetujui',
                                    'paid' => 'Dibayar'
                                ];
                            @endphp
                            <span class="badge bg-{{ $statusColors[$r->status] ?? 'secondary' }}">
                                {{ $statusLabels[$r->status] ?? $r->status }}
                            </span>
                        </td>
                        <td>{{ $r->paid_date ? \Carbon\Carbon::parse($r->paid_date)->format('d/m/Y') : '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4 text-body-secondary">
                            Belum ada data upah pada periode ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot class="border-top border-2 fw-bold bg-light">
                    <tr>
                        <td colspan="4" class="text-end">TOTAL ({{ number_format($totals->total_rows, 0, ',', '.') }} Record)</td>
                        <td class="text-end text-primary">Rp {{ number_format($totals->total_wage, 0, ',', '.') }}</td>
                        <td class="text-end text-success">Rp {{ number_format($totals->total_paid, 0, ',', '.') }}</td>
                        <td class="text-end text-danger">Rp {{ number_format($totals->total_unpaid, 0, ',', '.') }}</td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    @if($rows->hasPages()) 
        <div class="card-footer bg-white d-print-none">
            {{ $rows->links() }}
        </div> 
    @endif
</div>

<!-- Modal Preview -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header d-print-none">
                <h5 class="modal-title" id="previewModalLabel">Preview Laporan Upah Karyawan</h5>
                <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-light p-0">
                <div id="printArea" class="bg-white p-5 shadow-sm mx-auto" style="max-width: 21cm; min-height: 29.7cm;">
                    <x-kop-surat />
                    
                    <div class="text-center mb-4">
                        <h4 class="fw-bold text-uppercase mb-1">LAPORAN PERHITUNGAN UPAH KARYAWAN</h4>
                        <p class="text-secondary">Periode: {{ \Carbon\Carbon::parse($dari)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($sampai)->format('d/m/Y') }}</p>
                    </div>

                    <div class="row mb-3">
                        <div class="col-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td style="width: 150px;">Skema Upah</td>
                                    <td>: {{ $skemaUpah ?: 'Semua' }}</td>
                                </tr>
                                <tr>
                                    <td>Total Record</td>
                                    <td>: {{ $totals->total_rows }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td style="width: 150px;">Total Upah</td>
                                    <td>: <strong>Rp {{ number_format($totals->total_wage, 0, ',', '.') }}</strong></td>
                                </tr>
                                <tr>
                                    <td>Sudah Dibayar</td>
                                    <td>: <span class="text-success">Rp {{ number_format($totals->total_paid, 0, ',', '.') }}</span></td>
                                </tr>
                                <tr>
                                    <td>Belum Dibayar</td>
                                    <td>: <span class="text-danger">Rp {{ number_format($totals->total_unpaid, 0, ',', '.') }}</span></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <table class="table table-bordered border-dark table-sm">
                        <thead class="table-light border-dark">
                            <tr>
                                <th class="text-center" style="width: 40px;">No</th>
                                <th>Periode</th>
                                <th>Nama Karyawan</th>
                                <th>Skema</th>
                                <th class="text-end">Total Upah</th>
                                <th class="text-end">Sdh Dibayar</th>
                                <th class="text-end">Blm Dibayar</th>
                                <th class="text-center">Status</th>
                                <th>Tgl Bayar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php 
                                $allRowsForPrint = \App\Models\WageCalculation::with('user')
                                    ->join('users', 'wage_calculations.user_id', '=', 'users.id')
                                    ->select('wage_calculations.*')
                                    ->when($dari, fn ($q) => $q->whereDate('week_start', '>=', $dari))
                                    ->when($sampai, fn ($q) => $q->whereDate('week_start', '<=', $sampai))
                                    ->when($skemaUpah, fn ($q) => $q->where('users.salary_type', $skemaUpah))
                                    ->orderByDesc('week_start')
                                    ->get(); 
                                $statusLabels = [
                                    'pending' => 'Pending',
                                    'approved' => 'Disetujui',
                                    'paid' => 'Dibayar'
                                ];
                            @endphp
                            @foreach($allRowsForPrint as $index => $r)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td class="small">{{ \Carbon\Carbon::parse($r->week_start)->format('d/m/y') }}-{{ \Carbon\Carbon::parse($r->week_end)->format('d/m/y') }}</td>
                                <td>{{ $r->user->name ?? '-' }}</td>
                                <td class="text-capitalize small">{{ $r->user->salary_type ?? '-' }}</td>
                                <td class="text-end fw-bold">Rp {{ number_format($r->total_wage, 0, ',', '.') }}</td>
                                <td class="text-end">
                                    {{ $r->status === 'paid' ? 'Rp ' . number_format($r->total_wage, 0, ',', '.') : '-' }}
                                </td>
                                <td class="text-end">
                                    {{ $r->status !== 'paid' ? 'Rp ' . number_format($r->total_wage, 0, ',', '.') : '-' }}
                                </td>
                                <td class="text-center small">{{ $statusLabels[$r->status] ?? $r->status }}</td>
                                <td>{{ $r->paid_date ? \Carbon\Carbon::parse($r->paid_date)->format('d/m/y') : '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="border-dark fw-bold">
                            <tr>
                                <td colspan="4" class="text-end">TOTAL</td>
                                <td class="text-end">Rp {{ number_format($totals->total_wage, 0, ',', '.') }}</td>
                                <td class="text-end">Rp {{ number_format($totals->total_paid, 0, ',', '.') }}</td>
                                <td class="text-end">Rp {{ number_format($totals->total_unpaid, 0, ',', '.') }}</td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>

                    <div class="row mt-5">
                        <div class="col-8"></div>
                        <div class="col-4 text-center">
                            <p class="mb-5">Dicetak pada: {{ now()->format('d/m/Y H:i') }}</p>
                            <div class="mt-5">
                                <p class="fw-bold mb-0">( ____________________ )</p>
                                <p class="text-secondary small">Admin HRD / Operasional</p>
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
        .sidebar, .header, .mobile-bottom-nav, .modal-backdrop, .breadcrumb, .page-header, .card, form, .no-print, .d-print-none {
            display: none !important;
        }
        .wrapper { padding: 0 !important; margin: 0 !important; }
        .body { padding: 0 !important; margin: 0 !important; }
        .container-fluid { padding: 0 !important; margin: 0 !important; }
        .modal {
            display: block !important;
            position: absolute !important;
            left: 0 !important;
            top: 0 !important;
            width: 100% !important;
            opacity: 1 !important;
            visibility: visible !important;
            background: white !important;
        }
        .modal-dialog {
            max-width: 100% !important;
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        .modal-header, .modal-footer {
            display: none !important;
        }
        .modal-content, .modal-body {
            display: block !important;
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
            background: white !important;
            visibility: visible !important;
            opacity: 1 !important;
        }
        #printArea {
            visibility: visible !important;
            opacity: 1 !important;
            display: block !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        #printArea * {
            visibility: visible !important;
            opacity: 1 !important;
        }
    }
</style>
@endpush
