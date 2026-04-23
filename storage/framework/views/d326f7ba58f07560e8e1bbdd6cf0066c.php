<?php $__env->startSection('title', 'Laporan Perhitungan Upah Karyawan'); ?>

<?php $__env->startSection('content'); ?>

<div class="page-header d-print-none">
    <div><h1>Laporan Perhitungan Upah Karyawan</h1></div>
    <div class="d-flex gap-2 align-items-center">
        <button type="button" class="btn btn-outline-primary shadow-sm" data-coreui-toggle="modal" data-coreui-target="#previewModal">
            <i class="cil-zoom-in me-1"></i> Preview & Cetak
        </button>
        <div class="btn-group shadow-sm">
            <a href="<?php echo e(route('admin.laporan-operasional.upah', array_merge(request()->all(), ['export' => 'pdf']))); ?>" target="_blank" class="btn btn-danger" title="Export PDF">
                <i class="cil-file me-1"></i> PDF
            </a>
            <a href="<?php echo e(route('admin.laporan-operasional.upah', array_merge(request()->all(), ['export' => 'excel']))); ?>" class="btn btn-success" title="Export Excel">
                <i class="cil-spreadsheet me-1"></i> Excel
            </a>
        </div>
    </div>
</div>

<div class="card mb-4 d-print-none">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-auto">
                <label class="form-label mb-0 small text-body-secondary">Bulan</label>
                <select name="month" class="form-select">
                    <option value="">-- Bebas --</option>
                    <?php $__currentLoopData = [
                        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
                        '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
                        '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($m); ?>" <?php echo e(request('month') == $m ? 'selected' : ''); ?>><?php echo e($name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-auto">
                <label class="form-label mb-0 small text-body-secondary">Tahun</label>
                <select name="year" class="form-select">
                    <?php for($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                        <option value="<?php echo e($y); ?>" <?php echo e(request('year', date('Y')) == $y ? 'selected' : ''); ?>><?php echo e($y); ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-auto">
                <div class="vr h-100 mx-2"></div>
            </div>
            <div class="col-auto">
                <label class="form-label mb-0 small text-body-secondary">Dari</label>
                <input type="date" name="dari" class="form-control" value="<?php echo e($dari); ?>">
            </div>
            <div class="col-auto">
                <label class="form-label mb-0 small text-body-secondary">Sampai</label>
                <input type="date" name="sampai" class="form-control" value="<?php echo e($sampai); ?>">
            </div>
            <div class="col-auto">
                <label class="form-label mb-0 small text-body-secondary">Skema Upah</label>
                <select name="skema_upah" class="form-select">
                    <option value="">-- Semua --</option>
                    <option value="harian" <?php echo e(request('skema_upah') == 'harian' ? 'selected' : ''); ?>>Harian</option>
                    <option value="bulanan" <?php echo e(request('skema_upah') == 'bulanan' ? 'selected' : ''); ?>>Bulanan</option>
                    <option value="borongan" <?php echo e(request('skema_upah') == 'borongan' ? 'selected' : ''); ?>>Borongan</option>
                </select>
            </div>
            <div class="col-auto">
                <button class="btn btn-primary" type="submit">
                    <i class="cil-filter me-1"></i> Filter
                </button>
                <a href="<?php echo e(route('admin.laporan-operasional.upah')); ?>" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="row mb-4 d-print-none">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm bg-primary text-white">
            <div class="card-body">
                <div class="text-white-50 small mb-1">Total Upah (Gross)</div>
                <div class="fs-4 fw-bold">Rp <?php echo e(number_format($totals->total_wage, 0, ',', '.')); ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm bg-success text-white">
            <div class="card-body">
                <div class="text-white-50 small mb-1">Sudah Dibayar</div>
                <div class="fs-4 fw-bold">Rp <?php echo e(number_format($totals->total_paid, 0, ',', '.')); ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm bg-danger text-white">
            <div class="card-body">
                <div class="text-white-50 small mb-1">Belum Dibayar</div>
                <div class="fs-4 fw-bold">Rp <?php echo e(number_format($totals->total_unpaid, 0, ',', '.')); ?></div>
            </div>
        </div>
    </div>
</div>

<div class="card" id="printable">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Periode</th>
                        <th>Karyawan</th>
                        <th>H</th>
                        <th>S/I</th>
                        <th>A</th>
                        <th>Skema</th>
                        <th class="text-end">Output</th>
                        <th class="text-end">Total Upah</th>
                        <th class="text-end">Sdh Dibayar</th>
                        <th class="text-end">Belum Dibayar</th>
                        <th class="text-center">Status</th>
                        <th>Tgl Bayar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td>
                            <div class="small text-body-secondary"><?php echo e(\Carbon\Carbon::parse($r->week_start)->format('d/m/Y')); ?></div>
                            <div class="small text-body-secondary"><?php echo e(\Carbon\Carbon::parse($r->week_end)->format('d/m/Y')); ?></div>
                        </td>
                        <td>
                            <strong><?php echo e($r->user->name ?? '-'); ?></strong>
                            <div class="small text-body-secondary"><?php echo e($r->user->position ?? '-'); ?></div>
                        </td>
                        <td><?php echo e($r->stats->hadir ?? 0); ?></td>
                        <td><?php echo e(($r->stats->sakit ?? 0) + ($r->stats->izin ?? 0)); ?></td>
                        <td class="text-danger"><?php echo e($r->stats->mangkir ?? 0); ?></td>
                        <td><span class="text-capitalize"><?php echo e($r->user->salary_type ?? '-'); ?></span></td>
                        <td class="text-end"><?php echo e(number_format($r->total_quantity, 2, ',', '.')); ?></td>
                        <td class="text-end fw-bold">Rp <?php echo e(number_format($r->total_wage, 0, ',', '.')); ?></td>
                        <td class="text-end text-success">
                            <?php echo e($r->status === 'paid' ? 'Rp ' . number_format($r->total_wage, 0, ',', '.') : '-'); ?>

                        </td>
                        <td class="text-end text-danger">
                            <?php echo e($r->status !== 'paid' ? 'Rp ' . number_format($r->total_wage, 0, ',', '.') : '-'); ?>

                        </td>
                        <td class="text-center">
                            <?php 
                                $statusColors = [
                                    'pending' => 'warning',
                                    'approved' => 'info',
                                    'paid' => 'success'
                                ]; 
                                $statusLabels = [
                                    'pending' => 'Pending',
                                    'approved' => 'Disetujui',
                                    'paid' => 'Dibayar'
                                ];
                            ?>
                            <span class="badge bg-<?php echo e($statusColors[$r->status] ?? 'secondary'); ?>">
                                <?php echo e($statusLabels[$r->status] ?? $r->status); ?>

                            </span>
                        </td>
                        <td><?php echo e($r->paid_date ? \Carbon\Carbon::parse($r->paid_date)->format('d/m/Y') : '-'); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="9" class="text-center py-4 text-body-secondary">
                            Belum ada data upah pada periode ini.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
                <tfoot class="border-top border-2 fw-bold bg-light">
                    <tr>
                        <td colspan="4" class="text-end">TOTAL (<?php echo e(number_format($totals->total_rows, 0, ',', '.')); ?> Record)</td>
                        <td class="text-end text-primary">Rp <?php echo e(number_format($totals->total_wage, 0, ',', '.')); ?></td>
                        <td class="text-end text-success">Rp <?php echo e(number_format($totals->total_paid, 0, ',', '.')); ?></td>
                        <td class="text-end text-danger">Rp <?php echo e(number_format($totals->total_unpaid, 0, ',', '.')); ?></td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <?php if($rows->hasPages()): ?> 
        <div class="card-footer bg-white d-print-none">
            <?php echo e($rows->links()); ?>

        </div> 
    <?php endif; ?>
</div>

<!-- Modal Preview -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header d-print-none">
                <h5 class="modal-title" id="previewModalLabel">Preview Laporan Upah Karyawan</h5>
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
                        <h4 class="fw-bold text-uppercase mb-1">LAPORAN PERHITUNGAN UPAH KARYAWAN</h4>
                        <p class="text-secondary">Periode: <?php echo e(\Carbon\Carbon::parse($dari)->format('d/m/Y')); ?> - <?php echo e(\Carbon\Carbon::parse($sampai)->format('d/m/Y')); ?></p>
                    </div>

                    <div class="row mb-3">
                        <div class="col-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td style="width: 150px;">Skema Upah</td>
                                    <td>: <?php echo e($skemaUpah ?: 'Semua'); ?></td>
                                </tr>
                                <tr>
                                    <td>Total Record</td>
                                    <td>: <?php echo e($totals->total_rows); ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td style="width: 150px;">Total Upah</td>
                                    <td>: <strong>Rp <?php echo e(number_format($totals->total_wage, 0, ',', '.')); ?></strong></td>
                                </tr>
                                <tr>
                                    <td>Sudah Dibayar</td>
                                    <td>: <span class="text-success">Rp <?php echo e(number_format($totals->total_paid, 0, ',', '.')); ?></span></td>
                                </tr>
                                <tr>
                                    <td>Belum Dibayar</td>
                                    <td>: <span class="text-danger">Rp <?php echo e(number_format($totals->total_unpaid, 0, ',', '.')); ?></span></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <table class="table table-bordered border-dark table-sm">
                        <thead class="table-light border-dark">
                            <tr>
                                <th class="text-center" style="width: 40px;">No</th>
                                <th>Periode</th>
                                <th>Nama Karyawan</th>
                                <th>Skema</th>
                                <th class="text-end">Total Upah</th>
                                <th class="text-end">Sdh Dibayar</th>
                                <th class="text-end">Blm Dibayar</th>
                                <th class="text-center">Status</th>
                                <th>Tgl Bayar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                $allRowsForPrint = \App\Models\WageCalculation::with('user')
                                    ->join('users', 'wage_calculations.user_id', '=', 'users.id')
                                    ->select('wage_calculations.*')
                                    ->when($dari, fn ($q) => $q->whereDate('week_start', '>=', $dari))
                                    ->when($sampai, fn ($q) => $q->whereDate('week_start', '<=', $sampai))
                                    ->when($skemaUpah, fn ($q) => $q->where('users.salary_type', $skemaUpah))
                                    ->orderByDesc('week_start')
                                    ->get(); 
                                $statusLabels = [
                                    'pending' => 'Pending',
                                    'approved' => 'Disetujui',
                                    'paid' => 'Dibayar'
                                ];
                            ?>
                            <?php $__currentLoopData = $allRowsForPrint; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td class="text-center"><?php echo e($index + 1); ?></td>
                                <td class="small"><?php echo e(\Carbon\Carbon::parse($r->week_start)->format('d/m/y')); ?>-<?php echo e(\Carbon\Carbon::parse($r->week_end)->format('d/m/y')); ?></td>
                                <td><?php echo e($r->user->name ?? '-'); ?></td>
                                <td class="text-capitalize small"><?php echo e($r->user->salary_type ?? '-'); ?></td>
                                <td class="text-end fw-bold">Rp <?php echo e(number_format($r->total_wage, 0, ',', '.')); ?></td>
                                <td class="text-end">
                                    <?php echo e($r->status === 'paid' ? 'Rp ' . number_format($r->total_wage, 0, ',', '.') : '-'); ?>

                                </td>
                                <td class="text-end">
                                    <?php echo e($r->status !== 'paid' ? 'Rp ' . number_format($r->total_wage, 0, ',', '.') : '-'); ?>

                                </td>
                                <td class="text-center small"><?php echo e($statusLabels[$r->status] ?? $r->status); ?></td>
                                <td><?php echo e($r->paid_date ? \Carbon\Carbon::parse($r->paid_date)->format('d/m/y') : '-'); ?></td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                        <tfoot class="border-dark fw-bold">
                            <tr>
                                <td colspan="4" class="text-end">TOTAL</td>
                                <td class="text-end">Rp <?php echo e(number_format($totals->total_wage, 0, ',', '.')); ?></td>
                                <td class="text-end">Rp <?php echo e(number_format($totals->total_paid, 0, ',', '.')); ?></td>
                                <td class="text-end">Rp <?php echo e(number_format($totals->total_unpaid, 0, ',', '.')); ?></td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>

                    <div class="row mt-5">
                        <div class="col-8"></div>
                        <div class="col-4 text-center">
                            <p class="mb-5">Dicetak pada: <?php echo e(now()->format('d/m/Y H:i')); ?></p>
                            <div class="mt-5">
                                <p class="fw-bold mb-0">( ____________________ )</p>
                                <p class="text-secondary small">Admin HRD / Operasional</p>
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
        .sidebar, .header, .mobile-bottom-nav, .modal-backdrop, .breadcrumb, .page-header, .card, form, .no-print, .d-print-none {
            display: none !important;
        }
        .wrapper { padding: 0 !important; margin: 0 !important; }
        .body { padding: 0 !important; margin: 0 !important; }
        .container-fluid { padding: 0 !important; margin: 0 !important; }
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

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECT_HERD\tpst-app\resources\views/admin/laporan/upah.blade.php ENDPATH**/ ?>