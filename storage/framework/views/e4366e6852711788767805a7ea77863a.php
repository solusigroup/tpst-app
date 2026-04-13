<?php $__env->startSection('title', 'Data Mesin'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1>Master Data Mesin</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Mesin</li>
            </ol>
        </nav>
    </div>
    <a href="<?php echo e(route('admin.machines.create')); ?>" class="btn btn-primary">
        <i class="cil-plus"></i> Tambah Mesin
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode/Nomor Mesin</th>
                        <th>Nama Mesin</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $machines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $machine): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($loop->iteration + $machines->firstItem() - 1); ?></td>
                        <td><span class="badge bg-secondary"><?php echo e($machine->nomor_mesin); ?></span></td>
                        <td><?php echo e($machine->nama_mesin); ?></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="<?php echo e(route('admin.machines.edit', $machine->id)); ?>" class="btn btn-outline-info" title="Edit">
                                    <i class="cil-pencil"></i>
                                </a>
                                <form action="<?php echo e(route('admin.machines.destroy', $machine->id)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus mesin ini?');">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-outline-danger" title="Hapus">
                                        <i class="cil-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="4" class="text-center py-4 text-muted">Belum ada data mesin.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <div class="mt-3">
            <?php echo e($machines->withQueryString()->links()); ?>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECT_HERD\tpst-app\resources\views/admin/machines/index.blade.php ENDPATH**/ ?>