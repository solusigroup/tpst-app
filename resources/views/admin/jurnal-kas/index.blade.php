@extends('layouts.admin')
@section('title', 'Jurnal Kas')

@section('content')
<div class="page-header">
    <div>
        <div class="d-flex align-items-center gap-3">
            <h1>Jurnal Kas</h1>
            @php $isNegatif = ($saldoKas ?? 0) < 0; @endphp
            <div class="badge {{ $isNegatif ? 'bg-danger bg-opacity-10 text-danger border border-danger' : 'bg-success bg-opacity-10 text-success border border-success' }} border-opacity-25 px-3 py-2 fs-6 rounded-pill shadow-sm">
                <i class="cil-wallet me-1"></i> Saldo Kas: Rp {{ number_format($saldoKas ?? 0, 0, ',', '.') }}
            </div>
        </div>
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li><li class="breadcrumb-item active">Jurnal Kas</li></ol></nav>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.transfer-kas.create') }}" class="btn btn-outline-primary"><i class="cil-transfer me-1"></i> Transfer Kas/Bank</a>
        @if($isNegatif)
            <button class="btn btn-secondary" disabled title="Saldo Kas negatif — transaksi dinonaktifkan"><i class="cil-plus me-1"></i> Tambah</button>
        @else
            <a href="{{ route('admin.jurnal-kas.create') }}" class="btn btn-primary"><i class="cil-plus me-1"></i> Tambah</a>
        @endif
    </div>
</div>

@if($isNegatif || session('error_saldo_negatif'))
<div class="alert alert-danger border-danger d-flex align-items-start gap-3 mb-0 rounded-3 shadow-sm" role="alert">
    <i class="cil-warning fs-4 mt-1 flex-shrink-0"></i>
    <div>
        <strong class="d-block mb-1">Saldo Kas Bernilai Negatif!</strong>
        Transaksi Kas Kecil (tambah, edit, hapus) dinonaktifkan sementara.<br>
        Silakan lakukan <a href="{{ route('admin.transfer-kas.create') }}" class="alert-link fw-bold">pengisian kas via Transfer Kas/Bank</a> terlebih dahulu untuk memulihkan saldo.
    </div>
