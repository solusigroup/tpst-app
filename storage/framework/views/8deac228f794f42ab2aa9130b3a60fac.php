<?php $__env->startSection('title', 'Manajemen Tenant'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header d-flex justify-content-between align-items-center">
    <div><h1>Manajemen Tenant</h1><nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?php echo e(route('central.dashboard')); ?>">Central</a></li><li class="breadcrumb-item active">Tenant</li></ol></nav></div>
    <a href="<?php echo e(route('central.tenants.create')); ?>" class="btn btn-primary"><i class="cil-plus me-1"></i> Tambah Tenant Baru</a>
</div>

<div class="card mb-4"><div class="card-body py-3">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-4"><label class="form-label mb-0 small text-body-secondary">Cari</label><input type="text" name="search" class="form-control" value="<?php echo e(request('search')); ?>" placeholder="Nama / Domain"></div>
        <div class="col-auto"><button class="btn btn-secondary" type="submit"><i class="cil-search"></i> Cari</button></div>
    </form>
</div></div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light"><tr><th>ID</th><th>Nama Tenant</th><th>Domain</th><th class="text-center">Jumlah User</th><th>Tanggal Dibuat</th><th class="text-end">Aksi</th></tr></thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $tenants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($t->id); ?></td>
                        <td><strong><?php echo e($t->name); ?></strong></td>
                        <td><code><?php echo e($t->domain); ?></code></td>
                        <td class="text-center"><span class="badge bg-secondary rounded-pill"><?php echo e($t->users_count); ?></span></td>
                        <td><?php echo e($t->created_at->format('d M Y H:i')); ?></td>
                        <td class="text-end">
                            <a href="<?php echo e(route('central.tenants.edit', $t->id)); ?>" class="btn btn-sm btn-warning"><i class="cil-pencil"></i></a>
                            <form action="<?php echo e(route('central.tenants.destroy', $t->id)); ?>" method="POST" class="d-inline" >
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?> <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus data ini?')"><i class="cil-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="6" class="text-center py-4 text-body-secondary">Data tenant tidak ditemukan.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if($tenants->hasPages()): ?> <div class="card-footer bg-white"><?php echo e($tenants->links()); ?></div> <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECT_HERD\tpst-app\resources\views\central\tenants\index.blade.php ENDPATH**/ ?>