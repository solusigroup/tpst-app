@extends('layouts.admin')
@section('title', 'Jurnal Kas')

@section('content')
<div class="page-header">
    <div>
        <div class="d-flex align-items-center gap-3">
            <h1>Jurnal Kas</h1>
            <div class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 fs-6 rounded-pill shadow-sm">
                <i class="cil-wallet me-1"></i> Saldo Kas: Rp {{ number_format($saldoKas ?? 0, 0, ',', '.') }}
            </div>
        </div>
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li><li class="breadcrumb-item active">Jurnal Kas</li></ol></nav>
    </div>
    <a href="{{ route('admin.jurnal-kas.create') }}" class="btn btn-primary"><i class="cil-plus me-1"></i> Tambah</a>
</div>
<div class="card">
    <div class="card-header bg-white py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-auto">
                <label class="form-label small text-muted mb-1">Dari Tanggal</label>
                <input type="date" name="dari" class="form-control form-control-sm" value="{{ request('dari') }}">
            </div>
            <div class="col-auto">
                <label class="form-label small text-muted mb-1">Sampai Tanggal</label>
                <input type="date" name="sampai" class="form-control form-control-sm" value="{{ request('sampai') }}">
            </div>
            <div class="col-auto">
                <label class="form-label small text-muted mb-1">Cari Keterangan</label>
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari deskripsi..." value="{{ request('search') }}">
            </div>
            <div class="col-auto">
                <label class="form-label small text-muted mb-1">Jenis</label>
                <select name="jenis" class="form-select form-select-sm"><option value="">Semua</option><option value="masuk" {{ request('jenis')=='masuk'?'selected':'' }}>Kas Masuk</option><option value="keluar" {{ request('jenis')=='keluar'?'selected':'' }}>Kas Keluar</option></select>
            </div>
            <div class="col-auto">
                <label class="form-label small text-muted mb-1">Urutkan</label>
                <select name="sort" class="form-select form-select-sm">
                    <option value="desc" {{ request('sort') != 'asc' ? 'selected' : '' }}>Terbaru</option>
                    <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>Terlama</option>
                </select>
            </div>
            <div class="col-auto"><button class="btn btn-sm btn-outline-primary" type="submit"><i class="cil-search me-1"></i> Filter</button></div>
            @if(request()->hasAny(['search','jenis','dari','sampai','sort']))<div class="col-auto"><a href="{{ route('admin.jurnal-kas.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a></div>@endif
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light"><tr><th>Tanggal</th><th>Jenis</th><th>Akun</th><th>Jumlah</th><th>Deskripsi</th><th>Status</th><th>Bukti</th><th class="text-end">Aksi</th></tr></thead>
                <tbody>
                    @forelse($jurnalKas as $item)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                        <td><span class="badge bg-{{ $item->tipe=='Penerimaan'?'success':'danger' }}">{{ $item->tipe=='Penerimaan'?'Kas Masuk':'Kas Keluar' }}</span></td>
                        <td>{{ $item->coaLawan->nama_akun ?? '-' }}</td>
                        <td><strong>Rp {{ number_format($item->nominal, 0, ',', '.') }}</strong></td>
                        <td>{{ \Illuminate\Support\Str::limit($item->deskripsi, 40) }}</td>
                        <td>
                            @if($item->status == 'posted')
                                <span class="badge bg-success">Posted</span>
                            @else
                                <span class="badge bg-warning text-dark">Unposted</span>
                            @endif
                        </td>
                        <td>
                            @if($item->bukti_transaksi)
                                <a href="{{ Storage::url($item->bukti_transaksi) }}" target="_blank" class="badge bg-info text-decoration-none" title="Lihat Bukti"><i class="cil-paperclip"></i> Lihat</a>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                @if($item->is_jurnal_umum)
                                    <a href="{{ route('admin.jurnal.edit', $item->id) }}" class="btn btn-outline-primary" title="Edit Jurnal Umum"><i class="cil-pencil"></i></a>
                                    <form method="POST" action="{{ route('admin.jurnal.destroy', $item->id) }}" class="d-inline">@csrf @method('DELETE')<button type="submit" onclick="return confirm('Yakin hapus Jurnal Umum ini?')" class="btn btn-outline-danger"><i class="cil-trash"></i></button></form>
                                @else
                                    <a href="{{ route('admin.jurnal-kas.edit', $item->id) }}" class="btn btn-outline-primary" title="Edit Jurnal Kas"><i class="cil-pencil"></i></a>
                                    <form method="POST" action="{{ route('admin.jurnal-kas.destroy', $item->id) }}" class="d-inline">@csrf @method('DELETE')<button type="submit" onclick="return confirm('Yakin hapus?')" class="btn btn-outline-danger"><i class="cil-trash"></i></button></form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center py-4 text-body-secondary">Belum ada data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($jurnalKas->hasPages()) <div class="card-footer bg-white">{{ $jurnalKas->links() }}</div> @endif
</div>
@endsection
