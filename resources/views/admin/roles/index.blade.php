@extends('layouts.admin')

@section('title', 'Manajemen Role')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="page-header">
            <div>
                <h1>Manajemen Role & Izin</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item">Administrasi</li>
                        <li class="breadcrumb-item active" aria-current="page">Role</li>
                    </ol>
                </nav>
            </div>
            @if(auth()->user()->hasRole('super_admin'))
            <div>
                <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
                    <i class="cil-plus me-1"></i> Tambah Role
                </a>
            </div>
            @endif
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Role</th>
                                <th>Total Izin (Permissions)</th>
                                @if(auth()->user()->hasRole('super_admin'))
                                <th class="text-end">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($roles as $role)
                            <tr>
                                <td class="fw-semibold">
                                    {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                                    @if($role->name === 'super_admin')
                                        <span class="badge bg-danger ms-2">Full Access</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info text-dark">{{ $role->permissions->count() }} Izin</span>
                                </td>
                                @if(auth()->user()->hasRole('super_admin'))
                                <td class="text-end">
                                    <div class="btn-group">
                                        <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                            <i class="cil-pencil"></i>
                                        </a>
                                        @if($role->name !== 'super_admin')
                                        <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus Role ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                                <i class="cil-trash"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                                @endif
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">Belum ada role tambahan.</td>
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
