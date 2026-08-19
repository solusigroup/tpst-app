@extends('layouts.admin')
@section('title', 'Invoice')

@section('content')
<div class="page-header">
    <div>
        <h1>Invoice</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li><li class="breadcrumb-item active">Invoice</li></ol></nav>
    </div>
    <div class="d-flex gap-2">
        <form method="POST" action="{{ Route::has('admin.invoice.rebuild-all-journals') ? route('admin.invoice.rebuild-all-journals') : url('admin/invoice/rebuild-all-journals') }}" class="m-0">
            @csrf
            <button type="submit" class="btn btn-outline-info" onclick="return confirm('Bangun ulang seluruh jurnal invoice dan sinkronkan Buku Pembantu sesuai aturan COA terbaru?')">
                <i class="cil-sync me-1"></i> Sinkron Semua Jurnal
            </button>
        </form>
        <form method="POST" action="{{ route('admin.invoice.merge-drafts') }}" class="m-0">
            @csrf
            <button type="submit" class="btn btn-warning text-dark" onclick="return confirm('Apakah Anda yakin ingin menggabungkan semua Invoice Draft dari Klien yang sama? (Termasuk konsolidasi Klien DLH ke Dinas Lingkungan Hidup)')">
                <i class="cil-object-group me-1"></i> Gabung Draft
            </button>
        </form>
        <button type="button" class="btn btn-danger" id="btnPurgeSelected" style="display:none;" onclick="document.getElementById('purgeModal').classList.add('show'); document.getElementById('purgeModal').style.display='block';">
            <i class="cil-fire me-1"></i> Purge Terpilih (<span id="selectedCount">0</span>)
        </button>
        <a href="{{ route('admin.invoice.create') }}" class="btn btn-primary"><i class="cil-plus me-1"></i> Buat Invoice</a>
    </div>
