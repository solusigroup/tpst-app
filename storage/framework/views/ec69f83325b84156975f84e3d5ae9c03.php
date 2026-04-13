<?php $__env->startSection('title', 'Catat Pengangkutan Residu'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h1>Catat Pengangkutan Residu</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?php echo e(route('admin.pengangkutan-residu.index')); ?>">Pengangkutan Residu</a></li>
                <li class="breadcrumb-item active">Tambah</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form action="<?php echo e(route('admin.pengangkutan-residu.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Armada / Truk <span class="text-danger">*</span></label>
                            <select name="armada_id" class="form-select <?php $__errorArgs = ['armada_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                <option value="">Pilih Armada...</option>
                                <?php $__currentLoopData = $armadas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $armada): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($armada->id); ?>" <?php echo e(old('armada_id') == $armada->id ? 'selected' : ''); ?>>
                                        <?php echo e($armada->plat_nomor); ?> - <?php echo e($armada->nama_armada); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['armada_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal" class="form-control <?php $__errorArgs = ['tanggal'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('tanggal', date('Y-m-d'))); ?>" required>
                            <?php $__errorArgs = ['tanggal'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Waktu Keluar TPST</label>
                            <input type="time" name="waktu_keluar" class="form-control <?php $__errorArgs = ['waktu_keluar'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('waktu_keluar', date('H:i'))); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Waktu Kembali / Masuk</label>
                            <input type="time" name="waktu_masuk" class="form-control <?php $__errorArgs = ['waktu_masuk'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('waktu_masuk')); ?>">
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label">Berat Bruto (Kg) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="berat_bruto" id="berat_bruto" class="form-control <?php $__errorArgs = ['berat_bruto'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('berat_bruto', 0)); ?>" required>
                            <small class="text-muted">Berat saat keluar TPST (Penuh)</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Berat Tarra (Kg) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="berat_tarra" id="berat_tarra" class="form-control <?php $__errorArgs = ['berat_tarra'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('berat_tarra', 0)); ?>" required>
                            <small class="text-muted">Berat truk kosong</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Berat Netto (Kg)</label>
                            <input type="number" id="berat_netto" class="form-control bg-light" value="0" readonly>
                            <small class="text-muted">Hasil Pengurangan</small>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Tujuan Pembuangan</label>
                        <input type="text" name="tujuan" class="form-control" value="<?php echo e(old('tujuan', 'TPA Tambakrigadung')); ?>">
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="3"><?php echo e(old('keterangan')); ?></textarea>
                    </div>

                    <div class="alert alert-info d-flex align-items-center mb-4">
                        <i class="cil-info me-2 fs-5"></i>
                        <div>
                            Sistem akan secara otomatis mencatat <strong>Biaya Retribusi Rp 30.000</strong> dan membuat jurnal pengeluaran (Utang Biaya TPA).
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="<?php echo e(route('admin.pengangkutan-residu.index')); ?>" class="btn btn-outline-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary px-4">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    const brutoInput = document.getElementById('berat_bruto');
    const tarraInput = document.getElementById('berat_tarra');
    const nettoInput = document.getElementById('berat_netto');

    function calculateNetto() {
        const bruto = parseFloat(brutoInput.value) || 0;
        const tarra = parseFloat(tarraInput.value) || 0;
        nettoInput.value = (bruto - tarra).toFixed(2);
    }

    brutoInput.addEventListener('input', calculateNetto);
    tarraInput.addEventListener('input', calculateNetto);
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECT_HERD\tpst-app\resources\views/admin/pengangkutan_residu/create.blade.php ENDPATH**/ ?>