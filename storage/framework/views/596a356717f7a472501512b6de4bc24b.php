<?php $__env->startSection('title', isset($jurnalKas) ? 'Edit Jurnal Kas' : 'Tambah Jurnal Kas'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h1><?php echo e(isset($jurnalKas) ? 'Edit' : 'Tambah'); ?> Jurnal Kas</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li><li class="breadcrumb-item"><a href="<?php echo e(route('admin.jurnal-kas.index')); ?>">Jurnal Kas</a></li><li class="breadcrumb-item active"><?php echo e(isset($jurnalKas) ? 'Edit' : 'Tambah'); ?></li></ol></nav>
    </div>
</div>
<div class="row"><div class="col-lg-8"><div class="card"><div class="card-body">
    <form method="POST" action="<?php echo e(isset($jurnalKas) ? route('admin.jurnal-kas.update', $jurnalKas) : route('admin.jurnal-kas.store')); ?>" enctype="multipart/form-data">
        <?php echo csrf_field(); ?> <?php if(isset($jurnalKas)): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                <input type="date" name="tanggal" class="form-control <?php $__errorArgs = ['tanggal'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('tanggal', isset($jurnalKas) ? \Carbon\Carbon::parse($jurnalKas->tanggal)->format('Y-m-d') : '')); ?>" required>
                <?php $__errorArgs = ['tanggal'];
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
                <select name="jenis" class="form-select <?php $__errorArgs = ['jenis'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                    <option value="">-- Pilih --</option>
                    <option value="masuk" <?php echo e(old('jenis', ($jurnalKas->tipe ?? '') == 'Penerimaan' ? 'masuk' : '') == 'masuk' ? 'selected' : ''); ?>>Kas Masuk</option>
                    <option value="keluar" <?php echo e(old('jenis', ($jurnalKas->tipe ?? '') == 'Pengeluaran' ? 'keluar' : '') == 'keluar' ? 'selected' : ''); ?>>Kas Keluar</option>
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
            <div class="col-md-6">
                <label class="form-label">Akun (COA) <span class="text-danger">*</span></label>
                <select name="coa_id" class="form-select <?php $__errorArgs = ['coa_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                    <option value="">-- Pilih --</option>
                    <?php $__currentLoopData = $coas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($c->id); ?>" <?php echo e(old('coa_id', $jurnalKas->coa_id ?? ($jurnalKas->coa_lawan_id ?? '')) == $c->id ? 'selected' : ''); ?>><?php echo e($c->kode_akun); ?> - <?php echo e($c->nama_akun); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php $__errorArgs = ['coa_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="col-md-6">
                <label class="form-label">Mitra (Opsional)</label>
                <select name="contactable_type_id" class="form-select <?php $__errorArgs = ['contactable_type_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                    <option value="">-- Tanpa Mitra --</option>
                    <optgroup label="Klien">
                        <?php $__currentLoopData = $kliens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="App\Models\Klien:<?php echo e($k->id); ?>" <?php echo e((isset($jurnalKas) && $jurnalKas->contactable_type === 'App\Models\Klien' && $jurnalKas->contactable_id == $k->id) ? 'selected' : ''); ?>><?php echo e($k->nama_klien); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </optgroup>
                    <optgroup label="Vendor">
                        <?php $__currentLoopData = $vendors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="App\Models\Vendor:<?php echo e($v->id); ?>" <?php echo e((isset($jurnalKas) && $jurnalKas->contactable_type === 'App\Models\Vendor' && $jurnalKas->contactable_id == $v->id) ? 'selected' : ''); ?>><?php echo e($v->nama_vendor); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </optgroup>
                </select>
                <?php $__errorArgs = ['contactable_type_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="col-md-6">
                <label class="form-label">Jumlah (Rp) <span class="text-danger">*</span></label>
                <input type="number" name="jumlah" class="form-control <?php $__errorArgs = ['jumlah'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('jumlah', isset($jurnalKas) ? $jurnalKas->nominal : '')); ?>" required>
                <?php $__errorArgs = ['jumlah'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="col-12">
                <label class="form-label">Deskripsi</label>
                <textarea name="deskripsi" class="form-control" rows="3"><?php echo e(old('deskripsi', $jurnalKas->deskripsi ?? '')); ?></textarea>
            </div>
            <div class="col-12">
                <label class="form-label">Bukti Transaksi <span class="text-danger">*</span></label>
                <input type="file" name="bukti_transaksi" class="form-control <?php $__errorArgs = ['bukti_transaksi'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" accept=".jpg,.jpeg,.png,.pdf" <?php echo e(isset($jurnalKas) && $jurnalKas->bukti_transaksi ? '' : 'required'); ?>>
                <div class="form-text">Format: JPG, PNG, PDF. Maks: 2MB. Bisa ambil dari Kamera Pustaka/Galeri.</div>
                <?php $__errorArgs = ['bukti_transaksi'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                
                <?php if(isset($jurnalKas) && $jurnalKas->bukti_transaksi): ?>
                    <div class="mt-2">
                        <a href="<?php echo e(Storage::url($jurnalKas->bukti_transaksi)); ?>" target="_blank" class="btn btn-sm btn-info text-white">
                            <i class="cil-external-link me-1"></i> Lihat Bukti Saat Ini
                        </a>
                        <div class="form-text text-warning mt-1">Mengunggah file baru akan menimpa file yang lama.</div>
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-12 d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="cil-save me-1"></i> <?php echo e(isset($jurnalKas) ? 'Perbarui' : 'Simpan'); ?></button>
                <a href="<?php echo e(route('admin.jurnal-kas.index')); ?>" class="btn btn-outline-secondary">Kembali</a>
            </div>
        </div>
    </form>
</div></div></div></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECT_HERD\tpst-app\resources\views/admin/jurnal-kas/form.blade.php ENDPATH**/ ?>