</div>
<div class="card">
    <div class="card-header bg-white py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-auto"><input type="text" name="search" class="form-control" placeholder="Cari No. Invoice / Klien..." value="{{ request('search') }}" style="min-width: 250px;"></div>
            <div class="col-auto">
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    @foreach(['Draft','Sent','Paid','Canceled'] as $s)
                        <option value="{{ $s }}" {{ request('status')==$s?'selected':'' }}>{{ $s }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <select name="jenis" class="form-select">
                    <option value="">Semua Jenis Klien</option>
                    @foreach(['DLH','Swasta','Offtaker','Internal'] as $j)
                        <option value="{{ $j }}" {{ request('jenis')==$j?'selected':'' }}>{{ $j }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto"><button class="btn btn-outline-primary" type="submit"><i class="cil-search me-1"></i> Cari</button></div>
            @if(request()->hasAny(['search','status','jenis']))<div class="col-auto"><a href="{{ route('admin.invoice.index') }}" class="btn btn-outline-secondary">Reset</a></div>@endif
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light"><tr><th style="width:40px;"><input type="checkbox" id="selectAll" title="Pilih Semua"></th><th>No. Invoice</th><th>Klien</th><th>Jenis</th><th>Periode</th><th>Total</th><th>DP</th><th>Sisa</th><th>Status</th><th>Tgl Invoice</th><th class="text-end">Aksi</th></tr></thead>
                <tbody>
                    @forelse($invoices as $item)
                    <tr>
                        <td onclick="event.stopPropagation()"><input type="checkbox" class="purge-checkbox" value="{{ $item->id }}"></td>
                        <td onclick="window.location='{{ route('admin.invoice.show', $item) }}'" style="cursor: pointer;"><strong>{{ $item->nomor_invoice ?? '-' }}</strong></td>
                        <td onclick="window.location='{{ route('admin.invoice.show', $item) }}'" style="cursor: pointer;">{{ $item->klien->nama_klien ?? '-' }}</td>
                        <td><span class="badge bg-light text-dark border">{{ $item->klien->jenis ?? '-' }}</span></td>
                        <td>{{ $item->periode_bulan }}/{{ $item->periode_tahun }}</td>
                        <td>Rp {{ number_format($item->total_tagihan, 0, ',', '.') }}</td>
                        <td class="text-danger">Rp {{ number_format($item->uang_muka, 0, ',', '.') }}</td>
                        <td class="fw-bold">Rp {{ number_format($item->total_tagihan - $item->uang_muka, 0, ',', '.') }}</td>
                        <td>
                            @php $invColors = ['Paid'=>'success','Sent'=>'info','Draft'=>'warning','Canceled'=>'danger']; @endphp
                            <span class="badge bg-{{ $invColors[$item->status] ?? 'secondary' }}">{{ $item->status }}</span>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($item->tanggal_invoice)->format('d/m/Y') }}</td>
                        <td class="text-end" onclick="event.stopPropagation()">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('invoices.print', $item) }}" target="_blank" class="btn btn-outline-success" title="Cetak"><i class="cil-print"></i></a>
                                @if($item->status !== 'Paid')
                                    <a href="{{ route('admin.jurnal.create', ['ref_type' => urlencode('App\Models\Invoice'), 'ref_id' => $item->id]) }}" class="btn btn-success text-white fw-bold" title="Pelunasan via Bank Jatim"><i class="cil-money me-1"></i> Pelunasan</a>
                                @else
                                    <a href="{{ route('admin.jurnal.create', ['ref_type' => urlencode('App\Models\Invoice'), 'ref_id' => $item->id]) }}" class="btn btn-outline-info" title="Buat Jurnal Ledger"><i class="cil-book"></i></a>
                                @endif
                                @if(in_array($item->klien->jenis ?? '', ['Swasta', 'Offtaker']) && !in_array($item->status, ['Paid', 'Canceled']))
                                    <form method="POST" action="{{ route('admin.invoice.send-wa', $item) }}" class="d-inline" target="_blank">
                                        @csrf
                                        <button type="submit" onclick="return confirm('Buka WhatsApp untuk mengirim pesan ke {{ $item->klien->nama_klien ?? '-' }}?')" class="btn btn-outline-success" title="Kirim WA">
                                            <i class="cib-whatsapp"></i>
                                        </button>
                                    </form>
                                @endif
                                <a href="{{ route('admin.tracing.show', ['type' => 'invoice', 'id' => $item->id]) }}" class="btn btn-outline-secondary" title="Lacak Alur"><i class="cil-find-in-page"></i></a>
                                <a href="{{ route('admin.invoice.edit', $item) }}" class="btn btn-outline-primary"><i class="cil-pencil"></i></a>
                                <form method="POST" action="{{ route('admin.invoice.destroy', $item) }}" class="d-inline">@csrf @method('DELETE')<button type="submit" onclick="return confirm('Yakin hapus?')" class="btn btn-outline-danger"><i class="cil-trash"></i></button></form>
                                <form method="POST" action="{{ route('admin.invoice.purge', $item) }}" class="d-inline">@csrf<button type="submit" onclick="return confirm('⚠️ PURGE PERMANEN!\n\nInvoice {{ $item->nomor_invoice }} akan dihapus permanen.\nRitase & Penjualan terkait akan dikembalikan ke Draft.\nJurnal piutang terkait akan dihapus.\n\nLanjutkan?')" class="btn btn-danger" title="Purge Permanen"><i class="cil-fire"></i></button></form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="11" class="text-center py-4 text-body-secondary">Belum ada data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($invoices->hasPages()) <div class="card-footer bg-white">{{ $invoices->links() }}</div> @endif
</div>

{{-- Purge Selected Modal --}}
<div class="modal fade" id="purgeModal" tabindex="-1" style="display:none; background:rgba(0,0,0,0.5);">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-danger">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="cil-fire me-2"></i>Purge Invoice Terpilih</h5>
                <button type="button" class="btn-close btn-close-white" onclick="document.getElementById('purgeModal').classList.remove('show'); document.getElementById('purgeModal').style.display='none';"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger mb-3">
                    <strong>⚠️ Peringatan:</strong> Tindakan ini bersifat <strong>PERMANEN</strong> dan tidak dapat dibatalkan!
                </div>
                <p>Anda akan melakukan purge terhadap <strong><span id="modalCount">0</span> invoice</strong>. Proses ini akan:</p>
                <ul class="mb-0">
                    <li>Menghapus invoice secara permanen</li>
                    <li>Mengembalikan Ritase & Penjualan terkait ke status <span class="badge bg-warning text-dark">Draft</span></li>
                    <li>Menghapus jurnal piutang & entri Buku Pembantu terkait</li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('purgeModal').classList.remove('show'); document.getElementById('purgeModal').style.display='none';">Batal</button>
                <form method="POST" action="{{ route('admin.invoice.purge-selected') }}" id="purgeForm">
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

        // Clear old hidden inputs
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
