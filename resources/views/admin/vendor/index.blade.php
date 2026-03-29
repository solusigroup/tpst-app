@extends('layouts.admin')
@section('title', 'Vendor')

@section('content')
<div class="page-header">
    <div>
        <h1>Vendor</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Vendor</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('admin.vendor.create') }}" class="btn btn-primary">
        <i class="cil-plus me-1"></i> Tambah Vendor
    </a>
</div>

<div class="card">
    <div class="card-header bg-white py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-auto">
                <input type="text" name="search" class="form-control" placeholder="Cari nama vendor..." value="{{ request('search') }}">
            </div>
            <div class="col-auto">
                <button class="btn btn-outline-primary" type="submit">
                    <i class="cil-search me-1"></i> Cari
                </button>
            </div>
            @if(request()->has('search'))
                <div class="col-auto">
                    <a href="{{ route('admin.vendor.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            @endif
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nama Vendor</th>
                        <th>Kontak</th>
                        <th>Alamat</th>
                        <th>Dibuat</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vendors as $item)
                    <tr>
                        <td><strong>{{ $item->nama_vendor }}</strong></td>
                        <td>{{ $item->kontak ?? '-' }}</td>
                        <td>{{ $item->alamat ?? '-' }}</td>
                        <td>{{ $item->created_at?->format('d/m/Y H:i') }}</td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.vendor.edit', $item) }}" class="btn btn-outline-primary">
                                    <i class="cil-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.vendor.destroy', $item) }}" class="d-inline" >
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Yakin ingin menghapus data ini?')">
                                        <i class="cil-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-body-secondary">Belum ada data vendor.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($vendors->hasPages())
        <div class="card-footer bg-white">
            {{ $vendors->links() }}
        </div>
    @endif
</div>
@endsection
