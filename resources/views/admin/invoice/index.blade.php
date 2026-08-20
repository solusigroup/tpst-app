@extends('layouts.admin')
@section('title', 'Invoice')

@section('content')
<div class="page-header">
    <div>
        <h1>Invoice</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li><li class="breadcrumb-item active">Invoice</li></ol></nav>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <button type="button" class="btn btn-success text-white" onclick="openMonthlyDlhModal()">
            <i class="cil-description me-1"></i> Rekap Invoice DLH Bulanan
        </button>
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
                        <td onclick="window.location='{{ route('admin.invoice.show', $item) }}'" style="cursor: pointer;">{{ $item->klien?->nama_klien ?? '-' }}</td>
                        <td><span class="badge bg-light text-dark border">{{ $item->klien?->jenis ?? '-' }}</span></td>
                        <td>{{ !empty($item->periode_bulan) ? \App\Helpers\DateHelper::indonesianMonthName($item->periode_bulan) : '-' }} {{ $item->periode_tahun ?? '' }}</td>
                        <td>Rp {{ number_format((float)($item->total_tagihan ?? 0), 0, ',', '.') }}</td>
                        <td class="text-danger">Rp {{ number_format((float)($item->uang_muka ?? 0), 0, ',', '.') }}</td>
                        <td class="fw-bold">Rp {{ number_format((float)(($item->total_tagihan ?? 0) - ($item->uang_muka ?? 0)), 0, ',', '.') }}</td>
                        <td>
                            @php $invColors = ['Paid'=>'success','Sent'=>'info','Draft'=>'warning','Canceled'=>'danger']; @endphp
                            <span class="badge bg-{{ $invColors[$item->status] ?? 'secondary' }}">{{ $item->status ?? 'Draft' }}</span>
                        </td>
                        <td>{{ !empty($item->tanggal_invoice) ? \Carbon\Carbon::parse($item->tanggal_invoice)->format('d/m/Y') : '-' }}</td>
                        <td class="text-end" onclick="event.stopPropagation()">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('invoices.print', $item) }}" target="_blank" class="btn btn-outline-success" title="Cetak"><i class="cil-print"></i></a>
                                @if($item->status !== 'Paid')
                                    <a href="{{ route('admin.jurnal.create', ['ref_type' => urlencode('App\Models\Invoice'), 'ref_id' => $item->id]) }}" class="btn btn-success text-white fw-bold" title="Pelunasan via Bank Jatim"><i class="cil-money me-1"></i> Pelunasan</a>
                                @else
                                    <a href="{{ route('admin.jurnal.create', ['ref_type' => urlencode('App\Models\Invoice'), 'ref_id' => $item->id]) }}" class="btn btn-outline-info" title="Buat Jurnal Ledger"><i class="cil-book"></i></a>
                                @endif
                                @if(in_array($item->klien?->jenis ?? '', ['Swasta', 'Offtaker']) && !in_array($item->status, ['Paid', 'Canceled']))
                                    <form method="POST" action="{{ route('admin.invoice.send-wa', $item) }}" class="d-inline" target="_blank">
                                        @csrf
                                        <button type="submit" onclick="return confirm('Buka WhatsApp untuk mengirim pesan ke {{ $item->klien?->nama_klien ?? '-' }}?')" class="btn btn-outline-success" title="Kirim WA">
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

