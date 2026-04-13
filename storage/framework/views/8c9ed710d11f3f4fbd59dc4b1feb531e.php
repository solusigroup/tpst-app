<?php $__env->startSection('title', isset($role) ? 'Edit Role' : 'Tambah Role'); ?>

<?php $__env->startSection('content'); ?>
<div class="row mb-4">
    <div class="col-12">
        <div class="page-header">
            <div>
                <h1><?php echo e(isset($role) ? 'Edit Role' : 'Tambah Role Baru'); ?></h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo e(route('admin.roles.index')); ?>">Role & Izin</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?php echo e(isset($role) ? 'Edit' : 'Tambah'); ?></li>
                    </ol>
                </nav>
            </div>
            <div>
                <a href="<?php echo e(route('admin.roles.index')); ?>" class="btn btn-secondary">
                    <i class="cil-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="<?php echo e(isset($role) ? route('admin.roles.update', $role) : route('admin.roles.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php if(isset($role)): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>

                    <div class="mb-4">
                        <label for="name" class="form-label text-primary fw-bold">Nama Akses (Role Name)</label>
                        <input type="text" class="form-control form-control-lg <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="name" name="name" 
                            value="<?php echo e(old('name', $role->name ?? '')); ?>" 
                            placeholder="Contoh: mandor, kasir, logistik" required>
                        <div class="form-text">Gunakan huruf kecil tanpa spasi (bisa pakai underscore _). Contoh: <code>supervisor_lapangan</code>.</div>
                        <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <hr>
                    <h5 class="mb-3">Pengaturan Izin (Permissions)</h5>
                    <p class="text-muted small mb-4">Pilih modul/fitur apa saja yang (CAN) diakses oleh Role ini. Jika tidak dicentang maka mereka (CANNOT) melihat/mengubah data tersebut.</p>

                    <div class="row g-4">
                        <?php $__currentLoopData = $permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group => $perms): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="col-md-4 col-sm-6">
                                <div class="card h-100 bg-light border-0 shadow-sm">
                                    <div class="card-header bg-transparent border-bottom border-secondary text-uppercase fw-bold text-secondary">
                                        Modul: <?php echo e(str_replace('_', ' ', $group)); ?>

                                    </div>
                                    <div class="card-body p-3">
                                        <?php $__currentLoopData = $perms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $perm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="form-check form-switch mb-2">
                                                <input class="form-check-input" type="checkbox" role="switch" 
                                                    id="perm_<?php echo e($perm->id); ?>" name="permissions[]" value="<?php echo e($perm->name); ?>"
                                                    <?php echo e((isset($rolePermissions) && in_array($perm->name, $rolePermissions)) ? 'checked' : ''); ?>>
                                                <label class="form-check-label" for="perm_<?php echo e($perm->id); ?>">
                                                    <?php echo e(str_replace('_', ' ', $perm->name)); ?>

                                                </label>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>

                    <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                        <a href="<?php echo e(route('admin.roles.index')); ?>" class="btn btn-light me-2">Batal</a>
                        <button type="submit" class="btn btn-primary text-white">
                            <i class="cil-save me-1"></i> Simpan Role
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECT_HERD\tpst-app\resources\views/admin/roles/form.blade.php ENDPATH**/ ?>