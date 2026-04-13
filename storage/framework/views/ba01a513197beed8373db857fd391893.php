<?php $__env->startSection('title', 'Perhitungan Upah'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h1>Perhitungan Upah</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li><li class="breadcrumb-item active">Perhitungan Upah</li></ol></nav>
    </div>
    <button type="button" class="btn btn-primary" data-coreui-toggle="modal" data-coreui-target="#calculateModal"><i class="cil-calculator me-1"></i> Hitung Upah</button>
</div>

<!-- Calculate Modal -->
<div class="modal fade" id="calculateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="<?php echo e(route('admin.hrd.wage-calculation.calculate')); ?>" method="POST" class="modal-content">
            <?php echo csrf_field(); ?>
            <div class="modal-header">
                <h5 class="modal-title">Hitung Upah Mingguan</h5>
                <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Tanggal (Dalam Minggu yang Ingin Dihitung) <span class="text-danger">*</span></label>
                    <input type="date" name="week_start" class="form-control" required value="<?php echo e(date('Y-m-d')); ?>">
                    <small class="text-body-secondary">Sistem otomatis mengambil awal minggu dari tanggal ini.</small>
                </div>
                <div class="mb-3">
                    <label class="form-label">Karyawan (Opsional)</label>
                    <select name="user_id" class="form-select">
                        <option value="">Semua Karyawan</option>
                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($user->id); ?>"><?php echo e($user->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <small class="text-body-secondary">Kosongkan untuk menghitung semua karyawan.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Hitung</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Karyawan</label>
                <select name="user_id" class="form-select">
                    <option value="">Semua Karyawan</option>
                    <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($u->id); ?>" <?php echo e(request('user_id') == $u->id ? 'selected' : ''); ?>><?php echo e($u->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="pending" <?php echo e(request('status') == 'pending' ? 'selected' : ''); ?>>Pending</option>
                    <option value="approved" <?php echo e(request('status') == 'approved' ? 'selected' : ''); ?>>Disetujui</option>
                    <option value="paid" <?php echo e(request('status') == 'paid' ? 'selected' : ''); ?>>Dibayar</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Mulai Tanggal (Filter)</label>
                <input type="date" name="week_start" class="form-control" value="<?php echo e(request('week_start')); ?>">
            </div>
            <div class="col-auto"><button class="btn btn-outline-primary" type="submit"><i class="cil-search me-1"></i> Filter</button></div>
            <?php if(request()->hasAny(['user_id','status','week_start'])): ?>
                <div class="col-auto"><a href="<?php echo e(route('admin.hrd.wage-calculation.index')); ?>" class="btn btn-outline-secondary">Reset</a></div>
            <?php endif; ?>
            <div class="col-auto ms-auto">
                <a href="<?php echo e(route('admin.hrd.wage-calculation.export-rekap', request()->all())); ?>" target="_blank" class="btn btn-danger text-white"><i class="cil-print me-1"></i> Cetak Rekap (PDF)</a>
            </div>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>Oleh</th><th>Skema Upah</th><th>Periode Mingguan</th><th>Total Upah</th><th>Total Output</th><th>Status</th><th class="text-end">Aksi</th></tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $wages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><strong><?php echo e($item->user->name ?? 'Unknown'); ?></strong></td>
                        <td><span class="badge bg-secondary"><?php echo e(ucfirst($item->user->salary_type ?? 'Borongan')); ?></span></td>
                        <td>
                            <?php if($item->user->salary_type === 'bulanan'): ?>
                                Bulan <?php echo e(\Carbon\Carbon::parse($item->week_start)->translatedFormat('F Y')); ?>

                            <?php else: ?>
                                <?php echo e(\Carbon\Carbon::parse($item->week_start)->format('d/m/Y')); ?> - <?php echo e(\Carbon\Carbon::parse($item->week_end)->format('d/m/Y')); ?>

                            <?php endif; ?>
                        </td>
                        <td><strong>Rp <?php echo e(number_format($item->total_wage, 2, ',', '.')); ?></strong></td>
                        <td><?php echo e(number_format($item->total_quantity, 2, ',', '.')); ?> kg</td>
                        <td>
                            <?php if($item->status == 'pending'): ?> <span class="badge bg-warning">Pending</span>
                            <?php elseif($item->status == 'approved'): ?> <span class="badge bg-info">Disetujui</span>
                            <?php elseif($item->status == 'paid'): ?> <span class="badge bg-success">Dibayar</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="<?php echo e(route('admin.hrd.wage-calculation.show', $item)); ?>" class="btn btn-outline-info"><i class="cil-search"></i> Detail</a>
                                <a href="<?php echo e(route('admin.jurnal.create', ['ref_type' => urlencode('App\Models\WageCalculation'), 'ref_id' => $item->id])); ?>" class="btn btn-outline-primary" title="Buat Jurnal"><i class="cil-book"></i> Jurnal</a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="7" class="text-center py-4 text-body-secondary">Belum ada data perhitungan upah.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if($wages->hasPages()): ?> <div class="card-footer bg-white"><?php echo e($wages->links()); ?></div> <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECT_HERD\tpst-app\resources\views/admin/hrd/wage-calculation/index.blade.php ENDPATH**/ ?>