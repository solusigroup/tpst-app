<?php $__env->startSection('title', 'Klien'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h1>Klien</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li><li class="breadcrumb-item active">Klien</li></ol></nav>
    </div>
    <a href="<?php echo e(route('admin.klien.create')); ?>" class="btn btn-primary"><i class="cil-plus me-1"></i> Tambah Klien</a>
</div>

<div class="card">
    <div class="card-header bg-white py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-auto">
                <input type="text" name="search" class="form-control" placeholder="Cari nama klien..." value="<?php echo e(request('search')); ?>">
            </div>
            <div class="col-auto">
                <select name="jenis" class="form-select">
                    <option value="">Semua Jenis</option>
                    <?php $__currentLoopData = ['DLH','Swasta','Offtaker','Internal']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $j): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($j); ?>" <?php echo e(request('jenis') == $j ? 'selected' : ''); ?>><?php echo e($j); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-auto"><button class="btn btn-outline-primary" type="submit"><i class="cil-search me-1"></i> Cari</button></div>
            <?php if(request()->hasAny(['search','jenis'])): ?>
                <div class="col-auto"><a href="<?php echo e(route('admin.klien.index')); ?>" class="btn btn-outline-secondary">Reset</a></div>
            <?php endif; ?>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>Nama Klien</th><th>Jenis</th><th>Jenis Tarif</th><th>Besaran Tarif</th><th>Kontak</th><th>Dibuat</th><th class="text-end">Aksi</th></tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $kliens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><strong><a href="<?php echo e(route('admin.klien.show', $item)); ?>" class="text-decoration-none"><?php echo e($item->nama_klien); ?></a></strong></td>
                        <td>
                            <?php
                                $badgeColor = match($item->jenis) {
                                    'DLH' => 'bg-info',
                                    'Swasta' => 'bg-primary',
                                    'Offtaker' => 'bg-success',
                                    'Internal' => 'bg-warning text-dark',
                                    default => 'bg-secondary'
                                };
                            ?>
                            <span class="badge <?php echo e($badgeColor); ?>"><?php echo e($item->jenis); ?></span>
                        </td>
                        <td>
                            <?php if($item->jenis_tarif): ?> 
                                <span class="badge border border-secondary text-secondary"><?php echo e($item->jenis_tarif); ?></span> 
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php echo e($item->besaran_tarif ? 'Rp ' . number_format($item->besaran_tarif, 0, ',', '.') : '-'); ?>

                        </td>
                        <td><?php echo e($item->kontak ?? '-'); ?></td>
                        <td><?php echo e($item->created_at?->format('d/m/Y H:i')); ?></td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="<?php echo e(route('admin.klien.edit', $item)); ?>" class="btn btn-outline-primary"><i class="cil-pencil"></i></a>
                                <form method="POST" action="<?php echo e(route('admin.klien.destroy', $item)); ?>" class="d-inline"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?><button type="submit" onclick="return confirm('Yakin hapus?')" class="btn btn-outline-danger"><i class="cil-trash"></i></button></form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="7" class="text-center py-4 text-body-secondary">Belum ada data klien.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if($kliens->hasPages()): ?> <div class="card-footer bg-white"><?php echo e($kliens->links()); ?></div> <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECT_HERD\tpst-app\resources\views/admin/klien/index.blade.php ENDPATH**/ ?>