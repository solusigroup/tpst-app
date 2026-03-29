@extends('layouts.admin')
@section('title', 'Manajemen Tenant')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div><h1>Manajemen Tenant</h1><nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('central.dashboard') }}">Central</a></li><li class="breadcrumb-item active">Tenant</li></ol></nav></div>
    <a href="{{ route('central.tenants.create') }}" class="btn btn-primary"><i class="cil-plus me-1"></i> Tambah Tenant Baru</a>
</div>

<div class="card mb-4"><div class="card-body py-3">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-4"><label class="form-label mb-0 small text-body-secondary">Cari</label><input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Nama / Domain"></div>
        <div class="col-auto"><button class="btn btn-secondary" type="submit"><i class="cil-search"></i> Cari</button></div>
    </form>
</div></div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light"><tr><th>ID</th><th>Nama Tenant</th><th>Domain</th><th class="text-center">Jumlah User</th><th>Tanggal Dibuat</th><th class="text-end">Aksi</th></tr></thead>
                <tbody>
                    @forelse($tenants as $t)
                    <tr>
                        <td>{{ $t->id }}</td>
                        <td><strong>{{ $t->name }}</strong></td>
                        <td><code>{{ $t->domain }}</code></td>
                        <td class="text-center"><span class="badge bg-secondary rounded-pill">{{ $t->users_count }}</span></td>
                        <td>{{ $t->created_at->format('d M Y H:i') }}</td>
                        <td class="text-end">
                            <a href="{{ route('central.tenants.edit', $t->id) }}" class="btn btn-sm btn-warning"><i class="cil-pencil"></i></a>
                            <form action="{{ route('central.tenants.destroy', $t->id) }}" method="POST" class="d-inline" >
                                @csrf @method('DELETE') <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus data ini?')"><i class="cil-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-4 text-body-secondary">Data tenant tidak ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($tenants->hasPages()) <div class="card-footer bg-white">{{ $tenants->links() }}</div> @endif
</div>
@endsection
