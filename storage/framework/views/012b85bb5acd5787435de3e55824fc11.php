<?php $__env->startSection('title', 'Activity Log'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1>Activity Log</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Log Aktivitas</li>
            </ol>
        </nav>
    </div>
</div>

<div class="card mb-4"><div class="card-body py-3">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-4">
            <label class="form-label mb-0 small text-body-secondary">Cari Log</label>
            <input type="text" name="search" class="form-control" value="<?php echo e(request('search')); ?>" placeholder="Deskripsi / Modul...">
        </div>
        <div class="col-auto"><button class="btn btn-secondary" type="submit"><i class="cil-search"></i> Cari</button></div>
    </form>
</div></div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 15%">Waktu</th>
                        <th style="width: 15%">User</th>
                        <th style="width: 15%">Event</th>
                        <th style="width: 25%">Model</th>
                        <th style="width: 30%">Deskripsi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($activities->isEmpty()): ?>
                    <tr>
                        <td colspan="5" class="text-center py-4 text-body-secondary">Tidak ada log aktivitas.</td>
                    </tr>
                    <?php else: ?>
                    <?php $__currentLoopData = $activities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($log->created_at->format('d M Y H:i:s')); ?></td>
                        <td>
                            <?php if($log->causer): ?>
                                <strong><?php echo e($log->causer->name); ?></strong><br>
                                <small class="text-body-secondary"><?php echo e(class_basename($log->causer_type)); ?></small>
                            <?php else: ?>
                                <span class="text-body-secondary fst-italic">System</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php
                                $badgeColor = match($log->event) {
                                    'created' => 'success',
                                    'updated' => 'info',
                                    'deleted' => 'danger',
                                    default => 'secondary'
                                };
                            ?>
                            <span class="badge bg-<?php echo e($badgeColor); ?>"><?php echo e(strtoupper($log->event)); ?></span>
                        </td>
                        <td><?php echo e(class_basename($log->subject_type) ?? '-'); ?><br><small class="text-body-secondary">ID: <?php echo e($log->subject_id ?? '-'); ?></small></td>
                        <td><?php echo e($log->description); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if($activities->hasPages()): ?>
    <div class="card-footer bg-white">
        <?php echo e($activities->links()); ?>

    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECT_HERD\tpst-app\resources\views\admin\activities\index.blade.php ENDPATH**/ ?>