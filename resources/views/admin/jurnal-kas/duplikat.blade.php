@extends('layouts.admin')
@section('title', 'Pemeriksaan Double Jurnal Kas')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <div class="d-flex align-items-center gap-3">
            <h1 class="h3 fw-bold mb-0">Pemeriksaan Double Jurnal Kas</h1>
            @php $isNegatif = ($saldoKas ?? 0) < 0; @endphp
            <div class="badge {{ $isNegatif ? 'bg-danger bg-opacity-10 text-danger border border-danger' : 'bg-success bg-opacity-10 text-success border border-success' }} border-opacity-25 px-3 py-2 fs-6 rounded-pill shadow-sm">
                <i class="cil-wallet me-1"></i> Saldo Kas: Rp {{ number_format($saldoKas ?? 0, 0, ',', '.') }}
            </div>
        </div>
        <nav aria-label="breadcrumb" class="mt-1">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.jurnal-kas.index') }}">Jurnal Kas</a></li>
                <li class="breadcrumb-item active" aria-current="page">Pemeriksaan Duplikat</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.jurnal-kas.index') }}" class="btn btn-outline-secondary">
            <i class="cil-arrow-left me-1"></i> Kembali ke Jurnal Kas
        </a>
        <a href="{{ route('admin.jurnal-kas.duplikat') }}" class="btn btn-primary">
            <i class="cil-reload me-1"></i> Refresh Deteksi
        </a>
    </div>
</div>

{{-- KPI Summary Cards --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100 {{ ($duplicateStats['group_count'] ?? 0) > 0 ? 'border-start border-warning border-4' : 'border-start border-success border-4' }}">
            <div class="card-body p-3">
                <div class="text-body-secondary small text-uppercase fw-semibold mb-1">Kelompok Duplikat</div>
                <div class="fs-4 fw-bold {{ ($duplicateStats['group_count'] ?? 0) > 0 ? 'text-warning' : 'text-success' }}">
                    {{ number_format($duplicateStats['group_count'] ?? 0, 0, ',', '.') }} <span class="fs-6 fw-normal text-muted">kelompok</span>
                </div>
                <div class="small text-muted mt-1">
                    @if(($parameter ?? 'kemiripan') == 'persis')
                        Tanggal, nominal, & deskripsi persis sama
                    @elseif(($parameter ?? 'kemiripan') == 'tanggal_nominal')
                        Tanggal & nominal sama (abaikan deskripsi)
                    @else
                        Tanggal & nominal sama, serta deskripsi mirip
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100 {{ ($duplicateStats['total_items_count'] ?? 0) > 0 ? 'border-start border-danger border-4' : 'border-start border-success border-4' }}">
            <div class="card-body p-3">
                <div class="text-body-secondary small text-uppercase fw-semibold mb-1">Total Transaksi Kembar</div>
                <div class="fs-4 fw-bold {{ ($duplicateStats['total_items_count'] ?? 0) > 0 ? 'text-danger' : 'text-success' }}">
                    {{ number_format($duplicateStats['total_items_count'] ?? 0, 0, ',', '.') }} <span class="fs-6 fw-normal text-muted">transaksi</span>
                </div>
                <div class="small text-muted mt-1">Total baris yang terindikasi</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100 border-start border-info border-4">
            <div class="card-body p-3">
                <div class="text-body-secondary small text-uppercase fw-semibold mb-1">Total Nilai Tercatat</div>
                <div class="fs-4 fw-bold text-info">
                    Rp {{ number_format($duplicateStats['total_nominal'] ?? 0, 0, ',', '.') }}
                </div>
                <div class="small text-muted mt-1">Akumulasi seluruh transaksi kembar</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100 {{ ($duplicateStats['potensi_redundansi_nominal'] ?? 0) > 0 ? 'border-start border-danger border-4' : 'border-start border-secondary border-4' }}">
            <div class="card-body p-3">
                <div class="text-body-secondary small text-uppercase fw-semibold mb-1">Potensi Nilai Redundan</div>
                <div class="fs-4 fw-bold {{ ($duplicateStats['potensi_redundansi_nominal'] ?? 0) > 0 ? 'text-danger' : 'text-secondary' }}">
                    Rp {{ number_format($duplicateStats['potensi_redundansi_nominal'] ?? 0, 0, ',', '.') }}
                </div>
                <div class="small text-muted mt-1">Selisih nominal berpotensi ganda</div>
            </div>
        </div>
    </div>
</div>

{{-- Nav Tabs (Potensi Duplikat vs Diverifikasi Sah) --}}
<ul class="nav nav-pills mb-4 gap-2">
    <li class="nav-item">
        <a class="nav-link {{ ($tab ?? 'potensi') === 'potensi' ? 'active' : 'bg-light text-dark border' }} d-flex align-items-center gap-2 fw-semibold px-3 py-2" 
           href="{{ route('admin.jurnal-kas.duplikat', array_merge(request()->query(), ['tab' => 'potensi'])) }}">
            <i class="cil-copy"></i>
            <span>Potensi Transaksi Kembar</span>
            @if(($duplicateStats['group_count'] ?? 0) > 0)
                <span class="badge {{ ($tab ?? 'potensi') === 'potensi' ? 'bg-white text-danger' : 'bg-danger text-white' }} rounded-pill">
                    {{ $duplicateStats['group_count'] }}
                </span>
            @endif
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ ($tab ?? 'potensi') === 'diverifikasi' ? 'active bg-success' : 'bg-light text-dark border' }} d-flex align-items-center gap-2 fw-semibold px-3 py-2" 
           href="{{ route('admin.jurnal-kas.duplikat', array_merge(request()->query(), ['tab' => 'diverifikasi'])) }}">
            <i class="cil-check-circle"></i>
            <span>Diverifikasi Sah (Bukan Duplikat)</span>
            @if(($verifiedCount ?? 0) > 0)
                <span class="badge {{ ($tab ?? 'potensi') === 'diverifikasi' ? 'bg-white text-success' : 'bg-success text-white' }} rounded-pill">
                    {{ $verifiedCount }}
                </span>
            @endif
        </a>
    </li>
