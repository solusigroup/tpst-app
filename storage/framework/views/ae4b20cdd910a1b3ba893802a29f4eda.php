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
                    <tr>
                        <th>Jenis Tarif</th>
                        <td>: <?php echo e($klien->jenis_tarif ?? '-'); ?></td>
                    </tr>
                    <tr>
                        <th>Besaran Tarif</th>
                        <td>: <?php echo e($klien->besaran_tarif ? 'Rp ' . number_format($klien->besaran_tarif, 0, ',', '.') : '-'); ?></td>
                    </tr>
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

        
        <div class="row mb-4">
            <div class="col-6">
                <div class="card border-start border-4 border-primary">
                    <div class="card-body py-3">
                        <div class="text-muted small text-uppercase fw-semibold">Total Armada</div>
                        <div class="fs-4 fw-bold text-primary"><?php echo e($klien->armada->count()); ?></div>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card border-start border-4 border-info">
                    <div class="card-body py-3">
                        <div class="text-muted small text-uppercase fw-semibold">Total Ritase</div>
                        <div class="fs-4 fw-bold text-info"><?php echo e($klien->ritase->count()); ?></div>
                    </div>
                </div>
            </div>
            <?php if($klien->jenis === 'Offtaker'): ?>
            <div class="col-6 mt-3">
                <div class="card border-start border-4 border-success">
                    <div class="card-body py-3">
                        <div class="text-muted small text-uppercase fw-semibold">Total Penjualan</div>
                        <div class="fs-4 fw-bold text-success"><?php echo e($klien->penjualan->count()); ?></div>
                    </div>
                </div>
            </div>
            <div class="col-6 mt-3">
                <div class="card border-start border-4 border-warning">
                    <div class="card-body py-3">
                        <div class="text-muted small text-uppercase fw-semibold">Nilai Penjualan</div>
                        <div class="fs-5 fw-bold text-warning">Rp <?php echo e(number_format($klien->penjualan->sum('total_harga'), 0, ',', '.')); ?></div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="col-md-7">
        
        <div class="card mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0"><i class="cil-truck me-2"></i>Daftar Armada (<?php echo e($klien->armada->count()); ?>)</h5>
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

        
        <div class="card mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0"><i class="cil-transfer me-2"></i>Riwayat Ritase (<?php echo e($klien->ritase->count()); ?>)</h5>
                <span class="badge bg-info fs-6">Total Netto: <?php echo e(number_format($klien->ritase->sum('berat_netto'), 2, ',', '.')); ?> kg</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light" style="position: sticky; top: 0; z-index: 1;">
                            <tr>
                                <th>No</th>
                                <th>No. Tiket</th>
                                <th>Armada</th>
                                <th>Waktu Masuk</th>
                                <th>Berat Netto</th>
                                <th>Biaya Tipping</th>
                                <th>Status</th>
                                <th>Invoice</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $klien->ritase; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $ritaseItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($index + 1); ?></td>
                                <td>
                                    <a href="<?php echo e(route('admin.ritase.show', $ritaseItem)); ?>" class="text-decoration-none fw-semibold">
                                        <?php echo e($ritaseItem->nomor_tiket ?? '-'); ?>

                                    </a>
                                </td>
                                <td><?php echo e($ritaseItem->armada->plat_nomor ?? '-'); ?></td>
                                <td><?php echo e($ritaseItem->waktu_masuk ? $ritaseItem->waktu_masuk->format('d/m/Y H:i') : '-'); ?></td>
                                <td><?php echo e(number_format($ritaseItem->berat_netto, 2, ',', '.')); ?> kg</td>
                                <td>Rp <?php echo e(number_format($ritaseItem->biaya_tipping, 0, ',', '.')); ?></td>
                                <td>
                                    <?php $statusColors = ['masuk'=>'info','timbang'=>'warning','keluar'=>'primary','selesai'=>'success']; ?>
                                    <span class="badge bg-<?php echo e($statusColors[$ritaseItem->status] ?? 'secondary'); ?>"><?php echo e(ucfirst($ritaseItem->status)); ?></span>
                                </td>
                                <td>
                                    <?php $invoiceColors = ['Draft'=>'secondary','Sent'=>'info','Paid'=>'success','Canceled'=>'danger']; ?>
                                    <span class="badge bg-<?php echo e($invoiceColors[$ritaseItem->status_invoice] ?? 'secondary'); ?>"><?php echo e($ritaseItem->status_invoice ?? 'Unbilled'); ?></span>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">Belum ada data ritase untuk klien ini.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        
        <?php if($klien->jenis === 'Offtaker'): ?>
        <div class="card mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0"><i class="cil-cart me-2"></i>Riwayat Penjualan (<?php echo e($klien->penjualan->count()); ?>)</h5>
                <span class="badge bg-success fs-6">Total: Rp <?php echo e(number_format($klien->penjualan->sum('total_harga'), 0, ',', '.')); ?></span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light" style="position: sticky; top: 0; z-index: 1;">
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Jenis Produk</th>
                                <th>Berat (kg)</th>
                                <th>Harga Satuan</th>
                                <th>Total Harga</th>
                                <th>Invoice</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $klien->penjualan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $penjualanItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($index + 1); ?></td>
                                <td><?php echo e($penjualanItem->tanggal ? $penjualanItem->tanggal->format('d/m/Y') : '-'); ?></td>
                                <td><?php echo e($penjualanItem->jenis_produk); ?></td>
                                <td><?php echo e(number_format($penjualanItem->berat_kg, 2, ',', '.')); ?></td>
                                <td>Rp <?php echo e(number_format($penjualanItem->harga_satuan, 0, ',', '.')); ?></td>
                                <td><strong>Rp <?php echo e(number_format($penjualanItem->total_harga, 0, ',', '.')); ?></strong></td>
                                <td>
                                    <?php $invoiceColors = ['Draft'=>'secondary','Sent'=>'info','Paid'=>'success','Canceled'=>'danger']; ?>
                                    <span class="badge bg-<?php echo e($invoiceColors[$penjualanItem->status_invoice] ?? 'secondary'); ?>"><?php echo e($penjualanItem->status_invoice ?? 'Unbilled'); ?></span>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">Belum ada data penjualan untuk klien ini.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECT_HERD\tpst-app\resources\views\admin\klien\show.blade.php ENDPATH**/ ?>