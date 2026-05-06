<?php $__env->startSection('title', $title); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h1><?php echo e($title); ?></h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li><li class="breadcrumb-item active"><?php echo e($title); ?></li></ol></nav>
    </div>
</div>

<div class="card">
    <div class="card-body text-center py-5">
        <i class="cil-chart" style="font-size: 3rem; color: #94a3b8;"></i>
        <h4 class="mt-3 text-body-secondary"><?php echo e($title); ?></h4>
        <p class="text-body-secondary">Fitur laporan ini akan segera tersedia.</p>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECT_HERD\tpst-app\resources\views\admin\laporan\placeholder.blade.php ENDPATH**/ ?>