</ul>

{{-- Filter Card --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('admin.jurnal-kas.duplikat') }}" class="row g-2 align-items-end">
            <input type="hidden" name="tab" value="{{ $tab ?? 'potensi' }}">
            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">Dari Tanggal</label>
                <input type="date" name="dari" class="form-control form-control-sm" value="{{ request('dari') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">Sampai Tanggal</label>
                <input type="date" name="sampai" class="form-control form-control-sm" value="{{ request('sampai') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1 fw-semibold">Parameter Pengecekan</label>
                <select name="parameter" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="kemiripan" {{ request('parameter', 'kemiripan') == 'kemiripan' ? 'selected' : '' }}>Tanggal, Nominal & Kemiripan Deskripsi (Standar)</option>
                    <option value="persis" {{ request('parameter') == 'persis' ? 'selected' : '' }}>Tanggal, Nominal & Deskripsi Sama Persis</option>
                    <option value="tanggal_nominal" {{ request('parameter') == 'tanggal_nominal' ? 'selected' : '' }}>Tanggal & Nominal Sama (Abaikan Deskripsi)</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">Jenis</label>
                <select name="jenis" class="form-select form-select-sm">
                    <option value="">Semua Jenis</option>
                    <option value="masuk" {{ request('jenis') == 'masuk' ? 'selected' : '' }}>Kas Masuk</option>
                    <option value="keluar" {{ request('jenis') == 'keluar' ? 'selected' : '' }}>Kas Keluar</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">Cari Deskripsi</label>
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Ketik kata kunci..." value="{{ request('search') }}">
            </div>
            <div class="col-md-1 d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-outline-primary w-100" title="Terapkan Filter">
                    <i class="cil-search"></i>
                </button>
                @if(request()->hasAny(['dari', 'sampai', 'jenis', 'search', 'parameter']))
                    <a href="{{ route('admin.jurnal-kas.duplikat', ['tab' => $tab ?? 'potensi']) }}" class="btn btn-sm btn-outline-secondary" title="Reset Filter">
                        <i class="cil-x"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- Notification Banner --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-success shadow-sm" role="alert">
        <i class="cil-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-coreui-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-danger shadow-sm" role="alert">
        <i class="cil-warning me-1"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-coreui-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(($tab ?? 'potensi') === 'diverifikasi')
    {{-- Tabel Transaksi yang Telah Diverifikasi Sah --}}
    <div class="card border-0 shadow-sm overflow-hidden mb-4">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                    <i class="cil-check-circle text-success fs-5"></i> Transaksi Telah Diverifikasi Sah (Bukan Transaksi Kembar)
                </h5>
                <div class="small text-muted mt-1">
                    Transaksi dalam daftar ini telah dikonfirmasi valid/terpisah dan tidak lagi dihitung sebagai duplikat.
                </div>
            </div>
            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 fs-7 rounded-pill">
                Total: {{ $verifiedCount }} Transaksi Sah
            </span>
        </div>
        <div class="card-body p-0">
            @if(!$verifiedItems || $verifiedItems->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="cil-check-circle fs-1 text-success mb-2 d-block opacity-50"></i>
                    <h6 class="fw-bold text-dark">Belum Ada Transaksi yang Ditandai Sah</h6>
                    <p class="small text-muted mb-0">Jika ada transaksi kembar di tab Potensi yang ternyata memang sah/terpisah, klik tombol <strong>"Sah"</strong> untuk memindahkannya ke sini.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;" class="text-center">#</th>
                                <th>No. Ref / ID</th>
                                <th>Tanggal</th>
                                <th>Jenis</th>
                                <th>Nominal</th>
                                <th style="min-width: 220px;">Deskripsi</th>
                                <th>Akun Kas / Lawan</th>
                                <th>Alasan / Keterangan</th>
                                <th>Diverifikasi Oleh</th>
                                <th class="text-end" style="min-width: 120px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($verifiedItems as $index => $item)
                                @php
                                    $refNo = $item->jurnalHeader?->nomor_referensi ?? "ID-{$item->id}";
                                    $isPenerimaan = $item->tipe === 'Penerimaan';
                                @endphp
                                <tr>
                                    <td class="text-center text-muted fw-semibold">{{ $verifiedItems->firstItem() + $index }}</td>
                                    <td>
                                        <span class="fw-bold text-dark">{{ $refNo }}</span>
                                        <div class="small text-muted">Jurnal Kas #{{ $item->id }}</div>
                                    </td>
                                    <td>
                                        <span class="fw-semibold text-dark">
                                            {{ $item->tanggal ? $item->tanggal->format('d/m/Y') : '-' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $isPenerimaan ? 'success' : 'danger' }}">
                                            {{ $isPenerimaan ? 'Kas Masuk' : 'Kas Keluar' }}
                                        </span>
                                    </td>
                                    <td class="fw-bold fs-6 {{ $isPenerimaan ? 'text-success' : 'text-danger' }}">
                                        Rp {{ number_format($item->nominal, 0, ',', '.') }}
                                    </td>
                                    <td style="max-width: 250px; white-space: normal; word-break: break-word;">
                                        {{ $item->deskripsi ?: '-' }}
                                    </td>
                                    <td class="small">
                                        <div><span class="text-muted">Kas:</span> {{ $item->coaKas->kode_akun ?? '1101' }}</div>
                                        <div><span class="text-muted">Lawan:</span> {{ $item->coaLawan ? ($item->coaLawan->kode_akun . ' - ' . $item->coaLawan->nama_akun) : '-' }}</div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            <i class="cil-comment-square me-1 text-primary"></i> {{ $item->catatan_duplikat ?: 'Dikonfirmasi sah' }}
                                        </span>
                                    </td>
                                    <td class="small">
                                        <div class="fw-semibold text-dark">{{ $item->verifiedDuplikatBy->name ?? 'Admin' }}</div>
                                        <div class="text-muted">{{ $item->verified_duplikat_at ? $item->verified_duplikat_at->format('d/m/Y H:i') : '-' }}</div>
                                    </td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-sm btn-outline-warning" 
                                                onclick="confirmCancelNonDuplicate({{ $item->id }}, '{{ addslashes($refNo) }}')"
                                                title="Batalkan status sah dan kembalikan ke radar pemeriksaan duplikat">
                                            <i class="cil-action-undo me-1"></i> Batal Sah
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($verifiedItems->hasPages())
                    <div class="card-footer bg-white py-2 border-top">
                        {{ $verifiedItems->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
@else
    {{-- Petunjuk --}}
    <div class="alert alert-info border-info bg-info bg-opacity-10 d-flex align-items-start gap-2 mb-4 rounded-3 shadow-sm">
        <i class="cil-info fs-5 text-info mt-1 flex-shrink-0"></i>
        <div class="small">
            <strong>Ketentuan Deteksi Double Jurnal Kas:</strong> 
            Transaksi disebut <strong>DUPLIKAT</strong> apabila memiliki <strong>Tanggal Sama</strong>, <strong>Nominal Sama</strong>, dan <strong>Ada Kemiripan Deskripsi</strong>.
            Transaksi dengan tanggal berbeda atau deskripsi berbeda meskipun nominalnya sama tidak dianggap duplikat.
            <br>
            Parameter aktif: <strong>
                @if(($parameter ?? 'kemiripan') == 'persis')
                    Tanggal Transaksi, Nilai Nominal, dan Deskripsi Sama Persis
                @elseif(($parameter ?? 'kemiripan') == 'tanggal_nominal')
                    Tanggal Sama & Nilai Nominal Sama (Abaikan Deskripsi)
                @else
                    Tanggal Sama, Nominal Sama & Ada Kemiripan Deskripsi (Standar)
                @endif
            </strong>.
            <br>
            Jika transaksi kembar ternyata memang transaksi terpisah yang sah (bukan entri ganda), klik tombol hijau <span class="badge bg-success bg-opacity-10 text-success border border-success py-1"><i class="cil-check me-1"></i>Sah</span> untuk mengonfirmasi dan mengeluarkannya dari radar duplikat.
        </div>
    </div>

    {{-- Duplicate Groups Listing --}}
    @if($grouped->isEmpty())
        <div class="card border-0 shadow-sm text-center py-5">
            <div class="card-body py-4">
                <div class="mb-3">
                    <i class="cil-check-circle text-success" style="font-size: 4rem;"></i>
                </div>
                <h4 class="fw-bold text-dark">Tidak Ditemukan Double Jurnal Kas</h4>
                <p class="text-muted mx-auto mb-4" style="max-width: 500px;">
                    Semua data transaksi jurnal kas Anda rapi dan valid. Tidak ada transaksi yang memiliki kesamaan Tanggal, Nominal, dan Kemiripan Deskripsi secara bersamaan.
                </p>
                <a href="{{ route('admin.jurnal-kas.index') }}" class="btn btn-outline-primary">
                    <i class="cil-arrow-left me-1"></i> Kembali ke Daftar Jurnal Kas
                </a>
            </div>
        </div>
    @else
        <div class="d-flex flex-column gap-4">
            @php $groupIndex = 1; @endphp
            @foreach($grouped as $groupKey => $items)
                @php
                    $firstItem = $items->first();
                    $itemCount = $items->count();
                    $nominal = $firstItem->nominal;
                    $redundantNominal = ($itemCount - 1) * $nominal;
                    $isPenerimaan = $firstItem->tipe === 'Penerimaan';
                @endphp
                <div class="card border-0 shadow-sm overflow-hidden">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="badge bg-dark rounded-pill px-3 py-2 fs-7">
                                Kelompok #{{ $groupIndex++ }}
                            </span>
                            <span class="badge bg-{{ $isPenerimaan ? 'success' : 'danger' }} px-3 py-2">
                                {{ $isPenerimaan ? 'Kas Masuk' : 'Kas Keluar' }}
                            </span>
                            <span class="fw-bold fs-6 text-dark">
                                <i class="cil-calendar me-1 text-muted"></i> {{ \Carbon\Carbon::parse($firstItem->tanggal)->format('d/m/Y') }}
                            </span>
                            <span class="text-muted">•</span>
                            <span class="fw-bold fs-6 {{ $isPenerimaan ? 'text-success' : 'text-danger' }}">
                                Rp {{ number_format($nominal, 0, ',', '.') }}
                            </span>
                            <span class="text-muted">•</span>
                            @php
                                $uniqueDesks = $items->pluck('deskripsi')->map(fn($d) => trim((string)$d))->filter()->unique();
                            @endphp
                            @if($uniqueDesks->count() <= 1)
                                <span class="fst-italic text-dark bg-light px-2 py-1 rounded border">
                                    "{{ $firstItem->deskripsi ?: '(Tanpa Deskripsi)' }}"
                                </span>
                            @else
                                <span class="badge bg-secondary bg-opacity-25 text-dark border border-secondary border-opacity-50 px-2 py-1" title="{{ $items->pluck('deskripsi')->filter()->join(' | ') }}">
                                    <i class="cil-text me-1"></i> {{ $uniqueDesks->count() }} Variasi Deskripsi (Mirip)
                                </span>
                            @endif
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1">
                                <i class="cil-copy me-1"></i> {{ $itemCount }} Transaksi Kembar
                            </span>
                            <span class="badge bg-warning bg-opacity-25 text-dark border border-warning border-opacity-50 px-2 py-1" title="Potensi nilai yang tercatat lebih dari satu kali">
                                Redundan: Rp {{ number_format($redundantNominal, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 50px;" class="text-center">#</th>
                                        <th>No. Ref / ID</th>
                                        <th>Jenis</th>
                                        <th style="min-width: 180px;">Deskripsi</th>
                                        <th>Akun Kas</th>
                                        <th>Akun Lawan</th>
                                        <th>Mitra</th>
                                        <th>Waktu Input (Created)</th>
                                        <th>Status</th>
                                        <th>Bukti</th>
                                        <th class="text-end" style="min-width: 170px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $rowNumber = 1; @endphp
                                    @foreach($items as $item)
                                        @php
                                            $header = $item->jurnalHeader;
                                            $refNo = $header ? $header->nomor_referensi : "ID-{$item->id}";
                                        @endphp
                                        <tr>
                                            <td class="text-center text-muted fw-semibold">{{ $rowNumber++ }}</td>
                                            <td>
                                                <span class="fw-bold text-dark">{{ $refNo }}</span>
                                                <div class="small text-muted">Jurnal Kas #{{ $item->id }}</div>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $item->tipe === 'Penerimaan' ? 'success' : 'danger' }}">
                                                    {{ $item->tipe === 'Penerimaan' ? 'Kas Masuk' : 'Kas Keluar' }}
                                                </span>
                                            </td>
                                            <td style="max-width: 250px; white-space: normal; word-break: break-word;">
                                                {{ $item->deskripsi ?: '-' }}
                                            </td>
                                            <td>
                                                <span class="small fw-semibold text-secondary">
                                                    {{ $item->coaKas->kode_akun ?? '1101' }} - {{ $item->coaKas->nama_akun ?? 'Kas' }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="fw-semibold text-dark">
                                                    {{ $item->coaLawan ? ($item->coaLawan->kode_akun . ' - ' . $item->coaLawan->nama_akun) : '-' }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($item->contactable)
                                                    <span class="badge bg-light text-dark border">
                                                        {{ class_basename($item->contactable_type) }}: {{ $item->contactable->nama_klien ?? $item->contactable->nama_vendor ?? '-' }}
                                                    </span>
                                                @else
                                                    <span class="text-muted small">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div>{{ $item->created_at ? $item->created_at->format('d/m/Y H:i:s') : '-' }}</div>
                                                <div class="small text-muted">{{ $item->created_at ? $item->created_at->diffForHumans() : '-' }}</div>
                                            </td>
                                            <td>
                                                @if($item->status == 'posted')
                                                    <span class="badge bg-success">Posted</span>
                                                @else
                                                    <span class="badge bg-warning text-dark">Unposted</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($item->bukti_transaksi)
                                                    <a href="{{ Storage::url($item->bukti_transaksi) }}" target="_blank" class="badge bg-info text-decoration-none" title="Lihat Bukti">
                                                        <i class="cil-paperclip me-1"></i> Lihat
                                                    </a>
                                                @else
                                                    <span class="text-muted small">-</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-outline-success" 
                                                            onclick="confirmMarkAsNonDuplicate({{ $item->id }}, '{{ addslashes($refNo) }}', 'Rp {{ number_format($item->nominal, 0, ',', '.') }}', '{{ addslashes($item->deskripsi ?? '') }}')"
                                                            title="Tandai transaksi ini sah (bukan duplikat)">
                                                        <i class="cil-check me-1"></i> Sah
                                                    </button>
                                                    <a href="{{ route('admin.jurnal-kas.edit', $item->id) }}" class="btn btn-outline-primary" title="Edit Transaksi Ini">
                                                        <i class="cil-pencil me-1"></i> Edit
                                                    </a>
                                                    <button type="button" class="btn btn-outline-danger" 
                                                            onclick="confirmDeleteDuplicate({{ $item->id }}, '{{ addslashes($refNo) }}', 'Rp {{ number_format($item->nominal, 0, ',', '.') }}', '{{ addslashes($item->deskripsi ?? '') }}')"
                                                            title="Hapus Entri Duplikat Ini">
                                                        <i class="cil-trash me-1"></i> Hapus
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endif

{{-- Modal Konfirmasi Tandai Sah (Bukan Duplikat) --}}
<div class="modal fade" id="markNonDuplicateModal" tabindex="-1" aria-labelledby="markNonDuplicateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form id="markNonDuplicateForm" method="POST">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-success d-flex align-items-center gap-2" id="markNonDuplicateModalLabel">
                        <i class="cil-check-circle fs-4"></i> Konfirmasi Transaksi Sah (Bukan Duplikat)
                    </h5>
                    <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-3">
                    <p class="mb-3 text-muted">
                        Apakah transaksi ini memang merupakan transaksi terpisah yang sah dan bukan entri kembar yang tidak disengaja?
                    </p>
                    <div class="bg-light p-3 rounded border mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted small">No. Referensi:</span>
                            <strong class="text-dark" id="markModalRef">-</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted small">Nominal:</span>
                            <strong class="text-success" id="markModalNominal">-</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted small">Deskripsi:</span>
                            <span class="text-dark text-end fw-semibold" id="markModalDeskripsi" style="max-width: 250px;">-</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small text-muted fw-semibold mb-1">Keterangan / Alasan Verifikasi (Opsional)</label>
                        <input type="text" name="catatan" class="form-control form-control-sm" placeholder="Misal: Pembelian BBM ritase ke-2 sore hari">
                        <div class="form-text small">Catatan ini tersimpan sebagai jejak audit bagi manajemen dan staf lain.</div>
                    </div>
                    <div class="alert alert-success bg-opacity-10 border-success small mb-0 py-2">
                        <i class="cil-info me-1"></i> Setelah ditandai, transaksi ini akan dikeluarkan dari daftar potensi duplikat dan tanda peringatan pada daftar Jurnal Kas akan hilang.
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light border" data-coreui-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success text-white fw-semibold">
                        <i class="cil-check me-1"></i> Ya, Tandai Sebagai Sah
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Hidden Form for Cancel Non-Duplicate --}}
<form id="cancelNonDuplicateForm" method="POST" style="display: none;">
    @csrf
</form>

{{-- Hidden Form for Safe Deletion --}}
<form id="deleteDuplicateForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
    <input type="hidden" name="redirect_to" value="duplikat">
</form>

{{-- Modal Konfirmasi Hapus Duplikat --}}
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-danger d-flex align-items-center gap-2" id="deleteConfirmModalLabel">
                    <i class="cil-warning fs-4"></i> Konfirmasi Hapus Transaksi Duplikat
                </h5>
                <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-3">
                <p class="mb-3">
                    Apakah Anda yakin ingin menghapus transaksi jurnal kas ini?
                </p>
                <div class="bg-light p-3 rounded border mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted small">No. Referensi:</span>
                        <strong class="text-dark" id="delModalRef">-</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted small">Nominal:</span>
                        <strong class="text-success" id="delModalNominal">-</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted small">Deskripsi:</span>
                        <span class="text-dark text-end fw-semibold" id="delModalDeskripsi" style="max-width: 250px;">-</span>
                    </div>
                </div>
                <div class="alert alert-warning small mb-0 py-2">
                    <i class="cil-info me-1"></i> Tindakan ini akan menghapus entri Jurnal Kas beserta Jurnal Akuntansi terkait secara otomatis.
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light border" data-coreui-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger text-white fw-semibold" id="btnConfirmDeleteAction">
                    <i class="cil-trash me-1"></i> Ya, Hapus Transaksi
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentDeleteId = null;
let deleteModalInstance = null;
let markModalInstance = null;

document.addEventListener('DOMContentLoaded', function() {
    const delModalEl = document.getElementById('deleteConfirmModal');
    if (delModalEl) {
        if (typeof coreui !== 'undefined' && coreui.Modal) {
            deleteModalInstance = new coreui.Modal(delModalEl);
        } else if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            deleteModalInstance = new bootstrap.Modal(delModalEl);
        }
    }

    const markModalEl = document.getElementById('markNonDuplicateModal');
    if (markModalEl) {
        if (typeof coreui !== 'undefined' && coreui.Modal) {
            markModalInstance = new coreui.Modal(markModalEl);
        } else if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            markModalInstance = new bootstrap.Modal(markModalEl);
        }
    }

    const btnConfirmDel = document.getElementById('btnConfirmDeleteAction');
    if (btnConfirmDel) {
        btnConfirmDel.addEventListener('click', function() {
            if (!currentDeleteId) return;
            const form = document.getElementById('deleteDuplicateForm');
            form.action = "{{ url('admin/jurnal-kas') }}/" + currentDeleteId;
            form.submit();
        });
    }
});

function confirmMarkAsNonDuplicate(id, refNo, nominal, deskripsi) {
    const form = document.getElementById('markNonDuplicateForm');
    form.action = "{{ url('admin/jurnal-kas') }}/" + id + "/tandai-bukan-duplikat";
    document.getElementById('markModalRef').textContent = refNo;
    document.getElementById('markModalNominal').textContent = nominal;
    document.getElementById('markModalDeskripsi').textContent = deskripsi || '(Tanpa Deskripsi)';

    if (markModalInstance) {
        markModalInstance.show();
    } else {
        if (confirm('Konfirmasi bahwa transaksi ' + refNo + ' (' + nominal + ') adalah transaksi sah (bukan duplikat)?')) {
            form.submit();
        }
    }
}

function confirmCancelNonDuplicate(id, refNo) {
    if (confirm('Batalkan status sah transaksi ' + refNo + ' dan kembalikan ke radar pengecekan duplikat?')) {
        const form = document.getElementById('cancelNonDuplicateForm');
        form.action = "{{ url('admin/jurnal-kas') }}/" + id + "/batalkan-bukan-duplikat";
        form.submit();
    }
}

function confirmDeleteDuplicate(id, refNo, nominal, deskripsi) {
    currentDeleteId = id;
    document.getElementById('delModalRef').textContent = refNo;
    document.getElementById('delModalNominal').textContent = nominal;
    document.getElementById('delModalDeskripsi').textContent = deskripsi || '(Tanpa Deskripsi)';

    if (deleteModalInstance) {
        deleteModalInstance.show();
    } else {
        if (confirm('Yakin ingin menghapus transaksi jurnal kas ' + refNo + ' (' + nominal + ')?')) {
            const form = document.getElementById('deleteDuplicateForm');
            form.action = "{{ url('admin/jurnal-kas') }}/" + id;
            form.submit();
        }
    }
}
</script>
@endpush
@endsection
