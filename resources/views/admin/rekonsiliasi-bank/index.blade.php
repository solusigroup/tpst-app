@extends('layouts.admin')
@section('title', 'Rekonsiliasi Bank Jatim')

@section('content')
<div class="page-header">
    <div>
        <div class="d-flex align-items-center gap-3">
            <h1>Rekonsiliasi Bank Jatim</h1>
            <div class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-2 fs-6 rounded-pill shadow-sm">
                <i class="cil-swap-horizontal me-1"></i> Cocokkan Mutasi Rekening
            </div>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Rekonsiliasi Bank</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <!-- Form Upload dan Parameter -->
    <div class="col-md-12 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0 text-dark fw-bold"><i class="cil-cloud-upload me-2 text-primary"></i>Parameter Rekonsiliasi</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.rekonsiliasi-bank.proses') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small text-muted">1. Pilih Akun Kas / Bank (COA)</label>
                            <select name="coa_id" class="form-select @error('coa_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Akun --</option>
                                @foreach($kasBankCoas as $coaItem)
                                    <option value="{{ $coaItem->id }}" {{ (old('coa_id') == $coaItem->id || (isset($coa) && $coa->id == $coaItem->id)) ? 'selected' : '' }}>
                                        {{ $coaItem->kode_akun }} - {{ $coaItem->nama_akun }} (Saldo: Rp {{ number_format($coaItem->saldo ?? 0, 0, ',', '.') }})
                                    </option>
                                @endforeach
                            </select>
                            @error('coa_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small text-muted">2. Periode Tanggal</label>
                            <div class="input-group">
                                <input type="date" name="dari" class="form-control @error('dari') is-invalid @enderror" 
                                       value="{{ old('dari', isset($dari) ? $dari->format('Y-m-d') : date('Y-m-01')) }}" required>
                                <span class="input-group-text bg-light text-muted">s/d</span>
                                <input type="date" name="sampai" class="form-control @error('sampai') is-invalid @enderror" 
                                       value="{{ old('sampai', isset($sampai) ? $sampai->format('Y-m-d') : date('Y-m-t')) }}" required>
                            </div>
                            @error('dari')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            @error('sampai')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold small text-muted">3. Toleransi Tanggal (Hari)</label>
                            <input type="number" name="toleransi_hari" class="form-control @error('toleransi_hari') is-invalid @enderror" 
                                   value="{{ old('toleransi_hari', $toleransiHari ?? 3) }}" min="0" max="14" required>
                            <div class="form-text small text-muted">Toleransi kliring bank (&plusmn; hari)</div>
                            @error('toleransi_hari')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small text-muted">4. Upload CSV Mutasi Bank Jatim</label>
                            <input type="file" name="csv_file" class="form-control @error('csv_file') is-invalid @enderror" accept=".csv,.txt" required>
                            <div class="form-text small text-muted">Format CSV dengan separator koma (,) atau titik koma (;)</div>
                            @error('csv_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                        <div class="small text-muted">
                            <i class="cil-info me-1"></i> Aturan: Kredit Bank &leftrightarrow; Debit Aplikasi, Debit Bank &leftrightarrow; Kredit Aplikasi.
                        </div>
                        <button type="submit" class="btn btn-primary px-4 py-2 shadow-sm">
                            <i class="cil-media-play me-2"></i> Proses Rekonsiliasi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@if(isset($stats))
<!-- Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm stat-card stat-info bg-white h-100">
            <div class="card-body d-flex align-items-center p-3">
                <div class="stat-icon bg-info-light me-3">
                    <i class="cil-library"></i>
                </div>
                <div>
                    <div class="text-muted small text-uppercase fw-semibold">Total Mutasi Bank</div>
                    <h4 class="mb-0 fw-bold">{{ $stats['total_bank_transaksi'] }} <span class="fs-6 text-muted font-normal">Trx</span></h4>
                    <div class="small text-muted mt-1">
                        D: Rp {{ number_format($stats['total_bank_debit'], 0, ',', '.') }}<br>
                        K: Rp {{ number_format($stats['total_bank_kredit'], 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm stat-card stat-primary bg-white h-100">
            <div class="card-body d-flex align-items-center p-3">
                <div class="stat-icon bg-primary-light me-3">
                    <i class="cil-file"></i>
                </div>
                <div>
                    <div class="text-muted small text-uppercase fw-semibold">Total Jurnal Buku</div>
                    <h4 class="mb-0 fw-bold">{{ $stats['total_buku_transaksi'] }} <span class="fs-6 text-muted font-normal">Trx</span></h4>
                    <div class="small text-muted mt-1">
                        D: Rp {{ number_format($stats['total_buku_debit'], 0, ',', '.') }}<br>
                        K: Rp {{ number_format($stats['total_buku_kredit'], 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm stat-card stat-success bg-white h-100">
            <div class="card-body d-flex align-items-center p-3">
                <div class="stat-icon bg-success-light me-3">
                    <i class="cil-check-alt"></i>
                </div>
                <div>
                    <div class="text-muted small text-uppercase fw-semibold">Transaksi Cocok</div>
                    <h4 class="mb-0 fw-bold text-success">{{ $stats['matched_count'] + $stats['partial_count'] }} <span class="fs-6 text-muted font-normal">Trx</span></h4>
                    <div class="small text-muted mt-1">
                        Cocok Sempurna: <strong>{{ $stats['matched_count'] }}</strong><br>
                        Selisih Hari: <strong>{{ $stats['partial_count'] }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm stat-card stat-danger bg-white h-100">
            <div class="card-body d-flex align-items-center p-3">
                <div class="stat-icon bg-danger-light me-3">
                    <i class="cil-warning"></i>
                </div>
                <div>
                    <div class="text-muted small text-uppercase fw-semibold">Tingkat Kecocokan</div>
                    <h4 class="mb-0 fw-bold {{ $stats['match_rate'] >= 80 ? 'text-success' : ($stats['match_rate'] >= 50 ? 'text-warning' : 'text-danger') }}">
                        {{ $stats['match_rate'] }}%
                    </h4>
                    <div class="small text-muted mt-1">
                        Belum Cocok Bank: <strong>{{ $stats['unmatched_bank_count'] }}</strong><br>
                        Belum Cocok Buku: <strong>{{ $stats['unmatched_buku_count'] }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hasil Rekonsiliasi Tabs -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white p-0">
        <ul class="nav nav-tabs card-header-tabs px-3 pt-2 border-0" id="reconciliationTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-semibold text-dark" id="matched-tab" data-coreui-toggle="tab" data-coreui-target="#matched-pane" type="button" role="tab">
                    <i class="cil-check-circle text-success me-1"></i> Cocok Sempurna 
                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill ms-1 fs-12">{{ $stats['matched_count'] }}</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold text-dark" id="partial-tab" data-coreui-toggle="tab" data-coreui-target="#partial-pane" type="button" role="tab">
                    <i class="cil-warning text-warning me-1"></i> Selisih Tanggal 
                    <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill ms-1 fs-12">{{ $stats['partial_count'] }}</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold text-dark" id="unmatched-bank-tab" data-coreui-toggle="tab" data-coreui-target="#unmatched-bank-pane" type="button" role="tab">
                    <i class="cil-x-circle text-danger me-1"></i> Hanya di Bank (Unmatched Bank)
                    <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill ms-1 fs-12">{{ $stats['unmatched_bank_count'] }}</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold text-dark" id="unmatched-buku-tab" data-coreui-toggle="tab" data-coreui-target="#unmatched-buku-pane" type="button" role="tab">
                    <i class="cil-x-circle text-danger me-1"></i> Hanya di Buku (Unmatched Buku)
                    <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill ms-1 fs-12">{{ $stats['unmatched_buku_count'] }}</span>
                </button>
            </li>
        </ul>
    </div>
    
    <div class="card-body p-0">
        <div class="tab-content" id="reconciliationTabsContent">
            <!-- TAB 1: Cocok Sempurna -->
            <div class="tab-pane fade show active" id="matched-pane" role="tabpanel" tabindex="0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50%;">Data Mutasi Bank Jatim</th>
                                <th style="width: 50%;">Data Jurnal Aplikasi (COA: {{ $coa->nama_akun }})</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($matched as $match)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center justify-content-between p-2">
                                        <div>
                                            <div class="fw-bold">{{ \Carbon\Carbon::parse($match['bank']['tanggal'])->format('d/m/Y') }}</div>
                                            <div class="text-muted small text-truncate" style="max-width: 350px;">{{ $match['bank']['keterangan'] }}</div>
                                        </div>
                                        <div class="text-end">
                                            @if($match['bank']['debit'] > 0)
                                                <span class="badge bg-danger bg-opacity-10 text-danger">D: Rp {{ number_format($match['bank']['debit'], 0, ',', '.') }}</span>
                                            @else
                                                <span class="badge bg-success bg-opacity-10 text-success">K: Rp {{ number_format($match['bank']['kredit'], 0, ',', '.') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="border-start">
                                    <div class="d-flex align-items-center justify-content-between p-2">
                                        <div>
                                            <div class="fw-bold">{{ \Carbon\Carbon::parse($match['buku']['tanggal'])->format('d/m/Y') }}</div>
                                            <div class="text-muted small text-truncate" style="max-width: 350px;">
                                                <span class="badge bg-secondary me-1">{{ $match['buku']['nomor_referensi'] }}</span>{{ $match['buku']['deskripsi'] }}
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            @if($match['buku']['debit'] > 0)
                                                <span class="badge bg-success bg-opacity-10 text-success">D: Rp {{ number_format($match['buku']['debit'], 0, ',', '.') }}</span>
                                            @else
                                                <span class="badge bg-danger bg-opacity-10 text-danger">K: Rp {{ number_format($match['buku']['kredit'], 0, ',', '.') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2" class="text-center py-5 text-muted">
                                    <i class="cil-check-alt fs-2 text-success mb-2 d-block"></i>
                                    Tidak ada transaksi yang cocok sempurna pada rentang tanggal ini.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 2: Selisih Tanggal -->
            <div class="tab-pane fade" id="partial-pane" role="tabpanel" tabindex="0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 45%;">Data Mutasi Bank Jatim</th>
                                <th style="width: 10%;" class="text-center">Keterangan Selisih</th>
                                <th style="width: 45%;">Data Jurnal Aplikasi (COA: {{ $coa->nama_akun }})</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($matchedPartial as $match)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center justify-content-between p-2">
                                        <div>
                                            <div class="fw-bold">{{ \Carbon\Carbon::parse($match['bank']['tanggal'])->format('d/m/Y') }}</div>
                                            <div class="text-muted small text-truncate" style="max-width: 300px;">{{ $match['bank']['keterangan'] }}</div>
                                        </div>
                                        <div class="text-end">
                                            @if($match['bank']['debit'] > 0)
                                                <span class="badge bg-danger bg-opacity-10 text-danger">D: Rp {{ number_format($match['bank']['debit'], 0, ',', '.') }}</span>
                                            @else
                                                <span class="badge bg-success bg-opacity-10 text-success">K: Rp {{ number_format($match['bank']['kredit'], 0, ',', '.') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="border-start text-center">
                                    <span class="badge bg-warning text-dark px-2 py-1.5 shadow-sm">
                                        <i class="cil-history me-1"></i> Selisih {{ $match['selisih_hari'] }} Hari
                                    </span>
                                </td>
                                <td class="border-start">
                                    <div class="d-flex align-items-center justify-content-between p-2">
                                        <div>
                                            <div class="fw-bold">{{ \Carbon\Carbon::parse($match['buku']['tanggal'])->format('d/m/Y') }}</div>
                                            <div class="text-muted small text-truncate" style="max-width: 300px;">
                                                <span class="badge bg-secondary me-1">{{ $match['buku']['nomor_referensi'] }}</span>{{ $match['buku']['deskripsi'] }}
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            @if($match['buku']['debit'] > 0)
                                                <span class="badge bg-success bg-opacity-10 text-success">D: Rp {{ number_format($match['buku']['debit'], 0, ',', '.') }}</span>
                                            @else
                                                <span class="badge bg-danger bg-opacity-10 text-danger">K: Rp {{ number_format($match['buku']['kredit'], 0, ',', '.') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-5 text-muted">
                                    <i class="cil-info fs-2 text-warning mb-2 d-block"></i>
                                    Tidak ada transaksi yang cocok sebagian (selisih tanggal) pada rentang tanggal ini.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 3: Hanya di Bank -->
            <div class="tab-pane fade" id="unmatched-bank-pane" role="tabpanel" tabindex="0">
                <div class="p-3 bg-light border-bottom">
                    <div class="alert alert-info border-info mb-0 d-flex align-items-center gap-2">
                        <i class="cil-info flex-shrink-0"></i>
                        <span>Transaksi terdaftar di mutasi bank tetapi belum dicatat dalam Jurnal Aplikasi. Umumnya mencakup biaya admin bank, bunga bank, transfer masuk yang belum diidentifikasi, dll.</span>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Tanggal</th>
                                <th>Keterangan Mutasi</th>
                                <th>Tipe</th>
                                <th class="text-end">Debit (Keluar)</th>
                                <th class="text-end">Kredit (Masuk)</th>
                                <th class="text-end" style="width: 15%;">Aksi Cepat</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($unmatchedBank as $bank)
                            <tr>
                                <td><strong>{{ \Carbon\Carbon::parse($bank['tanggal'])->format('d/m/Y') }}</strong></td>
                                <td>{{ $bank['keterangan'] }}</td>
                                <td>
                                    @if($bank['kredit'] > 0)
                                        <span class="badge bg-success">Setoran/Masuk</span>
                                    @else
                                        <span class="badge bg-danger">Tarikan/Keluar</span>
                                    @endif
                                </td>
                                <td class="text-end font-monospace text-danger">
                                    {{ $bank['debit'] > 0 ? 'Rp ' . number_format($bank['debit'], 0, ',', '.') : '-' }}
                                </td>
                                <td class="text-end font-monospace text-success">
                                    {{ $bank['kredit'] > 0 ? 'Rp ' . number_format($bank['kredit'], 0, ',', '.') : '-' }}
                                </td>
                                <td class="text-end">
                                    @php
                                        $queryParam = [
                                            'tanggal' => $bank['tanggal'],
                                            'deskripsi' => $bank['keterangan'],
                                            'nominal' => $bank['kredit'] > 0 ? $bank['kredit'] : $bank['debit'],
                                            'tipe' => $bank['kredit'] > 0 ? 'Penerimaan' : 'Pengeluaran',
                                            'rekonsiliasi_target_coa' => $coa->id
                                        ];
                                    @endphp
                                    <a href="{{ route('admin.jurnal-kas.create', $queryParam) }}" class="btn btn-sm btn-primary py-1 px-2 text-nowrap" target="_blank">
                                        <i class="cil-external-link me-1"></i> Buat Jurnal
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="cil-check-circle fs-2 text-success mb-2 d-block"></i>
                                    Semua mutasi bank telah berhasil dicocokkan!
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 4: Hanya di Buku -->
            <div class="tab-pane fade" id="unmatched-buku-pane" role="tabpanel" tabindex="0">
                <div class="p-3 bg-light border-bottom">
                    <div class="alert alert-info border-info mb-0 d-flex align-items-center gap-2">
                        <i class="cil-info flex-shrink-0"></i>
                        <span>Transaksi terdaftar di Jurnal Aplikasi tetapi tidak tercermin di mutasi bank. Biasanya berupa cek beredar, transfer yang tertunda, atau kesalahan pencatatan internal.</span>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Tanggal Jurnal</th>
                                <th>Referensi</th>
                                <th>Deskripsi</th>
                                <th class="text-end">Debit (Penambahan)</th>
                                <th class="text-end">Kredit (Pengurangan)</th>
                                <th class="text-end" style="width: 15%;">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($unmatchedBuku as $buku)
                            <tr>
                                <td><strong>{{ \Carbon\Carbon::parse($buku['tanggal'])->format('d/m/Y') }}</strong></td>
                                <td><span class="badge bg-secondary">{{ $buku['nomor_referensi'] }}</span></td>
                                <td>{{ $buku['deskripsi'] }}</td>
                                <td class="text-end font-monospace text-success">
                                    {{ $buku['debit'] > 0 ? 'Rp ' . number_format($buku['debit'], 0, ',', '.') : '-' }}
                                </td>
                                <td class="text-end font-monospace text-danger">
                                    {{ $buku['kredit'] > 0 ? 'Rp ' . number_format($buku['kredit'], 0, ',', '.') : '-' }}
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.jurnal.index', ['search' => $buku['nomor_referensi']]) }}" class="btn btn-sm btn-outline-secondary py-1 px-2" target="_blank">
                                        <i class="cil-search me-1"></i> Detail Jurnal
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="cil-check-circle fs-2 text-success mb-2 d-block"></i>
                                    Semua data jurnal kas/bank internal telah dicocokkan dengan mutasi rekening.
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
@endif

@endsection

@section('styles')
<style>
    .fs-12 {
        font-size: 0.75rem !important;
    }
    .font-normal {
        font-weight: normal !important;
    }
    .nav-tabs .nav-link {
        border-top-left-radius: 0.375rem;
        border-top-right-radius: 0.375rem;
        border: 1px solid transparent;
        padding: 0.75rem 1.25rem;
    }
    .nav-tabs .nav-link.active {
        border-color: #dee2e6 #dee2e6 #fff;
        background-color: #fff;
    }
</style>
@endsection
