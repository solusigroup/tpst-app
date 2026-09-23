@extends('layouts.admin')

@section('title', 'Pemeriksaan Anomali Data')

@section('content')
<div class="container-fluid px-0">
    {{-- Page Header --}}
    <div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">
                <i class="cil-warning text-warning me-2"></i>Pemeriksaan Anomali Entry Data
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Pemeriksaan Anomali</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.anomali-data.export-excel', request()->all()) }}" class="btn btn-outline-success shadow-sm">
                <i class="cil-cloud-download me-1"></i> Export Excel
            </a>
            <a href="{{ route('admin.anomali-data.index') }}" class="btn btn-outline-primary shadow-sm" title="Pindai Ulang">
                <i class="cil-reload me-1"></i> Pindai Ulang Data
            </a>
        </div>
    </div>

    {{-- Success / Warning Flash Message --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4" role="alert">
            <i class="cil-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-coreui-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Top Statistics Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card border-start border-4 border-danger h-100 shadow-sm">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-body-secondary text-uppercase fw-semibold small">Anomali Kritis</div>
                        <div class="fs-3 fw-bold text-danger">{{ number_format($stats['danger']) }}</div>
                        <div class="small text-muted">Perlu perbaikan segera</div>
                    </div>
                    <div class="rounded-circle bg-danger-subtle p-3 text-danger">
                        <i class="cil-x-circle fs-3"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card border-start border-4 border-warning h-100 shadow-sm">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-body-secondary text-uppercase fw-semibold small">Peringatan</div>
                        <div class="fs-3 fw-bold text-warning">{{ number_format($stats['warning']) }}</div>
                        <div class="small text-muted">Perlu kelengkapan data</div>
                    </div>
                    <div class="rounded-circle bg-warning-subtle p-3 text-warning">
                        <i class="cil-warning fs-3"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card border-start border-4 border-primary h-100 shadow-sm">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-body-secondary text-uppercase fw-semibold small">Total Temuan</div>
                        <div class="fs-3 fw-bold text-primary">{{ number_format($stats['total']) }}</div>
                        <div class="small text-muted">Dari seluruh modul operasional</div>
                    </div>
                    <div class="rounded-circle bg-primary-subtle p-3 text-primary">
                        <i class="cil-find-in-page fs-3"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card border-start border-4 {{ $stats['danger'] > 0 ? 'border-danger' : ($stats['warning'] > 0 ? 'border-warning' : 'border-success') }} h-100 shadow-sm">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-body-secondary text-uppercase fw-semibold small">Kualitas Data</div>
                        <div class="fs-4 fw-bold {{ $stats['danger'] > 0 ? 'text-danger' : ($stats['warning'] > 0 ? 'text-warning' : 'text-success') }}">
                            @if($stats['danger'] > 0)
                                Perlu Perhatian
                            @elseif($stats['warning'] > 0)
                                Cukup Baik
                            @else
                                Sangat Baik
                            @endif
                        </div>
                        <div class="small text-muted">{{ $stats['danger'] == 0 && $stats['warning'] == 0 ? 'Tidak ada anomali' : 'Tinjau tabel di bawah' }}</div>
                    </div>
                    <div class="rounded-circle {{ $stats['danger'] > 0 ? 'bg-danger-subtle text-danger' : ($stats['warning'] > 0 ? 'bg-warning-subtle text-warning' : 'bg-success-subtle text-success') }} p-3">
                        <i class="cil-shield-alt fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Auto-Fill Notice jika ada banyak ritase keterangan kosong --}}
    @php
        $emptyKetRitase = \App\Models\Ritase::whereNull('keterangan')->orWhere('keterangan', '')->count();
    @endphp
    @if($emptyKetRitase > 0)
        <div class="alert alert-info border-info d-flex align-items-center justify-content-between shadow-sm mb-4 flex-wrap gap-2">
            <div>
                <i class="cil-info me-2 text-info fs-5"></i>
                <strong>Perhatian:</strong> Ditemukan <strong>{{ $emptyKetRitase }}</strong> data ritase lama yang belum memiliki keterangan operasional (akibat penambahan kolom migrasi baru).
            </div>
            <form action="{{ route('admin.anomali-data.autofill-keterangan') }}" method="POST" onsubmit="return confirm('Isi keterangan default [Diterima di TPST] untuk data ritase lama yang belum ada keterangan?')">
                @csrf
                <button type="submit" class="btn btn-sm btn-info text-white fw-semibold">
                    <i class="cil-magic-wand me-1"></i> Auto-Fill 'Diterima di TPST'
                </button>
            </form>
        </div>
    @endif

    {{-- Filter Card --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-body p-3">
            <form action="{{ route('admin.anomali-data.index') }}" method="GET">
                <div class="row g-2 align-items-end">
                    {{-- Modul --}}
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Modul Operasional</label>
                        <select name="module" class="form-select form-select-sm">
                            <option value="all" {{ ($filters['module'] ?? '') === 'all' ? 'selected' : '' }}>Semua Modul ({{ $stats['total'] }})</option>
                            <option value="ritase" {{ ($filters['module'] ?? '') === 'ritase' ? 'selected' : '' }}>Ritase Masuk ({{ $stats['ritase'] }})</option>
                            <option value="residu" {{ ($filters['module'] ?? '') === 'residu' ? 'selected' : '' }}>Pengangkutan Residu ({{ $stats['residu'] }})</option>
                            <option value="hasil_pilahan" {{ ($filters['module'] ?? '') === 'hasil_pilahan' ? 'selected' : '' }}>Hasil Pilahan ({{ $stats['hasil_pilahan'] }})</option>
                            <option value="penjualan" {{ ($filters['module'] ?? '') === 'penjualan' ? 'selected' : '' }}>Penjualan Offtaker ({{ $stats['penjualan'] }})</option>
                            <option value="stok" {{ ($filters['module'] ?? '') === 'stok' ? 'selected' : '' }}>Stok Barang ({{ $stats['stok'] }})</option>
                        </select>
                    </div>

                    {{-- Severity --}}
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold">Tingkat Keparahan</label>
                        <select name="severity" class="form-select form-select-sm">
                            <option value="all" {{ ($filters['severity'] ?? '') === 'all' ? 'selected' : '' }}>Semua Tingkat</option>
                            <option value="danger" {{ ($filters['severity'] ?? '') === 'danger' ? 'selected' : '' }}>🔴 Kritis (Danger)</option>
                            <option value="warning" {{ ($filters['severity'] ?? '') === 'warning' ? 'selected' : '' }}>🟡 Peringatan (Warning)</option>
                        </select>
                    </div>

                    {{-- Dari Tanggal --}}
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold">Dari Tanggal</label>
                        <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $filters['start_date'] ?? '' }}">
                    </div>

                    {{-- Sampai Tanggal --}}
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold">Sampai Tanggal</label>
                        <input type="date" name="end_date" class="form-control form-control-sm" value="{{ $filters['end_date'] ?? '' }}">
                    </div>

                    {{-- Kata Kunci --}}
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold">Kata Kunci</label>
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="No. Tiket / Uraian..." value="{{ $filters['search'] ?? '' }}">
                    </div>

                    {{-- Tombol Aksi Filter --}}
                    <div class="col-md-1 d-flex gap-1">
                        <button type="submit" class="btn btn-sm btn-primary w-100" title="Terapkan Filter">
                            <i class="cil-filter"></i>
                        </button>
                        <a href="{{ route('admin.anomali-data.index') }}" class="btn btn-sm btn-outline-secondary" title="Reset">
                            <i class="cil-x"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Main Anomaly Table Card --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between border-bottom">
            <h5 class="card-title mb-0 fw-bold">
                <i class="cil-list me-2 text-primary"></i>Daftar Temuan Anomali
                <span class="badge bg-secondary ms-2">{{ $anomalies->total() }} Data</span>
            </h5>
            <small class="text-body-secondary">Halaman {{ $anomalies->currentPage() }} dari {{ $anomalies->lastPage() ?: 1 }}</small>
        </div>
        <div class="card-body p-0">
            @if($anomalies->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;" class="text-center">No</th>
                                <th style="width: 110px;">Keparahan</th>
                                <th style="width: 140px;">Modul</th>
                                <th style="width: 130px;">Tanggal</th>
                                <th style="width: 170px;">No. Referensi / Tiket</th>
                                <th>Uraian & Penjelasan Anomali</th>
                                <th style="width: 130px;" class="text-center">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($anomalies as $index => $item)
                                <tr>
                                    <td class="text-center text-muted fw-semibold">
                                        {{ ($anomalies->currentPage() - 1) * $anomalies->perPage() + $loop->iteration }}
                                    </td>
                                    <td>
                                        @if($item['severity'] === 'danger')
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                                                <i class="cil-x-circle me-1"></i> Kritis
                                            </span>
                                        @else
                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">
                                                <i class="cil-warning me-1"></i> Peringatan
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="fw-semibold">{{ $item['module'] }}</span>
                                    </td>
                                    <td>
                                        <span class="text-nowrap">{{ $item['date'] }}</span>
                                    </td>
                                    <td>
                                        <code class="fw-bold fs-7">{{ $item['ref_number'] }}</code>
                                    </td>
                                    <td>
                                        <div class="fw-bold {{ $item['severity'] === 'danger' ? 'text-danger' : 'text-warning-emphasis' }} mb-1">
                                            {{ $item['title'] }}
                                        </div>
                                        <div class="small text-body-secondary">
                                            {{ $item['description'] }}
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @if(!empty($item['edit_url']))
                                            <a href="{{ $item['edit_url'] }}" target="_blank" class="btn btn-sm btn-outline-primary shadow-sm" title="Buka dan perbaiki transaksi ini">
                                                <i class="cil-pencil me-1"></i> Perbaiki
                                            </a>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <div class="rounded-circle bg-success-subtle text-success d-inline-flex p-3 mb-3">
                        <i class="cil-check-circle fs-1"></i>
                    </div>
                    <h5 class="fw-bold text-success mb-1">Semua Data Bersih & Konsisten</h5>
                    <p class="text-muted small mb-0">Tidak ditemukan indikasi anomali pada filter dan data yang dipilih.</p>
                </div>
            @endif
        </div>
        @if($anomalies->hasPages())
            <div class="card-footer bg-white py-3 border-top d-flex justify-content-between align-items-center">
                <div class="small text-muted">
                    Menampilkan {{ $anomalies->firstItem() }} sampai {{ $anomalies->lastItem() }} dari {{ $anomalies->total() }} anomali
                </div>
                <div>
                    {{ $anomalies->links() }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
