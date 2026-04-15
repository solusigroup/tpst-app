<?php $__env->startSection('title', 'Laporan Ritase'); ?>

<?php $__env->startSection('content'); ?>


<div class="page-header d-print-none">
    <div><h1>Laporan Ritase</h1></div>
    <div class="d-flex gap-2 align-items-center">
        <button type="button" class="btn btn-outline-primary shadow-sm" data-coreui-toggle="modal" data-coreui-target="#previewModal">
            <i class="cil-zoom-in me-1"></i> Preview & Cetak
        </button>
        <div class="btn-group shadow-sm">
            <a href="<?php echo e(route('admin.laporan-operasional.ritase', array_merge(request()->all(), ['export' => 'pdf']))); ?>" target="_blank" class="btn btn-danger" title="Export PDF">
                <i class="cil-file me-1"></i> PDF
            </a>
            <a href="<?php echo e(route('admin.laporan-operasional.ritase', array_merge(request()->all(), ['export' => 'excel']))); ?>" class="btn btn-success" title="Export Excel">
                <i class="cil-spreadsheet me-1"></i> Excel
            </a>
        </div>
    </div>
</div>

<div class="card mb-4"><div class="card-body py-3">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-auto"><label class="form-label mb-0 small text-body-secondary">Dari</label><input type="date" name="dari" class="form-control" value="<?php echo e($dari); ?>"></div>
        <div class="col-auto"><label class="form-label mb-0 small text-body-secondary">Sampai</label><input type="date" name="sampai" class="form-control" value="<?php echo e($sampai); ?>"></div>
        <div class="col-auto">
            <label class="form-label mb-0 small text-body-secondary">Klien</label>
            <select name="klien_id" class="form-select">
                <option value="">-- Semua Klien --</option>
                <?php $__currentLoopData = $kliens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($k->id); ?>" <?php echo e($klienId == $k->id ? 'selected' : ''); ?>><?php echo e($k->nama_klien); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="col-auto">
            <label class="form-label mb-0 small text-body-secondary">Status</label>
            <select name="status" class="form-select">
                <option value="">-- Semua --</option>
                <?php $__currentLoopData = ['masuk','timbang','keluar','selesai']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($s); ?>" <?php echo e($status == $s ? 'selected' : ''); ?>><?php echo e(ucfirst($s)); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="col-auto"><button class="btn btn-primary" type="submit"><i class="cil-filter me-1"></i> Filter</button></div>
    </form>
