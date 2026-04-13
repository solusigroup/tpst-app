<?php $__env->startSection('title', 'Manajemen Karyawan'); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>Daftar Karyawan</strong>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create_employee')): ?>
                    <a href="<?php echo e(route('admin.hrd.employee.create')); ?>" class="btn btn-primary btn-sm">
                        <i class="cil-plus"></i> Tambah Karyawan
                    </a>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <?php if(session('success')): ?>
                    <div class="alert alert-success"><?php echo e(session('success')); ?></div>
                <?php endif; ?>
                <?php if(session('error')): ?>
                    <div class="alert alert-danger"><?php echo e(session('error')); ?></div>
                <?php endif; ?>

                <form action="<?php echo e(route('admin.hrd.employee.index')); ?>" method="GET" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="Cari Nama / No KTP" value="<?php echo e(request('search')); ?>">
                        </div>
                        <div class="col-md-3">
                            <select name="salary_type" class="form-select">
                                <option value="">Semua Tipe Gaji</option>
                                <option value="bulanan" <?php echo e(request('salary_type') == 'bulanan' ? 'selected' : ''); ?>>Bulanan</option>
                                <option value="borongan" <?php echo e(request('salary_type') == 'borongan' ? 'selected' : ''); ?>>Borongan</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-secondary w-100">Filter</button>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Foto</th>
                                <th>Nama & Jabatan</th>
                                <th>No. KTP</th>
                                <th>Tipe Gaji</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td class="text-center">
                                        <?php if($emp->photo): ?>
                                            <img src="<?php echo e(Storage::url($emp->photo)); ?>" alt="Foto" class="img-thumbnail" style="max-height: 50px;">
                                        <?php else: ?>
                                            <div class="avatar bg-secondary text-white d-flex align-items-center justify-content-center mx-auto" style="width: 50px; height: 50px; border-radius: 5px;">
                                                <?php echo e(substr($emp->name, 0, 1)); ?>

                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong><?php echo e($emp->name); ?></strong><br>
                                        <small class="text-muted"><?php echo e($emp->position ?? '-'); ?></small>
                                    </td>
                                    <td><?php echo e($emp->ktp_number ?? '-'); ?></td>
                                    <td>
                                        <?php if($emp->salary_type): ?>
                                            <span class="badge bg-<?php echo e($emp->salary_type == 'bulanan' ? 'info' : 'success'); ?>">
                                                <?php echo e(ucfirst($emp->salary_type)); ?>

                                            </span>
                                            <?php if($emp->salary_type == 'bulanan' && $emp->monthly_salary): ?>
                                                <br><small class="text-muted">Rp <?php echo e(number_format($emp->monthly_salary, 0, ',', '.')); ?></small>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update_employee')): ?>
                                            <a href="<?php echo e(route('admin.hrd.employee.edit', $emp->id)); ?>" class="btn btn-warning btn-sm">Edit</a>
                                        <?php endif; ?>
                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete_employee')): ?>
                                            <form action="<?php echo e(route('admin.hrd.employee.destroy', $emp->id)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus karyawan ini?');">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="text-center">Tidak ada data karyawan</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php echo e($employees->links()); ?>

            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECT_HERD\tpst-app\resources\views/admin/hrd/employee/index.blade.php ENDPATH**/ ?>