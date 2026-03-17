@extends('layouts.admin')

@section('title', 'Manajemen Karyawan')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>Daftar Karyawan</strong>
                @can('create_employee')
                    <a href="{{ route('admin.hrd.employee.create') }}" class="btn btn-primary btn-sm">
                        <i class="cil-plus"></i> Tambah Karyawan
                    </a>
                @endcan
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <form action="{{ route('admin.hrd.employee.index') }}" method="GET" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="Cari Nama / No KTP" value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="salary_type" class="form-select">
                                <option value="">Semua Tipe Gaji</option>
                                <option value="bulanan" {{ request('salary_type') == 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                                <option value="borongan" {{ request('salary_type') == 'borongan' ? 'selected' : '' }}>Borongan</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-secondary w-100">Filter</button>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Foto</th>
                                <th>Nama & Jabatan</th>
                                <th>No. KTP</th>
                                <th>Tipe Gaji</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($employees as $emp)
                                <tr>
                                    <td class="text-center">
                                        @if($emp->photo)
                                            <img src="{{ Storage::url($emp->photo) }}" alt="Foto" class="img-thumbnail" style="max-height: 50px;">
                                        @else
                                            <div class="avatar bg-secondary text-white d-flex align-items-center justify-content-center mx-auto" style="width: 50px; height: 50px; border-radius: 5px;">
                                                {{ substr($emp->name, 0, 1) }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $emp->name }}</strong><br>
                                        <small class="text-muted">{{ $emp->position ?? '-' }}</small>
                                    </td>
                                    <td>{{ $emp->ktp_number ?? '-' }}</td>
                                    <td>
                                        @if($emp->salary_type)
                                            <span class="badge bg-{{ $emp->salary_type == 'bulanan' ? 'info' : 'success' }}">
                                                {{ ucfirst($emp->salary_type) }}
                                            </span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @can('update_employee')
                                            <a href="{{ route('admin.hrd.employee.edit', $emp->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                        @endcan
                                        @can('delete_employee')
                                            <form action="{{ route('admin.hrd.employee.destroy', $emp->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus karyawan ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                            </form>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">Tidak ada data karyawan</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{ $employees->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
