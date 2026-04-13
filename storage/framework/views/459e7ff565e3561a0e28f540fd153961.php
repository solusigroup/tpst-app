<?php $__env->startSection('title', 'Users'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div><h1>Users</h1></div>
    <a href="<?php echo e(route('admin.users.create')); ?>" class="btn btn-primary"><i class="cil-plus me-1"></i> Tambah User</a>
</div>
<div class="card">
    <div class="card-header bg-white py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-auto"><input type="text" name="search" class="form-control" placeholder="Cari nama/email..." value="<?php echo e(request('search')); ?>"></div>
            <div class="col-auto"><button class="btn btn-outline-primary" type="submit"><i class="cil-search me-1"></i> Cari</button></div>
            <?php if(request('search')): ?><div class="col-auto"><a href="<?php echo e(route('admin.users.index')); ?>" class="btn btn-outline-secondary">Reset</a></div><?php endif; ?>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light"><tr><th>Nama</th><th>Username</th><th>Email</th><th>Role</th><th>Tenant</th><th class="text-end">Aksi</th></tr></thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><strong><?php echo e($user->name); ?></strong></td>
                        <td><?php echo e($user->username); ?></td>
                        <td><?php echo e($user->email); ?></td>
                        <td>
                            <?php $__currentLoopData = $user->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <span class="badge bg-primary me-1"><?php echo e(ucfirst(str_replace('_', ' ', $role->name))); ?></span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php if($user->roles->isEmpty()): ?>
                                <span class="badge bg-secondary me-1">Belum ada role</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo e($user->tenant->name ?? '-'); ?></td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="<?php echo e(route('admin.users.edit', $user)); ?>" class="btn btn-outline-primary"><i class="cil-pencil"></i></a>
                                <?php if (! ($user->isSuperAdmin())): ?>
                                <form method="POST" action="<?php echo e(route('admin.users.destroy', $user)); ?>" class="d-inline"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?><button type="submit" onclick="return confirm('Yakin hapus?')" class="btn btn-outline-danger"><i class="cil-trash"></i></button></form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="6" class="text-center py-4 text-body-secondary">Belum ada data.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if($users->hasPages()): ?> <div class="card-footer bg-white"><?php echo e($users->links()); ?></div> <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECT_HERD\tpst-app\resources\views/admin/users/index.blade.php ENDPATH**/ ?>