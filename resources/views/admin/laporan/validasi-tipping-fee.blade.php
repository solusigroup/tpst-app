@extends('layouts.admin')
@section('title', 'Validasi Tipping Fee')

@section('content')

<div class="page-header d-print-none">
    <div>
        <h1 class="d-flex align-items-center gap-2">
            <i class="cil-shield-alt text-warning"></i>
            Validasi Tipping Fee
        </h1>
        <p class="text-body-secondary mb-0 small">
            Periksa konsistensi perhitungan biaya tipping dengan perjanjian tarif klien (tonase × tarif/ton)
        </p>
    </div>
</div>

{{-- Filter --}}
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-auto">
                <label class="form-label mb-0 small text-body-secondary">Dari</label>
                <input type="date" name="dari" class="form-control" value="{{ $dari }}">
            </div>
            <div class="col-auto">
                <label class="form-label mb-0 small text-body-secondary">Sampai</label>
                <input type="date" name="sampai" class="form-control" value="{{ $sampai }}">
            </div>
            <div class="col-auto">
                <label class="form-label mb-0 small text-body-secondary">Klien</label>
                <select name="klien_id" class="form-select" style="min-width: 200px;">
                    <option value="">-- Semua Klien --</option>
                    @foreach($kliens as $k)
                        <option value="{{ $k->id }}" {{ $klienId == $k->id ? 'selected' : '' }}>
                            {{ $k->nama_klien }} ({{ $k->jenis_tarif }} - Rp {{ number_format($k->besaran_tarif, 0, ',', '.') }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <label class="form-label mb-0 small text-body-secondary">Tampilkan</label>
                <select name="only_error" class="form-select">
                    <option value="0" {{ !$onlyError ? 'selected' : '' }}>Semua Data</option>
                    <option value="1" {{ $onlyError ? 'selected' : '' }}>Hanya Anomali</option>
                </select>
            </div>
            <div class="col-auto">
                <button class="btn btn-primary" type="submit">
                    <i class="cil-filter me-1"></i> Validasi
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Summary Cards --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-body-secondary small mb-1">Total Ritase Diperiksa</div>
                        <div class="fs-3 fw-bold text-dark">{{ number_format($results->count(), 0, ',', '.') }}</div>
                    </div>
                    <div class="p-2 bg-primary bg-opacity-10 rounded-3">
                        <i class="cil-list fs-4 text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100 {{ $totalAnomalic > 0 ? 'border-danger border' : '' }}">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-body-secondary small mb-1">Ritase Anomali</div>
                        <div class="fs-3 fw-bold {{ $totalAnomalic > 0 ? 'text-danger' : 'text-success' }}">
                            {{ number_format($totalAnomalic, 0, ',', '.') }}
                        </div>
                        @if($results->count() > 0)
                            <div class="small text-body-secondary">
                                {{ number_format(($totalAnomalic / $results->count()) * 100, 1) }}% dari total
                            </div>
                        @endif
                    </div>
                    <div class="p-2 {{ $totalAnomalic > 0 ? 'bg-danger' : 'bg-success' }} bg-opacity-10 rounded-3">
                        <i class="cil-{{ $totalAnomalic > 0 ? 'warning' : 'check-circle' }} fs-4 text-{{ $totalAnomalic > 0 ? 'danger' : 'success' }}"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-body-secondary small mb-1">Total Tipping Aktual (DB)</div>
                        <div class="fs-5 fw-bold text-dark">Rp {{ number_format($totalAktual, 0, ',', '.') }}</div>
                    </div>
                    <div class="p-2 bg-info bg-opacity-10 rounded-3">
                        <i class="cil-dollar fs-4 text-info"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100 {{ abs($totalSelisih) > 0 ? 'border-warning border' : '' }}">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-body-secondary small mb-1">Selisih (Aktual - Seharusnya)</div>
                        <div class="fs-5 fw-bold {{ $totalSelisih > 0 ? 'text-danger' : ($totalSelisih < 0 ? 'text-warning' : 'text-success') }}">
                            {{ $totalSelisih >= 0 ? '+' : '' }}Rp {{ number_format($totalSelisih, 0, ',', '.') }}
                        </div>
                        <div class="small text-body-secondary">
                            Seharusnya: Rp {{ number_format($totalExpected, 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="p-2 bg-warning bg-opacity-10 rounded-3">
                        <i class="cil-swap-horizontal fs-4 text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Penjelasan Rumus --}}
<div class="alert alert-info d-flex gap-3 align-items-start mb-4">
    <i class="cil-info fs-5 mt-1 flex-shrink-0"></i>
    <div>
        <strong>Formula Validasi:</strong>
        <ul class="mb-0 mt-1">
            <li><strong>Per Ton:</strong> <code>Biaya Tipping = (Berat Netto kg ÷ 1000) × Tarif per Ton</code>
                &nbsp;&mdash;&nbsp; Contoh: Dinas Lingkungan Hidup = Netto Ton × Rp 80.000</li>
            <li><strong>Per Ritase:</strong> <code>Biaya Tipping = Tarif per Ritase</code> (flat per kunjungan)</li>
        </ul>
        <div class="mt-1 text-body-secondary small">
            Toleransi: ±Rp 0,50 (pembulatan desimal). Data yang melebihi toleransi ditandai <span class="badge bg-danger">Anomali</span>.
        </div>
    </div>
</div>

{{-- Tabel Detail Ritase --}}
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <strong>Detail Validasi Per Ritase</strong>
        <span class="badge bg-{{ $totalAnomalic > 0 ? 'danger' : 'success' }} fs-6">
            {{ $totalAnomalic }} Anomali Ditemukan
        </span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 small">
                <thead class="table-light">
                    <tr>
                        <th>Tanggal</th>
                        <th>No Tiket</th>
                        <th>Klien</th>
                        <th>Jenis Tarif</th>
                        <th class="text-end">Netto (kg)</th>
                        <th class="text-end">Tarif</th>
                        <th class="text-end text-info">Seharusnya</th>
                        <th class="text-end">Aktual (DB)</th>
                        <th class="text-end fw-bold">Selisih</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Invoice</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($results as $r)
                    <tr class="{{ $r->is_anomali ? 'table-danger' : '' }}">
                        <td>{{ \Carbon\Carbon::parse($r->waktu_masuk)->format('d/m/Y') }}</td>
                        <td>
                            <a href="{{ route('admin.ritase.show', $r->id) }}" class="fw-semibold text-decoration-none">
                                {{ $r->nomor_tiket }}
                            </a>
                        </td>
                        <td>
                            <div>{{ $r->klien->nama_klien ?? '-' }}</div>
                            <span class="badge bg-{{ $r->klien->jenis === 'DLH' ? 'info' : 'primary' }} text-white">
                                {{ $r->klien->jenis ?? '-' }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-secondary">{{ $r->jenis_tarif }}</span>
                        </td>
                        <td class="text-end">{{ number_format($r->berat_netto, 2, ',', '.') }}</td>
                        <td class="text-end">Rp {{ number_format($r->besaran_tarif, 0, ',', '.') }}</td>
                        <td class="text-end text-info fw-semibold">
                            Rp {{ number_format($r->expected, 0, ',', '.') }}
                        </td>
                        <td class="text-end {{ $r->is_anomali ? 'fw-bold' : '' }}">
                            Rp {{ number_format($r->aktual, 0, ',', '.') }}
                        </td>
                        <td class="text-end fw-bold {{ $r->selisih > 0.5 ? 'text-danger' : ($r->selisih < -0.5 ? 'text-warning' : 'text-success') }}">
                            @if($r->is_anomali)
                                {{ $r->selisih >= 0 ? '+' : '' }}Rp {{ number_format($r->selisih, 0, ',', '.') }}
                            @else
                                <span class="text-success">✓ OK</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($r->is_anomali)
                                <span class="badge bg-danger">⚠ Anomali</span>
                            @else
                                <span class="badge bg-success">✓ Valid</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($r->invoice_id)
                                <a href="{{ route('admin.invoice.show', $r->invoice_id) }}" class="badge bg-{{ $r->status_invoice === 'Paid' ? 'success' : ($r->status_invoice === 'Sent' ? 'info' : 'secondary') }} text-decoration-none">
                                    #{{ $r->invoice_id }}
                                </a>
                            @else
                                <span class="text-body-secondary">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="text-center py-5 text-body-secondary">
                            <i class="cil-check-circle fs-2 d-block mb-2 text-success"></i>
                            Tidak ada data pada periode dan filter yang dipilih.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot class="border-top border-2 fw-bold table-light">
                    <tr>
                        <td colspan="6" class="text-end">TOTAL ({{ number_format($results->count(), 0, ',', '.') }} Ritase)</td>
                        <td class="text-end text-info">Rp {{ number_format($totalExpected, 0, ',', '.') }}</td>
                        <td class="text-end">Rp {{ number_format($totalAktual, 0, ',', '.') }}</td>
                        <td class="text-end {{ $totalSelisih > 0 ? 'text-danger' : ($totalSelisih < 0 ? 'text-warning' : 'text-success') }}">
                            {{ $totalSelisih >= 0 ? '+' : '' }}Rp {{ number_format($totalSelisih, 0, ',', '.') }}
                        </td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

{{-- Validasi Invoice DLH --}}
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <strong>Validasi Invoice Dinas Lingkungan Hidup (DLH)</strong>
        @php $anomaliInvoice = $invoiceValidation->filter(fn($i) => $i->is_anomali)->count(); @endphp
        <span class="badge bg-{{ $anomaliInvoice > 0 ? 'danger' : 'success' }} fs-6">
            {{ $anomaliInvoice }} Anomali Invoice
        </span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 small">
                <thead class="table-light">
                    <tr>
                        <th>Nomor Invoice</th>
                        <th>Klien</th>
                        <th class="text-center">Periode</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Jml Ritase</th>
                        <th class="text-end">Total Netto (kg)</th>
                        <th class="text-end text-info">Tagihan dari Netto</th>
                        <th class="text-end">Total Tagihan Invoice</th>
                        <th class="text-end fw-bold">Selisih</th>
                        <th class="text-center">Validasi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoiceValidation as $inv)
                    <tr class="{{ $inv->is_anomali ? 'table-warning' : '' }}">
                        <td>
                            <a href="{{ route('admin.invoice.show', $inv->id) }}" class="fw-semibold text-decoration-none">
                                {{ $inv->nomor_invoice }}
                            </a>
                        </td>
                        <td>{{ $inv->klien->nama_klien ?? '-' }}</td>
                        <td class="text-center">{{ $inv->periode }}</td>
                        <td class="text-center">
                            @php $statusColors = ['Draft'=>'secondary','Sent'=>'info','Paid'=>'success','Canceled'=>'danger']; @endphp
                            <span class="badge bg-{{ $statusColors[$inv->status] ?? 'secondary' }}">{{ $inv->status }}</span>
                        </td>
                        <td class="text-center">{{ $inv->ritase_count }}</td>
                        <td class="text-end">{{ number_format($inv->total_netto, 2, ',', '.') }}</td>
                        <td class="text-end text-info fw-semibold">
                            Rp {{ number_format($inv->expected_netto, 0, ',', '.') }}
                        </td>
                        <td class="text-end">
                            Rp {{ number_format($inv->total_tagihan, 0, ',', '.') }}
                        </td>
                        <td class="text-end fw-bold {{ $inv->selisih > 1 ? 'text-danger' : ($inv->selisih < -1 ? 'text-warning' : 'text-success') }}">
                            @if($inv->is_anomali)
                                {{ $inv->selisih >= 0 ? '+' : '' }}Rp {{ number_format($inv->selisih, 0, ',', '.') }}
                            @else
                                <span class="text-success">✓ OK</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($inv->is_anomali)
                                <span class="badge bg-warning text-dark">⚠ Periksa</span>
                            @else
                                <span class="badge bg-success">✓ Valid</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-4 text-body-secondary">Tidak ada invoice DLH ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($invoiceValidation->where('is_anomali', true)->count() > 0)
    <div class="card-footer bg-light">
        <div class="alert alert-warning mb-0 d-flex gap-2">
            <i class="cil-warning mt-1 flex-shrink-0"></i>
            <div>
                <strong>Perhatian:</strong> Terdapat invoice DLH dengan total tagihan yang tidak sesuai dengan jumlah biaya tipping dari ritase terlampir.
                Kemungkinan penyebab:
                <ul class="mb-0 mt-1">
                    <li>Ritase di-approve saat tarif klien berbeda dengan tarif perjanjian</li>
                    <li>Data biaya tipping diinput manual (bukan hasil kalkulasi otomatis)</li>
                    <li>Ritase telah dihapus setelah masuk ke invoice (orphaned invoice)</li>
                    <li>Invoice total diedit manual tanpa melalui recalculate</li>
                </ul>
                <div class="mt-2">
                    <strong>Langkah perbaikan:</strong> Buka detail invoice &rarr; klik tombol <em>Recalculate</em> untuk menghitung ulang total berdasarkan ritase aktual.
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

{{-- Legend --}}
<div class="card border-0 bg-light mb-4">
    <div class="card-body py-3">
        <div class="row align-items-center">
            <div class="col-auto"><strong class="small text-body-secondary">Keterangan Warna:</strong></div>
            <div class="col-auto"><span class="badge bg-danger">⚠ Anomali</span> Selisih &gt; Rp 0,50</div>
            <div class="col-auto"><span class="badge bg-success">✓ Valid</span> Sesuai perjanjian tarif</div>
            <div class="col-auto"><span class="badge bg-warning text-dark">⚠ Periksa</span> Total invoice tidak cocok dengan ritase</div>
            <div class="col-auto text-info fw-semibold small">Biru = Nilai seharusnya (formula)</div>
        </div>
    </div>
</div>

@endsection
