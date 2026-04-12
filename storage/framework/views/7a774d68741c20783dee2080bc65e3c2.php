<?php $__env->startSection('title', 'Logbook Mesin'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1>Logbook Operasional Mesin</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Logbook Mesin</li>
            </ol>
        </nav>
    </div>
    <a href="<?php echo e(route('admin.machine-logs.create')); ?>" class="btn btn-primary">
        <i class="cil-pen"></i> Isi Logbook Baru
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle">
                <thead>
                    <tr>
                        <th>Waktu Log</th>
                        <th>Waktu Cek</th>
                        <th>Mesin</th>
                        <th>Status Lampu</th>
                        <th>Keterangan</th>
                        <th>Operator</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($log->created_at->format('d M Y H:i')); ?></td>
                        <td><span class="badge bg-secondary"><?php echo e($log->waktu_cek); ?></span></td>
                        <td><strong><?php echo e($log->machine->nama_mesin); ?></strong> <br><small class="text-muted"><?php echo e($log->machine->nomor_mesin); ?></small></td>
                        <td>
                            <?php if($log->status_lampu == 'Hijau'): ?>
                                <span class="badge bg-success"><i class="cil-check-circle"></i> Hijau (Normal)</span>
                            <?php elseif($log->status_lampu == 'Kuning'): ?>
                                <span class="badge bg-warning"><i class="cil-warning"></i> Kuning (Attention)</span>
                            <?php elseif($log->status_lampu == 'Biru'): ?>
                                <span class="badge bg-info"><i class="cil-settings"></i> Biru (Maintenance)</span>
                            <?php elseif($log->status_lampu == 'Merah'): ?>
                                <span class="badge bg-danger"><i class="cil-x-circle"></i> Merah (Emergency)</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo e(Str::limit($log->keterangan ?? '-', 50)); ?></td>
                        <td><?php echo e($log->user->name ?? 'Sistem'); ?></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="<?php echo e(route('admin.machine-logs.edit', $log->id)); ?>" class="btn btn-outline-info" title="Edit">
                                    <i class="cil-pencil"></i>
                                </a>
                                <form action="<?php echo e(route('admin.machine-logs.destroy', $log->id)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin menghapus log ini?');">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-outline-danger" title="Hapus">
                                        <i class="cil-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">Belum ada riwayat logbook mesin.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <div class="mt-3">
            <?php echo e($logs->withQueryString()->links()); ?>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECT_HERD\tpst-app\resources\views/admin/machine_logs/index.blade.php ENDPATH**/ ?>