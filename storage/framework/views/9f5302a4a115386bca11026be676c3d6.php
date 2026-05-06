<?php $__env->startSection('title', 'Manajemen Role'); ?>

<?php $__env->startSection('content'); ?>
<div class="row mb-4">
    <div class="col-12">
        <div class="page-header">
            <div>
                <h1>Manajemen Role & Izin</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li>
                        <li class="breadcrumb-item">Administrasi</li>
                        <li class="breadcrumb-item active" aria-current="page">Role</li>
                    </ol>
                </nav>
            </div>
            <?php if(auth()->user()->hasRole('super_admin')): ?>
            <div>
                <a href="<?php echo e(route('admin.roles.create')); ?>" class="btn btn-primary">
                    <i class="cil-plus me-1"></i> Tambah Role
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Role</th>
                                <th>Total Izin (Permissions)</th>
                                <?php if(auth()->user()->hasRole('super_admin')): ?>
                                <th class="text-end">Aksi</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="fw-semibold">
                                    <?php echo e(ucfirst(str_replace('_', ' ', $role->name))); ?>

                                    <?php if($role->name === 'super_admin'): ?>
                                        <span class="badge bg-danger ms-2">Full Access</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-info text-dark"><?php echo e($role->permissions->count()); ?> Izin</span>
                                </td>
                                <?php if(auth()->user()->hasRole('super_admin')): ?>
                                <td class="text-end">
                                    <div class="btn-group">
                                        <a href="<?php echo e(route('admin.roles.edit', $role)); ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                                            <i class="cil-pencil"></i>
                                        </a>
                                        <?php if($role->name !== 'super_admin'): ?>
                                        <form action="<?php echo e(route('admin.roles.destroy', $role)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus Role ini?');">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                                <i class="cil-trash"></i>
                                            </button>
                                        </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <?php endif; ?>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">Belum ada role tambahan.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECT_HERD\tpst-app\resources\views/admin/roles/index.blade.php ENDPATH**/ ?>