<?php $__env->startSection('title', 'Kategori Sampah'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h1>Kategori Sampah</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li><li class="breadcrumb-item active">Kategori Sampah</li></ol></nav>
    </div>
    <a href="<?php echo e(route('admin.hrd.waste-category.create')); ?>" class="btn btn-primary"><i class="cil-plus me-1"></i> Tambah Kategori</a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>Nama Kategori</th><th>Deskripsi</th><th>Satuan</th><th>Status</th><th class="text-end">Aksi</th></tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><strong><?php echo e($item->name); ?></strong></td>
                        <td><small class="text-body-secondary"><?php echo e(Str::limit($item->description ?? '-', 50)); ?></small></td>
                        <td><?php echo e($item->unit); ?></td>
                        <td>
                            <?php if($item->is_active): ?> <span class="badge bg-success">Aktif</span>
                            <?php else: ?> <span class="badge bg-danger">Non-aktif</span> <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="<?php echo e(route('admin.hrd.waste-category.edit', $item)); ?>" class="btn btn-outline-primary"><i class="cil-pencil"></i></a>
                                <?php if($item->is_active): ?>
                                <form method="POST" action="<?php echo e(route('admin.hrd.waste-category.destroy', $item)); ?>" class="d-inline" >
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Yakin ingin menghapus data ini?')"><i class="cil-trash"></i></button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="5" class="text-center py-4 text-body-secondary">Belum ada data kategori.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if($categories->hasPages()): ?> <div class="card-footer bg-white"><?php echo e($categories->links()); ?></div> <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECT_HERD\tpst-app\resources\views/admin/hrd/waste-category/index.blade.php ENDPATH**/ ?>