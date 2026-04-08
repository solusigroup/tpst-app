@extends('layouts.admin')
@section('title', 'Detail Ritase')

@section('content')
<div class="page-header">
    <div>
        <h1>Detail Ritase: {{ $ritase->nomor_tiket ?? '-' }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.ritase.index') }}">Ritase</a></li>
                <li class="breadcrumb-item active">{{ $ritase->nomor_tiket ?? 'Detail' }}</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.ritase.edit', $ritase) }}" class="btn btn-primary"><i class="cil-pencil me-1"></i> Edit Ritase</a>
        <a href="{{ route('admin.ritase.index') }}" class="btn btn-outline-secondary">Kembali</a>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header bg-white pb-0">
                <h5 class="card-title">Informasi Tiket & Waktu</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="40%">Nomor Tiket Server</th>
                        <td>: <strong>{{ $ritase->nomor_tiket ?? '-' }}</strong></td>
                    </tr>
                    <tr>
                        <th>Tiket Manual</th>
                        <td>: {{ $ritase->tiket ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Status Ritase</th>
                        <td>: 
                            @php $statusColors = ['masuk'=>'info','timbang'=>'warning','keluar'=>'primary','selesai'=>'success']; @endphp
                            <span class="badge bg-{{ $statusColors[$ritase->status] ?? 'secondary' }}">{{ ucfirst($ritase->status) }}</span>
                        </td>
                    </tr>
                    <tr>
                        <th>Waktu Masuk</th>
                        <td>: {{ $ritase->waktu_masuk ? \Carbon\Carbon::parse($ritase->waktu_masuk)->format('d/m/Y H:i') : '-' }}</td>
                    </tr>
                    <tr>
                        <th>Waktu Keluar</th>
                        <td>: {{ $ritase->waktu_keluar ? \Carbon\Carbon::parse($ritase->waktu_keluar)->format('d/m/Y H:i') : '-' }}</td>
                    </tr>
                    <tr>
                        <th>Approval</th>
                        <td>: 
                            @if($ritase->is_approved)
                                <span class="badge bg-success"><i class="cil-check-circle me-1"></i> Approved at {{ $ritase->approved_at ? \Carbon\Carbon::parse($ritase->approved_at)->format('d/m/Y H:i') : '' }}</span>
                            @else
                                <span class="badge bg-secondary">Pending Approval</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Status Invoice</th>
                        <td>: 
                            @php $invoiceColors = ['Draft'=>'secondary','Sent'=>'info','Paid'=>'success','Canceled'=>'danger']; @endphp
                            <span class="badge bg-{{ $invoiceColors[$ritase->status_invoice] ?? 'secondary' }}">{{ $ritase->status_invoice ?? 'Unbilled' }}</span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-white pb-0">
                <h5 class="card-title">Informasi Tonase & Armada</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="40%">Klien</th>
                        <td>: <strong>{{ $ritase->klien->nama_klien ?? '-' }}</strong></td>
                    </tr>
                    <tr>
                        <th>Armada Plat Nomor</th>
                        <td>: <strong>{{ $ritase->armada->plat_nomor ?? '-' }}</strong></td>
                    </tr>
                    <tr>
                        <th>Jenis/Asal Sampah</th>
                        <td>: {{ $ritase->jenis_sampah ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Berat Bruto</th>
                        <td>: {{ number_format($ritase->berat_bruto, 2, ',', '.') }} kg</td>
                    </tr>
                    <tr>
                        <th>Berat Tarra</th>
                        <td>: {{ number_format($ritase->berat_tarra, 2, ',', '.') }} kg</td>
                    </tr>
                    <tr>
                        <th>Berat Netto</th>
                        <td class="text-primary fs-5">: <strong>{{ number_format($ritase->berat_netto, 2, ',', '.') }} kg</strong></td>
                    </tr>
                    <tr>
                        <th>Biaya Tipping</th>
                        <td>: {{ $ritase->biaya_tipping ? 'Rp ' . number_format($ritase->biaya_tipping, 0, ',', '.') : '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header bg-white pb-0">
                <h5 class="card-title">Foto Tiket</h5>
            </div>
            <div class="card-body text-center bg-light">
                @if($ritase->foto_tiket)
                    <img src="{{ asset('storage/' . $ritase->foto_tiket) }}" alt="Foto Tiket" class="img-fluid rounded border shadow-sm" style="max-height: 450px; object-fit: contain;">
                @else
                    <div class="py-5 text-muted">
                        <i class="cil-image" style="font-size: 4rem;"></i>
                        <p class="mt-3">Tidak ada foto tiket yang diunggah.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
