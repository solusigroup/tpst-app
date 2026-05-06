<?php $__env->startSection('title', 'Manajemen Central Users'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header d-flex justify-content-between align-items-center">
    <div><h1>Semua User (Central)</h1><nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?php echo e(route('central.dashboard')); ?>">Central</a></li><li class="breadcrumb-item active">Users</li></ol></nav></div>
    <a href="<?php echo e(route('central.users.create')); ?>" class="btn btn-primary"><i class="cil-plus me-1"></i> Tambah User</a>
</div>

<div class="card mb-4"><div class="card-body py-3">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-3"><label class="form-label mb-0 small text-body-secondary">Cari</label><input type="text" name="search" class="form-control" value="<?php echo e(request('search')); ?>" placeholder="Nama / Email"></div>
        <div class="col-md-3">
            <label class="form-label mb-0 small text-body-secondary">Tenant</label>
            <select name="tenant_id" class="form-select">
                <option value="">-- Semua Tenant --</option>
                <?php $__currentLoopData = $tenants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($t->id); ?>" <?php echo e(request('tenant_id') == $t->id ? 'selected' : ''); ?>><?php echo e($t->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label mb-0 small text-body-secondary">Role</label>
            <select name="role" class="form-select">
                <option value="">-- Semua Role --</option>
                <?php $__currentLoopData = ['admin','timbangan','keuangan']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($r); ?>" <?php echo e(request('role') == $r ? 'selected' : ''); ?>><?php echo e(ucfirst($r)); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="col-auto"><button class="btn btn-secondary" type="submit"><i class="cil-search"></i> Filter</button></div>
    </form>
</div></div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light"><tr><th>ID</th><th>Tenant</th><th>Nama</th><th>Email</th><th>Role</th><th class="text-center">Super Admin</th><th>Dibuat</th><th class="text-end">Aksi</th></tr></thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($u->id); ?></td>
                        <td><?php echo e($u->tenant->name ?? '-'); ?></td>
                        <td><strong><?php echo e($u->name); ?></strong><br><small class="text-body-secondary"><?php echo e($u->username); ?></small></td>
                        <td><?php echo e($u->email); ?></td>
                        <td>
                            <?php $roleColors = ['admin'=>'success','timbangan'=>'info','keuangan'=>'warning']; ?>
                            <span class="badge bg-<?php echo e($roleColors[$u->role] ?? 'secondary'); ?>"><?php echo e(ucfirst($u->role)); ?></span>
                        </td>
                        <td class="text-center"><?php if($u->is_super_admin): ?> <i class="cil-check-circle text-success fs-5"></i> <?php else: ?> <i class="cil-x-circle text-danger fs-5"></i> <?php endif; ?></td>
                        <td><?php echo e($u->created_at->format('d M Y')); ?></td>
                        <td class="text-end">
                            <a href="<?php echo e(route('central.users.edit', $u->id)); ?>" class="btn btn-sm btn-warning"><i class="cil-pencil"></i></a>
                            <?php if($u->id !== auth()->id()): ?>
                            <form action="<?php echo e(route('central.users.destroy', $u->id)); ?>" method="POST" class="d-inline" >
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?> <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus data ini?')"><i class="cil-trash"></i></button>
                            </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="8" class="text-center py-4 text-body-secondary">Data user tidak ditemukan.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if($users->hasPages()): ?> <div class="card-footer bg-white"><?php echo e($users->links()); ?></div> <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECT_HERD\tpst-app\resources\views\central\users\index.blade.php ENDPATH**/ ?>