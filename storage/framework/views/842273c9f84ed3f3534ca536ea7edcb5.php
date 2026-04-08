<?php $__env->startSection('title', isset($klien) ? 'Edit Klien' : 'Tambah Klien'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-header">
        <div>
            <h1><?php echo e(isset($klien) ? 'Edit Klien' : 'Tambah Klien'); ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.klien.index')); ?>">Klien</a></li>
                    <li class="breadcrumb-item active"><?php echo e(isset($klien) ? 'Edit' : 'Tambah'); ?></li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form method="POST"
                        action="<?php echo e(isset($klien) ? route('admin.klien.update', $klien) : route('admin.klien.store')); ?>">
                        <?php echo csrf_field(); ?>
                        <?php if(isset($klien)): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Klien <span class="text-danger">*</span></label>
                                <input type="text" name="nama_klien"
                                    class="form-control <?php $__errorArgs = ['nama_klien'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    value="<?php echo e(old('nama_klien', $klien->nama_klien ?? '')); ?>" required>
                                <?php $__errorArgs = ['nama_klien'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Jenis <span class="text-danger">*</span></label>
                                <select name="jenis" id="jenis_select"
                                    class="form-select <?php $__errorArgs = ['jenis'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                    <option value="">-- Pilih --</option>
                                    <?php $__currentLoopData = ['DLH' => 'DLH', 'Swasta' => 'Swasta', 'Offtaker' => 'Offtaker', 'Internal' => 'Internal']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($val); ?>" <?php echo e(old('jenis', $klien->jenis ?? '') == $val ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php $__errorArgs = ['jenis'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-md-6" id="tarif_container">
                                <div class="row">
                                    <div class="col-6">
                                        <label class="form-label">Jenis Tarif</label>
                                        <select name="jenis_tarif"
                                            class="form-select <?php $__errorArgs = ['jenis_tarif'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                            <option value="">-- Pilih --</option>
                                            <option value="Bulanan" <?php echo e(old('jenis_tarif', $klien->jenis_tarif ?? '') == 'Bulanan' ? 'selected' : ''); ?>>Bulanan</option>
                                            <option value="Per Ritase" <?php echo e(old('jenis_tarif', $klien->jenis_tarif ?? '') == 'Per Ritase' ? 'selected' : ''); ?>>Per Ritase</option>
                                        </select>
                                        <?php $__errorArgs = ['jenis_tarif'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label">Besaran Tarif</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" name="besaran_tarif" class="form-control"
                                                value="<?php echo e(old('besaran_tarif', $klien->besaran_tarif ?? '')); ?>" min="0">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Kontak</label>
                                <input type="text" name="kontak" class="form-control"
                                    value="<?php echo e(old('kontak', $klien->kontak ?? '')); ?>">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Alamat</label>
                                <textarea name="alamat" class="form-control"
                                    rows="3"><?php echo e(old('alamat', $klien->alamat ?? '')); ?></textarea>
                            </div>
                            <div class="col-12 d-flex gap-2">
                                <button type="submit" class="btn btn-primary"><i class="cil-save me-1"></i>
                                    <?php echo e(isset($klien) ? 'Perbarui' : 'Simpan'); ?></button>
                                <a href="<?php echo e(route('admin.klien.index')); ?>" class="btn btn-outline-secondary">Kembali</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECT_HERD\tpst-app\resources\views/admin/klien/form.blade.php ENDPATH**/ ?>