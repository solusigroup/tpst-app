@extends('layouts.admin')
@section('title', 'Audit Trail - Detail Tracing')

@section('content')
<div class="page-header">
    <div>
        <h1>Audit Trail Transaksi</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.tracing.index') }}">Tracing</a></li>
                <li class="breadcrumb-item active">Detail</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('admin.tracing.index') }}" class="btn btn-outline-secondary"><i class="cil-arrow-left me-1"></i> Kembali</a>
</div>

{{-- Integrity Check Alerts --}}
@if(!empty($data['checks']))
    @if(!empty($data['checks']['invoice_missing']))
        <div class="alert alert-danger d-flex align-items-center mb-3">
            <i class="cil-warning fs-4 me-2"></i>
            <div><strong>Invoice belum dibuat.</strong> Transaksi ini belum memiliki invoice penagihan.</div>
        </div>
    @else
        @if(isset($data['checks']['has_jurnal']) && !$data['checks']['has_jurnal'])
            <div class="alert alert-danger d-flex align-items-center mb-2">
                <i class="cil-warning fs-4 me-2"></i>
                <div><strong>Jurnal GL tidak ditemukan.</strong> Invoice ini belum memiliki pencatatan jurnal umum.</div>
            </div>
        @endif
        @if(isset($data['checks']['has_bp']) && !$data['checks']['has_bp'])
            <div class="alert alert-warning d-flex align-items-center mb-2">
                <i class="cil-warning fs-4 me-2"></i>
                <div><strong>Buku Pembantu tidak ditemukan.</strong> Piutang belum tercatat di buku pembantu.</div>
            </div>
        @endif
        @if(isset($data['checks']['amount_match']) && !$data['checks']['amount_match'])
            <div class="alert alert-danger d-flex align-items-center mb-2">
                <i class="cil-warning fs-4 me-2"></i>
                <div>
                    <strong>Selisih Nominal.</strong>
                    Invoice: Rp {{ number_format($data['invoice']->total_tagihan ?? 0, 0, ',', '.') }}
                    vs Jurnal Debit: Rp {{ number_format($data['checks']['jurnal_debit'] ?? 0, 0, ',', '.') }}
                </div>
            </div>
        @endif
        @if(isset($data['checks']['status_consistent']) && !$data['checks']['status_consistent'])
            <div class="alert alert-warning d-flex align-items-center mb-2">
                <i class="cil-warning fs-4 me-2"></i>
                <div><strong>Status tidak sinkron.</strong> Status Invoice dan Buku Pembantu tidak konsisten.</div>
            </div>
        @endif
        @if(
            (isset($data['checks']['has_jurnal']) && $data['checks']['has_jurnal']) &&
            (isset($data['checks']['has_bp']) && $data['checks']['has_bp']) &&
            (!isset($data['checks']['amount_match']) || $data['checks']['amount_match']) &&
            (!isset($data['checks']['status_consistent']) || $data['checks']['status_consistent'])
        )
            <div class="alert alert-success d-flex align-items-center mb-3">
                <i class="cil-check-circle fs-4 me-2"></i>
                <div><strong>Semua alur transaksi terverifikasi.</strong> Tidak ditemukan ketidaksesuaian data.</div>
            </div>
        @endif
    @endif
@endif

