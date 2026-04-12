@extends('layouts.admin')
@section('title', 'Perhitungan Upah')

@section('content')
<div class="page-header">
    <div>
        <h1>Perhitungan Upah</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li><li class="breadcrumb-item active">Perhitungan Upah</li></ol></nav>
    </div>
    <button type="button" class="btn btn-primary" data-coreui-toggle="modal" data-coreui-target="#calculateModal"><i class="cil-calculator me-1"></i> Hitung Upah</button>
</div>

<!-- Calculate Modal -->
<div class="modal fade" id="calculateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.hrd.wage-calculation.calculate') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Hitung Upah Mingguan</h5>
                <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Tanggal (Dalam Minggu yang Ingin Dihitung) <span class="text-danger">*</span></label>
                    <input type="date" name="week_start" class="form-control" required value="{{ date('Y-m-d') }}">
                    <small class="text-body-secondary">Sistem otomatis mengambil awal minggu dari tanggal ini.</small>
                </div>
                <div class="mb-3">
                    <label class="form-label">Karyawan (Opsional)</label>
                    <select name="user_id" class="form-select">
                        <option value="">Semua Karyawan</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                    <small class="text-body-secondary">Kosongkan untuk menghitung semua karyawan.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Hitung</button>
            </div>
        </form>
    </div>
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
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Dibayar</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Mulai Tanggal (Filter)</label>
                <input type="date" name="week_start" class="form-control" value="{{ request('week_start') }}">
            </div>
            <div class="col-auto"><button class="btn btn-outline-primary" type="submit"><i class="cil-search me-1"></i> Filter</button></div>
            @if(request()->hasAny(['user_id','status','week_start']))
                <div class="col-auto"><a href="{{ route('admin.hrd.wage-calculation.index') }}" class="btn btn-outline-secondary">Reset</a></div>
            @endif
            <div class="col-auto ms-auto">
                <a href="{{ route('admin.hrd.wage-calculation.export-rekap', request()->all()) }}" target="_blank" class="btn btn-danger text-white"><i class="cil-print me-1"></i> Cetak Rekap (PDF)</a>
            </div>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>Oleh</th><th>Skema Upah</th><th>Periode Mingguan</th><th>Total Upah</th><th>Total Output</th><th>Status</th><th class="text-end">Aksi</th></tr>
                </thead>
                <tbody>
                    @forelse($wages as $item)
                    <tr>
                        <td><strong>{{ $item->user->name ?? 'Unknown' }}</strong></td>
                        <td><span class="badge bg-secondary">{{ ucfirst($item->user->salary_type ?? 'Borongan') }}</span></td>
                        <td>
                            @if($item->user->salary_type === 'bulanan')
                                Bulan {{ \Carbon\Carbon::parse($item->week_start)->translatedFormat('F Y') }}
                            @else
                                {{ \Carbon\Carbon::parse($item->week_start)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($item->week_end)->format('d/m/Y') }}
                            @endif
                        </td>
                        <td><strong>Rp {{ number_format($item->total_wage, 2, ',', '.') }}</strong></td>
                        <td>{{ number_format($item->total_quantity, 2, ',', '.') }} kg</td>
                        <td>
                            @if($item->status == 'pending') <span class="badge bg-warning">Pending</span>
                            @elseif($item->status == 'approved') <span class="badge bg-info">Disetujui</span>
                            @elseif($item->status == 'paid') <span class="badge bg-success">Dibayar</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.hrd.wage-calculation.show', $item) }}" class="btn btn-outline-info"><i class="cil-search"></i> Detail</a>
                                <a href="{{ route('admin.jurnal.create', ['ref_type' => urlencode('App\Models\WageCalculation'), 'ref_id' => $item->id]) }}" class="btn btn-outline-primary" title="Buat Jurnal"><i class="cil-book"></i> Jurnal</a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-4 text-body-secondary">Belum ada data perhitungan upah.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($wages->hasPages()) <div class="card-footer bg-white">{{ $wages->links() }}</div> @endif
</div>
@endsection