</div>
@endif
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
                <label class="form-label small text-muted mb-1">Cari Jumlah</label>
                <input type="number" name="jumlah" class="form-control form-control-sm" placeholder="Nominal..." value="{{ request('jumlah') }}" min="0">
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
            <div class="col-auto">
                <label class="form-label small text-muted mb-1">Tampilkan</label>
                <select name="per_page" class="form-select form-select-sm" onchange="this.form.submit()" title="Tampilkan per halaman">
                    <option value="25" {{ request('per_page', 50) == 25 ? 'selected' : '' }}>25 baris</option>
                    <option value="50" {{ request('per_page', 50) == 50 ? 'selected' : '' }}>50 baris</option>
                    <option value="100" {{ request('per_page', 50) == 100 ? 'selected' : '' }}>100 baris</option>
                    <option value="200" {{ request('per_page', 50) == 200 ? 'selected' : '' }}>200 baris</option>
                    <option value="500" {{ request('per_page', 50) == 500 ? 'selected' : '' }}>500 baris</option>
                </select>
            </div>
            <div class="col-auto"><button class="btn btn-sm btn-outline-primary" type="submit"><i class="cil-search me-1"></i> Filter</button></div>
            @if(request()->hasAny(['search','jumlah','jenis','dari','sampai','sort','per_page']))<div class="col-auto"><a href="{{ route('admin.jurnal-kas.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a></div>@endif
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light"><tr><th>Tanggal</th><th>Jenis</th><th>Akun</th><th>Jumlah</th><th style="min-width:260px;">Deskripsi</th><th>Status</th><th>Bukti</th><th class="text-end">Aksi</th></tr></thead>
                <tbody>
                    @forelse($jurnalKas as $item)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                        <td><span class="badge bg-{{ $item->tipe=='Penerimaan'?'success':'danger' }}">{{ $item->tipe=='Penerimaan'?'Kas Masuk':'Kas Keluar' }}</span></td>
                        <td>{{ $item->coaLawan->nama_akun ?? '-' }}</td>
                        <td><strong>Rp {{ number_format($item->nominal, 0, ',', '.') }}</strong></td>
                        <td style="max-width:350px; white-space:normal; word-break:break-word;">
                            {{ $item->deskripsi ?? '-' }}
                            @if(($item->comments_count ?? 0) > 0)
                                <div class="mt-1">
                                    <span class="badge bg-warning bg-opacity-25 text-dark border border-warning border-opacity-50 small cursor-pointer" onclick="openCommentModal({{ $item->jurnal_header_id ?? $item->id }}, '{{ addslashes($item->nomor_referensi ?? '-') }}', '{{ addslashes($item->deskripsi ?? '-') }}', 'Rp {{ number_format($item->nominal, 0, ',', '.') }}')">
                                        <i class="cil-speech me-1"></i> {{ $item->comments_count }} catatan/konfirmasi
                                    </span>
                                </div>
                            @endif
                        </td>
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
                                <button type="button" 
                                        class="btn btn-outline-{{ ($item->comments_count ?? 0) > 0 ? 'warning text-dark' : 'secondary' }} btn-comment-trigger" 
                                        id="btn-comment-{{ $item->jurnal_header_id ?? $item->id }}"
                                        onclick="openCommentModal({{ $item->jurnal_header_id ?? $item->id }}, '{{ addslashes($item->nomor_referensi ?? '-') }}', '{{ addslashes($item->deskripsi ?? '-') }}', 'Rp {{ number_format($item->nominal, 0, ',', '.') }}')"
                                        title="{{ ($item->comments_count ?? 0) > 0 ? ($item->comments_count . ' Komentar / Konfirmasi Staf') : 'Beri Catatan / Konfirmasi Staf' }}">
                                    <i class="cil-speech"></i>
                                    @if(($item->comments_count ?? 0) > 0)
                                        <span class="badge bg-danger rounded-pill ms-1 badge-count" style="font-size:0.65rem;">{{ $item->comments_count }}</span>
                                    @else
                                        <span class="badge bg-danger rounded-pill ms-1 badge-count d-none" style="font-size:0.65rem;">0</span>
                                    @endif
                                </button>
                                @if($item->is_jurnal_umum)
                                    <a href="{{ route('admin.jurnal.edit', $item->id) }}" class="btn btn-outline-primary" title="Edit Jurnal Umum"><i class="cil-pencil"></i></a>
                                    <form method="POST" action="{{ route('admin.jurnal.destroy', $item->id) }}" class="d-inline">@csrf @method('DELETE')<button type="submit" onclick="return confirm('Yakin hapus Jurnal Umum ini?')" class="btn btn-outline-danger"><i class="cil-trash"></i></button></form>
                                @else
                                    @if($isNegatif)
                                        <button class="btn btn-outline-secondary" disabled title="Saldo Kas negatif — transaksi dinonaktifkan"><i class="cil-pencil"></i></button>
                                        <button class="btn btn-outline-secondary" disabled title="Saldo Kas negatif — transaksi dinonaktifkan"><i class="cil-trash"></i></button>
                                    @else
                                        <a href="{{ route('admin.jurnal-kas.edit', $item->id) }}" class="btn btn-outline-primary" title="Edit Jurnal Kas"><i class="cil-pencil"></i></a>
                                        <form method="POST" action="{{ route('admin.jurnal-kas.destroy', $item->id) }}" class="d-inline">@csrf @method('DELETE')<button type="submit" onclick="return confirm('Yakin hapus?')" class="btn btn-outline-danger"><i class="cil-trash"></i></button></form>
                                    @endif
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
    <div class="card-footer bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="text-body-secondary small">
            Menampilkan {{ $jurnalKas->firstItem() ?? 0 }} - {{ $jurnalKas->lastItem() ?? 0 }} dari total {{ number_format($jurnalKas->total(), 0, ',', '.') }} data
        </div>
        @if($jurnalKas->hasPages())
            <div>{{ $jurnalKas->links() }}</div>
        @endif
    </div>
</div>

