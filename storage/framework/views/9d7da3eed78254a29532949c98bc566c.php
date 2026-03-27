<?php $__env->startSection('title', 'Buku Pembantu Utang Lancar'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h1>Buku Pembantu Utang Lancar</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li>
                <li class="breadcrumb-item active">Buku Pembantu Utang Lancar</li>
            </ol>
        </nav>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-auto">
                <input type="text" name="search" class="form-control" placeholder="Cari nama vendor..." value="<?php echo e(request('search')); ?>">
            </div>
            <div class="col-auto">
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="pending" <?php echo e(request('status') == 'pending' ? 'selected' : ''); ?>>Pending</option>
                    <option value="lunas" <?php echo e(request('status') == 'lunas' ? 'selected' : ''); ?>>Lunas</option>
                </select>
            </div>
            <div class="col-auto">
                <button class="btn btn-outline-primary" type="submit">
                    <i class="cil-search me-1"></i> Cari
                </button>
            </div>
            <?php if(request()->hasAny(['search', 'status'])): ?>
                <div class="col-auto">
                    <a href="<?php echo e(route('admin.buku-pembantu.utang')); ?>" class="btn btn-outline-secondary">Reset</a>
                </div>
            <?php endif; ?>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Tanggal</th>
                        <th>Nama Vendor</th>
                        <th>Keterangan</th>
                        <th class="text-end">Jumlah</th>
                        <th>Jatuh Tempo</th>
                        <th>Bukti</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $entries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($item->tanggal->format('d/m/Y')); ?></td>
                        <td><strong><?php echo e($item->contactable->nama_vendor ?? $item->contactable->nama_klien ?? 'N/A'); ?></strong></td>
                        <td class="small"><?php echo e($item->keterangan); ?></td>
                        <td class="text-end fw-bold text-danger">
                            <div>Rp <?php echo e(number_format($item->jumlah, 0, ',', '.')); ?></div>
                            <?php if($item->terbayar > 0 && $item->status == 'pending'): ?>
                                <div class="text-muted small">Sisa: Rp <?php echo e(number_format($item->jumlah - $item->terbayar, 0, ',', '.')); ?></div>
                            <?php endif; ?>
                        </td>
                        <td class="<?php echo e($item->tanggal_jatuh_tempo < now() && $item->status == 'pending' ? 'text-danger fw-bold' : ''); ?>">
                            <?php echo e($item->tanggal_jatuh_tempo?->format('d/m/Y') ?? '-'); ?>

                        </td>
                        <td>
                            <?php if($item->jurnalHeader?->bukti_transaksi): ?>
                                <a href="<?php echo e(asset('storage/' . $item->jurnalHeader->bukti_transaksi)); ?>" target="_blank" class="btn btn-sm btn-outline-info">
                                    <i class="cil-image"></i>
                                </a>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge bg-<?php echo e($item->status == 'lunas' ? 'success' : 'warning'); ?>">
                                <?php echo e(ucfirst($item->status)); ?>

                            </span>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-body-secondary">Belum ada data utang lancar.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if($entries->hasPages()): ?>
        <div class="card-footer bg-white">
            <?php echo e($entries->links()); ?>

        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECT_HERD\tpst-app\resources\views/admin/buku_pembantu/utang.blade.php ENDPATH**/ ?>