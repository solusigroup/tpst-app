<?php $__env->startSection('title', isset($coa) ? 'Edit COA' : 'Tambah COA'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h1><?php echo e(isset($coa) ? 'Edit' : 'Tambah'); ?> Chart of Account</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li><li class="breadcrumb-item"><a href="<?php echo e(route('admin.coa.index')); ?>">COA</a></li><li class="breadcrumb-item active"><?php echo e(isset($coa) ? 'Edit' : 'Tambah'); ?></li></ol></nav>
    </div>
</div>
<div class="row"><div class="col-lg-8"><div class="card"><div class="card-body">
    <form method="POST" action="<?php echo e(isset($coa) ? route('admin.coa.update', $coa) : route('admin.coa.store')); ?>">
        <?php echo csrf_field(); ?> <?php if(isset($coa)): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Kode Akun <span class="text-danger">*</span></label>
                <input type="text" name="kode_akun" class="form-control <?php $__errorArgs = ['kode_akun'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('kode_akun', $coa->kode_akun ?? '')); ?>" required>
                <?php $__errorArgs = ['kode_akun'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="col-md-6">
                <label class="form-label">Nama Akun <span class="text-danger">*</span></label>
                <input type="text" name="nama_akun" class="form-control <?php $__errorArgs = ['nama_akun'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('nama_akun', $coa->nama_akun ?? '')); ?>" required>
                <?php $__errorArgs = ['nama_akun'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="col-md-6">
                <label class="form-label">Tipe <span class="text-danger">*</span></label>
                <select name="tipe" id="tipe" class="form-select <?php $__errorArgs = ['tipe'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required onchange="updateKlasifikasi()">
                    <option value="">-- Pilih --</option>
                    <?php $__currentLoopData = ['Asset','Liability','Equity','Revenue','Expense']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($t); ?>" <?php echo e(old('tipe', $coa->tipe ?? '') == $t ? 'selected' : ''); ?>><?php echo e($t); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php $__errorArgs = ['tipe'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="col-md-6">
                <label class="form-label">Klasifikasi <span class="text-danger">*</span></label>
                <select name="klasifikasi" id="klasifikasi" class="form-select <?php $__errorArgs = ['klasifikasi'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                    <option value="">-- Pilih Tipe dulu --</option>
                </select>
                <?php $__errorArgs = ['klasifikasi'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="col-12 d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="cil-save me-1"></i> <?php echo e(isset($coa) ? 'Perbarui' : 'Simpan'); ?></button>
                <a href="<?php echo e(route('admin.coa.index')); ?>" class="btn btn-outline-secondary">Kembali</a>
            </div>
        </div>
    </form>
</div></div></div></div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
const klasifikasiMap = {
    'Asset': {'Aset Lancar':'Aset Lancar','Aset Tidak Lancar':'Aset Tidak Lancar'},
    'Liability': {'Liabilitas Jangka Pendek':'Liabilitas Jangka Pendek','Liabilitas Jangka Panjang':'Liabilitas Jangka Panjang'},
    'Equity': {'Ekuitas':'Ekuitas'},
    'Revenue': {'Pendapatan':'Pendapatan'},
    'Expense': {'Beban':'Beban'},
};
function updateKlasifikasi() {
    const tipe = document.getElementById('tipe').value;
    const sel = document.getElementById('klasifikasi');
    sel.innerHTML = '<option value="">-- Pilih --</option>';
    if (klasifikasiMap[tipe]) {
        Object.entries(klasifikasiMap[tipe]).forEach(([k,v]) => {
            const opt = document.createElement('option');
            opt.value = k; opt.textContent = v;
            sel.appendChild(opt);
        });
    }
    // Restore old value
    const oldVal = '<?php echo e(old("klasifikasi", $coa->klasifikasi ?? "")); ?>';
    if (oldVal) sel.value = oldVal;
}
document.addEventListener('DOMContentLoaded', updateKlasifikasi);
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECT_HERD\tpst-app\resources\views/admin/coa/form.blade.php ENDPATH**/ ?>