{{-- Modal Catatan & Konfirmasi Transaksi --}}
<div class="modal fade" id="commentModal" tabindex="-1" aria-labelledby="commentModalLabel" aria-hidden="true" style="display:none; background: rgba(0,0,0,0.45);">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-white border-bottom py-3">
                <div>
                    <h5 class="modal-title fw-bold mb-0 text-dark" id="commentModalLabel">
                        <i class="cil-speech me-2 text-primary"></i>Catatan & Konfirmasi Transaksi
                    </h5>
                    <div class="small text-muted mt-1">
                        Ruang komunikasi Superadmin / Manajemen dengan Staf Entry
                    </div>
                </div>
                <button type="button" class="btn-close" onclick="closeCommentModal()" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3">
                <div class="p-2 mb-3 bg-light rounded border d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <span class="badge bg-secondary me-1" id="infoRef">-</span>
                        <span class="fw-semibold text-dark" id="infoDeskripsi">-</span>
                    </div>
                    <div class="fw-bold text-success fs-6" id="infoNominal">
                        Rp 0
                    </div>
                </div>

                <div class="comment-stream-container border rounded p-3 mb-3" id="commentStream" style="max-height: 350px; min-height: 180px; overflow-y: auto; background-color: #f8fafc;">
                    <div class="text-center py-4 text-muted" id="commentLoading">
                        <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                        Memuat diskusi & catatan...
                    </div>
                    <div id="commentList" style="display: none;"></div>
                </div>

                {{-- Template Saran Cepat --}}
                <div class="mb-2">
                    <div class="text-muted small mb-1 fw-semibold"><i class="cil-lightbulb me-1"></i>Template Cepat:</div>
                    <div class="d-flex flex-wrap gap-1">
                        <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 quick-chip" style="font-size: 0.75rem;" data-text="Mohon upload ulang/perjelas foto bukti transaksi fisik.">
                            📷 Bukti kurang jelas
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 quick-chip" style="font-size: 0.75rem;" data-text="Tolong cek kembali nominal, apakah sudah sesuai dengan nota/kwitansi?">
                            ⚠️ Cek nominal nota
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 quick-chip" style="font-size: 0.75rem;" data-text="Mohon pastikan akun (COA) lawan sudah sesuai peruntukan transaksi ini.">
                            ❓ Konfirmasi akun COA
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 quick-chip" style="font-size: 0.75rem;" data-text="Sudah dicek oleh manajemen dan transaksi telah sesuai.">
                            ✅ Data sudah sesuai
                        </button>
                    </div>
                </div>

                {{-- Form Kirim Komentar --}}
                <form id="commentForm" class="mt-2">
                    <div class="input-group">
                        <textarea class="form-control" id="commentInput" rows="2" placeholder="Tulis catatan arahan atau pertanyaan konfirmasi..." required></textarea>
                        <button class="btn btn-primary px-3 d-flex flex-column align-items-center justify-content-center" type="submit" id="btnSubmitComment">
                            <i class="cil-send fs-5"></i>
                            <span class="small" style="font-size: 0.7rem;">Kirim</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.comment-bubble {
    border-radius: 10px;
    padding: 10px 14px;
    margin-bottom: 10px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    box-shadow: 0 1px 2px rgba(0,0,0,0.04);
}
.comment-bubble.my-comment {
    background: #eff6ff;
    border-color: #bfdbfe;
}
.cursor-pointer {
    cursor: pointer;
}
</style>
@endpush

@push('scripts')
<script>
let currentJurnalId = null;

function openCommentModal(jurnalId, ref, deskripsi, nominal) {
    currentJurnalId = jurnalId;
    document.getElementById('infoRef').textContent = ref;
    document.getElementById('infoDeskripsi').textContent = deskripsi;
    document.getElementById('infoNominal').textContent = nominal;
    document.getElementById('commentInput').value = '';

    const modalEl = document.getElementById('commentModal');
    modalEl.classList.add('show');
    modalEl.style.display = 'block';

    loadComments(jurnalId);
}

function closeCommentModal() {
    const modalEl = document.getElementById('commentModal');
    modalEl.classList.remove('show');
    modalEl.style.display = 'none';
    currentJurnalId = null;
}

// Quick chip fill
document.querySelectorAll('.quick-chip').forEach(btn => {
    btn.addEventListener('click', function() {
        const text = this.getAttribute('data-text');
        const input = document.getElementById('commentInput');
        input.value = text;
        input.focus();
    });
});

