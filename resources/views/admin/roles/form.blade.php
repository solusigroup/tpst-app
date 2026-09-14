@extends('layouts.admin')

@section('title', isset($role) ? 'Edit Role' : 'Tambah Role')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="page-header">
            <div>
                <h1>{{ isset($role) ? 'Edit Role' : 'Tambah Role Baru' }}</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">Role & Izin</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ isset($role) ? 'Edit' : 'Tambah' }}</li>
                    </ol>
                </nav>
            </div>
            <div>
                <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
                    <i class="cil-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ isset($role) ? route('admin.roles.update', $role) : route('admin.roles.store') }}" method="POST">
                    @csrf
                    @if(isset($role)) @method('PUT') @endif

                    <div class="mb-4">
                        <label for="name" class="form-label text-primary fw-bold">Nama Akses (Role Name)</label>
                        <input type="text" class="form-control form-control-lg @error('name') is-invalid @enderror" id="name" name="name" 
                            value="{{ old('name', $role->name ?? '') }}" 
                            placeholder="Contoh: mandor, kasir, logistik" required>
                        <div class="form-text">Gunakan huruf kecil tanpa spasi (bisa pakai underscore _). Contoh: <code>supervisor_lapangan</code>.</div>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr class="my-4">

                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                        <div>
                            <h5 class="mb-1 text-dark fw-bold">Pengaturan Izin Akses (Permissions)</h5>
                            <p class="text-muted small mb-0">Tentukan modul dan fitur yang dapat diakses oleh Role ini. Centang izin yang diperbolehkan.</p>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 fw-semibold" id="selectedCounterBadge">
                                <i class="cil-check-circle me-1"></i> <span id="selectedCount">0</span> izin terpilih
                            </span>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="btnSelectAll">
                                <i class="cil-check-alt me-1"></i> Pilih Semua
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="btnDeselectAll">
                                <i class="cil-x me-1"></i> Kosongkan
                            </button>
                        </div>
                    </div>

                    {{-- Search Box --}}
                    <div class="mb-4">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="cil-magnifying-glass"></i></span>
                            <input type="text" class="form-control border-start-0 ps-0" id="permSearch" placeholder="Cari izin atau nama modul... (cth: kpi, ritase, jurnal, dll)">
                            <button class="btn btn-outline-secondary" type="button" id="clearSearch" style="display:none;">Reset</button>
                        </div>
                    </div>

                    <div class="row g-4" id="modulesContainer">
                        @foreach($structuredModules as $groupKey => $mod)
                            <div class="col-lg-6 col-12 module-card" data-group="{{ $groupKey }}">
                                <div class="card h-100 border shadow-sm" style="border-radius: 0.75rem;">
                                    <div class="card-header bg-light border-bottom d-flex justify-content-between align-items-center py-3">
                                        <div>
                                            <h6 class="mb-1 fw-bold text-primary d-flex align-items-center">
                                                <i class="{{ $mod['icon'] }} me-2 fs-5"></i>
                                                {{ $mod['name'] }}
                                            </h6>
                                            @if(!empty($mod['description']))
                                                <div class="text-muted small">{{ $mod['description'] }}</div>
                                            @endif
                                        </div>
                                        <div class="form-check form-switch ms-3 mb-0" title="Pilih / Batalkan semua izin di modul ini">
                                            <input class="form-check-input group-select-all" type="checkbox" role="switch" id="group_switch_{{ $groupKey }}" data-target="{{ $groupKey }}">
                                            <label class="form-check-label small fw-semibold text-secondary" for="group_switch_{{ $groupKey }}">Semua</label>
                                        </div>
                                    </div>
                                    <div class="card-body p-3">
                                        <div class="row g-2">
                                            @foreach($mod['permissions'] as $perm)
                                                @php
                                                    $isChecked = (isset($rolePermissions) && in_array($perm['name'], $rolePermissions));
                                                @endphp
                                                <div class="col-12 perm-item" data-text="{{ strtolower($perm['label'] . ' ' . $perm['name']) }}">
                                                    <div class="form-check form-switch p-2 rounded hover-bg d-flex align-items-start gap-2">
                                                        <input class="form-check-input perm-checkbox ms-0 mt-1" type="checkbox" role="switch" 
                                                            id="perm_{{ $perm['id'] }}" name="permissions[]" value="{{ $perm['name'] }}"
                                                            data-group="{{ $groupKey }}"
                                                            {{ $isChecked ? 'checked' : '' }}>
                                                        <label class="form-check-label flex-grow-1 cursor-pointer" for="perm_{{ $perm['id'] }}" style="line-height: 1.35;">
                                                            <div class="fw-medium text-dark">{{ $perm['label'] }}</div>
                                                            <code class="text-muted" style="font-size: 0.75rem;">{{ $perm['name'] }}</code>
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                        <a href="{{ route('admin.roles.index') }}" class="btn btn-light">
                            <i class="cil-arrow-left me-1"></i> Batal & Kembali
                        </a>
                        <button type="submit" class="btn btn-primary text-white px-4 py-2 fw-semibold shadow-sm">
                            <i class="cil-save me-1"></i> Simpan Role & Hak Akses
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.hover-bg:hover {
    background-color: rgba(59, 125, 221, 0.05);
}
.cursor-pointer {
    cursor: pointer;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.perm-checkbox');
    const groupSwitches = document.querySelectorAll('.group-select-all');
    const selectedCountSpan = document.getElementById('selectedCount');
    const btnSelectAll = document.getElementById('btnSelectAll');
    const btnDeselectAll = document.getElementById('btnDeselectAll');
    const searchInput = document.getElementById('permSearch');
    const clearSearchBtn = document.getElementById('clearSearch');
    const moduleCards = document.querySelectorAll('.module-card');

    function updateCounters() {
        let count = 0;
        checkboxes.forEach(cb => {
            if (cb.checked) count++;
        });
        selectedCountSpan.textContent = count;

        // Update each group switch state
        groupSwitches.forEach(groupSwitch => {
            const group = groupSwitch.getAttribute('data-target');
            const groupBoxes = document.querySelectorAll(`.perm-checkbox[data-group="${group}"]`);
            if (groupBoxes.length > 0) {
                const checkedInGroup = Array.from(groupBoxes).filter(cb => cb.checked).length;
                groupSwitch.checked = (checkedInGroup === groupBoxes.length);
                groupSwitch.indeterminate = (checkedInGroup > 0 && checkedInGroup < groupBoxes.length);
            }
        });
    }

    // Toggle single group switch
    groupSwitches.forEach(groupSwitch => {
        groupSwitch.addEventListener('change', function() {
            const group = this.getAttribute('data-target');
            const groupBoxes = document.querySelectorAll(`.perm-checkbox[data-group="${group}"]`);
            groupBoxes.forEach(cb => {
                // If filtered out by search, still honor group check
                cb.checked = groupSwitch.checked;
            });
            updateCounters();
        });
    });

    // Checkbox individual change
    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateCounters);
    });

    // Global Select All
    btnSelectAll.addEventListener('click', function() {
        checkboxes.forEach(cb => { cb.checked = true; });
        updateCounters();
    });

    // Global Deselect All
    btnDeselectAll.addEventListener('click', function() {
        checkboxes.forEach(cb => { cb.checked = false; });
        updateCounters();
    });

    // Search filter
    searchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        clearSearchBtn.style.display = query ? 'inline-block' : 'none';

        moduleCards.forEach(card => {
            let hasMatchInCard = false;
            const items = card.querySelectorAll('.perm-item');
            items.forEach(item => {
                const text = item.getAttribute('data-text') || '';
                if (!query || text.includes(query)) {
                    item.style.display = '';
                    hasMatchInCard = true;
                } else {
                    item.style.display = 'none';
                }
            });

            card.style.display = (hasMatchInCard || !query) ? '' : 'none';
        });
    });

    clearSearchBtn.addEventListener('click', function() {
        searchInput.value = '';
        clearSearchBtn.style.display = 'none';
        moduleCards.forEach(card => {
            card.style.display = '';
            card.querySelectorAll('.perm-item').forEach(item => {
                item.style.display = '';
            });
        });
        searchInput.focus();
    });

    // Initial counter evaluation
    updateCounters();
});
</script>
@endsection
