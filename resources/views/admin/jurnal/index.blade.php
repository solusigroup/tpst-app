@extends('layouts.admin')
@section('title', 'Jurnal')

@section('content')
<div class="page-header">
    <div>
        <h1>Jurnal</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li><li class="breadcrumb-item active">Jurnal</li></ol></nav>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-danger" id="btnPurgeSelected" style="display:none;" onclick="document.getElementById('purgeModal').classList.add('show'); document.getElementById('purgeModal').style.display='block';">
            <i class="cil-fire me-1"></i> Purge Terpilih (<span id="selectedCount">0</span>)
        </button>
        <a href="{{ route('admin.jurnal.create') }}" class="btn btn-primary"><i class="cil-plus me-1"></i> Tambah Jurnal</a>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-auto"><input type="text" name="search" class="form-control" placeholder="Cari referensi/deskripsi..." value="{{ request('search') }}"></div>
            <div class="col-auto">
                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}" placeholder="Mulai">
            </div>
            <div class="col-auto">
                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}" placeholder="Selesai">
            </div>
            <div class="col-auto">
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="posted" {{ request('status') == 'posted' ? 'selected' : '' }}>Posted</option>
                    <option value="unposted" {{ request('status') == 'unposted' ? 'selected' : '' }}>Unposted</option>
                </select>
            </div>
            <div class="col-auto"><button class="btn btn-outline-primary" type="submit"><i class="cil-search me-1"></i> Filter</button></div>
            @if(request()->hasAny(['search','status','start_date','end_date']))<div class="col-auto"><a href="{{ route('admin.jurnal.index') }}" class="btn btn-outline-secondary">Reset</a></div>@endif
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light"><tr><th style="width:40px;"><input type="checkbox" id="selectAll" title="Pilih Semua"></th><th>Tanggal</th><th>No. Referensi</th><th>Deskripsi</th><th>Status</th><th>Bukti</th><th class="text-end">Aksi</th></tr></thead>
                <tbody>
                    @forelse($jurnals as $item)
                    <tr>
                        <td><input type="checkbox" class="purge-checkbox" value="{{ $item->id }}"></td>
                        <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                        <td><strong>{{ $item->nomor_referensi ?? '-' }}</strong></td>
                        <td>{{ \Illuminate\Support\Str::limit($item->deskripsi, 50) }}</td>
                        <td><span class="badge bg-{{ $item->status === 'posted' ? 'success' : 'warning' }}">{{ ucfirst($item->status) }}</span></td>
                        <td>
                            @if($item->bukti_transaksi)
                                <img src="{{ asset('storage/' . $item->bukti_transaksi) }}" class="rounded" style="width:32px;height:32px;object-fit:cover;" alt="bukti">
                            @else -
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                @if($item->status !== 'posted')
                                    <form method="POST" action="{{ route('admin.jurnal.post', $item) }}" class="d-inline" >@csrf<button class="btn btn-outline-success" title="Post"><i class="cil-check-circle"></i></button></form>
                                @else
                                    <form method="POST" action="{{ route('admin.jurnal.unpost', $item) }}" class="d-inline" >@csrf<button class="btn btn-outline-warning" title="Unpost"><i class="cil-x-circle"></i></button></form>
                                @endif
                                <a href="{{ route('admin.jurnal.show', $item) }}" class="btn btn-outline-info" title="Lihat"><i class="cil-search"></i></a>
                                <a href="{{ route('admin.jurnal.edit', $item) }}" class="btn btn-outline-primary" title="Edit"><i class="cil-pencil"></i></a>
                                <form method="POST" action="{{ route('admin.jurnal.destroy', $item) }}" class="d-inline">@csrf @method('DELETE')<button type="submit" onclick="return confirm('Yakin hapus?')" class="btn btn-outline-danger"><i class="cil-trash"></i></button></form>
                                <form method="POST" action="{{ route('admin.jurnal.purge', $item) }}" class="d-inline">@csrf<button type="submit" onclick="return confirm('⚠️ PURGE PERMANEN!\n\nJurnal {{ $item->nomor_referensi }} akan dihapus permanen.\nPelunasan di Buku Pembantu akan di-revert.\nDetail jurnal & bukti transaksi akan dihapus.\n\nLanjutkan?')" class="btn btn-danger" title="Purge Permanen"><i class="cil-fire"></i></button></form>
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
    @if($jurnals->hasPages()) <div class="card-footer bg-white">{{ $jurnals->links() }}</div> @endif
</div>

{{-- Purge Selected Modal --}}
<div class="modal fade" id="purgeModal" tabindex="-1" style="display:none; background:rgba(0,0,0,0.5);">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-danger">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="cil-fire me-2"></i>Purge Jurnal Terpilih</h5>
                <button type="button" class="btn-close btn-close-white" onclick="document.getElementById('purgeModal').classList.remove('show'); document.getElementById('purgeModal').style.display='none';"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger mb-3">
                    <strong>⚠️ Peringatan:</strong> Tindakan ini bersifat <strong>PERMANEN</strong> dan tidak dapat dibatalkan!
                </div>
                <p>Anda akan melakukan purge terhadap <strong><span id="modalCount">0</span> jurnal</strong>. Proses ini akan:</p>
                <ul class="mb-0">
                    <li>Menghapus jurnal dan detail secara permanen</li>
                    <li>Mengembalikan pelunasan Buku Pembantu ke status <span class="badge bg-warning text-dark">Pending</span></li>
                    <li>Menghapus bukti transaksi dari storage</li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('purgeModal').classList.remove('show'); document.getElementById('purgeModal').style.display='none';">Batal</button>
                <form method="POST" action="{{ route('admin.jurnal.purge-selected') }}" id="purgeForm">
                    @csrf
                    <button type="submit" class="btn btn-danger"><i class="cil-fire me-1"></i> Ya, Purge Sekarang</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAll');
    const btnPurge = document.getElementById('btnPurgeSelected');
    const selectedCount = document.getElementById('selectedCount');
    const modalCount = document.getElementById('modalCount');
    const purgeForm = document.getElementById('purgeForm');

    function updatePurgeState() {
        const checked = document.querySelectorAll('.purge-checkbox:checked');
        const count = checked.length;
        btnPurge.style.display = count > 0 ? 'inline-block' : 'none';
        selectedCount.textContent = count;
        modalCount.textContent = count;

        purgeForm.querySelectorAll('input[name="ids[]"]').forEach(el => el.remove());
        checked.forEach(cb => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids[]';
            input.value = cb.value;
            purgeForm.appendChild(input);
        });
    }

    selectAll.addEventListener('change', function() {
        document.querySelectorAll('.purge-checkbox').forEach(cb => cb.checked = this.checked);
        updatePurgeState();
    });

    document.querySelectorAll('.purge-checkbox').forEach(cb => {
        cb.addEventListener('change', updatePurgeState);
    });
});
</script>
@endpush
@endsection
