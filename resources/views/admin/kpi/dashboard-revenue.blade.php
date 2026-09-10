@extends('layouts.admin')

@section('title', 'Dashboard Revenue & Tipping Fee - TPST Performance Management')

@section('content')
<div class="container-fluid">
    {{-- Header & Filter Bar --}}
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body p-3">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <h4 class="mb-1 text-primary fw-bold">
                        <i class="cil-dollar me-2"></i>Dashboard Revenue & Tipping Fee Swasta
                    </h4>
                    <p class="text-muted mb-0 small">
                        Pencapaian Target Tipping Fee Komersial (Target Rp 20.000.000/Bln) & Commercial Data (PIC: <strong>Nita Khoirunisa</strong>)
                    </p>
                </div>
                <form action="{{ route('admin.kpi.dashboard.revenue') }}" method="GET" class="d-flex flex-wrap align-items-center gap-2">
                    <select name="filter" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                        <option value="mingguan" {{ $filter == 'mingguan' ? 'selected' : '' }}>Mingguan</option>
                        <option value="bulanan" {{ $filter == 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                        <option value="harian" {{ $filter == 'harian' ? 'selected' : '' }}>Harian</option>
                    </select>
                    <div class="input-group input-group-sm" style="width: auto;">
                        <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                        <span class="input-group-text">s/d</span>
                        <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                        <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Highlight Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card text-white bg-primary h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="fs-5 fw-semibold">Rp {{ number_format($globalKpi['realisasi_tipping'], 0, ',', '.') }}</div>
                            <div class="small text-white-50">Tipping Fee Terkumpul</div>
                        </div>
                        <i class="cil-money fs-2 text-white-50"></i>
                    </div>
                    @php
                        $targetTippingPeriode = (20000000 / 30) * $globalKpi['jumlah_hari'];
                        $achievePct = $targetTippingPeriode > 0 ? ($globalKpi['realisasi_tipping'] / $targetTippingPeriode) * 100 : 100;
                    @endphp
                    <div class="progress progress-white progress-thin mt-3" style="height: 6px;">
                        <div class="progress-bar bg-white" style="width: {{ min(100, $achievePct) }}%"></div>
                    </div>
                    <small class="text-white-50 d-block mt-2">Achievement: {{ round($achievePct, 1) }}%</small>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card text-white bg-success h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="fs-5 fw-semibold">Rp {{ number_format($totalPenjualanProduk, 0, ',', '.') }}</div>
                            <div class="small text-white-50">Penjualan Produk Sortir</div>
                        </div>
                        <i class="cil-cart fs-2 text-white-50"></i>
                    </div>
                    <div class="progress progress-white progress-thin mt-3" style="height: 6px;">
                        <div class="progress-bar bg-white" style="width: 100%"></div>
                    </div>
                    <small class="text-white-50 d-block mt-2">RDF, Plastik, Kardus, dll.</small>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card text-white bg-info h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="fs-5 fw-semibold">Rp {{ number_format($globalKpi['realisasi_tipping'] + $totalPenjualanProduk, 0, ',', '.') }}</div>
                            <div class="small text-white-50">Total Revenue TPST</div>
                        </div>
                        <i class="cil-chart-pie fs-2 text-white-50"></i>
                    </div>
                    <div class="progress progress-white progress-thin mt-3" style="height: 6px;">
                        <div class="progress-bar bg-white" style="width: 100%"></div>
                    </div>
                    <small class="text-white-50 d-block mt-2">Tipping Fee + Material Sales</small>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card text-white bg-warning h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="fs-4 fw-semibold text-dark">{{ $nitaKpi['total_skor'] }}%</div>
                            <div class="small text-dark text-opacity-75">Skor KPI Nita Khoirunisa</div>
                        </div>
                        <span class="badge bg-dark text-white">{{ $nitaKpi['predikat'] }}</span>
                    </div>
                    <div class="progress progress-thin mt-3" style="height: 6px;">
                        <div class="progress-bar bg-dark" style="width: {{ min(100, $nitaKpi['total_skor']) }}%"></div>
                    </div>
                    <small class="text-dark text-opacity-75 d-block mt-2">Admin, Data & Commercial (Bobot 30%)</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Rincian KPI Nita & Invoice Pelanggan --}}
    <div class="row g-4 mb-4">
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-dark">
                        <i class="cil-task me-2 text-primary"></i>KPI Nita (Admin, Data & Commercial)
                    </h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @foreach($nitaKpi['kpi_items'] as $item)
                        <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                            <div>
                                <div class="fw-semibold small">{{ $item['nama'] }}</div>
                                <div class="text-muted" style="font-size: 0.75rem;">Target: {{ $item['target'] }} | Realisasi: {{ $item['realisasi'] }}</div>
                            </div>
                            <div class="text-end">
                                <span class="badge {{ $item['nilai'] >= 100 ? 'bg-success' : 'bg-warning' }}">{{ $item['nilai'] }}%</span>
                                <div class="text-muted" style="font-size: 0.75rem;">Bobot: {{ $item['bobot'] }}%</div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-dark">
                        <i class="cil-description me-2 text-primary"></i>Tagihan & Invoice Klien Swasta
                    </h5>
                    <a href="{{ route('admin.invoice.index') }}" class="btn btn-sm btn-outline-primary">
                        Kelola Invoice
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>No Invoice</th>
                                    <th>Klien</th>
                                    <th>Tanggal</th>
                                    <th class="text-end">Total Tagihan</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($invoices as $inv)
                                <tr>
                                    <td class="fw-semibold">{{ $inv->nomor_invoice }}</td>
                                    <td>{{ $inv->klien ? $inv->klien->nama_klien : 'Klien Swasta' }}</td>
                                    <td>{{ $inv->tanggal_invoice ? $inv->tanggal_invoice->format('d/m/Y') : '-' }}</td>
                                    <td class="text-end fw-bold">Rp {{ number_format($inv->total_tagihan, 0, ',', '.') }}</td>
                                    <td>
                                        <span class="badge {{ $inv->status === 'paid' ? 'bg-success' : ($inv->status === 'sent' ? 'bg-info' : 'bg-secondary') }}">
                                            {{ strtoupper($inv->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        Tidak ada data invoice dalam periode ini.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Penjualan Hasil Pilahan & RDF --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold text-dark">
                <i class="cil-recycle me-2 text-success"></i>Pendapatan Penjualan Produk (RDF, Plastik, Logam, Kompos)
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Produk / Kategori</th>
                            <th>Klien Pembeli / Offtaker</th>
                            <th class="text-center">Berat (kg)</th>
                            <th class="text-end">Harga Satuan</th>
                            <th class="text-end">Total Nilai (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($penjualan as $p)
                        <tr>
                            <td>{{ $p->tanggal->format('d/m/Y') }}</td>
                            <td class="fw-semibold">{{ $p->wasteCategory ? $p->wasteCategory->name : $p->jenis_produk }}</td>
                            <td>{{ $p->klien ? $p->klien->nama_klien : '-' }}</td>
                            <td class="text-center">{{ number_format($p->berat_kg, 1) }} kg</td>
                            <td class="text-end">Rp {{ number_format($p->harga_satuan, 0, ',', '.') }}</td>
                            <td class="text-end fw-bold text-success">Rp {{ number_format($p->total_harga, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-3 text-muted">
                                Belum ada transaksi penjualan dalam periode ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