{{-- Visual Flowchart --}}
<div class="card mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="cil-graph me-2"></i> Visual Alur Transaksi</h5>
    </div>
    <div class="card-body">
        <div class="d-flex align-items-stretch justify-content-between flex-wrap gap-0" style="min-height: 80px;">
            {{-- Step 1: Dokumen Operasional --}}
            @php
                $hasOp = ($data['operational']['ritase']->count() > 0 || $data['operational']['penjualan']->count() > 0);
            @endphp
            <div class="text-center flex-fill" style="min-width: 140px;">
                <div class="rounded-3 p-3 h-100 {{ $hasOp ? 'bg-success bg-opacity-10 border border-success' : 'bg-light border' }}">
                    <i class="cil-truck fs-3 d-block mb-1 {{ $hasOp ? 'text-success' : 'text-body-secondary' }}"></i>
                    <div class="fw-semibold small">1. Operasional</div>
                    <div class="text-body-secondary" style="font-size: 0.75rem;">
                        {{ $data['operational']['ritase']->count() }} Ritase,
                        {{ $data['operational']['penjualan']->count() }} Penjualan
                    </div>
                </div>
            </div>
            <div class="d-flex align-items-center px-1"><i class="cil-arrow-right fs-4 text-body-secondary"></i></div>

            {{-- Step 2: Invoice --}}
            @php $hasInv = $data['invoice'] !== null; @endphp
            <div class="text-center flex-fill" style="min-width: 140px;">
                <div class="rounded-3 p-3 h-100 {{ $hasInv ? 'bg-primary bg-opacity-10 border border-primary' : 'bg-light border' }}">
                    <i class="cil-description fs-3 d-block mb-1 {{ $hasInv ? 'text-primary' : 'text-body-secondary' }}"></i>
                    <div class="fw-semibold small">2. Invoice</div>
                    @if($hasInv)
                        <div class="text-body-secondary" style="font-size: 0.75rem;">{{ $data['invoice']->nomor_invoice }}</div>
                        @php $invC = ['Paid'=>'success','Sent'=>'info','Draft'=>'warning','Canceled'=>'danger']; @endphp
                        <span class="badge bg-{{ $invC[$data['invoice']->status] ?? 'secondary' }}" style="font-size: 0.65rem;">{{ $data['invoice']->status }}</span>
                    @else
                        <div class="text-danger" style="font-size: 0.75rem;">Belum ada</div>
                    @endif
                </div>
            </div>
            <div class="d-flex align-items-center px-1"><i class="cil-arrow-right fs-4 text-body-secondary"></i></div>

            {{-- Step 3: Jurnal GL --}}
            @php $hasJurnal = $data['jurnal'] !== null; @endphp
            <div class="text-center flex-fill" style="min-width: 140px;">
                <div class="rounded-3 p-3 h-100 {{ $hasJurnal ? 'bg-warning bg-opacity-10 border border-warning' : 'bg-light border' }}">
                    <i class="cil-book fs-3 d-block mb-1 {{ $hasJurnal ? 'text-warning' : 'text-body-secondary' }}"></i>
                    <div class="fw-semibold small">3. Jurnal GL</div>
                    @if($hasJurnal)
                        <div class="text-body-secondary" style="font-size: 0.75rem;">{{ $data['jurnal']->nomor_referensi }}</div>
                        <span class="badge bg-{{ $data['jurnal']->status === 'posted' ? 'success' : 'secondary' }}" style="font-size: 0.65rem;">{{ ucfirst($data['jurnal']->status) }}</span>
                    @else
                        <div class="text-danger" style="font-size: 0.75rem;">Belum ada</div>
                    @endif
                </div>
            </div>
            <div class="d-flex align-items-center px-1"><i class="cil-arrow-right fs-4 text-body-secondary"></i></div>

            {{-- Step 4: Buku Pembantu --}}
            @php $hasBP = $data['buku_pembantu'] !== null; @endphp
            <div class="text-center flex-fill" style="min-width: 140px;">
                <div class="rounded-3 p-3 h-100 {{ $hasBP ? 'bg-info bg-opacity-10 border border-info' : 'bg-light border' }}">
                    <i class="cil-library fs-3 d-block mb-1 {{ $hasBP ? 'text-info' : 'text-body-secondary' }}"></i>
                    <div class="fw-semibold small">4. Buku Pembantu</div>
                    @if($hasBP)
                        <span class="badge bg-{{ $data['buku_pembantu']->status === 'lunas' ? 'success' : 'warning' }}" style="font-size: 0.65rem;">{{ ucfirst($data['buku_pembantu']->status) }}</span>
                        <div class="text-body-secondary" style="font-size: 0.75rem;">
                            Rp {{ number_format($data['buku_pembantu']->jumlah, 0, ',', '.') }}
                        </div>
                    @else
                        <div class="text-danger" style="font-size: 0.75rem;">Belum ada</div>
                    @endif
                </div>
            </div>
            <div class="d-flex align-items-center px-1"><i class="cil-arrow-right fs-4 text-body-secondary"></i></div>

            {{-- Step 5: Pelunasan --}}
            @php $hasPay = $data['payment'] !== null; @endphp
            <div class="text-center flex-fill" style="min-width: 140px;">
                <div class="rounded-3 p-3 h-100 {{ $hasPay ? 'bg-success bg-opacity-10 border border-success' : 'bg-light border' }}">
                    <i class="cil-money fs-3 d-block mb-1 {{ $hasPay ? 'text-success' : 'text-body-secondary' }}"></i>
                    <div class="fw-semibold small">5. Pelunasan</div>
                    @if($hasPay)
                        <div class="text-body-secondary" style="font-size: 0.75rem;">{{ $data['payment']->nomor_referensi }}</div>
                        <div class="text-success" style="font-size: 0.75rem;">Terbayar</div>
                    @else
                        <div class="text-body-secondary" style="font-size: 0.75rem;">Belum ada</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Detailed Information Cards --}}
