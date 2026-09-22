@extends('layouts.admin')
@section('title', 'Master Asal Sampah')

@section('content')
<div class="page-header">
    <div>
        <h1>Master Asal Sampah</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Master Asal Sampah</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-primary" data-coreui-toggle="modal" data-coreui-target="#addModal">
            <i class="cil-plus me-1"></i> Tambah Asal Sampah
        </button>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-auto">
                <input type="text" name="search" class="form-control" placeholder="Cari nama asal sampah/klien..." value="{{ request('search') }}">
            </div>
            <div class="col-auto">
                <select name="klien_id" class="form-select">
                    <option value="">Semua Klien</option>
                    @foreach($kliens as $k)
                        <option value="{{ $k->id }}" {{ request('klien_id') == $k->id ? 'selected' : '' }}>{{ $k->nama_klien }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>
            <div class="col-auto">
                <button class="btn btn-outline-primary" type="submit"><i class="cil-search me-1"></i> Cari</button>
            </div>
            @if(request()->hasAny(['search', 'klien_id', 'status']))
                <div class="col-auto">
                    <a href="{{ route('admin.master-asal-sampah.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            @endif
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Asal Sampah</th>
                        <th>Klien</th>
                        <th>Kategori</th>
                        <th>Status</th>
                        <th>Total Ritase</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($asalSampahList as $item)
                    <tr>
                        <td><strong>{{ $item->nama_asal_sampah }}</strong></td>
                        <td>{{ $item->klien->nama_klien ?? '-' }}</td>
                        <td>{{ $item->kategori ?? '-' }}</td>
                        <td>
                            @if($item->is_active)
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-danger">Nonaktif</span>
                            @endif
                        </td>
                        <td>{{ $item->total_ritase }}</td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-primary" data-coreui-toggle="modal" data-coreui-target="#editModal{{ $item->id }}">
                                    <i class="cil-pencil"></i>
                                </button>
                                <button type="button" class="btn btn-outline-warning" data-coreui-toggle="modal" data-coreui-target="#mergeModal{{ $item->id }}" title="Gabungkan (Merge)">
                                    <i class="cil-object-group"></i>
                                </button>
                                <form method="POST" action="{{ route('admin.master-asal-sampah.destroy', $item) }}" class="d-inline">
                                    @csrf 
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Yakin hapus asal sampah ini?')" class="btn btn-outline-danger">
                                        <i class="cil-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="editModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form method="POST" action="{{ route('admin.master-asal-sampah.update', $item) }}">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Asal Sampah</h5>
                                        <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body text-start">
                                        <div class="mb-3">
                                            <label class="form-label">Klien <span class="text-danger">*</span></label>
                                            <select name="klien_id" class="form-select" required>
                                                <option value="">-- Pilih Klien --</option>
                                                @foreach($kliens as $k)
                                                    <option value="{{ $k->id }}" {{ $item->klien_id == $k->id ? 'selected' : '' }}>{{ $k->nama_klien }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Nama Asal Sampah <span class="text-danger">*</span></label>
                                            <input type="text" name="nama_asal_sampah" class="form-control" value="{{ $item->nama_asal_sampah }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Kategori</label>
                                            <input type="text" name="kategori" class="form-control" value="{{ $item->kategori }}">
                                        </div>
                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActiveEdit{{ $item->id }}" {{ $item->is_active ? 'checked' : '' }}>
                                                <label class="form-check-label" for="isActiveEdit{{ $item->id }}">Aktif</label>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="update_history" value="1" id="updateHistoryEdit{{ $item->id }}">
                                                <label class="form-check-label" for="updateHistoryEdit{{ $item->id }}">Selaraskan Riwayat Ritase Lama</label>
                                                <div class="form-text">Jika dicentang, semua transaksi ritase lama yang menggunakan nama sebelumnya akan diperbarui.</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary">Simpan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Merge Modal -->
                    <div class="modal fade" id="mergeModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form method="POST" action="{{ route('admin.master-asal-sampah.merge') }}">
                                    @csrf
                                    <input type="hidden" name="source_id" value="{{ $item->id }}">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Gabungkan (Merge) Asal Sampah</h5>
                                        <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body text-start">
                                        <p>Anda akan menggabungkan data berikut ke data lain (Target).</p>
                                        <div class="alert alert-warning">
                                            <strong>Sumber (Akan dihapus):</strong><br>
                                            {{ $item->nama_asal_sampah }} ({{ $item->klien->nama_klien ?? '-' }})<br>
                                            Total Ritase: {{ $item->total_ritase }}
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Pilih Target Asal Sampah Utama <span class="text-danger">*</span></label>
                                            <select name="target_id" class="form-select" required>
                                                <option value="">-- Pilih Target --</option>
                                                @foreach($asalSampahList as $target)
                                                    @if($target->id != $item->id)
                                                        <option value="{{ $target->id }}">
                                                            {{ $target->nama_asal_sampah }} ({{ $target->klien->nama_klien ?? '-' }})
                                                        </option>
                                                    @endif
                                                @endforeach
                                            </select>
                                            <div class="form-text">Data ritase dari sumber akan dipindahkan ke target ini, lalu data sumber akan dihapus.</div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-warning text-dark" onclick="return confirm('Apakah Anda yakin ingin melakukan merge? Tindakan ini tidak dapat dibatalkan.')">Gabungkan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-body-secondary">Belum ada data master asal sampah.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($asalSampahList->hasPages())
        <div class="card-footer bg-white">{{ $asalSampahList->links() }}</div>
    @endif
</div>

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.master-asal-sampah.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Master Asal Sampah</h5>
                    <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Klien <span class="text-danger">*</span></label>
                        <select name="klien_id" class="form-select" required>
                            <option value="">-- Pilih Klien --</option>
                            @foreach($kliens as $k)
                                <option value="{{ $k->id }}">{{ $k->nama_klien }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Asal Sampah <span class="text-danger">*</span></label>
                        <input type="text" name="nama_asal_sampah" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kategori</label>
                        <input type="text" name="kategori" class="form-control">
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActiveAdd" checked>
                            <label class="form-check-label" for="isActiveAdd">Aktif</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
