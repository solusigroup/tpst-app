@extends('layouts.admin')

@section('title', 'Input Harian KPI TPST - Performance Management')

@section('content')
<div class="container-fluid">
    {{-- Header & Date Picker --}}
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body p-3">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <h4 class="mb-1 text-primary fw-bold">
                        <i class="cil-pencil me-2"></i>Form Input Harian Indikator KPI
                    </h4>
                    <p class="text-muted mb-0 small">
                        Pencatatan Harian: Checklist Kebersihan & Bau (Ana), Log Alat Berat & Mesin (Agung), serta Keluhan Stakeholder (Nita/Budi).
                    </p>
                </div>
                <form action="{{ route('admin.kpi.daily-input.index') }}" method="GET" class="d-flex align-items-center gap-2">
                    <label class="small fw-semibold text-nowrap">Pilih Tanggal:</label>
                    <input type="date" name="tanggal" class="form-control form-control-sm" value="{{ $tanggal }}" onchange="this.form.submit()">
                </form>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="cil-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Tabs Input Harian --}}
    <ul class="nav nav-pills mb-4" id="dailyInputTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active fw-semibold" id="tab-kebersihan" data-bs-toggle="pill" data-bs-target="#content-kebersihan" type="button">
                <i class="cil-brush me-1"></i> 1. Kebersihan & Bau Area (Ana / Agung)
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link fw-semibold" id="tab-mesin" data-bs-toggle="pill" data-bs-target="#content-mesin" type="button">
                <i class="cil-memory me-1"></i> 2. Mesin & Wheel Loader (Agung)
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link fw-semibold" id="tab-complaint" data-bs-toggle="pill" data-bs-target="#content-complaint" type="button">
                <i class="cil-speech me-1"></i> 3. Keluhan Stakeholder (Nita / Budi)
            </button>
        </li>
    </ul>

    <div class="tab-content" id="dailyInputTabContent">
        {{-- ================= TAB 1: KEBERSIHAN & BAU ================= --}}
        <div class="tab-pane fade show active" id="content-kebersihan" role="tabpanel">
            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0 fw-bold text-dark">
                                <i class="cil-plus me-1 text-success"></i>Form Checklist Kebersihan & Bau
                            </h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.kpi.daily-input.checklist') }}" method="POST">
                                @csrf
                                <input type="hidden" name="tanggal" value="{{ $tanggal }}">

                                <div class="mb-3">
                                    <label class="form-label">Area TPST <span class="text-danger">*</span></label>
                                    <select name="area" class="form-select" required>
                                        <option value="Receiving Area (Timbangan & Bongkar)">Receiving Area (Timbangan & Bongkar)</option>
                                        <option value="Conveyor Pemilahan">Conveyor Pemilahan</option>
                                        <option value="Area Sortir & Baling">Area Sortir & Baling</option>
                                        <option value="Area Mesin RDF & Residu">Area Mesin RDF & Residu</option>
                                        <option value="Halaman Luar & Drainase">Halaman Luar & Drainase</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Status Kebersihan <span class="text-danger">*</span></label>
                                    <select name="status_kebersihan" class="form-select" required>
                                        <option value="Bersih">Bersih (Skor 100)</option>
                                        <option value="Kurang Bersih">Kurang Bersih (Skor 70)</option>
                                        <option value="Kotor">Kotor (Skor 0)</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="ada_tumpukan_sampah" value="1" id="tumpukanSwitch">
                                        <label class="form-check-label fw-semibold text-danger" for="tumpukanSwitch">
                                            Ada tumpukan sampah liar/terbuka? (Pelanggaran KPI Zero Tumpukan)
                                        </label>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Kondisi Bau di Lokasi <span class="text-danger">*</span></label>
                                    <select name="status_bau" class="form-select" required>
                                        <option value="Tidak Bau">Tidak Bau (Normal - Skor 100)</option>
                                        <option value="Bau Ringan">Bau Ringan (Tercium Dekat - Skor 70)</option>
                                        <option value="Bau Berat">Bau Berat (Menyengat ke Luar - Skor 0)</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Catatan Lapangan</label>
                                    <textarea name="catatan" class="form-control" rows="2" placeholder="Kondisi cuaca, tumpukan, penanganan sanitasi/enzim..."></textarea>
                                </div>

                                <button type="submit" class="btn btn-success w-100">
                                    <i class="cil-save me-1"></i> Simpan Checklist
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0 fw-bold text-dark">
                                Riwayat Checklist Tanggal: <span class="text-primary">{{ \Carbon\Carbon::parse($tanggal)->format('d F Y') }}</span>
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Area</th>
                                            <th>Kebersihan</th>
                                            <th>Tumpukan</th>
                                            <th>Bau</th>
                                            <th>Skor</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($checklists as $c)
                                        <tr>
                                            <td class="fw-semibold">{{ $c->area }}</td>
                                            <td>
                                                <span class="badge {{ $c->status_kebersihan === 'Bersih' ? 'bg-success' : 'bg-warning' }}">
                                                    {{ $c->status_kebersihan }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge {{ $c->ada_tumpukan_sampah ? 'bg-danger' : 'bg-success' }}">
                                                    {{ $c->ada_tumpukan_sampah ? 'Ada' : 'Nihil' }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge {{ $c->status_bau === 'Tidak Bau' ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $c->status_bau }}
                                                </span>
                                            </td>
                                            <td class="fw-bold">{{ $c->skor_kebersihan }} / {{ $c->skor_bau }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-muted">
                                                Belum ada checklist kebersihan yang diinput untuk tanggal ini.
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
        </div>

        {{-- ================= TAB 2: MESIN & ALAT BERAT ================= --}}
        <div class="tab-pane fade" id="content-mesin" role="tabpanel">
            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0 fw-bold text-dark">
                                <i class="cil-plus me-1 text-warning"></i>Form Log Aktivitas Mesin & Wheel Loader
                            </h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.kpi.daily-input.machine-log') }}" method="POST">
                                @csrf
                                <input type="hidden" name="tanggal" value="{{ $tanggal }}">

                                <div class="mb-3">
                                    <label class="form-label">Nama Alat / Mesin <span class="text-danger">*</span></label>
                                    <select name="nama_alat" class="form-select" required>
                                        <option value="Wheel Loader">Wheel Loader (Feeding & Unloading)</option>
                                        <option value="Conveyor Pemilahan">Conveyor Pemilahan</option>
                                        <option value="Separator Organik-Anorganik">Separator Organik-Anorganik</option>
                                        <option value="Mesin Pengolah RDF">Mesin Pengolah RDF</option>
                                        <option value="Mesin Press Baler Residu">Mesin Press Baler Residu</option>
                                    </select>
                                </div>

                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label">Jam Start</label>
                                        <input type="time" name="jam_start" class="form-control" value="08:00">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label">Jam Stop</label>
                                        <input type="time" name="jam_stop" class="form-control" value="16:00">
                                    </div>
                                </div>

                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label">Jam Operasi (Jam) <span class="text-danger">*</span></label>
                                        <input type="number" step="0.5" name="jam_operasi" class="form-control" value="8.0" required>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label">Jam Downtime (Jam) <span class="text-danger">*</span></label>
                                        <input type="number" step="0.5" name="jam_downtime" class="form-control" value="0.0" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Konsumsi BBM (Liter Solar) - jika ada</label>
                                    <input type="number" step="0.5" name="bbm_liter" class="form-control" value="0">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Status Kesiapan Alat <span class="text-danger">*</span></label>
                                    <select name="status_alat" class="form-select" required>
                                        <option value="Siap Operasi">Siap Operasi (Optimal)</option>
                                        <option value="Dalam Perbaikan">Dalam Perbaikan (Maintenance)</option>
                                        <option value="Rusak Total">Rusak Total (Breakdown)</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Catatan Kendala / Troubleshooting (PT PBS)</label>
                                    <textarea name="catatan_kendala" class="form-control" rows="2" placeholder="Ganti oli, belt slip, koordinasi PT PBS..."></textarea>
                                </div>

                                <button type="submit" class="btn btn-warning w-100 text-dark fw-semibold">
                                    <i class="cil-save me-1"></i> Simpan Log Mesin
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0 fw-bold text-dark">
                                Log Mesin Tanggal: <span class="text-primary">{{ \Carbon\Carbon::parse($tanggal)->format('d F Y') }}</span>
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Nama Alat</th>
                                            <th>Operasi</th>
                                            <th>Downtime</th>
                                            <th>BBM</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($machineLogs as $m)
                                        <tr>
                                            <td class="fw-semibold">{{ $m->nama_alat }}</td>
                                            <td>{{ $m->jam_operasi }} Jam</td>
                                            <td class="{{ $m->jam_downtime > 0 ? 'text-danger fw-bold' : '' }}">{{ $m->jam_downtime }} Jam</td>
                                            <td>{{ $m->bbm_liter }} L</td>
                                            <td>
                                                <span class="badge {{ $m->status_alat === 'Siap Operasi' ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $m->status_alat }}
                                                </span>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-muted">
                                                Belum ada catatan aktivitas mesin untuk tanggal ini.
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
        </div>

        {{-- ================= TAB 3: KELUHAN STAKEHOLDER ================= --}}
        <div class="tab-pane fade" id="content-complaint" role="tabpanel">
            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0 fw-bold text-dark">
                                <i class="cil-plus me-1 text-danger"></i>Form Catatan Keluhan Stakeholder
                            </h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.kpi.daily-input.complaint') }}" method="POST">
                                @csrf
                                <input type="hidden" name="tanggal" value="{{ $tanggal }}">

                                <div class="mb-3">
                                    <label class="form-label">Pihak Stakeholder <span class="text-danger">*</span></label>
                                    <select name="stakeholder_type" class="form-select" required>
                                        <option value="DLH">Dinas Lingkungan Hidup (DLH Lamongan)</option>
                                        <option value="Penggerobak/Desa">Penggerobak / Tossa Desa</option>
                                        <option value="Klien Swasta">Klien Komersial Swasta</option>
                                        <option value="Warga/Masyarakat">Warga / Lingkungan Sekitar</option>
                                        <option value="Lainnya">Lainnya</option>
                                    </select>
                                </div>

                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label">Nama Pelapor</label>
                                        <input type="text" name="nama_pelapor" class="form-control" placeholder="Nama petugas / warga">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label">Kontak / No HP</label>
                                        <input type="text" name="kontak_pelapor" class="form-control" placeholder="08xx">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Isi Keluhan / Catatan Kendala <span class="text-danger">*</span></label>
                                    <textarea name="isi_keluhan" class="form-control" rows="3" required placeholder="Antrean timbangan lama, bau ceceran, kekeliruan data ritase..."></textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Tingkat Urgensi <span class="text-danger">*</span></label>
                                    <select name="tingkat_urgensi" class="form-select" required>
                                        <option value="Rendah">Rendah (Dapat dijadwalkan)</option>
                                        <option value="Sedang" selected>Sedang (Perlu respon hari ini)</option>
                                        <option value="Tinggi">Tinggi (Kritis / Teguran DLH)</option>
                                    </select>
                                </div>

                                <button type="submit" class="btn btn-danger w-100">
                                    <i class="cil-save me-1"></i> Catat Keluhan
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0 fw-bold text-dark">
                                Daftar Keluhan Tanggal: <span class="text-primary">{{ \Carbon\Carbon::parse($tanggal)->format('d F Y') }}</span>
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Stakeholder</th>
                                            <th>Isi Keluhan</th>
                                            <th>Urgensi</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($complaints as $cmp)
                                        <tr>
                                            <td>
                                                <span class="badge bg-secondary">{{ $cmp->stakeholder_type }}</span>
                                                <div class="small text-muted">{{ $cmp->nama_pelapor ?? '-' }}</div>
                                            </td>
                                            <td><small>{{ $cmp->isi_keluhan }}</small></td>
                                            <td>
                                                <span class="badge {{ $cmp->tingkat_urgensi === 'Tinggi' ? 'bg-danger' : 'bg-warning text-dark' }}">
                                                    {{ $cmp->tingkat_urgensi }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge {{ $cmp->status_penanganan === 'Resolved' ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $cmp->status_penanganan }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($cmp->status_penanganan !== 'Resolved')
                                                <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#resolveModal{{ $cmp->id }}">
                                                    Tindak Lanjut
                                                </button>

                                                {{-- Modal Tindak Lanjut --}}
                                                <div class="modal fade" id="resolveModal{{ $cmp->id }}" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <form action="{{ route('admin.kpi.daily-input.complaint.resolve', $cmp) }}" method="POST">
                                                                @csrf
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">Tindak Lanjut Keluhan Stakeholder</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <p class="small text-muted"><strong>Keluhan:</strong> {{ $cmp->isi_keluhan }}</p>
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Tindakan Perbaikan yang Dilakukan <span class="text-danger">*</span></label>
                                                                        <textarea name="tindakan_perbaikan" class="form-control" rows="3" required placeholder="Contoh: Pembersihan ceceran receiving dan penambahan desinfektan..."></textarea>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Status Penyelesaian</label>
                                                                        <select name="status_penanganan" class="form-select" required>
                                                                            <option value="Resolved">Selesai (Resolved)</option>
                                                                            <option value="Proses">Sedang Diproses</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                                    <button type="submit" class="btn btn-success">Simpan Solusi</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                                @else
                                                <span class="small text-success"><i class="cil-check"></i> Selesai</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-muted">
                                                Nihil keluhan pada tanggal ini.
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
        </div>
    </div>
</div>
@endsection
