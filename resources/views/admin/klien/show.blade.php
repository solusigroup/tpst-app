@extends('layouts.admin')
@section('title', 'Detail Klien')

@section('content')
<div class="page-header">
    <div>
        <h1>Detail Klien: {{ $klien->nama_klien }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.klien.index') }}">Klien</a></li>
                <li class="breadcrumb-item active">{{ $klien->nama_klien }}</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.klien.edit', $klien) }}" class="btn btn-primary"><i class="cil-pencil me-1"></i> Edit Klien</a>
        <a href="{{ route('admin.klien.index') }}" class="btn btn-outline-secondary">Kembali</a>
    </div>
</div>

<div class="row">
    <div class="col-md-5">
        <div class="card mb-4">
            <div class="card-header bg-white pb-0">
                <h5 class="card-title">Informasi Klien</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="40%">Nama Klien</th>
                        <td>: {{ $klien->nama_klien }}</td>
                    </tr>
                    <tr>
                        <th>Jenis</th>
                        <td>: 
                            @php
                                $badgeColor = match($klien->jenis) {
                                    'DLH' => 'bg-info',
                                    'Swasta' => 'bg-primary',
                                    'Offtaker' => 'bg-success',
                                    'Internal' => 'bg-warning text-dark',
                                    default => 'bg-secondary'
                                };
                            @endphp
                            <span class="badge {{ $badgeColor }}">{{ $klien->jenis }}</span>
                        </td>
                    </tr>
                    <tr>
                        <th>Jenis Tarif</th>
                        <td>: {{ $klien->jenis_tarif ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Besaran Tarif</th>
                        <td>: {{ $klien->besaran_tarif ? 'Rp ' . number_format($klien->besaran_tarif, 0, ',', '.') : '-' }}</td>
                    </tr>
                    <tr>
                        <th>Kontak</th>
                        <td>: {{ $klien->kontak ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Alamat</th>
                        <td>: {{ $klien->alamat ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Bergabung Sejak</th>
                        <td>: {{ $klien->created_at?->format('d/m/Y') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-7">
        <div class="card mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Daftar Armada ({{ $klien->armada->count() }})</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Plat Nomor</th>
                                <th>Kapasitas Maksimal (kg)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($klien->armada as $index => $armada)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><strong>{{ $armada->plat_nomor }}</strong></td>
                                <td>{{ number_format($armada->kapasitas_maksimal, 2, ',', '.') }} kg</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">Belum ada armada untuk klien ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
