<?php $__env->startSection('title', isset($ritase) ? 'Edit Ritase' : 'Tambah Ritase'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h1><?php echo e(isset($ritase) ? 'Edit Ritase' : 'Tambah Ritase'); ?></h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li><li class="breadcrumb-item"><a href="<?php echo e(route('admin.ritase.index')); ?>">Ritase</a></li><li class="breadcrumb-item active"><?php echo e(isset($ritase) ? 'Edit' : 'Tambah'); ?></li></ol></nav>
    </div>
</div>

<form method="POST" action="<?php echo e(isset($ritase) ? route('admin.ritase.update', $ritase) : route('admin.ritase.store')); ?>" enctype="multipart/form-data">
    <?php echo csrf_field(); ?>
    <?php if(isset($ritase)): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-white"><h6 class="mb-0 fw-semibold">Informasi Ritase</h6></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Armada <span class="text-danger">*</span></label>
                            <select name="armada_id" class="form-select <?php $__errorArgs = ['armada_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                <option value="">-- Pilih Armada --</option>
                                <?php $__currentLoopData = $armadas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($a->id); ?>" <?php echo e(old('armada_id', $ritase->armada_id ?? '') == $a->id ? 'selected' : ''); ?>><?php echo e($a->plat_nomor); ?></option>
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
                            <label class="form-label">Klien <span class="text-danger">*</span></label>
                            <select name="klien_id" class="form-select <?php $__errorArgs = ['klien_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                <option value="">-- Pilih Klien --</option>
                                <?php $__currentLoopData = $kliens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($k->id); ?>" <?php echo e(old('klien_id', $ritase->klien_id ?? '') == $k->id ? 'selected' : ''); ?>><?php echo e($k->nama_klien); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['klien_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Waktu Masuk <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="waktu_masuk" class="form-control <?php $__errorArgs = ['waktu_masuk'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('waktu_masuk', isset($ritase) ? \Carbon\Carbon::parse($ritase->waktu_masuk)->format('Y-m-d\TH:i') : '')); ?>" required>
                            <?php $__errorArgs = ['waktu_masuk'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Waktu Keluar</label>
                            <input type="datetime-local" name="waktu_keluar" class="form-control <?php $__errorArgs = ['waktu_keluar'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('waktu_keluar', isset($ritase) && $ritase->waktu_keluar ? \Carbon\Carbon::parse($ritase->waktu_keluar)->format('Y-m-d\TH:i') : '')); ?>">
                            <?php $__errorArgs = ['waktu_keluar'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header bg-white"><h6 class="mb-0 fw-semibold">Pengukuran Berat</h6></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Berat Bruto (kg) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="berat_bruto" id="berat_bruto" class="form-control <?php $__errorArgs = ['berat_bruto'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('berat_bruto', $ritase->berat_bruto ?? '')); ?>" required oninput="calcNetto()">
                            <?php $__errorArgs = ['berat_bruto'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Berat Tarra (kg) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="berat_tarra" id="berat_tarra" class="form-control <?php $__errorArgs = ['berat_tarra'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('berat_tarra', $ritase->berat_tarra ?? '')); ?>" required oninput="calcNetto()">
                            <?php $__errorArgs = ['berat_tarra'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Berat Netto (kg)</label>
                            <input type="number" step="0.01" id="berat_netto" class="form-control bg-light" value="<?php echo e(old('berat_netto', $ritase->berat_netto ?? '0')); ?>" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-white"><h6 class="mb-0 fw-semibold">Detail</h6></div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Jenis Sampah</label>
                        <input type="text" name="jenis_sampah" class="form-control" value="<?php echo e(old('jenis_sampah', $ritase->jenis_sampah ?? '')); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Biaya Tipping (Rp)</label>
                        <input type="number" name="biaya_tipping" class="form-control" value="<?php echo e(old('biaya_tipping', $ritase->biaya_tipping ?? '')); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            <?php $__currentLoopData = ['masuk'=>'Masuk','timbang'=>'Timbang','keluar'=>'Keluar','selesai'=>'Selesai']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($val); ?>" <?php echo e(old('status', $ritase->status ?? 'masuk') == $val ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tiket (Manual)</label>
                        <input type="text" name="tiket" class="form-control" value="<?php echo e(old('tiket', $ritase->tiket ?? '')); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Foto Tiket</label>
                        <div class="d-flex gap-2 mb-2">
                            <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('foto_tiket').click()">
                                <i class="cil-camera me-1"></i> Ambil Foto / Pilih File
                            </button>
                        </div>
                        <input type="file" name="foto_tiket" id="foto_tiket" class="form-control d-none <?php $__errorArgs = ['foto_tiket'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" accept="image/*" capture="environment" onchange="previewImage(this)">
                        <div id="file-name-display" class="small text-muted mb-2"></div>
                        <?php $__errorArgs = ['foto_tiket'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback d-block"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        
                        <div id="image-preview" class="mt-2 text-center border p-2 rounded <?php echo e((isset($ritase) && $ritase->foto_tiket) ? '' : 'd-none'); ?>">
                            <?php if(isset($ritase) && $ritase->foto_tiket): ?>
                                <a href="<?php echo e(asset('storage/' . $ritase->foto_tiket)); ?>" target="_blank" id="preview-link">
                                    <img src="<?php echo e(asset('storage/' . $ritase->foto_tiket)); ?>" id="preview-img" class="img-fluid rounded" style="max-height: 200px;">
                                </a>
                            <?php else: ?>
                                <a href="#" target="_blank" id="preview-link">
                                    <img src="" id="preview-img" class="img-fluid rounded" style="max-height: 200px;">
                                </a>
                            <?php endif; ?>
                            <p class="small text-muted mt-1 mb-0">Preview foto tiket</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-body d-grid gap-2">
                    <button type="submit" class="btn btn-primary"><i class="cil-save me-1"></i> <?php echo e(isset($ritase) ? 'Perbarui' : 'Simpan'); ?></button>
                    <a href="<?php echo e(route('admin.ritase.index')); ?>" class="btn btn-outline-secondary">Kembali</a>
                </div>
            </div>
        </div>
    </div>
</form>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function calcNetto() {
    const bruto = parseFloat(document.getElementById('berat_bruto').value) || 0;
    const tarra = parseFloat(document.getElementById('berat_tarra').value) || 0;
    document.getElementById('berat_netto').value = (bruto - tarra).toFixed(2);
}

function previewImage(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const reader = new FileReader();
        
        document.getElementById('file-name-display').textContent = 'File terpilih: ' + file.name;
        
        reader.onload = function(e) {
            document.getElementById('image-preview').classList.remove('d-none');
            document.getElementById('preview-img').src = e.target.result;
            document.getElementById('preview-link').href = e.target.result;
        }
        
        reader.readAsDataURL(file);
    }
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECT_HERD\tpst-app\resources\views/admin/ritase/form.blade.php ENDPATH**/ ?>