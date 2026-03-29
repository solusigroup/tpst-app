@extends('layouts.admin')
@section('title', 'Output Pemilah')

@section('content')
<div class="page-header">
    <div>
        <h1>Output Pemilah</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li><li class="breadcrumb-item active">Output Pemilah</li></ol></nav>
    </div>
    <a href="{{ route('admin.hrd.output.create') }}" class="btn btn-primary"><i class="cil-plus me-1"></i> Tambah Output</a>
</div>

<div class="card">
    <div class="card-header bg-white py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Karyawan</label>
                <select name="user_id" class="form-select">
                    <option value="">Semua Karyawan</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Kategori Sampah</label>
                <select name="waste_category_id" class="form-select">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}" {{ request('waste_category_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Dari Tanggal</label>
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Sampai Tanggal</label>
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
            <div class="col-auto"><button class="btn btn-outline-primary" type="submit"><i class="cil-search me-1"></i> Filter</button></div>
            @if(request()->hasAny(['user_id','waste_category_id','date_from','date_to']))
                <div class="col-auto"><a href="{{ route('admin.hrd.output.index') }}" class="btn btn-outline-secondary">Reset</a></div>
            @endif
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>Tanggal</th><th>Karyawan</th><th>Kategori</th><th>Jumlah</th><th>Catatan</th><th class="text-end">Aksi</th></tr>
                </thead>
                <tbody>
                    @forelse($outputs as $item)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($item->output_date)->format('d/m/Y') }}</td>
                        <td><strong>{{ $item->user->name }}</strong></td>
                        <td><span class="badge bg-secondary">{{ $item->wasteCategory->name }}</span></td>
                        <td>{{ number_format($item->quantity, 2, ',', '.') }} {{ $item->unit }}</td>
                        <td><small class="text-body-secondary">{{ $item->notes ?? '-' }}</small></td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.hrd.output.edit', $item) }}" class="btn btn-outline-primary"><i class="cil-pencil"></i></a>
                                <form method="POST" action="{{ route('admin.hrd.output.destroy', $item) }}" class="d-inline" >
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Yakin ingin menghapus data ini?')"><i class="cil-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-4 text-body-secondary">Belum ada data output.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($outputs->hasPages()) <div class="card-footer bg-white">{{ $outputs->links() }}</div> @endif
</div>
@endsection
