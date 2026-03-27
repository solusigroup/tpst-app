<?php $__env->startSection('title', 'Vendor'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h1>Vendor</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li>
                <li class="breadcrumb-item active">Vendor</li>
            </ol>
        </nav>
    </div>
    <a href="<?php echo e(route('admin.vendor.create')); ?>" class="btn btn-primary">
        <i class="cil-plus me-1"></i> Tambah Vendor
    </a>
</div>

<div class="card">
    <div class="card-header bg-white py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-auto">
                <input type="text" name="search" class="form-control" placeholder="Cari nama vendor..." value="<?php echo e(request('search')); ?>">
            </div>
            <div class="col-auto">
                <button class="btn btn-outline-primary" type="submit">
                    <i class="cil-search me-1"></i> Cari
                </button>
            </div>
            <?php if(request()->has('search')): ?>
                <div class="col-auto">
                    <a href="<?php echo e(route('admin.vendor.index')); ?>" class="btn btn-outline-secondary">Reset</a>
                </div>
            <?php endif; ?>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Nama Vendor</th>
                        <th>Kontak</th>
                        <th>Alamat</th>
                        <th>Dibuat</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $vendors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><strong><?php echo e($item->nama_vendor); ?></strong></td>
                        <td><?php echo e($item->kontak ?? '-'); ?></td>
                        <td><?php echo e($item->alamat ?? '-'); ?></td>
                        <td><?php echo e($item->created_at?->format('d/m/Y H:i')); ?></td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="<?php echo e(route('admin.vendor.edit', $item)); ?>" class="btn btn-outline-primary">
                                    <i class="cil-pencil"></i>
                                </a>
                                <form method="POST" action="<?php echo e(route('admin.vendor.destroy', $item)); ?>" class="d-inline" onsubmit="return confirm('Yakin hapus?')">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button class="btn btn-outline-danger">
                                        <i class="cil-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="5" class="text-center py-4 text-body-secondary">Belum ada data vendor.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if($vendors->hasPages()): ?>
        <div class="card-footer bg-white">
            <?php echo e($vendors->links()); ?>

        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECT_HERD\tpst-app\resources\views/admin/vendor/index.blade.php ENDPATH**/ ?>