@extends('layouts.admin')
@section('title', 'Chart of Account')

@section('content')
<div class="page-header">
    <div>
        <h1>Chart of Account</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li><li class="breadcrumb-item active">COA</li></ol></nav>
    </div>
    <a href="{{ route('admin.coa.create') }}" class="btn btn-primary"><i class="cil-plus me-1"></i> Tambah Akun</a>
</div>

<div class="card">
    <div class="card-header bg-white py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-auto"><input type="text" name="search" class="form-control" placeholder="Cari kode/nama akun..." value="{{ request('search') }}"></div>
            <div class="col-auto">
                <select name="tipe" class="form-select">
                    <option value="">Semua Tipe</option>
                    @foreach(['Asset','Liability','Equity','Revenue','Expense'] as $t)<option value="{{ $t }}" {{ request('tipe') == $t ? 'selected' : '' }}>{{ $t }}</option>@endforeach
                </select>
            </div>
            <div class="col-auto"><button class="btn btn-outline-primary" type="submit"><i class="cil-search me-1"></i> Cari</button></div>
            @if(request()->hasAny(['search','tipe']))<div class="col-auto"><a href="{{ route('admin.coa.index') }}" class="btn btn-outline-secondary">Reset</a></div>@endif
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light"><tr><th>Kode Akun</th><th>Nama Akun</th><th>Tipe</th><th>Klasifikasi</th><th class="text-end">Aksi</th></tr></thead>
                <tbody>
                    @forelse($coas as $item)
                    <tr>
                        <td><strong>{{ $item->kode_akun }}</strong></td>
                        <td>{{ $item->nama_akun }}</td>
                        <td><span class="badge bg-primary">{{ $item->tipe }}</span></td>
                        <td><span class="badge bg-secondary">{{ $item->klasifikasi }}</span></td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.coa.edit', $item) }}" class="btn btn-outline-primary"><i class="cil-pencil"></i></a>
                                <form method="POST" action="{{ route('admin.coa.destroy', $item) }}" class="d-inline" onsubmit="return confirm('Yakin hapus?')">@csrf @method('DELETE')<button class="btn btn-outline-danger"><i class="cil-trash"></i></button></form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-4 text-body-secondary">Belum ada data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($coas->hasPages()) <div class="card-footer bg-white">{{ $coas->links() }}</div> @endif
</div>
@endsection
