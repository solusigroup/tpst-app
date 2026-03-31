@extends('layouts.admin')
@section('title', 'Detail Jurnal - ' . $jurnal->nomor_referensi)

@section('content')
<div class="page-header">
    <div>
        <h1>Detail Jurnal</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.jurnal.index') }}">Jurnal</a></li>
                <li class="breadcrumb-item active">{{ $jurnal->nomor_referensi }}</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.jurnal.edit', $jurnal) }}" class="btn btn-outline-primary">
            <i class="cil-pencil me-1"></i> Edit
        </a>
        <a href="{{ route('admin.jurnal.index') }}" class="btn btn-outline-secondary">
            <i class="cil-arrow-left me-1"></i> Kembali
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header bg-white"><h6 class="mb-0 fw-semibold">Informasi Dasar</h6></div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <th class="ps-0" style="width: 130px;">No. Referensi</th>
                        <td>: <strong>{{ $jurnal->nomor_referensi }}</strong></td>
                    </tr>
                    <tr>
                        <th class="ps-0">Tanggal</th>
                        <td>: {{ $jurnal->tanggal->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <th class="ps-0">Status</th>
                        <td>: <span class="badge bg-{{ $jurnal->status === 'posted' ? 'success' : 'warning' }}">{{ ucfirst($jurnal->status) }}</span></td>
                    </tr>
                    <tr>
                        <th class="ps-0">Dibuat Pada</th>
                        <td>: {{ $jurnal->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                </table>
                <div class="mt-4 pt-3 border-top">
                    <h6 class="small fw-bold text-muted text-uppercase mb-2">Deskripsi</h6>
                    <p class="mb-0">{{ $jurnal->deskripsi ?: 'Tidak ada deskripsi.' }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header bg-white"><h6 class="mb-0 fw-semibold">Rincian Ayat Jurnal</h6></div>
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Akun</th>
                            <th>Kontak</th>
                            <th class="text-end">Debit</th>
                            <th class="text-end">Kredit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $totalDebit = 0; $totalKredit = 0; @endphp
                        @foreach($jurnal->jurnalDetails as $detail)
                        @php 
                            $totalDebit += $detail->debit; 
                            $totalKredit += $detail->kredit; 
                        @endphp
                        <tr>
                            <td>
                                <div><strong>{{ $detail->coa->nama_akun }}</strong></div>
                                <div class="small text-muted">{{ $detail->coa->kode_akun }}</div>
                            </td>
                            <td>
                                @if($detail->contactable)
                                    <span class="badge bg-light text-dark border">
                                        <i class="cil-user me-1"></i> 
                                        {{ $detail->contactable->nama_klien ?? $detail->contactable->nama_vendor ?? '-' }}
                                    </span>
                                @else -
                                @endif
                            </td>
                            <td class="text-end fw-semibold">{{ $detail->debit > 0 ? 'Rp ' . number_format($detail->debit, 0, ',', '.') : '-' }}</td>
                            <td class="text-end fw-semibold">{{ $detail->kredit > 0 ? 'Rp ' . number_format($detail->kredit, 0, ',', '.') : '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr class="fw-bold">
                            <td colspan="2" class="text-end">TOTAL</td>
                            <td class="text-end">Rp {{ number_format($totalDebit, 0, ',', '.') }}</td>
                            <td class="text-end">Rp {{ number_format($totalKredit, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        @if($jurnal->bukti_transaksi)
        <div class="card">
            <div class="card-header bg-white"><h6 class="mb-0 fw-semibold">Bukti Transaksi</h6></div>
            <div class="card-body">
                <div class="border rounded p-3 text-center bg-light">
                    <img src="{{ asset('storage/' . $jurnal->bukti_transaksi) }}" class="img-fluid rounded shadow-sm" style="max-height: 400px;" alt="Bukti Transaksi">
                    <div class="mt-3">
                        <a href="{{ asset('storage/' . $jurnal->bukti_transaksi) }}" target="_blank" class="btn btn-primary btn-sm">
                            <i class="cil-external-link me-1"></i> Lihat Ukuran Penuh
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
