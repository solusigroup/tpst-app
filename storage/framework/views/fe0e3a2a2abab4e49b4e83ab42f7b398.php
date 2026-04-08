<?php $__env->startSection('title', 'Detail Klien'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h1>Detail Klien: <?php echo e($klien->nama_klien); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?php echo e(route('admin.klien.index')); ?>">Klien</a></li>
                <li class="breadcrumb-item active"><?php echo e($klien->nama_klien); ?></li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <a href="<?php echo e(route('admin.klien.edit', $klien)); ?>" class="btn btn-primary"><i class="cil-pencil me-1"></i> Edit Klien</a>
        <a href="<?php echo e(route('admin.klien.index')); ?>" class="btn btn-outline-secondary">Kembali</a>
    </div>
</div>

<div class="row">
    <div class="col-md-5">
        <div class="card mb-4">
            <div class="card-header bg-white pb-0">
                <h5 class="card-title">Informasi Klien</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="40%">Nama Klien</th>
                        <td>: <?php echo e($klien->nama_klien); ?></td>
                    </tr>
                    <tr>
                        <th>Jenis</th>
                        <td>: 
                            <?php
                                $badgeColor = match($klien->jenis) {
                                    'DLH' => 'bg-info',
                                    'Swasta' => 'bg-primary',
                                    'Offtaker' => 'bg-success',
                                    'Internal' => 'bg-warning text-dark',
                                    default => 'bg-secondary'
                                };
                            ?>
                            <span class="badge <?php echo e($badgeColor); ?>"><?php echo e($klien->jenis); ?></span>
                        </td>
                    </tr>
                    <?php if($klien->jenis == 'Swasta'): ?>
                    <tr>
                        <th>Jenis Tarif</th>
                        <td>: <?php echo e($klien->jenis_tarif ?? '-'); ?></td>
                    </tr>
                    <tr>
                        <th>Tarif Bulanan</th>
                        <td>: <?php echo e($klien->tarif_bulanan ? 'Rp ' . number_format($klien->tarif_bulanan, 0, ',', '.') : '-'); ?></td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <th>Kontak</th>
                        <td>: <?php echo e($klien->kontak ?? '-'); ?></td>
                    </tr>
                    <tr>
                        <th>Alamat</th>
                        <td>: <?php echo e($klien->alamat ?? '-'); ?></td>
                    </tr>
                    <tr>
                        <th>Bergabung Sejak</th>
                        <td>: <?php echo e($klien->created_at?->format('d/m/Y')); ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-7">
        <div class="card mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Daftar Armada (<?php echo e($klien->armada->count()); ?>)</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Plat Nomor</th>
                                <th>Kapasitas Maksimal (kg)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $klien->armada; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $armada): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($index + 1); ?></td>
                                <td><strong><?php echo e($armada->plat_nomor); ?></strong></td>
                                <td><?php echo e(number_format($armada->kapasitas_maksimal, 2, ',', '.')); ?> kg</td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">Belum ada armada untuk klien ini.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECT_HERD\tpst-app\resources\views/admin/klien/show.blade.php ENDPATH**/ ?>