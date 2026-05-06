<?php $__env->startSection('title', 'Pengaturan Perusahaan'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header"><div><h1>Pengaturan Perusahaan</h1></div></div>

<form method="POST" action="<?php echo e(route('admin.company-settings.update')); ?>">
    <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-white"><h6 class="mb-0 fw-semibold">Identitas Perusahaan</h6><small class="text-body-secondary">Nama dan alamat resmi perusahaan</small></div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Perusahaan <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('name', $tenant->name ?? '')); ?>" required>
                        <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat Lengkap</label>
                        <textarea name="address" class="form-control" rows="3"><?php echo e(old('address', $tenant->address ?? '')); ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email Perusahaan</label>
                        <input type="email" name="email" class="form-control" value="<?php echo e(old('email', $tenant->email ?? '')); ?>">
                    </div>
                </div>
            </div>
            <div class="card mb-4">
                <div class="card-header bg-white"><h6 class="mb-0 fw-semibold">Informasi Rekening Bank</h6></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label">Nama Bank</label><input type="text" name="bank_name" class="form-control" value="<?php echo e(old('bank_name', $tenant->bank_name ?? '')); ?>"></div>
                        <div class="col-md-6"><label class="form-label">No. Rekening</label><input type="text" name="bank_account_number" class="form-control" value="<?php echo e(old('bank_account_number', $tenant->bank_account_number ?? '')); ?>"></div>
                        <div class="col-md-6"><label class="form-label">Nama Pemilik</label><input type="text" name="bank_account_name" class="form-control" value="<?php echo e(old('bank_account_name', $tenant->bank_account_name ?? '')); ?>"></div>
                    </div>
                </div>
            </div>
            <div class="card mb-4">
                <div class="card-header bg-white"><h6 class="mb-0 fw-semibold">Pejabat & Otorisasi</h6></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4"><label class="form-label">Nama Direktur</label><input type="text" name="director_name" class="form-control" value="<?php echo e(old('director_name', $tenant->director_name ?? '')); ?>"></div>
                        <div class="col-md-4"><label class="form-label">Nama Manajer</label><input type="text" name="manager_name" class="form-control" value="<?php echo e(old('manager_name', $tenant->manager_name ?? '')); ?>"></div>
                        <div class="col-md-4"><label class="form-label">Bag. Keuangan</label><input type="text" name="finance_name" class="form-control" value="<?php echo e(old('finance_name', $tenant->finance_name ?? '')); ?>"></div>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary"><i class="cil-save me-1"></i> Simpan Perubahan</button>
        </div>
    </div>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECT_HERD\tpst-app\resources\views\admin\company-settings.blade.php ENDPATH**/ ?>