</div></div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header bg-light"><strong>Rekap Jenis Armada</strong></div>
            <div class="card-body p-0">
                <table class="table table-sm table-hover mb-0">
                    <thead class="small table-light">
                        <tr>
                            <th>Jenis Armada</th>
                            <th class="text-center">Ritase</th>
                            <th class="text-end">Tonase (kg)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $rekapJenis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rj): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($rj->jenis_armada ?? 'N/A'); ?></td>
                            <td class="text-center"><?php echo e(number_format($rj->total_ritase, 0, ',', '.')); ?></td>
                            <td class="text-end"><?php echo e(number_format($rj->total_netto, 2, ',', '.')); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                    <tfoot class="fw-bold table-light">
                        <tr>
                            <td>TOTAL</td>
                            <td class="text-center"><?php echo e(number_format($rekapJenis->sum('total_ritase'), 0, ',', '.')); ?></td>
                            <td class="text-end"><?php echo e(number_format($rekapJenis->sum('total_netto'), 2, ',', '.')); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="card" id="printable">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light"><tr><th>Tanggal</th><th>No Tiket</th><th>Tiket (M)</th><th>Armada</th><th>Jenis Armada</th><th>Klien</th><th class="text-end">Berat Netto</th><th class="text-end">Biaya Tipping</th><th>Status Tiket</th><th>Status Invoice</th></tr></thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e(\Carbon\Carbon::parse($r->waktu_masuk)->format('d M Y')); ?></td>
                        <td><strong><?php echo e($r->nomor_tiket); ?></strong></td>
                        <td><?php echo e($r->tiket ?? '-'); ?></td>
                        <td><?php echo e($r->armada->plat_nomor ?? '-'); ?></td>
                        <td><?php echo e($r->armada->jenis_armada ?? '-'); ?></td>
                        <td><?php echo e($r->klien->nama_klien ?? '-'); ?></td>
                        <td class="text-end"><?php echo e(number_format($r->berat_netto, 2, ',', '.')); ?> kg</td>
                        <td class="text-end">Rp <?php echo e(number_format($r->biaya_tipping, 0, ',', '.')); ?></td>
                        <td>
                            <?php $statusColors = ['masuk'=>'warning','timbang'=>'info','keluar'=>'primary','selesai'=>'success']; ?>
                            <span class="badge bg-<?php echo e($statusColors[$r->status] ?? 'secondary'); ?>"><?php echo e(ucfirst($r->status)); ?></span>
                        </td>
                        <td>
                            <?php $invoiceColors = ['Draft'=>'secondary','Sent'=>'info','Paid'=>'success','Canceled'=>'danger']; ?>
                            <span class="badge bg-<?php echo e($invoiceColors[$r->status_invoice] ?? 'secondary'); ?>"><?php echo e($r->status_invoice ?? 'Unbilled'); ?></span>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="10" class="text-center py-4 text-body-secondary">Belum ada data ritase.</td></tr>
                    <?php endif; ?>
                </tbody>
                <tfoot class="border-top border-2 fw-bold">
                    <tr><td colspan="6" class="text-end">TOTAL (<?php echo e(number_format($totals->total_rows ?? 0, 0, ',', '.')); ?> Ritase)</td><td class="text-end"><?php echo e(number_format($totals->total_netto ?? 0, 2, ',', '.')); ?> kg</td><td class="text-end">Rp <?php echo e(number_format($totals->total_tipping ?? 0, 0, ',', '.')); ?></td><td colspan="2"></td></tr>
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
                <h5 class="modal-title" id="previewModalLabel">Preview Laporan Ritase</h5>
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
                        <h4 class="fw-bold text-uppercase mb-1">LAPORAN RITASE</h4>
                        <p class="text-secondary">Periode: <?php echo e(\Carbon\Carbon::parse($dari)->format('d/m/Y')); ?> - <?php echo e(\Carbon\Carbon::parse($sampai)->format('d/m/Y')); ?></p>
                    </div>

                    <?php if(isset($rekapJenis) && count($rekapJenis) > 0): ?>
                    <div class="mb-4" style="width: 50%;">
                        <h6 class="fw-bold mb-2">Rekap Jenis Armada</h6>
                        <table class="table table-bordered table-sm border-dark">
                            <thead class="table-light border-dark">
                                <tr>
                                    <th>Jenis Armada</th>
                                    <th class="text-center">Ritase</th>
                                    <th class="text-end">Tonase (kg)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $rekapJenis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rj): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($rj->jenis_armada ?? 'N/A'); ?></td>
                                    <td class="text-center"><?php echo e(number_format($rj->total_ritase, 0, ',', '.')); ?></td>
                                    <td class="text-end"><?php echo e(number_format($rj->total_netto, 2, ',', '.')); ?></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                            <tfoot class="fw-bold">
                                <tr>
                                    <td>TOTAL</td>
                                    <td class="text-center"><?php echo e(number_format($rekapJenis->sum('total_ritase'), 0, ',', '.')); ?></td>
                                    <td class="text-end"><?php echo e(number_format($rekapJenis->sum('total_netto'), 2, ',', '.')); ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <?php endif; ?>

                    <table class="table table-bordered border-dark table-sm">
                        <thead class="table-light border-dark">
                            <tr>
                                <th class="text-center" style="width: 40px;">No</th>
                                <th>Tanggal</th>
                                <th>No Tiket</th>
                                <th>Armada</th>
                                <th>Jenis</th>
                                <th>Klien</th>
                                <th class="text-end">Netto (kg)</th>
                                <th class="text-end">Tipping</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                $allRowsForPrint = \App\Models\Ritase::with(['armada', 'klien'])
                                    ->when($dari, fn($q)=>$q->whereDate('waktu_masuk','>=',$dari))
                                    ->when($sampai, fn($q)=>$q->whereDate('waktu_masuk','<=',$sampai))
                                    ->when($klienId, fn($q)=>$q->where('klien_id',$klienId))
                                    ->when($status, fn($q)=>$q->where('status',$status))
                                    ->orderByDesc('waktu_masuk')
                                    ->get(); 
                            ?>
                            <?php $__currentLoopData = $allRowsForPrint; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td class="text-center"><?php echo e($index + 1); ?></td>
                                <td><?php echo e(\Carbon\Carbon::parse($r->waktu_masuk)->format('d/m/Y')); ?></td>
                                <td><?php echo e($r->nomor_tiket); ?></td>
                                <td><?php echo e($r->armada->plat_nomor ?? '-'); ?></td>
                                <td><?php echo e($r->armada->jenis_armada ?? '-'); ?></td>
                                <td><?php echo e($r->klien->nama_klien ?? '-'); ?></td>
                                <td class="text-end"><?php echo e(number_format($r->berat_netto, 2, ',', '.')); ?></td>
                                <td class="text-end"><?php echo e(number_format($r->biaya_tipping, 0, ',', '.')); ?></td>
                                <td><?php echo e(ucfirst($r->status)); ?></td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                        <tfoot class="fw-bold">
                            <tr class="table-light border-dark">
                                <td colspan="6" class="text-end">TOTAL KESELURUHAN</td>
                                <td class="text-end"><?php echo e(number_format($totals->total_netto ?? 0, 2, ',', '.')); ?></td>
                                <td class="text-end">Rp <?php echo e(number_format($totals->total_tipping ?? 0, 0, ',', '.')); ?></td>
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
        /* Sembunyikan semua elemen UI aplikasi */
        .sidebar, .header, .mobile-bottom-nav, .modal-backdrop, .breadcrumb, .page-header, .card, form, .no-print, .d-print-none {
            display: none !important;
        }
        
        /* Reset layout agar penuh halaman */
        .wrapper { padding: 0 !important; margin: 0 !important; }
        .body { padding: 0 !important; margin: 0 !important; }
        .container-fluid { padding: 0 !important; margin: 0 !important; }
        
        /* Tampilkan Modal secara absolut di atas halaman */
        .modal {
            display: block !important;
            position: absolute !important;
            left: 0 !important;
            top: 0 !important;
            width: 100% !important;
            opacity: 1 !important;
            visibility: visible !important;
            background: white !important;
        }
        .modal-dialog {
            max-width: 100% !important;
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        .modal-header, .modal-footer {
            display: none !important;
        }
        .modal-content, .modal-body {
            display: block !important;
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
            background: white !important;
            visibility: visible !important;
            opacity: 1 !important;
        }
        
        #printArea {
            visibility: visible !important;
            opacity: 1 !important;
            display: block !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        #printArea * {
            visibility: visible !important;
            opacity: 1 !important;
        }
    }
</style>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECT_HERD\tpst-app\resources\views/admin/laporan/ritase.blade.php ENDPATH**/ ?>