@extends('layouts.admin')
@section('title', 'Jurnal Kas')

@section('content')
<div class="page-header">
    <div>
        <h1>Jurnal Kas</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li><li class="breadcrumb-item active">Jurnal Kas</li></ol></nav>
    </div>
    <a href="{{ route('admin.jurnal-kas.create') }}" class="btn btn-primary"><i class="cil-plus me-1"></i> Tambah</a>
</div>
<div class="card">
    <div class="card-header bg-white py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-auto"><input type="text" name="search" class="form-control" placeholder="Cari deskripsi..." value="{{ request('search') }}"></div>
            <div class="col-auto">
                <select name="jenis" class="form-select"><option value="">Semua</option><option value="masuk" {{ request('jenis')=='masuk'?'selected':'' }}>Kas Masuk</option><option value="keluar" {{ request('jenis')=='keluar'?'selected':'' }}>Kas Keluar</option></select>
            </div>
            <div class="col-auto"><button class="btn btn-outline-primary" type="submit"><i class="cil-search me-1"></i> Cari</button></div>
            @if(request()->hasAny(['search','jenis']))<div class="col-auto"><a href="{{ route('admin.jurnal-kas.index') }}" class="btn btn-outline-secondary">Reset</a></div>@endif
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light"><tr><th>Tanggal</th><th>Jenis</th><th>Akun</th><th>Jumlah</th><th>Deskripsi</th><th>Bukti</th><th class="text-end">Aksi</th></tr></thead>
                <tbody>
                    @forelse($jurnalKas as $item)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                        <td><span class="badge bg-{{ $item->tipe=='Penerimaan'?'success':'danger' }}">{{ $item->tipe=='Penerimaan'?'Kas Masuk':'Kas Keluar' }}</span></td>
                        <td>{{ $item->coaLawan->nama_akun ?? '-' }}</td>
                        <td><strong>Rp {{ number_format($item->nominal, 0, ',', '.') }}</strong></td>
                        <td>{{ \Illuminate\Support\Str::limit($item->deskripsi, 40) }}</td>
                        <td>
                            @if($item->bukti_transaksi)
                                <a href="{{ Storage::url($item->bukti_transaksi) }}" target="_blank" class="badge bg-info text-decoration-none" title="Lihat Bukti"><i class="cil-paperclip"></i> Lihat</a>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.jurnal-kas.edit', $item) }}" class="btn btn-outline-primary"><i class="cil-pencil"></i></a>
                                <form method="POST" action="{{ route('admin.jurnal-kas.destroy', $item) }}" class="d-inline">@csrf @method('DELETE')<button type="submit" onclick="return confirm('Yakin hapus?')" class="btn btn-outline-danger"><i class="cil-trash"></i></button></form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-4 text-body-secondary">Belum ada data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($jurnalKas->hasPages()) <div class="card-footer bg-white">{{ $jurnalKas->links() }}</div> @endif
</div>
@endsection
