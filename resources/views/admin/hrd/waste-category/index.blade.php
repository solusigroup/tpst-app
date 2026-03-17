@extends('layouts.admin')
@section('title', 'Kategori Sampah')

@section('content')
<div class="page-header">
    <div>
        <h1>Kategori Sampah</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li><li class="breadcrumb-item active">Kategori Sampah</li></ol></nav>
    </div>
    <a href="{{ route('admin.hrd.waste-category.create') }}" class="btn btn-primary"><i class="cil-plus me-1"></i> Tambah Kategori</a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr><th>Nama Kategori</th><th>Deskripsi</th><th>Satuan</th><th>Status</th><th class="text-end">Aksi</th></tr>
                </thead>
                <tbody>
                    @forelse($categories as $item)
                    <tr>
                        <td><strong>{{ $item->name }}</strong></td>
                        <td><small class="text-body-secondary">{{ Str::limit($item->description ?? '-', 50) }}</small></td>
                        <td>{{ $item->unit }}</td>
                        <td>
                            @if($item->is_active) <span class="badge bg-success">Aktif</span>
                            @else <span class="badge bg-danger">Non-aktif</span> @endif
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.hrd.waste-category.edit', $item) }}" class="btn btn-outline-primary"><i class="cil-pencil"></i></a>
                                @if($item->is_active)
                                <form method="POST" action="{{ route('admin.hrd.waste-category.destroy', $item) }}" class="d-inline" onsubmit="return confirm('Yakin non-aktifkan kategori ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-outline-danger"><i class="cil-trash"></i></button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-4 text-body-secondary">Belum ada data kategori.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($categories->hasPages()) <div class="card-footer bg-white">{{ $categories->links() }}</div> @endif
</div>
@endsection
