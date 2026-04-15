<?php $__env->startSection('title', 'Laporan Penjualan'); ?>

<?php $__env->startSection('content'); ?>


<div class="page-header d-print-none">
    <div><h1>Laporan Penjualan</h1></div>
    <div class="d-flex gap-2 align-items-center">
        <button type="button" class="btn btn-outline-primary shadow-sm" data-coreui-toggle="modal" data-coreui-target="#previewModal">
            <i class="cil-zoom-in me-1"></i> Preview & Cetak
        </button>
        <div class="btn-group shadow-sm">
            <a href="<?php echo e(route('admin.laporan-operasional.penjualan', array_merge(request()->all(), ['export' => 'pdf']))); ?>" target="_blank" class="btn btn-danger" title="Export PDF">
                <i class="cil-file me-1"></i> PDF
            </a>
            <a href="<?php echo e(route('admin.laporan-operasional.penjualan', array_merge(request()->all(), ['export' => 'excel']))); ?>" class="btn btn-success" title="Export Excel">
                <i class="cil-spreadsheet me-1"></i> Excel
            </a>
        </div>
    </div>
</div>

<div class="card mb-4"><div class="card-body py-3">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-auto"><label class="form-label mb-0 small text-body-secondary">Dari</label><input type="date" name="dari" class="form-control" value="<?php echo e($dari); ?>"></div>
        <div class="col-auto"><label class="form-label mb-0 small text-body-secondary">Sampai</label><input type="date" name="sampai" class="form-control" value="<?php echo e($sampai); ?>"></div>
        <div class="col-auto"><button class="btn btn-primary" type="submit"><i class="cil-filter me-1"></i> Filter</button></div>
    </form>
</div></div>

<div class="card" id="printable">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light"><tr><th>Tanggal</th><th>Klien</th><th>Jenis Produk</th><th class="text-end">Berat</th><th class="text-end">Harga Satuan</th><th class="text-end">Total Harga</th><th>Status Invoice</th></tr></thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e(\Carbon\Carbon::parse($r->tanggal)->format('d M Y')); ?></td>
                        <td><?php echo e($r->klien->nama_klien ?? '-'); ?></td>
                        <td><?php echo e($r->jenis_produk); ?></td>
                        <td class="text-end"><?php echo e(number_format($r->berat_kg, 2, ',', '.')); ?> kg</td>
                        <td class="text-end">Rp <?php echo e(number_format($r->harga_satuan, 0, ',', '.')); ?></td>
                        <td class="text-end fw-semibold">Rp <?php echo e(number_format($r->total_harga, 0, ',', '.')); ?></td>
                        <td>
                            <?php $invoiceColors = ['Draft'=>'secondary','Sent'=>'info','Paid'=>'success','Canceled'=>'danger']; ?>
                            <span class="badge bg-<?php echo e($invoiceColors[$r->status_invoice] ?? 'secondary'); ?>"><?php echo e($r->status_invoice ?? 'Unbilled'); ?></span>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="6" class="text-center py-4 text-body-secondary">Belum ada data penjualan.</td></tr>
                    <?php endif; ?>
                </tbody>
                <tfoot class="border-top border-2 fw-bold">
                    <tr><td colspan="3" class="text-end">TOTAL (<?php echo e(number_format($totals->total_rows ?? 0, 0, ',', '.')); ?> Transaksi)</td><td class="text-end"><?php echo e(number_format($totals->total_berat ?? 0, 2, ',', '.')); ?> kg</td><td></td><td class="text-end">Rp <?php echo e(number_format($totals->total_harga ?? 0, 0, ',', '.')); ?></td><td></td></tr>
                </tfoot>
            </table>
        </div>
    </div>
<?php if($rows->hasPages()): ?> <div class="card-footer bg-white"><?php echo e($rows->links()); ?></div> <?php endif; ?>
</div>

<!-- Modal Preview -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header d-print-none">
                <h5 class="modal-title" id="previewModalLabel">Preview Laporan Penjualan</h5>
                <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-light p-0">
                <div id="printArea" class="bg-white p-5 shadow-sm mx-auto" style="max-width: 21cm; min-height: 29.7cm;">
                    <?php if (isset($component)) { $__componentOriginalb7b80f38d0023f8f730a94fb78f032db = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb7b80f38d0023f8f730a94fb78f032db = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.kop-surat','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('kop-surat'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb7b80f38d0023f8f730a94fb78f032db)): ?>