function loadComments(jurnalId) {
    const loading = document.getElementById('commentLoading');
    const list = document.getElementById('commentList');
    loading.style.display = 'block';
    list.style.display = 'none';
    list.innerHTML = '';

    fetch(`/admin/jurnal/${jurnalId}/comments`, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(res => res.json())
    .then(data => {
        loading.style.display = 'none';
        list.style.display = 'block';

        if (!data.comments || data.comments.length === 0) {
            list.innerHTML = `
                <div class="text-center py-4 text-muted">
                    <i class="cil-speech fs-2 mb-2 d-block text-secondary opacity-50"></i>
                    Belum ada catatan atau konfirmasi untuk transaksi ini.<br>
                    <small class="text-muted">Gunakan form di bawah untuk memulai konfirmasi.</small>
                </div>
            `;
            return;
        }

        renderCommentList(data.comments);
    })
    .catch(err => {
        loading.style.display = 'none';
        list.style.display = 'block';
        list.innerHTML = `<div class="alert alert-danger py-2 mb-0">Gagal memuat komentar. Silakan coba lagi.</div>`;
    });
}

function renderCommentList(comments) {
    const list = document.getElementById('commentList');
    list.innerHTML = '';

    comments.forEach(c => {
        const bubble = document.createElement('div');
        bubble.className = `comment-bubble ${c.can_delete ? 'my-comment' : ''}`;
        bubble.id = `comment-item-${c.id}`;

        const roleBadgeClass = c.user_badge === 'danger' ? 'bg-danger' : (c.user_badge === 'primary' ? 'bg-primary' : (c.user_badge === 'info' ? 'bg-info text-dark' : 'bg-secondary'));

        bubble.innerHTML = `
            <div class="d-flex justify-content-between align-items-center mb-1">
                <div>
                    <strong class="text-dark me-1">${escapeHtml(c.user_name)}</strong>
                    <span class="badge ${roleBadgeClass}" style="font-size: 0.68rem;">${escapeHtml(c.user_role)}</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <small class="text-muted" title="${c.created_at_formatted}" style="font-size: 0.72rem;">${c.created_at_human}</small>
                    ${c.can_delete ? `
                        <button type="button" class="btn btn-link text-danger p-0 ms-1" style="font-size: 0.8rem; text-decoration:none;" onclick="deleteComment(${c.id})" title="Hapus Catatan">
                            <i class="cil-trash"></i>
                        </button>
                    ` : ''}
                </div>
            </div>
            <div class="text-secondary small" style="white-space: pre-wrap; word-break: break-word; font-size: 0.875rem;">${escapeHtml(c.comment)}</div>
        `;
        list.appendChild(bubble);
    });

    const stream = document.getElementById('commentStream');
    stream.scrollTop = stream.scrollHeight;
}

document.getElementById('commentForm').addEventListener('submit', function(e) {
    e.preventDefault();
    if (!currentJurnalId) return;

    const input = document.getElementById('commentInput');
    const commentText = input.value.trim();
    if (!commentText) return;

    const btnSubmit = document.getElementById('btnSubmitComment');
    btnSubmit.disabled = true;

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    fetch(`/admin/jurnal/${currentJurnalId}/comments`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ comment: commentText })
    })
    .then(res => res.json())
    .then(data => {
        btnSubmit.disabled = false;
        if (data.status === 'success') {
            input.value = '';
            loadComments(currentJurnalId);
            updateButtonBadge(currentJurnalId, data.total_count);
        } else {
            alert(data.message || 'Gagal mengirim komentar.');
        }
    })
    .catch(err => {
        btnSubmit.disabled = false;
        alert('Terjadi kesalahan koneksi saat mengirim komentar.');
    });
});

function deleteComment(commentId) {
    if (!confirm('Hapus komentar ini?')) return;

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    fetch(`/admin/jurnal/comments/${commentId}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            const item = document.getElementById(`comment-item-${commentId}`);
            if (item) item.remove();
            updateButtonBadge(currentJurnalId, data.remaining_count);
            if (data.remaining_count === 0) {
                loadComments(currentJurnalId);
            }
        } else {
            alert(data.message || 'Gagal menghapus komentar.');
        }
    })
    .catch(err => {
        alert('Gagal menghapus komentar.');
    });
}

function updateButtonBadge(jurnalId, count) {
    const btn = document.getElementById(`btn-comment-${jurnalId}`);
    if (btn) {
        const badge = btn.querySelector('.badge-count');
        if (badge) {
            badge.textContent = count;
            if (count > 0) {
                badge.classList.remove('d-none');
                btn.classList.remove('btn-outline-secondary');
                btn.classList.add('btn-outline-warning', 'text-dark');
            } else {
                badge.classList.add('d-none');
                btn.classList.remove('btn-outline-warning', 'text-dark');
                btn.classList.add('btn-outline-secondary');
            }
        }
    }
}

function escapeHtml(text) {
    if (!text) return '';
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.toString().replace(/[&<>"']/g, m => map[m]);
}
</script>
@endpush
@endsection