<div class="row g-3">
    {{-- Column 1: Operational + Invoice --}}
    <div class="col-lg-6">
        {{-- Operational Documents --}}
        <div class="card mb-3">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="cil-truck me-2 text-success"></i> Dokumen Operasional</h6>
            </div>
            <div class="card-body p-0">
                @if($data['operational']['ritase']->count() > 0)
                <h6 class="px-3 pt-3 pb-1 mb-0 small text-body-secondary text-uppercase">Ritase / Jasa Tipping</h6>
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead class="table-light"><tr><th>Klien</th><th>Waktu</th><th class="text-end">Biaya Tipping</th></tr></thead>
                        <tbody>
                            @foreach($data['operational']['ritase'] as $r)
                            <tr>
                                <td class="small">{{ $r->klien->nama_klien ?? '-' }}</td>
                                <td class="small">{{ $r->waktu_masuk ? \Carbon\Carbon::parse($r->waktu_masuk)->format('d/m/Y H:i') : '-' }}</td>
                                <td class="text-end small">Rp {{ number_format($r->biaya_tipping ?? 0, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif

                @if($data['operational']['penjualan']->count() > 0)
                <h6 class="px-3 pt-3 pb-1 mb-0 small text-body-secondary text-uppercase">Penjualan Hasil Pilahan</h6>
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead class="table-light"><tr><th>Klien</th><th>Jenis</th><th>Berat</th><th class="text-end">Total</th></tr></thead>
                        <tbody>
                            @foreach($data['operational']['penjualan'] as $p)
                            <tr>
                                <td class="small">{{ $p->klien->nama_klien ?? '-' }}</td>
                                <td class="small">{{ $p->jenis_produk }}</td>
                                <td class="small">{{ number_format($p->berat_kg, 2, ',', '.') }} kg</td>
                                <td class="text-end small">Rp {{ number_format($p->total_harga ?? 0, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif

                @if($data['operational']['ritase']->count() === 0 && $data['operational']['penjualan']->count() === 0)
                <div class="text-center text-body-secondary py-3">Tidak ada dokumen operasional terkait.</div>
                @endif
            </div>
        </div>

        {{-- Invoice Detail --}}
        <div class="card mb-3">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="cil-description me-2 text-primary"></i> Invoice Penagihan</h6>
            </div>
            <div class="card-body">
                @if($data['invoice'])
                    @php $inv = $data['invoice']; @endphp
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="text-body-secondary small">No. Invoice</div>
                            <div class="fw-semibold">{{ $inv->nomor_invoice }}</div>
                        </div>
                        <div class="col-6">
                            <div class="text-body-secondary small">Klien</div>
                            <div class="fw-semibold">{{ $inv->klien->nama_klien ?? '-' }}
                                <span class="badge bg-light text-dark border">{{ $inv->klien->jenis ?? '-' }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-body-secondary small">Total Tagihan</div>
                            <div class="fw-bold text-primary">Rp {{ number_format($inv->total_tagihan, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-6">
                            <div class="text-body-secondary small">Uang Muka / DP</div>
                            <div class="fw-semibold">Rp {{ number_format($inv->uang_muka ?? 0, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-6">
                            <div class="text-body-secondary small">Sisa Tagihan</div>
                            <div class="fw-bold text-danger">Rp {{ number_format($inv->total_tagihan - ($inv->uang_muka ?? 0), 0, ',', '.') }}</div>
                        </div>
                        <div class="col-6">
                            <div class="text-body-secondary small">Status</div>
                            @php $invC = ['Paid'=>'success','Sent'=>'info','Draft'=>'warning','Canceled'=>'danger']; @endphp
                            <span class="badge bg-{{ $invC[$inv->status] ?? 'secondary' }}">{{ $inv->status }}</span>
                        </div>
                        <div class="col-6">
                            <div class="text-body-secondary small">Tgl Invoice</div>
                            <div>{{ $inv->tanggal_invoice->format('d/m/Y') }}</div>
                        </div>
                        <div class="col-6">
                            <div class="text-body-secondary small">Jatuh Tempo</div>
                            <div class="{{ $inv->tanggal_jatuh_tempo < now() && $inv->status !== 'Paid' ? 'text-danger fw-bold' : '' }}">
                                {{ $inv->tanggal_jatuh_tempo->format('d/m/Y') }}
                            </div>
                        </div>
                    </div>
                    <div class="mt-2">
                        <a href="{{ route('admin.invoice.show', $inv) }}" class="btn btn-sm btn-outline-primary"><i class="cil-external-link me-1"></i> Lihat Invoice</a>
                    </div>
                @else
                    <div class="text-center text-body-secondary py-3">Invoice belum dibuat untuk transaksi ini.</div>
                @endif
            </div>
        </div>
    </div>

    {{-- Column 2: Journal + BP + Payment --}}
    <div class="col-lg-6">
        {{-- Journal GL --}}
        <div class="card mb-3">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="cil-book me-2 text-warning"></i> Jurnal Umum (General Ledger)</h6>
            </div>
            <div class="card-body p-0">
                @if($data['jurnal'])
                    @php $jh = $data['jurnal']; @endphp
                    <div class="px-3 py-2 border-bottom bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>{{ $jh->nomor_referensi }}</strong>
                                <span class="badge bg-{{ $jh->status === 'posted' ? 'success' : 'secondary' }} ms-2">{{ ucfirst($jh->status) }}</span>
                            </div>
                            <div class="text-body-secondary small">{{ \Carbon\Carbon::parse($jh->tanggal)->format('d/m/Y') }}</div>
                        </div>
                        <div class="text-body-secondary small">{{ $jh->deskripsi }}</div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead class="table-light"><tr><th>Akun (COA)</th><th class="text-end">Debit</th><th class="text-end">Kredit</th></tr></thead>
                            <tbody>
                                @foreach($jh->jurnalDetails as $detail)
                                <tr>
                                    <td class="small">
                                        <span class="text-body-secondary">{{ $detail->coa->kode_akun ?? '-' }}</span>
                                        {{ $detail->coa->nama_akun ?? '-' }}
                                    </td>
                                    <td class="text-end small {{ $detail->debit > 0 ? 'text-primary fw-semibold' : '' }}">
                                        {{ $detail->debit > 0 ? 'Rp ' . number_format($detail->debit, 0, ',', '.') : '' }}
                                    </td>
                                    <td class="text-end small {{ $detail->kredit > 0 ? 'text-success fw-semibold' : '' }}">
                                        {{ $detail->kredit > 0 ? 'Rp ' . number_format($detail->kredit, 0, ',', '.') : '' }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="px-3 py-2">
                        <a href="{{ route('admin.jurnal.show', $jh) }}" class="btn btn-sm btn-outline-warning"><i class="cil-external-link me-1"></i> Lihat Jurnal</a>
                    </div>
                @else
                    <div class="text-center text-body-secondary py-3">Jurnal umum belum dibuat.</div>
                @endif
            </div>
        </div>

        {{-- Buku Pembantu --}}
        <div class="card mb-3">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="cil-library me-2 text-info"></i> Buku Pembantu Piutang</h6>
            </div>
            <div class="card-body">
                @if($data['buku_pembantu'])
                    @php $bp = $data['buku_pembantu']; @endphp
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="text-body-secondary small">Kontak</div>
                            <div class="fw-semibold">{{ $bp->contactable->nama_klien ?? $bp->contactable->nama_vendor ?? '-' }}</div>
                        </div>
                        <div class="col-6">
                            <div class="text-body-secondary small">Tipe</div>
                            <div><span class="badge bg-primary">{{ ucfirst($bp->tipe) }}</span></div>
                        </div>
                        <div class="col-4">
                            <div class="text-body-secondary small">Jumlah Piutang</div>
                            <div class="fw-bold text-primary">Rp {{ number_format($bp->jumlah, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-4">
                            <div class="text-body-secondary small">Terbayar</div>
                            <div class="fw-semibold text-success">Rp {{ number_format($bp->terbayar, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-4">
                            <div class="text-body-secondary small">Sisa</div>
                            <div class="fw-bold {{ $bp->status === 'lunas' ? 'text-success' : 'text-danger' }}">
                                Rp {{ number_format($bp->jumlah - $bp->terbayar, 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-body-secondary small">Status</div>
                            <span class="badge bg-{{ $bp->status === 'lunas' ? 'success' : 'warning' }}">{{ ucfirst($bp->status) }}</span>
                        </div>
                        <div class="col-6">
                            <div class="text-body-secondary small">Jatuh Tempo</div>
                            <div class="{{ $bp->tanggal_jatuh_tempo < now() && $bp->status === 'pending' ? 'text-danger fw-bold' : '' }}">
                                {{ $bp->tanggal_jatuh_tempo ? $bp->tanggal_jatuh_tempo->format('d/m/Y') : '-' }}
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center text-body-secondary py-3">Belum tercatat di buku pembantu.</div>
                @endif
            </div>
        </div>

        {{-- Payment Journal --}}
        <div class="card mb-3">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="cil-money me-2 text-success"></i> Jurnal Pelunasan</h6>
            </div>
            <div class="card-body p-0">
                @if($data['payment'])
                    @php $pay = $data['payment']; @endphp
                    <div class="px-3 py-2 border-bottom bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>{{ $pay->nomor_referensi }}</strong>
                                <span class="badge bg-success ms-2">{{ ucfirst($pay->status) }}</span>
                            </div>
                            <div class="text-body-secondary small">{{ \Carbon\Carbon::parse($pay->tanggal)->format('d/m/Y') }}</div>
                        </div>
                        <div class="text-body-secondary small">{{ $pay->deskripsi }}</div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead class="table-light"><tr><th>Akun (COA)</th><th class="text-end">Debit</th><th class="text-end">Kredit</th></tr></thead>
                            <tbody>
                                @foreach($pay->jurnalDetails as $detail)
                                <tr>
                                    <td class="small">
                                        <span class="text-body-secondary">{{ $detail->coa->kode_akun ?? '-' }}</span>
                                        {{ $detail->coa->nama_akun ?? '-' }}
                                    </td>
                                    <td class="text-end small {{ $detail->debit > 0 ? 'text-primary fw-semibold' : '' }}">
                                        {{ $detail->debit > 0 ? 'Rp ' . number_format($detail->debit, 0, ',', '.') : '' }}
                                    </td>
                                    <td class="text-end small {{ $detail->kredit > 0 ? 'text-success fw-semibold' : '' }}">
                                        {{ $detail->kredit > 0 ? 'Rp ' . number_format($detail->kredit, 0, ',', '.') : '' }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="px-3 py-2">
                        <a href="{{ route('admin.jurnal.show', $pay) }}" class="btn btn-sm btn-outline-success"><i class="cil-external-link me-1"></i> Lihat Jurnal Pelunasan</a>
                    </div>
                @else
                    <div class="text-center text-body-secondary py-3">Belum ada jurnal pelunasan.</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
