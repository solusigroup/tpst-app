@extends('layouts.admin')
@section('title', 'Users')

@section('content')
<div class="page-header">
    <div><h1>Users</h1></div>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary"><i class="cil-plus me-1"></i> Tambah User</a>
</div>
<div class="card">
    <div class="card-header bg-white py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-auto"><input type="text" name="search" class="form-control" placeholder="Cari nama/email..." value="{{ request('search') }}"></div>
            <div class="col-auto"><button class="btn btn-outline-primary" type="submit"><i class="cil-search me-1"></i> Cari</button></div>
            @if(request('search'))<div class="col-auto"><a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Reset</a></div>@endif
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light"><tr><th>Nama</th><th>Username</th><th>Email</th><th>Role</th><th>Tenant</th><th class="text-end">Aksi</th></tr></thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td><strong>{{ $user->name }}</strong></td>
                        <td>{{ $user->username }}</td>
                        <td>{{ $user->email }}</td>
                        <td><span class="badge bg-primary me-1">{{ ucfirst($user->role) }}</span></td>
                        <td>{{ $user->tenant->name ?? '-' }}</td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-outline-primary"><i class="cil-pencil"></i></a>
                                @unless($user->isSuperAdmin())
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="d-inline" onsubmit="return confirm('Yakin hapus?')">@csrf @method('DELETE')<button class="btn btn-outline-danger"><i class="cil-trash"></i></button></form>
                                @endunless
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-4 text-body-secondary">Belum ada data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($users->hasPages()) <div class="card-footer bg-white">{{ $users->links() }}</div> @endif
</div>
@endsection
