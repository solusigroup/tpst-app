<?php $__env->startSection('title', 'Ritase'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h1>Ritase</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li><li class="breadcrumb-item active">Ritase</li></ol></nav>
    </div>
    </div>
    <div class="d-flex gap-2">
        <a href="<?php echo e(route('admin.ritase.export-rekap', request()->all())); ?>" class="btn btn-danger" target="_blank"><i class="cil-print me-1"></i> Cetak Rekap (PDF)</a>
        <a href="<?php echo e(route('admin.ritase.create')); ?>" class="btn btn-primary"><i class="cil-plus me-1"></i> Tambah Ritase</a>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-auto">
                <select name="search_by" class="form-select" title="Cari Berdasarkan">
                    <option value="tiket" <?php echo e(request('search_by') == 'tiket' ? 'selected' : ''); ?>>Tiket</option>
                    <option value="armada" <?php echo e(request('search_by') == 'armada' ? 'selected' : ''); ?>>Armada</option>
                    <option value="klien" <?php echo e(request('search_by') == 'klien' ? 'selected' : ''); ?>>Klien</option>
                    <option value="status_invoice" <?php echo e(request('search_by') == 'status_invoice' ? 'selected' : ''); ?>>Status Invoice</option>
                </select>
            </div>
            <div class="col-auto">
                <input type="text" name="search" class="form-control" placeholder="Teks pencarian..." value="<?php echo e(request('search')); ?>">
            </div>
            <div class="col-auto">
                <input type="date" name="start_date" class="form-control" title="Tanggal Mulai" value="<?php echo e(request('start_date')); ?>">
            </div>
            <div class="col-auto">
                <input type="date" name="end_date" class="form-control" title="Tanggal Selesai" value="<?php echo e(request('end_date')); ?>">
            </div>
            <div class="col-auto">
                <button class="btn btn-outline-primary" type="submit"><i class="cil-search me-1"></i> Cari</button>
            </div>
            <?php if(request()->hasAny(['search', 'start_date', 'end_date'])): ?>
                <div class="col-auto"><a href="<?php echo e(route('admin.ritase.index')); ?>" class="btn btn-outline-secondary">Reset</a></div>
            <?php endif; ?>
        </form>
    </div>
    
    <div class="card-body border-bottom bg-primary bg-opacity-10 py-3">
        <div class="d-flex justify-content-between align-items-center">
            <div class="fw-semibold text-primary">
                <i class="cil-weight me-2"></i> TOTAL BERAT NETTO 
                <?php if(request('start_date') && request('end_date')): ?>
                    (<?php echo e(\Carbon\Carbon::parse(request('start_date'))->translatedFormat('d M Y')); ?> - <?php echo e(\Carbon\Carbon::parse(request('end_date'))->translatedFormat('d M Y')); ?>)
                <?php elseif(request('start_date')): ?>
                    (Melampaui <?php echo e(\Carbon\Carbon::parse(request('start_date'))->translatedFormat('d M Y')); ?>)
                <?php elseif(request('end_date')): ?>
                    (Mendahului <?php echo e(\Carbon\Carbon::parse(request('end_date'))->translatedFormat('d M Y')); ?>)
                <?php else: ?>
                    (Semua Waktu)
                <?php endif; ?>
            </div>
            <div class="fs-4 fw-bold text-primary"><?php echo e(number_format($totalBeratNetto ?? 0, 2, ',', '.')); ?> kg</div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No. Tiket</th>
                        <th>Armada</th>
                        <th>Klien</th>
                        <th>Asal Sampah</th>
                        <th>Berat Netto</th>
                        <th>Status</th>
                        <th>Waktu Masuk</th>
                        <th>Bukti</th>
                        <th>Foto</th>
                        <th>Approved</th>
                        <th>Status Invoice</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $ritase; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><strong><?php echo e($item->nomor_tiket ?? '-'); ?></strong></td>
                        <td><?php echo e($item->armada->plat_nomor ?? '-'); ?></td>
                        <td><?php echo e($item->klien->nama_klien ?? '-'); ?></td>
                        <td><?php echo e($item->jenis_sampah ?? '-'); ?></td>
                        <td><?php echo e(number_format($item->berat_netto, 2, ',', '.')); ?> kg</td>
                        <td>
                            <?php $statusColors = ['masuk'=>'info','timbang'=>'warning','keluar'=>'primary','selesai'=>'success']; ?>
                            <span class="badge bg-<?php echo e($statusColors[$item->status] ?? 'secondary'); ?>"><?php echo e(ucfirst($item->status)); ?></span>
                        </td>
                        <td><?php echo e($item->waktu_masuk ? \Carbon\Carbon::parse($item->waktu_masuk)->format('d/m/Y H:i') : '-'); ?></td>
                        <td><?php echo e($item->tiket ?? '-'); ?></td>
                        <td>
                            <?php if($item->foto_tiket): ?>
                                <a href="<?php echo e(asset('storage/' . $item->foto_tiket)); ?>" target="_blank" class="btn btn-sm btn-outline-info">
                                    <i class="cil-image"></i>
                                </a>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($item->is_approved): ?>
                                <span class="badge bg-success"><i class="cil-check-circle me-1"></i> Approved</span>
                            <?php else: ?>
                                <form method="POST" action="<?php echo e(route('admin.ritase.approve', $item)); ?>">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="btn btn-sm btn-warning">
                                        <i class="cil-check me-1"></i> Approve
                                    </button>
                                </form>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php $invoiceColors = ['Draft'=>'secondary','Sent'=>'info','Paid'=>'success','Canceled'=>'danger']; ?>
                            <span class="badge bg-<?php echo e($invoiceColors[$item->status_invoice] ?? 'secondary'); ?>"><?php echo e($item->status_invoice ?? 'Unbilled'); ?></span>
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="<?php echo e(route('admin.ritase.show', $item)); ?>" class="btn btn-outline-info" title="Lihat"><i class="cil-magnifying-glass"></i></a>
                                <a href="<?php echo e(route('admin.ritase.edit', $item)); ?>" class="btn btn-outline-primary" title="Edit"><i class="cil-pencil"></i></a>
                                <form method="POST" action="<?php echo e(route('admin.ritase.destroy', $item)); ?>" class="d-inline" onsubmit="return confirm('Yakin hapus?')">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button class="btn btn-outline-danger" title="Hapus"><i class="cil-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="8" class="text-center py-4 text-body-secondary">Belum ada data ritase.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if($ritase->hasPages()): ?>
    <div class="card-footer bg-white"><?php echo e($ritase->links()); ?></div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECT_HERD\tpst-app\resources\views/admin/ritase/index.blade.php ENDPATH**/ ?>