@extends('layouts.admin')

@section('title', 'Profil Karyawan - ' . $employee->name)

@section('content')
<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Profil Karyawan</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.hrd.employee.index') }}">Manajemen Karyawan</a></li>
                <li class="breadcrumb-item active">Profil</li>
            </ol>
        </nav>
    </div>
    <div class="btn-group">
        <a href="{{ route('admin.hrd.employee.index') }}" class="btn btn-outline-secondary">
            <i class="cil-arrow-left"></i> Kembali
        </a>
        @can('update_employee')
            <a href="{{ route('admin.hrd.employee.edit', $employee->id) }}" class="btn btn-warning">
                <i class="cil-pencil"></i> Edit Profil
            </a>
        @endcan
    </div>
</div>

<div class="row">
    <!-- Profile Sidebar / Focus Photo -->
    <div class="col-md-4">
        <div class="card shadow-sm mb-4 border-0">
            <div class="card-body text-center p-4">
                <div class="mb-4">
                    @if($employee->photo)
                        <img src="{{ Storage::url($employee->photo) }}" alt="Foto {{ $employee->name }}" 
                             class="img-fluid rounded-circle shadow-lg border border-4 border-white" 
                             style="width: 200px; height: 200px; object-fit: cover;">
                    @else
                        <div class="bg-gradient-secondary text-white d-flex align-items-center justify-content-center mx-auto rounded-circle shadow" 
                             style="width: 200px; height: 200px; font-size: 80px;">
                            {{ substr($employee->name, 0, 1) }}
                        </div>
                    @endif
                </div>
                <h4 class="mb-1 font-weight-bold">{{ $employee->name }}</h4>
                <p class="text-primary mb-3">{{ $employee->position ?: 'Staf' }}</p>
                <div class="d-flex justify-content-center gap-2 mb-3">
                    <span class="badge bg-{{ $employee->salary_type == 'bulanan' ? 'info' : ($employee->salary_type == 'harian' ? 'success' : 'secondary') }} py-2 px-3">
                        Skema: {{ ucfirst($employee->salary_type ?: 'Borongan') }}
                    </span>
                    <span class="badge bg-{{ $employee->bpjs_status == 'Aktif' ? 'success' : 'danger' }} py-2 px-3">
                        BPJS: {{ $employee->bpjs_status }}
                    </span>
                </div>
                <hr>
                <div class="text-start">
                    <small class="text-muted d-block mb-1">Username / ID</small>
                    <p class="font-weight-medium">{{ $employee->username }}</p>
                    <small class="text-muted d-block mb-1">Email</small>
                    <p class="font-weight-medium">{{ $employee->email }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Details Column -->
    <div class="col-md-8">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white font-weight-bold py-3">
                <i class="cil-contact me-1 color-primary"></i> Informasi Pribadi & Kepegawaian
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="small text-muted mb-1">Nomor KTP</label>
                        <p class="mb-0 h6">{{ $employee->ktp_number ?: '-' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="small text-muted mb-1">Jenis Kelamin</label>
                        <p class="mb-0 h6">{{ $employee->gender ?: '-' }}</p>
                    </div>
                    <div class="col-md-12">
                        <label class="small text-muted mb-1">Alamat Domisili</label>
                        <p class="mb-0 h6">{{ $employee->address ?: '-' }}</p>
                    </div>
                    <div class="col-md-6 border-top pt-3">
                        <label class="small text-muted mb-1">Tanggal Mulai Kerja</label>
                        <p class="mb-0 h6 text-success">{{ $employee->joined_at ? \Carbon\Carbon::parse($employee->joined_at)->format('d F Y') : '-' }}</p>
                    </div>
                    <div class="col-md-6 border-top pt-3">
                        <label class="small text-muted mb-1">Tanggal Akhir Kerja</label>
                        <p class="mb-0 h6 text-danger">{{ $employee->ended_at ? \Carbon\Carbon::parse($employee->ended_at)->format('d F Y') : '-' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white font-weight-bold py-3">
                        <i class="cil-money me-1 color-success"></i> Kompensasi
                    </div>
                    <div class="card-body">
                        @if($employee->salary_type == 'bulanan')
                            <label class="small text-muted mb-1">Gaji Bulanan</label>
                            <h4 class="text-success mb-0 font-weight-bold">Rp {{ number_format($employee->monthly_salary, 0, ',', '.') }}</h4>
                        @elseif($employee->salary_type == 'harian')
                            <label class="small text-muted mb-1">Upah Harian</label>
                            <h4 class="text-success mb-0 font-weight-bold">Rp {{ number_format($employee->daily_wage, 0, ',', '.') }} <small class="text-muted">/hari</small></h4>
                            <label class="small text-muted mt-3 mb-1">Frekuensi Pembayaran</label>
                            <p class="mb-0 h6">{{ $employee->payment_frequency ?: 'Mingguan' }}</p>
                        @else
                            <label class="small text-muted mb-1">Tipe Upah</label>
                            <h4 class="text-secondary mb-0">Borongan</h4>
                            <p class="small text-muted mt-2">Dihitung berdasarkan jumlah output pilahan sampah.</p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white font-weight-bold py-3">
                        <i class="cil-shield-alt me-1 color-info"></i> BPJS & Jaminan
                    </div>
                    <div class="card-body">
                        <label class="small text-muted mb-1">Status Kepesertaan</label>
                        <div class="mb-3">
                            @if($employee->bpjs_status == 'Aktif')
                                <span class="badge bg-success py-1 px-3">Terdaftar & Aktif</span>
                            @else
                                <span class="badge bg-danger py-1 px-3">Tidak Aktif / Belum Terdaftar</span>
                            @endif
                        </div>
                        <label class="small text-muted mb-1">Nomor BPJS</label>
                        <p class="mb-0 h5 font-weight-bold letter-spacing-1">{{ $employee->bpjs_number ?: '-' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-gradient-secondary {
        background: linear-gradient(135deg, #8e9eab 0%, #eef2f3 100%);
    }
    .letter-spacing-1 {
        letter-spacing: 1px;
    }
    .font-weight-medium {
        font-weight: 500;
    }
    .color-primary { color: #321fdb; }
    .color-success { color: #2eb85c; }
    .color-info { color: #39f; }
</style>
@endsection