{{-- Modal Generate Rekap Invoice DLH Bulanan --}}
<div class="modal fade" id="generateDlhModal" tabindex="-1" style="display:none; background:rgba(0,0,0,0.5);">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form method="POST" action="{{ route('admin.invoice.generate-monthly-dlh') }}" id="generateDlhForm">
                @csrf
                <div class="modal-header bg-success text-white py-3">
                    <h5 class="modal-title fw-bold"><i class="cil-description me-2"></i>Buat Rekap Invoice Bulanan Khusus DLH</h5>
                    <button type="button" class="btn-close btn-close-white" onclick="closeMonthlyDlhModal()"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-info py-2 px-3 mb-3 small d-flex align-items-center">
                        <i class="cil-info me-2 fs-5"></i>
                        <div>Sistem akan merekap seluruh ritase <strong>Dinas Lingkungan Hidup (DLH)</strong> yang berstatus <em>Approved</em> pada bulan & tahun terpilih ke dalam <strong>1 Invoice Resmi</strong>.</div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Klien DLH <span class="text-danger">*</span></label>
                            <select name="klien_id" id="dlh_klien_id" class="form-select" required onchange="fetchDlhPreview()">
                                @foreach($dlhClients ?? [] as $k)
                                    <option value="{{ $k->id }}" {{ (isset($masterDlh) && $masterDlh->id == $k->id) ? 'selected' : '' }}>
                                        {{ $k->nama_klien }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Bulan Tagihan <span class="text-danger">*</span></label>
                            <select name="periode_bulan" id="dlh_periode_bulan" class="form-select" required onchange="fetchDlhPreview()">
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ now()->month == $m ? 'selected' : '' }}>
                                        {{ \App\Helpers\DateHelper::indonesianMonthName($m) }}
                                    </option>
                                @endfor
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Tahun Tagihan <span class="text-danger">*</span></label>
                            <select name="periode_tahun" id="dlh_periode_tahun" class="form-select" required onchange="fetchDlhPreview()">
                                @for($y = now()->year + 1; $y >= now()->year - 2; $y--)
                                    <option value="{{ $y }}" {{ now()->year == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tanggal Invoice <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_invoice" id="dlh_tanggal_invoice" class="form-control" value="{{ date('Y-m-d') }}" required onchange="updateDueDateRule()">
                            <small class="text-muted">Tanggal penerbitan tagihan resmi.</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tanggal Jatuh Tempo <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_jatuh_tempo" id="dlh_tanggal_jatuh_tempo" class="form-control" value="{{ date('Y-m-d', strtotime('+30 days')) }}" required>
                            <small class="text-muted">Maksimal 30 hari dari tanggal invoice (dapat dimajukan lebih cepat).</small>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Keterangan / Dasar Tagihan</label>
                            <input type="text" name="keterangan" id="dlh_keterangan" class="form-control" placeholder="Contoh: Tagihan Rekapitulasi Jasa Pengelolaan Sampah (Tipping Fee) Periode...">
                        </div>
                    </div>

                    {{-- Live Preview Card --}}
                    <div class="card bg-light border-0 mt-3" id="dlhPreviewBox">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold mb-0 text-dark"><i class="cil-chart-pie me-1 text-success"></i> Preview Ritase Approved:</h6>
                                <span class="badge bg-secondary" id="previewLoading" style="display:none;">Memuat data...</span>
                            </div>
                            <div class="row g-2 text-center" id="dlhPreviewContent">
                                <div class="col-4">
                                    <div class="bg-white p-2 rounded shadow-sm border">
                                        <small class="text-muted d-block">Jumlah Tiket</small>
                                        <span class="fs-5 fw-bold text-primary" id="previewCount">-</span>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="bg-white p-2 rounded shadow-sm border">
                                        <small class="text-muted d-block">Total Tonase</small>
                                        <span class="fs-5 fw-bold text-success" id="previewTonase">- Ton</span>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="bg-white p-2 rounded shadow-sm border">
                                        <small class="text-muted d-block">Estimasi Tagihan</small>
                                        <span class="fs-5 fw-bold text-dark" id="previewTipping">Rp -</span>
                                    </div>
                                </div>
                            </div>
                            <div id="previewExistingAlert" class="alert alert-warning mt-2 mb-0 py-2 small" style="display:none;">
                                <i class="cil-warning me-1"></i> <strong>Perhatian:</strong> Sudah terdapat invoice untuk periode ini (<span id="existingInvoiceLabel"></span>). Memproses ini akan menyinkronkan ritase baru yang belum masuk ke invoice tersebut.
                            </div>
                            <div id="previewEmptyAlert" class="alert alert-secondary mt-2 mb-0 py-2 small" style="display:none;">
                                <i class="cil-info me-1"></i> Belum ada tiket ritase DLH approved pada periode ini.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-outline-secondary" onclick="closeMonthlyDlhModal()">Batal</button>
                    <button type="submit" class="btn btn-success text-white" id="btnSubmitDlh">
                        <i class="cil-check-circle me-1"></i> Terbitkan / Rekap Invoice DLH
                    </button>
                </div>
            </form>
        </div>
    </div>
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
                <form method="POST" action="{{ Route::has('admin.invoice.purge-selected') ? route('admin.invoice.purge-selected') : url('admin/invoice/purge-selected') }}" id="purgeForm">
                    @csrf
                    <button type="submit" class="btn btn-danger"><i class="cil-fire me-1"></i> Ya, Purge Sekarang</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openMonthlyDlhModal() {
    const modal = document.getElementById('generateDlhModal');
    modal.classList.add('show');
    modal.style.display = 'block';
    updateDueDateRule();
    fetchDlhPreview();
}

function closeMonthlyDlhModal() {
    const modal = document.getElementById('generateDlhModal');
    modal.classList.remove('show');
    modal.style.display = 'none';
}

function updateDueDateRule() {
    const invInput = document.getElementById('dlh_tanggal_invoice');
    const dueInput = document.getElementById('dlh_tanggal_jatuh_tempo');
    if (!invInput || !dueInput || !invInput.value) return;

    const invDate = new Date(invInput.value);
    
    // Max 30 days
    const maxDueDate = new Date(invDate);
    maxDueDate.setDate(maxDueDate.getDate() + 30);
    
    const minStr = invInput.value;
    const maxStr = maxDueDate.toISOString().split('T')[0];
    
    dueInput.min = minStr;
    dueInput.max = maxStr;

    // Auto update default due date if it is out of range
    if (!dueInput.value || dueInput.value < minStr || dueInput.value > maxStr) {
        dueInput.value = maxStr;
    }
}

function fetchDlhPreview() {
    const bulan = document.getElementById('dlh_periode_bulan').value;
    const tahun = document.getElementById('dlh_periode_tahun').value;
    const klienId = document.getElementById('dlh_klien_id')?.value || '';
    const loading = document.getElementById('previewLoading');
    const existingAlert = document.getElementById('previewExistingAlert');
    const emptyAlert = document.getElementById('previewEmptyAlert');
    const btnSubmit = document.getElementById('btnSubmitDlh');

    if (loading) loading.style.display = 'inline-block';

    fetch(`{{ route('admin.invoice.preview-monthly-dlh') }}?periode_bulan=${bulan}&periode_tahun=${tahun}&klien_id=${klienId}`)
        .then(res => res.json())
        .then(data => {
            if (loading) loading.style.display = 'none';
            if (data.success) {
                document.getElementById('previewCount').textContent = data.count + ' Tiket';
                document.getElementById('previewTonase').textContent = data.total_netto_ton + ' Ton';
                document.getElementById('previewTipping').textContent = data.total_tipping_rp;

                if (data.has_existing) {
                    existingAlert.style.display = 'block';
                    document.getElementById('existingInvoiceLabel').textContent = `${data.existing_nomor} (Status: ${data.existing_status})`;
                } else {
                    existingAlert.style.display = 'none';
                }

                if (data.count === 0 && !data.has_existing) {
                    emptyAlert.style.display = 'block';
                    btnSubmit.disabled = true;
                } else {
                    emptyAlert.style.display = 'none';
                    btnSubmit.disabled = false;
                }
            }
        })
        .catch(err => {
            if (loading) loading.style.display = 'none';
            console.error('Error fetching DLH preview:', err);
        });
}

document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAll');
    const btnPurge = document.getElementById('btnPurgeSelected');
    const selectedCount = document.getElementById('selectedCount');
    const modalCount = document.getElementById('modalCount');
    const purgeForm = document.getElementById('purgeForm');

    function updatePurgeState() {
        const checked = document.querySelectorAll('.purge-checkbox:checked');
        const count = checked.length;
        if (btnPurge) btnPurge.style.display = count > 0 ? 'inline-block' : 'none';
        if (selectedCount) selectedCount.textContent = count;
        if (modalCount) modalCount.textContent = count;

        if (purgeForm) {
            purgeForm.querySelectorAll('input[name="ids[]"]').forEach(el => el.remove());
            checked.forEach(cb => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = cb.value;
                purgeForm.appendChild(input);
            });
        }
    }

    if (selectAll) {
        selectAll.addEventListener('change', function() {
            document.querySelectorAll('.purge-checkbox').forEach(cb => cb.checked = this.checked);
            updatePurgeState();
        });
    }

    document.querySelectorAll('.purge-checkbox').forEach(cb => {
        cb.addEventListener('change', updatePurgeState);
    });
});
</script>
@endpush
@endsection
