<?php $__env->startSection('title', 'Edit Logbook Mesin'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1>Edit Logbook Mesin</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?php echo e(route('admin.machine-logs.index')); ?>">Logbook Mesin</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit</li>
            </ol>
        </nav>
    </div>
</div>

<div class="card col-md-8">
    <div class="card-body">
        <form action="<?php echo e(route('admin.machine-logs.update', $machineLog->id)); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="machine_id" class="form-label">Mesin <span class="text-danger">*</span></label>
                    <select class="form-select <?php $__errorArgs = ['machine_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="machine_id" name="machine_id" required>
                        <option value="">Pilih Mesin...</option>
                        <?php $__currentLoopData = $machines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $machine): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($machine->id); ?>" <?php echo e(old('machine_id', $machineLog->machine_id) == $machine->id ? 'selected' : ''); ?>>
                                <?php echo e($machine->nomor_mesin); ?> - <?php echo e($machine->nama_mesin); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php $__errorArgs = ['machine_id'];
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
                
                <div class="col-md-6 mb-3">
                    <label for="waktu_cek" class="form-label">Waktu Pengecekan <span class="text-danger">*</span></label>
                    <select class="form-select <?php $__errorArgs = ['waktu_cek'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="waktu_cek" name="waktu_cek" required>
                        <option value="">Pilih Waktu...</option>
                        <option value="Engine On" <?php echo e(old('waktu_cek', $machineLog->waktu_cek) == 'Engine On' ? 'selected' : ''); ?>>Engine On (Pagi / Mulai Operasi)</option>
                        <option value="Engine Off" <?php echo e(old('waktu_cek', $machineLog->waktu_cek) == 'Engine Off' ? 'selected' : ''); ?>>Engine Off (Sore / Selesai Operasi)</option>
                    </select>
                    <?php $__errorArgs = ['waktu_cek'];
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
            </div>

            <div class="mb-4">
                <label class="form-label d-block">Status Lampu Menara <span class="text-danger">*</span></label>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-check p-3 border rounded h-100 shadow-sm" style="background-color: rgba(16, 185, 129, 0.05); border-left: 4px solid #10b981 !important;">
                            <input class="form-check-input ms-1" type="radio" name="status_lampu" id="statusHijau" value="Hijau" <?php echo e(old('status_lampu', $machineLog->status_lampu) == 'Hijau' ? 'checked' : ''); ?> required>
                            <label class="form-check-label ms-2 d-block w-100" for="statusHijau" style="cursor:pointer">
                                <strong>🟢 Hijau (Normal Operation)</strong><br>
                                <small class="text-muted">Mesin mencapai cycle time normal. Siap/Sedang beroperasi.</small>
                            </label>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-check p-3 border rounded h-100 shadow-sm" style="background-color: rgba(245, 158, 11, 0.05); border-left: 4px solid #f59e0b !important;">
                            <input class="form-check-input ms-1" type="radio" name="status_lampu" id="statusKuning" value="Kuning" <?php echo e(old('status_lampu', $machineLog->status_lampu) == 'Kuning' ? 'checked' : ''); ?>>
                            <label class="form-check-label ms-2 d-block w-100" for="statusKuning" style="cursor:pointer">
                                <strong>🟡 Kuning (Attention Required)</strong><br>
                                <small class="text-muted">Mesin hidup tapi ada isu non-teknis (misal stok tipis/QC).</small>
                            </label>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-check p-3 border rounded h-100 shadow-sm" style="background-color: rgba(6, 182, 212, 0.05); border-left: 4px solid #06b6d4 !important;">
                            <input class="form-check-input ms-1" type="radio" name="status_lampu" id="statusBiru" value="Biru" <?php echo e(old('status_lampu', $machineLog->status_lampu) == 'Biru' ? 'checked' : ''); ?>>
                            <label class="form-check-label ms-2 d-block w-100" for="statusBiru" style="cursor:pointer">
                                <strong>🔵 Biru (Under Maintenance)</strong><br>
                                <small class="text-muted">Sedang diperbaiki teknisi / Preventive maintenance.</small>
                            </label>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-check p-3 border rounded h-100 shadow-sm" style="background-color: rgba(239, 68, 68, 0.05); border-left: 4px solid #ef4444 !important;">
                            <input class="form-check-input ms-1" type="radio" name="status_lampu" id="statusMerah" value="Merah" <?php echo e(old('status_lampu', $machineLog->status_lampu) == 'Merah' ? 'checked' : ''); ?>>
                            <label class="form-check-label ms-2 d-block w-100" for="statusMerah" style="cursor:pointer">
                                <strong class="text-danger">🔴 Merah (Emergency / Breakdown)</strong><br>
                                <small class="text-muted">Kegagalan kritis/emergency stop.</small>
                            </label>
                        </div>
                    </div>
                </div>
                <?php $__errorArgs = ['status_lampu'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="text-danger mt-2 small"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="mb-3">
                <label for="keterangan" class="form-label">Keterangan Tambahan / RCA</label>
                <textarea class="form-control <?php $__errorArgs = ['keterangan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="keterangan" name="keterangan" rows="3" placeholder="Isi catatan khusus jika status Kuning, Biru, atau Merah"><?php echo e(old('keterangan', $machineLog->keterangan)); ?></textarea>
                <?php $__errorArgs = ['keterangan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                <small class="text-muted">Wajib diisi jika terjadi breakdown (Merah) untuk analisis Root Cause (RCA).</small>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="<?php echo e(route('admin.machine-logs.index')); ?>" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary" id="btnSubmit">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECT_HERD\tpst-app\resources\views\admin\machine_logs\edit.blade.php ENDPATH**/ ?>