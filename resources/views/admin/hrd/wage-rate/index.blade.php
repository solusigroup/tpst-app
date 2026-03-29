@extends('layouts.admin')
@section('title', 'Tarif Upah')

@section('content')
<div class="page-header">
    <div>
        <h1>Tarif Upah</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li><li class="breadcrumb-item active">Tarif Upah</li></ol></nav>
    </div>
    <a href="{{ route('admin.hrd.wage-rate.create') }}" class="btn btn-primary"><i class="cil-plus me-1"></i> Tambah Tarif</a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>Kategori Sampah</th><th>Tarif per Satuan</th><th>Tanggal Berlaku</th><th>Tanggal Berakhir</th><th>Status</th><th class="text-end">Aksi</th></tr>
                </thead>
                <tbody>
                    @forelse($rates as $item)
                    <tr>
                        <td><strong>{{ $item->wasteCategory->name }}</strong></td>
                        <td>Rp {{ number_format($item->rate_per_unit, 2, ',', '.') }} <small class="text-body-secondary">/ {{ $item->wasteCategory->unit }}</small></td>
                        <td>{{ \Carbon\Carbon::parse($item->effective_date)->format('d/m/Y') }}</td>
                        <td>{{ $item->end_date ? \Carbon\Carbon::parse($item->end_date)->format('d/m/Y') : '-' }}</td>
                        <td>
                            @if($item->is_active) <span class="badge bg-success">Aktif</span>
                            @else <span class="badge bg-danger">Non-aktif</span> @endif
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.hrd.wage-rate.edit', $item) }}" class="btn btn-outline-primary"><i class="cil-pencil"></i></a>
                                <form method="POST" action="{{ route('admin.hrd.wage-rate.destroy', $item) }}" class="d-inline" >
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Yakin ingin menghapus data ini?')"><i class="cil-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-4 text-body-secondary">Belum ada data tarif upah.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($rates->hasPages()) <div class="card-footer bg-white">{{ $rates->links() }}</div> @endif
</div>
@endsection
