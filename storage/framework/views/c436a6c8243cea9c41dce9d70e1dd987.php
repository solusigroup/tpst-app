<?php $__env->startSection('title', 'Detail Pengangkutan Residu'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h1>Detail Pengangkutan Residu: <?php echo e($item->nomor_tiket); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?php echo e(route('admin.pengangkutan-residu.index')); ?>">Pengangkutan Residu</a></li>
                <li class="breadcrumb-item active">Detail</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <div class="col-md-7">
        <div class="card mb-4">
            <div class="card-header bg-white fw-bold">Pencatatan Operasional</div>
            <div class="card-body">
                <table class="table table-borderless table-sm mb-0">
                    <tr>
                        <th width="35%">No. Tiket</th>
                        <td>: <strong><?php echo e($item->nomor_tiket); ?></strong></td>
                    </tr>
                    <tr>
                        <th>Tanggal</th>
                        <td>: <?php echo e($item->tanggal->format('d F Y')); ?></td>
                    </tr>
                    <tr>
                        <th>Armada</th>
                        <td>: <?php echo e($item->armada->plat_nomor); ?> (<?php echo e($item->armada->nama_armada); ?>)</td>
                    </tr>
                    <tr>
                        <th>Waktu</th>
                        <td>: <?php echo e($item->waktu_keluar ?? '--'); ?> s/d <?php echo e($item->waktu_masuk ?? '--'); ?></td>
                    </tr>
                    <tr>
                        <th>Tujuan</th>
                        <td>: <?php echo e($item->tujuan); ?></td>
                    </tr>
                </table>
                <hr>
                <div class="row text-center mt-3">
                    <div class="col-4 border-end">
                        <small class="text-muted d-block">Bruto</small>
                        <span class="fs-5 fw-bold"><?php echo e(number_format($item->berat_bruto, 0, ',', '.')); ?> kg</span>
                    </div>
                    <div class="col-4 border-end">
                        <small class="text-muted d-block">Tarra</small>
                        <span class="fs-5 fw-bold"><?php echo e(number_format($item->berat_tarra, 0, ',', '.')); ?> kg</span>
                    </div>
                    <div class="col-4 text-primary">
                        <small class="text-muted d-block">Netto</small>
                        <span class="fs-5 fw-bold"><?php echo e(number_format($item->berat_netto, 0, ',', '.')); ?> kg</span>
                    </div>
                </div>
            </div>
        </div>

        <?php if($item->keterangan): ?>
        <div class="card mb-4">
            <div class="card-header bg-white fw-bold">Keterangan</div>
            <div class="card-body">
                <?php echo e($item->keterangan); ?>

            </div>
        </div>
        <?php endif; ?>
    </div>

    <div class="col-md-5">
        <div class="card mb-4">
            <div class="card-header bg-white fw-bold">Informasi Keuangan</div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <span>Biaya Retribusi TPA</span>
                    <span class="fw-bold text-danger">Rp <?php echo e(number_format($item->biaya_retribusi, 0, ',', '.')); ?></span>
                </div>
                
                <?php if($item->jurnalHeader): ?>
                <div class="p-3 bg-light rounded border">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="small text-muted">Jurnal ID: #<?php echo e($item->jurnalHeader->id); ?></span>
                        <span class="badge bg-success text-uppercase"><?php echo e($item->jurnalHeader->status); ?></span>
                    </div>
                    <hr class="my-2">
                    <?php $__currentLoopData = $item->jurnalHeader->jurnalDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="d-flex justify-content-between small">
                        <span><?php echo e($detail->coa->nama_akun); ?></span>
                        <span class="<?php echo e($detail->debit > 0 ? 'text-primary' : ''); ?>">
                            <?php echo e($detail->debit > 0 ? 'Dr' : 'Cr'); ?> 
                            <?php echo e(number_format($detail->debit > 0 ? $detail->debit : $detail->kredit, 0, ',', '.')); ?>

                        </span>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php else: ?>
                <div class="alert alert-warning py-2 small">
                    <i class="cil-warning me-1"></i> Jurnal belum terbuat.
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="d-grid gap-2">
            <a href="<?php echo e(route('admin.pengangkutan-residu.edit', $item)); ?>" class="btn btn-warning">
                <i class="cil-pencil me-1"></i> Edit Data
            </a>
            <a href="<?php echo e(route('admin.pengangkutan-residu.index')); ?>" class="btn btn-outline-secondary">
                Kembali ke Daftar
            </a>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECT_HERD\tpst-app\resources\views\admin\pengangkutan_residu\show.blade.php ENDPATH**/ ?>