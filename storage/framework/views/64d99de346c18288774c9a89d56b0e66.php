<?php $__env->startSection('title', 'Detail Penjualan'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <h1>Detail Penjualan</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li><li class="breadcrumb-item"><a href="<?php echo e(route('admin.penjualan.index')); ?>">Penjualan</a></li><li class="breadcrumb-item active">Detail</li></ol></nav>
    </div>
    <div class="d-flex gap-2">
        <a href="<?php echo e(route('admin.penjualan.edit', $penjualan)); ?>" class="btn btn-warning"><i class="cil-pencil me-1"></i> Edit</a>
        <a href="<?php echo e(route('admin.penjualan.index')); ?>" class="btn btn-outline-secondary">Kembali</a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">Informasi Produk & Transaksi</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-sm-4 text-muted">Klien</div>
                    <div class="col-sm-8"><strong><?php echo e($penjualan->klien->nama_klien ?? '-'); ?></strong></div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 text-muted">Tanggal</div>
                    <div class="col-sm-8"><?php echo e(\Carbon\Carbon::parse($penjualan->tanggal)->format('d F Y')); ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 text-muted">Jenis Produk</div>
                    <div class="col-sm-8"><?php echo e($penjualan->jenis_produk); ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 text-muted">Berat (kg)</div>
                    <div class="col-sm-8"><?php echo e(number_format($penjualan->berat_kg, 2, ',', '.')); ?> kg</div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 text-muted">Harga Satuan</div>
                    <div class="col-sm-8">Rp <?php echo e(number_format($penjualan->harga_satuan, 0, ',', '.')); ?> / kg</div>
                </div>
                <hr>
                <div class="row mb-3">
                    <div class="col-sm-4 text-muted">Total Harga</div>
                    <div class="col-sm-8"><h4 class="text-primary mb-0">Rp <?php echo e(number_format($penjualan->total_harga, 0, ',', '.')); ?></h4></div>
                </div>
                <div class="row mb-3 alert alert-info">
                    <div class="col-sm-4 text-muted font-weight-bold">Jumlah Bayar / DP</div>
                    <div class="col-sm-8"><h4 class="mb-0 text-dark">Rp <?php echo e(number_format($penjualan->jumlah_bayar, 0, ',', '.')); ?></h4></div>
                </div>
                <div class="row mb-0">
                    <div class="col-sm-4 text-muted">Sisa Pelunasan</div>
                    <div class="col-sm-8">
                        <?php $sisa = $penjualan->total_harga - $penjualan->jumlah_bayar; ?>
                        <h4 class="<?php echo e($sisa > 0 ? 'text-danger' : 'text-success'); ?> mb-0">
                            Rp <?php echo e(number_format($sisa, 0, ',', '.')); ?>

                        </h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">Status Invoice</h5>
            </div>
            <div class="card-body">
                <?php if($penjualan->invoice_id): ?>
                    <div class="mb-3">
                        <label class="text-muted d-block small">Nomor Invoice</label>
                        <a href="<?php echo e(route('admin.invoice.show', $penjualan->invoice_id)); ?>" class="fw-bold"><?php echo e($penjualan->invoice->nomor_invoice); ?></a>
                    </div>
                    <div>
                        <label class="text-muted d-block small">Status</label>
                        <?php
                            $badgeClass = match($penjualan->status_invoice) {
                                'Paid' => 'bg-success',
                                'Draft' => 'bg-secondary',
                                'Sent' => 'bg-info',
                                'Canceled' => 'bg-danger',
                                default => 'bg-light text-dark'
                            };
                        ?>
                        <span class="badge <?php echo e($badgeClass); ?>"><?php echo e($penjualan->status_invoice); ?></span>
                    </div>
                <?php else: ?>
                    <div class="text-center py-3 text-muted">
                        <i class="cil-warning fs-1 mb-2"></i>
                        <p class="mb-0">Belum ditagihkan dalam Invoice.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECT_HERD\tpst-app\resources\views/admin/penjualan/show.blade.php ENDPATH**/ ?>