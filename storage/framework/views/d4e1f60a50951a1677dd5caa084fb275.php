<?php $__env->startSection('title', 'Detail Perhitungan Upah'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h1>Detail Perhitungan Upah</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li><li class="breadcrumb-item"><a href="<?php echo e(route('admin.hrd.wage-calculation.index')); ?>">Perhitungan Upah</a></li><li class="breadcrumb-item active">Detail</li></ol></nav>
    </div>
    <div>
        <a href="<?php echo e(route('admin.hrd.wage-calculation.export-slip', $wageCalculation)); ?>" target="_blank" class="btn btn-danger text-white"><i class="cil-print me-1"></i> Cetak Slip Gaji (PDF)</a>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header bg-white"><strong>Informasi Perhitungan</strong></div>
            <div class="card-body">
                <table class="table table-borderless table-sm mb-0">
                    <tr><td class="text-body-secondary">Karyawan</td><td>: <strong><?php echo e($wageCalculation->user->name ?? '-'); ?></strong></td></tr>
                    <tr><td class="text-body-secondary">Skema Upah</td><td>: <span class="badge bg-secondary"><?php echo e(ucfirst($wageCalculation->user->salary_type ?? 'Borongan')); ?></span></td></tr>
                    <tr><td class="text-body-secondary">Periode</td><td>: 
                        <?php if($wageCalculation->user->salary_type === 'bulanan'): ?>
                            Bulan <?php echo e(\Carbon\Carbon::parse($wageCalculation->week_start)->translatedFormat('F Y')); ?>

                        <?php else: ?>
                            <?php echo e(\Carbon\Carbon::parse($wageCalculation->week_start)->format('d/m/Y')); ?> - <?php echo e(\Carbon\Carbon::parse($wageCalculation->week_end)->format('d/m/Y')); ?>

                        <?php endif; ?>
                    </td></tr>
                    <?php if($wageCalculation->user->salary_type === 'harian'): ?>
                        <tr><td class="text-body-secondary">Upah Harian</td><td>: Rp <?php echo e(number_format($wageCalculation->user->daily_wage, 0, ',', '.')); ?></td></tr>
                    <?php else: ?>
                        <tr><td class="text-body-secondary">Total Output</td><td>: <?php echo e(number_format($wageCalculation->total_quantity, 2, ',', '.')); ?> kg</td></tr>
                    <?php endif; ?>
                    <tr><td class="text-body-secondary">Status</td><td>: 
                        <?php if($wageCalculation->status == 'pending'): ?> <span class="badge bg-warning">Pending</span>
                        <?php elseif($wageCalculation->status == 'approved'): ?> <span class="badge bg-info">Disetujui</span>
                        <?php elseif($wageCalculation->status == 'paid'): ?> <span class="badge bg-success">Dibayar (<?php echo e(\Carbon\Carbon::parse($wageCalculation->paid_date)->format('d M Y')); ?>)</span>
                        <?php endif; ?>
                    </td></tr>
                </table>
            </div>
            <div class="card-footer text-center bg-white">
                <h4 class="mb-0 text-primary">Total: Rp <?php echo e(number_format($wageCalculation->total_wage, 2, ',', '.')); ?></h4>
            </div>
            
            <?php if($wageCalculation->status == 'pending'): ?>
            <div class="card-body border-top">
                <form action="<?php echo e(route('admin.hrd.wage-calculation.approve', $wageCalculation)); ?>" method="POST" onsubmit="return confirm('Setujui perhitungan ini?')">
                    <?php echo csrf_field(); ?>
                    <button class="btn btn-info w-100 text-white"><i class="cil-check-circle me-1"></i> Setujui Upah</button>
                </form>
            </div>
            <?php endif; ?>

            <?php if($wageCalculation->status == 'approved'): ?>
            <div class="card-body border-top">
                <form action="<?php echo e(route('admin.hrd.wage-calculation.pay', $wageCalculation)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Pembayaran</label>
                        <input type="date" name="paid_date" class="form-control" value="<?php echo e(date('Y-m-d')); ?>" required>
                    </div>
                    <button class="btn btn-success w-100 text-white"><i class="cil-money me-1"></i> Tandai Dibayar</button>
                </form>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="col-md-8">
        <?php if($wageCalculation->user->salary_type === 'harian'): ?>
        <div class="card mb-4">
            <div class="card-header bg-white"><strong>Rincian Kehadiran (Harian)</strong></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr><th>Tanggal</th><th>Jam Masuk</th><th>Status</th><th class="text-end">Upah</th></tr>
                        </thead>
                        <tbody>
                            <?php $presentCount = 0; ?>
                            <?php $__empty_1 = true; $__currentLoopData = $attendances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $att): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php 
                                $isPaid = $att->status === 'present';
                                if($isPaid) $presentCount++;
                            ?>
                            <tr>
                                <td><?php echo e(\Carbon\Carbon::parse($att->attendance_date)->format('d/m/Y')); ?></td>
                                <td><?php echo e($att->check_in ? \Carbon\Carbon::parse($att->check_in)->format('H:i') : '-'); ?></td>
                                <td>
                                    <?php if($isPaid): ?> <span class="badge bg-success">Hadir / Dibayar</span>
                                    <?php else: ?> <span class="badge bg-danger"><?php echo e($att->status); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">Rp <?php echo e($isPaid ? number_format($wageCalculation->user->daily_wage, 0, ',', '.') : '0'); ?></td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="4" class="text-center py-4 text-body-secondary">Tidak ada data kehadiran.</td></tr>
                            <?php endif; ?>
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="3">Total Kehadiran Dibayar</th>
                                <th class="text-end"><?php echo e($presentCount); ?> Hari</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header bg-white"><strong>Rincian Output Karyawan</strong></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr><th>Tanggal</th><th>Kategori Sampah</th><th class="text-end">Jumlah</th></tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $outputs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $out): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e(\Carbon\Carbon::parse($out->output_date)->format('d/m/Y')); ?></td>
                                <td><span class="badge bg-secondary"><?php echo e($out->wasteCategory->name); ?></span></td>
                                <td class="text-end"><?php echo e(number_format($out->quantity, 2, ',', '.')); ?> <?php echo e($out->unit); ?></td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="3" class="text-center py-4 text-body-secondary">Tidak ada catatan output pada periode ini.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECT_HERD\tpst-app\resources\views/admin/hrd/wage-calculation/show.blade.php ENDPATH**/ ?>