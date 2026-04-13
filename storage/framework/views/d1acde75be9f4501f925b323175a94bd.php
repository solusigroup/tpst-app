<?php $__env->startSection('title', 'Kehadiran'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h1>Kehadiran</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li><li class="breadcrumb-item active">Kehadiran</li></ol></nav>
    </div>
    <?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', 'manajemen|hrd|super_admin')): ?>
    <a href="<?php echo e(route('admin.hrd.attendance.create')); ?>" class="btn btn-primary"><i class="cil-plus me-1"></i> Tambah Kehadiran</a>
    <?php endif; ?>
</div>

<?php if(Auth::user()->salary_type === 'bulanan'): ?>
<div class="card mb-4">
    <div class="card-header bg-white py-3">
        <h5 class="mb-3">Quick Check-in / Check-out</h5>
        <div class="row g-2 align-items-center">
            <div class="col-md-4">
                <p class="mb-0">Anda dapat melakukan check-in dan check-out sendiri.</p>
            </div>
            <div class="col-auto">
                <form method="GET" action="<?php echo e(route('attendance.check-in')); ?>">
                    <button type="submit" class="btn btn-success text-white"><i class="cil-account-login me-1"></i> Check In</button>
                </form>
            </div>
            <div class="col-auto">
                <form method="GET" action="<?php echo e(route('attendance.check-out')); ?>">
                    <button type="submit" class="btn btn-warning text-white"><i class="cil-account-logout me-1"></i> Check Out</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header bg-white py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Karyawan</label>
                <?php if(auth()->check() && auth()->user()->hasRole('karyawan')): ?>
                    <input type="text" class="form-control bg-light" value="<?php echo e(auth()->user()->name); ?>" readonly>
                    <input type="hidden" name="user_id" value="<?php echo e(auth()->id()); ?>">
                <?php else: ?>
                    <select name="user_id" class="form-select">
                        <option value="">Semua Karyawan</option>
                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($u->id); ?>" <?php echo e(request('user_id') == $u->id ? 'selected' : ''); ?>><?php echo e($u->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                <?php endif; ?>
            </div>
            <div class="col-md-2">
                <label class="form-label">Dari Tanggal</label>
                <input type="date" name="date_from" class="form-control" value="<?php echo e(request('date_from')); ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">Sampai Tanggal</label>
                <input type="date" name="date_to" class="form-control" value="<?php echo e(request('date_to')); ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="present" <?php echo e(request('status') == 'present' ? 'selected' : ''); ?>>Hadir</option>
                    <option value="absent" <?php echo e(request('status') == 'absent' ? 'selected' : ''); ?>>Mangkir</option>
                    <option value="sick" <?php echo e(request('status') == 'sick' ? 'selected' : ''); ?>>Sakit</option>
                    <option value="leave" <?php echo e(request('status') == 'leave' ? 'selected' : ''); ?>>Izin</option>
                </select>
            </div>
            <div class="col-auto"><button class="btn btn-outline-primary" type="submit"><i class="cil-search me-1"></i> Filter</button></div>
            <?php if(request()->hasAny(['user_id','date_from','date_to','status'])): ?>
                <div class="col-auto"><a href="<?php echo e(route('admin.hrd.attendance.index')); ?>" class="btn btn-outline-secondary">Reset</a></div>
            <?php endif; ?>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>Tanggal</th><th>Karyawan</th><th>Check In</th><th>Check Out</th><th>Status</th><th class="text-end">Aksi</th></tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $attendances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e(\Carbon\Carbon::parse($item->attendance_date)->format('d/m/Y')); ?></td>
                        <td><strong><?php echo e($item->user->name); ?></strong></td>
                        <td><?php echo e($item->check_in ? \Carbon\Carbon::parse($item->check_in)->format('H:i') : '-'); ?></td>
                        <td><?php echo e($item->check_out ? \Carbon\Carbon::parse($item->check_out)->format('H:i') : '-'); ?></td>
                        <td>
                            <?php if($item->status == 'present'): ?> <span class="badge bg-success">Hadir</span>
                            <?php elseif($item->status == 'absent'): ?> <span class="badge bg-danger">Mangkir</span>
                            <?php elseif($item->status == 'sick'): ?> <span class="badge bg-warning">Sakit</span>
                            <?php elseif($item->status == 'leave'): ?> <span class="badge bg-info">Izin</span>
                            <?php else: ?> <span class="badge bg-secondary"><?php echo e($item->status); ?></span> <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', 'manajemen|hrd|super_admin')): ?>
                            <div class="btn-group btn-group-sm">
                                <a href="<?php echo e(route('admin.hrd.attendance.edit', $item)); ?>" class="btn btn-outline-primary"><i class="cil-pencil"></i></a>
                                <form method="POST" action="<?php echo e(route('admin.hrd.attendance.destroy', $item)); ?>" class="d-inline" >
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Yakin ingin menghapus data ini?')"><i class="cil-trash"></i></button>
                                </form>
                            </div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="6" class="text-center py-4 text-body-secondary">Belum ada data kehadiran.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if($attendances->hasPages()): ?> <div class="card-footer bg-white"><?php echo e($attendances->links()); ?></div> <?php endif; ?>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    function submitQuick(action) {
        let userId = document.getElementById('quickUserSelect').value;
        if(!userId) {
            alert('Silakan pilih karyawan terlebih dahulu!');
            return;
        }
        let form = document.getElementById('quickActionForm');
        if(action === 'check-in') {
            form.action = "<?php echo e(url('admin/hrd/attendance')); ?>/" + userId + "/check-in";
        } else {
            form.action = "<?php echo e(url('admin/hrd/attendance')); ?>/" + userId + "/check-out";
        }
        form.submit();
    }
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECT_HERD\tpst-app\resources\views/admin/hrd/attendance/index.blade.php ENDPATH**/ ?>