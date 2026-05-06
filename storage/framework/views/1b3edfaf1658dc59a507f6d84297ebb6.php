<?php $__env->startSection('title', 'Central Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="row g-4 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="card bg-primary text-white h-100">
            <div class="card-body pb-0 d-flex justify-content-between align-items-start">
                <div><div class="fs-4 fw-semibold"><?php echo e($tenantCount); ?></div><div>Total Tenants</div></div>
                <div class="fs-1 opacity-50"><i class="cil-building"></i></div>
            </div>
            <div class="c-chart-wrapper mt-3 mx-3" style="height:70px;"></div>
        </div>
    </div>
    
    <div class="col-sm-6 col-lg-3">
        <div class="card bg-info text-white h-100">
            <div class="card-body pb-0 d-flex justify-content-between align-items-start">
                <div><div class="fs-4 fw-semibold"><?php echo e($userCount); ?></div><div>Total Users</div></div>
                <div class="fs-1 opacity-50"><i class="cil-people"></i></div>
            </div>
            <div class="c-chart-wrapper mt-3 mx-3" style="height:70px;"></div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header border-bottom-0"><h5 class="mb-0">Tenant Baru (Pendaftaran Terakhir)</h5></div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light"><tr><th>Nama Tenant</th><th>Domain</th><th>Tanggal Daftar</th></tr></thead>
                <tbody>
                    <?php $__currentLoopData = $recentTenants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr><td><strong><?php echo e($t->name); ?></strong></td><td><?php echo e($t->domain); ?></td><td><?php echo e($t->created_at->format('d M Y')); ?></td></tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php if($recentTenants->isEmpty()): ?><tr><td colspan="3" class="text-center py-4">Belum ada tenant</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white text-end"><a href="<?php echo e(route('central.tenants.index')); ?>" class="btn btn-sm btn-link">Lihat Semua Tenant &raquo;</a></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECT_HERD\tpst-app\resources\views\central\dashboard.blade.php ENDPATH**/ ?>