<?php $attributes = $__attributesOriginalb7b80f38d0023f8f730a94fb78f032db; ?>
<?php unset($__attributesOriginalb7b80f38d0023f8f730a94fb78f032db); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb7b80f38d0023f8f730a94fb78f032db)): ?>
<?php $component = $__componentOriginalb7b80f38d0023f8f730a94fb78f032db; ?>
<?php unset($__componentOriginalb7b80f38d0023f8f730a94fb78f032db); ?>
<?php endif; ?>
                    
                    <div class="text-center mb-4">
                        <h4 class="fw-bold text-uppercase mb-1">LAPORAN PENJUALAN</h4>
                        <p class="text-secondary">Periode: <?php echo e(\Carbon\Carbon::parse($dari)->format('d/m/Y')); ?> - <?php echo e(\Carbon\Carbon::parse($sampai)->format('d/m/Y')); ?></p>
                    </div>

                    <table class="table table-bordered border-dark table-sm">
                        <thead class="table-light border-dark">
                            <tr>
                                <th class="text-center" style="width: 40px;">No</th>
                                <th>Tanggal</th>
                                <th>Klien</th>
                                <th>Jenis Produk</th>
                                <th class="text-end">Berat (kg)</th>
                                <th class="text-end">Harga Satuan</th>
                                <th class="text-end">Total Harga</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                $allRowsForPrint = \App\Models\Penjualan::with('klien')
                                    ->when($dari, fn($q)=>$q->whereDate('tanggal','>=',$dari))
                                    ->when($sampai, fn($q)=>$q->whereDate('tanggal','<=',$sampai))
                                    ->orderByDesc('tanggal')
                                    ->get(); 
                            ?>
                            <?php $__currentLoopData = $allRowsForPrint; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td class="text-center"><?php echo e($index + 1); ?></td>
                                <td><?php echo e(\Carbon\Carbon::parse($r->tanggal)->format('d/m/Y')); ?></td>
                                <td><?php echo e($r->klien->nama_klien ?? '-'); ?></td>
                                <td><?php echo e($r->jenis_produk); ?></td>
                                <td class="text-end"><?php echo e(number_format($r->berat_kg, 2, ',', '.')); ?></td>
                                <td class="text-end"><?php echo e(number_format($r->harga_satuan, 0, ',', '.')); ?></td>
                                <td class="text-end fw-bold"><?php echo e(number_format($r->total_harga, 0, ',', '.')); ?></td>
                                <td><?php echo e($r->status_invoice ?? 'Unbilled'); ?></td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                        <tfoot class="fw-bold">
                            <tr class="table-light border-dark">
                                <td colspan="4" class="text-end">TOTAL</td>
                                <td class="text-end"><?php echo e(number_format($totals->total_berat ?? 0, 2, ',', '.')); ?> kg</td>
                                <td></td>
                                <td class="text-end">Rp <?php echo e(number_format($totals->total_harga ?? 0, 0, ',', '.')); ?></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>

                    <div class="row mt-5">
                        <div class="col-8"></div>
                        <div class="col-4 text-center">
                            <p class="mb-5">Dicetak pada: <?php echo e(now()->format('d/m/Y H:i')); ?></p>
                            <div class="mt-5">
                                <p class="fw-bold mb-0">( ____________________ )</p>
                                <p class="text-secondary small">Admin Operasional</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer d-print-none">
                <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="window.print()">
                    <i class="cil-print me-1"></i> Cetak Sekarang
                </button>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    @media print {
        body * { visibility: hidden; overflow: visible !important; }
        #printArea, #printArea * { visibility: visible; }
        #printArea {
            position: absolute; left: 0; top: 0; width: 100%;
            padding: 0 !important; margin: 0 !important;
        }
        .modal, .modal-backdrop, .sidebar, .header, .mobile-bottom-nav { display: none !important; }
        .modal-dialog, .modal-content, .modal-body {
            display: block !important; border: none !important;
            box-shadow: none !important; padding: 0 !important;
            margin: 0 !important; overflow: visible !important;
        }
    }
</style>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECT_HERD\tpst-app\resources\views/admin/laporan/penjualan.blade.php ENDPATH**/ ?>