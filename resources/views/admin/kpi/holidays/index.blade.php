@extends('layouts.admin')

@section('title', 'Hari Libur (Holiday) - TPST Performance Management')

@section('content')
<div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
    <div>
        <h1 class="h3 mb-1 text-primary fw-bold">
            <i class="cil-calendar me-2"></i>Daftar Hari Libur (Holiday)
        </h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item">Performance (KPI)</li>
                <li class="breadcrumb-item active" aria-current="page">Hari Libur</li>
            </ol>
        </nav>
    </div>
    @can('create_holiday')
    <a href="{{ route('admin.kpi.holidays.create') }}" class="btn btn-primary">
        <i class="cil-plus me-1"></i> Tambah Hari Libur
    </a>
    @endcan
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="cil-check-circle me-1"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-coreui-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form action="{{ route('admin.kpi.holidays.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-sm-4 col-md-3">
                <select name="year" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Semua Tahun</option>
                    @foreach($years as $yr)
                        <option value="{{ $yr }}" {{ request('year') == $yr ? 'selected' : '' }}>{{ $yr }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-6 col-md-4">
                <div class="input-group input-group-sm">
                    <input type="text" name="search" class="form-control" placeholder="Cari nama hari libur..." value="{{ request('search') }}">
                    <button class="btn btn-outline-secondary" type="submit">
                        <i class="cil-magnifying-glass"></i> Cari
                    </button>
                </div>
            </div>
            @if(request('year') || request('search'))
            <div class="col-sm-2 col-md-2">
                <a href="{{ route('admin.kpi.holidays.index') }}" class="btn btn-outline-danger btn-sm">
                    <i class="cil-x"></i> Reset
                </a>
            </div>
            @endif
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 60px;" class="text-center">No</th>
                        <th style="width: 200px;">Hari & Tanggal</th>
                        <th>Nama Hari Libur / Keterangan</th>
                        <th style="width: 140px;" class="text-end pe-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($holidays as $holiday)
                    <tr>
                        <td class="text-center">{{ $loop->iteration + $holidays->firstItem() - 1 }}</td>
                        <td>
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                                <i class="cil-calendar me-1"></i>
                                {{ \App\Helpers\DateHelper::formatHariTanggal($holiday->tanggal) }}
                            </span>
                        </td>
                        <td class="fw-semibold text-dark">{{ $holiday->nama }}</td>
                        <td class="text-end pe-3">
                            <div class="btn-group btn-group-sm">
                                @can('update_holiday')
                                <a href="{{ route('admin.kpi.holidays.edit', $holiday) }}" class="btn btn-outline-info" title="Edit">
                                    <i class="cil-pencil"></i>
                                </a>
                                @endcan
                                @can('delete_holiday')
                                <form action="{{ route('admin.kpi.holidays.destroy', $holiday) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus hari libur ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Hapus">
                                        <i class="cil-trash"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted">
                            <i class="cil-calendar d-block mb-2" style="font-size: 2rem;"></i>
                            Belum ada data hari libur yang tercatat.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($holidays->hasPages())
    <div class="card-footer bg-white border-top-0 d-flex justify-content-end py-3">
        {{ $holidays->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
