@extends('layouts.admin')
@section('title', 'Manajemen Central Users')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div><h1>Semua User (Central)</h1><nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('central.dashboard') }}">Central</a></li><li class="breadcrumb-item active">Users</li></ol></nav></div>
    <a href="{{ route('central.users.create') }}" class="btn btn-primary"><i class="cil-plus me-1"></i> Tambah User</a>
</div>

<div class="card mb-4"><div class="card-body py-3">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-3"><label class="form-label mb-0 small text-body-secondary">Cari</label><input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Nama / Email"></div>
        <div class="col-md-3">
            <label class="form-label mb-0 small text-body-secondary">Tenant</label>
            <select name="tenant_id" class="form-select">
                <option value="">-- Semua Tenant --</option>
                @foreach($tenants as $t)<option value="{{ $t->id }}" {{ request('tenant_id') == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>@endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label mb-0 small text-body-secondary">Role</label>
            <select name="role" class="form-select">
                <option value="">-- Semua Role --</option>
                @foreach(['admin','timbangan','keuangan'] as $r)<option value="{{ $r }}" {{ request('role') == $r ? 'selected' : '' }}>{{ ucfirst($r) }}</option>@endforeach
            </select>
        </div>
        <div class="col-auto"><button class="btn btn-secondary" type="submit"><i class="cil-search"></i> Filter</button></div>
    </form>
</div></div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light"><tr><th>ID</th><th>Tenant</th><th>Nama</th><th>Email</th><th>Role</th><th class="text-center">Super Admin</th><th>Dibuat</th><th class="text-end">Aksi</th></tr></thead>
                <tbody>
                    @forelse($users as $u)
                    <tr>
                        <td>{{ $u->id }}</td>
                        <td>{{ $u->tenant->name ?? '-' }}</td>
                        <td><strong>{{ $u->name }}</strong><br><small class="text-body-secondary">{{ $u->username }}</small></td>
                        <td>{{ $u->email }}</td>
                        <td>
                            @php $roleColors = ['admin'=>'success','timbangan'=>'info','keuangan'=>'warning']; @endphp
                            <span class="badge bg-{{ $roleColors[$u->role] ?? 'secondary' }}">{{ ucfirst($u->role) }}</span>
                        </td>
                        <td class="text-center">@if($u->is_super_admin) <i class="cil-check-circle text-success fs-5"></i> @else <i class="cil-x-circle text-danger fs-5"></i> @endif</td>
                        <td>{{ $u->created_at->format('d M Y') }}</td>
                        <td class="text-end">
                            <a href="{{ route('central.users.edit', $u->id) }}" class="btn btn-sm btn-warning"><i class="cil-pencil"></i></a>
                            @if($u->id !== auth()->id())
                            <form action="{{ route('central.users.destroy', $u->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus user ini?')">
                                @csrf @method('DELETE') <button class="btn btn-sm btn-danger"><i class="cil-trash"></i></button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center py-4 text-body-secondary">Data user tidak ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($users->hasPages()) <div class="card-footer bg-white">{{ $users->links() }}</div> @endif
</div>
@endsection
