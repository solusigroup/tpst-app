<?php $__env->startSection('title', 'Rekap Ritase per Tanggal & Jenis Klien'); ?>

<?php $__env->startSection('content'); ?>

<div class="page-header d-print-none">
    <div><h1>Rekap Ritase</h1></div>
    <div class="d-flex gap-2 align-items-center">
        <button type="button" class="btn btn-outline-primary shadow-sm" data-coreui-toggle="modal" data-coreui-target="#previewModal">
            <i class="cil-zoom-in me-1"></i> Preview & Cetak
        </button>
        <div class="btn-group shadow-sm">
            <a href="<?php echo e(route('admin.laporan-operasional.rekap-ritase', ['dari' => $dari, 'sampai' => $sampai, 'jenis_klien' => $jenisKlien, 'klien_id' => $klienId, 'export' => 'pdf'])); ?>" target="_blank" class="btn btn-danger" title="Export PDF">
                <i class="cil-file me-1"></i> PDF
            </a>
            <a href="<?php echo e(route('admin.laporan-operasional.rekap-ritase', ['dari' => $dari, 'sampai' => $sampai, 'jenis_klien' => $jenisKlien, 'klien_id' => $klienId, 'export' => 'excel'])); ?>" class="btn btn-success" title="Export Excel">
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
            <label class="form-label mb-0 small text-body-secondary">Jenis Klien</label>
            <select name="jenis_klien" class="form-select">
                <option value="">-- Semua Jenis --</option>
                <?php $__currentLoopData = ['DLH', 'Swasta', 'Offtaker', 'Internal']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $jk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($jk); ?>" <?php echo e($jenisKlien == $jk ? 'selected' : ''); ?>><?php echo e($jk); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="col-auto">
            <label class="form-label mb-0 small text-body-secondary">Klien</label>
            <select name="klien_id" class="form-select">
                <option value="">-- Semua Klien --</option>
                <?php $__currentLoopData = $kliens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($k->id); ?>" <?php echo e($klienId == $k->id ? 'selected' : ''); ?>><?php echo e($k->nama_klien); ?> (<?php echo e($k->jenis); ?>)</option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="col-auto"><button class="btn btn-primary" type="submit"><i class="cil-filter me-1"></i> Filter</button></div>
    </form>
</div></div>


<div class="row g-3 mb-4">
    <?php
        $jenisColors = [
            'DLH' => ['bg' => 'bg-info', 'border' => 'border-info', 'icon' => 'cil-building'],
            'Swasta' => ['bg' => 'bg-primary', 'border' => 'border-primary', 'icon' => 'cil-briefcase'],
            'Offtaker' => ['bg' => 'bg-success', 'border' => 'border-success', 'icon' => 'cil-people'],
            'Internal' => ['bg' => 'bg-secondary', 'border' => 'border-secondary', 'icon' => 'cil-home'],
        ];
    ?>
    <?php $__currentLoopData = $rekapPerJenis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rj): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php $style = $jenisColors[$rj->jenis] ?? ['bg' => 'bg-dark', 'border' => 'border-dark', 'icon' => 'cil-tag']; ?>
    <div class="col-xl-3 col-md-6">
        <div class="card border-start border-start-4 <?php echo e($style['border']); ?> h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="text-body-secondary small fw-semibold text-uppercase"><?php echo e($rj->jenis); ?></div>
                    <div class="<?php echo e($style['bg']); ?> text-white rounded-circle d-flex align-items-center justify-content-center" style="width:32px;height:32px;">
                        <i class="<?php echo e($style['icon']); ?>"></i>
                    </div>
                </div>
                <div class="fs-4 fw-bold"><?php echo e(number_format($rj->total_ritase, 0, ',', '.')); ?> <span class="small fw-normal text-body-secondary">ritase</span></div>
                <div class="small text-body-secondary mt-1">
                    <span class="fw-semibold"><?php echo e(number_format($rj->total_netto, 2, ',', '.')); ?></span> kg netto
                </div>
                <div class="small text-body-secondary">
                    Rp <span class="fw-semibold"><?php echo e(number_format($rj->total_tipping, 0, ',', '.')); ?></span> tipping
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    
    <div class="col-xl-3 col-md-6">
        <div class="card border-start border-start-4 border-warning h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="text-body-secondary small fw-semibold text-uppercase">TOTAL KESELURUHAN</div>
                    <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center" style="width:32px;height:32px;">
                        <i class="cil-chart-pie"></i>
                    </div>
                </div>
                <div class="fs-4 fw-bold"><?php echo e(number_format($grandTotals->total_ritase ?? 0, 0, ',', '.')); ?> <span class="small fw-normal text-body-secondary">ritase</span></div>
                <div class="small text-body-secondary mt-1">
                    <span class="fw-semibold"><?php echo e(number_format($grandTotals->total_netto ?? 0, 2, ',', '.')); ?></span> kg netto
                </div>
                <div class="small text-body-secondary">
                    Rp <span class="fw-semibold"><?php echo e(number_format($grandTotals->total_tipping ?? 0, 0, ',', '.')); ?></span> tipping
                </div>
            </div>
        </div>
    </div>
</div>


<div class="card mb-4">
    <div class="card-header bg-light d-flex align-items-center justify-content-between">
        <strong><i class="cil-calendar me-2"></i>Rekap Harian per Jenis Klien</strong>
        <span class="badge bg-primary"><?php echo e($pivotData->count()); ?> hari</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-sm align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th rowspan="2" class="align-middle text-center" style="min-width:110px;">Tanggal</th>
                        <?php $__currentLoopData = $jenisTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $jt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <th colspan="3" class="text-center border-start"><?php echo e($jt); ?></th>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <th colspan="3" class="text-center border-start bg-warning bg-opacity-10">Total Harian</th>
                    </tr>
                    <tr>
                        <?php $__currentLoopData = $jenisTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $jt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <th class="text-center border-start small">Ritase</th>
                        <th class="text-end small">Netto (kg)</th>
                        <th class="text-end small">Tipping (Rp)</th>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <th class="text-center border-start small">Ritase</th>
                        <th class="text-end small">Netto (kg)</th>
                        <th class="text-end small">Tipping (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $pivotData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="fw-semibold text-center"><?php echo e(\Carbon\Carbon::parse($row['tanggal'])->format('d M Y')); ?></td>
                        <?php $__currentLoopData = $jenisTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $jt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $cell = $row['jenis'][$jt] ?? null; ?>
                        <td class="text-center border-start"><?php echo e($cell ? number_format($cell['total_ritase'], 0, ',', '.') : '-'); ?></td>
                        <td class="text-end"><?php echo e($cell ? number_format($cell['total_netto'], 2, ',', '.') : '-'); ?></td>
                        <td class="text-end"><?php echo e($cell ? number_format($cell['total_tipping'], 0, ',', '.') : '-'); ?></td>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <td class="text-center border-start fw-bold"><?php echo e(number_format($row['total_ritase'], 0, ',', '.')); ?></td>
                        <td class="text-end fw-bold"><?php echo e(number_format($row['total_netto'], 2, ',', '.')); ?></td>
                        <td class="text-end fw-bold"><?php echo e(number_format($row['total_tipping'], 0, ',', '.')); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="<?php echo e(1 + (count($jenisTypes) * 3) + 3); ?>" class="text-center py-4 text-body-secondary">Tidak ada data ritase pada periode ini.</td></tr>
                    <?php endif; ?>
                </tbody>
                <?php if($pivotData->count() > 0): ?>
                <tfoot class="border-top border-2 fw-bold table-light">
                    <tr>
                        <td class="text-center">TOTAL</td>
                        <?php $__currentLoopData = $jenisTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $jt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $jtRekap = $rekapPerJenis->firstWhere('jenis', $jt);
                        ?>
                        <td class="text-center border-start"><?php echo e($jtRekap ? number_format($jtRekap->total_ritase, 0, ',', '.') : '-'); ?></td>
                        <td class="text-end"><?php echo e($jtRekap ? number_format($jtRekap->total_netto, 2, ',', '.') : '-'); ?></td>
                        <td class="text-end"><?php echo e($jtRekap ? number_format($jtRekap->total_tipping, 0, ',', '.') : '-'); ?></td>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <td class="text-center border-start"><?php echo e(number_format($grandTotals->total_ritase ?? 0, 0, ',', '.')); ?></td>
                        <td class="text-end"><?php echo e(number_format($grandTotals->total_netto ?? 0, 2, ',', '.')); ?></td>
                        <td class="text-end"><?php echo e(number_format($grandTotals->total_tipping ?? 0, 0, ',', '.')); ?></td>
                    </tr>
                </tfoot>
                <?php endif; ?>
            </table>
        </div>
    </div>
</div>


<div class="card mb-4">
    <div class="card-header bg-light d-flex align-items-center justify-content-between">
        <strong><i class="cil-people me-2"></i>Detail Rekap per Klien</strong>
        <span class="badge bg-primary"><?php echo e($rekapPerKlien->count()); ?> klien</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-sm align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Nama Klien</th>
                        <th>Jenis</th>
                        <th class="text-center">Total Ritase</th>
                        <th class="text-end">Berat Netto (kg)</th>
                        <th class="text-end">Biaya Tipping (Rp)</th>
                        <th class="text-end">Rata-rata Netto/Ritase</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $currentJenis = null; ?>
                    <?php $__currentLoopData = $rekapPerKlien; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $rk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($currentJenis !== $rk->jenis): ?>
                        <?php $currentJenis = $rk->jenis; ?>
                        <tr class="table-light">
                            <td colspan="7" class="fw-bold small text-uppercase">
                                <?php $jColor = $jenisColors[$rk->jenis] ?? ['bg' => 'bg-dark']; ?>
                                <span class="badge <?php echo e($jColor['bg']); ?> me-1"><?php echo e($rk->jenis); ?></span>
                            </td>
                        </tr>
                        <?php endif; ?>
                        <tr>
                            <td><?php echo e($index + 1); ?></td>
                            <td class="fw-semibold"><?php echo e($rk->nama_klien); ?></td>
                            <td>
                                <?php $color = $jenisColors[$rk->jenis]['bg'] ?? 'bg-dark'; ?>
                                <span class="badge <?php echo e($color); ?>"><?php echo e($rk->jenis); ?></span>
                            </td>
                            <td class="text-center"><?php echo e(number_format($rk->total_ritase, 0, ',', '.')); ?></td>
                            <td class="text-end"><?php echo e(number_format($rk->total_netto, 2, ',', '.')); ?></td>
                            <td class="text-end"><?php echo e(number_format($rk->total_tipping, 0, ',', '.')); ?></td>
                            <td class="text-end text-body-secondary"><?php echo e($rk->total_ritase > 0 ? number_format($rk->total_netto / $rk->total_ritase, 2, ',', '.') : '-'); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
                <?php if($rekapPerKlien->count() > 0): ?>
                <tfoot class="border-top border-2 fw-bold table-light">
                    <tr>
                        <td colspan="3" class="text-end">TOTAL KESELURUHAN</td>
                        <td class="text-center"><?php echo e(number_format($grandTotals->total_ritase ?? 0, 0, ',', '.')); ?></td>
                        <td class="text-end"><?php echo e(number_format($grandTotals->total_netto ?? 0, 2, ',', '.')); ?></td>
                        <td class="text-end"><?php echo e(number_format($grandTotals->total_tipping ?? 0, 0, ',', '.')); ?></td>
                        <td class="text-end text-body-secondary"><?php echo e(($grandTotals->total_ritase ?? 0) > 0 ? number_format(($grandTotals->total_netto ?? 0) / $grandTotals->total_ritase, 2, ',', '.') : '-'); ?></td>
                    </tr>
                </tfoot>
                <?php endif; ?>
            </table>
        </div>
    </div>
</div>

<!-- Modal Preview -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header d-print-none">
                <h5 class="modal-title" id="previewModalLabel">Preview Rekap Ritase</h5>
                <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-light p-0">
                <div id="printArea" class="bg-white p-5 shadow-sm mx-auto" style="max-width: 29.7cm; min-height: 21cm;">
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
                        <h4 class="fw-bold text-uppercase mb-1">REKAP RITASE PER TANGGAL & JENIS KLIEN</h4>
                        <p class="text-secondary">Periode: <?php echo e(\Carbon\Carbon::parse($dari)->format('d/m/Y')); ?> - <?php echo e(\Carbon\Carbon::parse($sampai)->format('d/m/Y')); ?></p>
                    </div>

                    
                    <h6 class="fw-bold mb-2">Ringkasan per Jenis Klien</h6>
                    <table class="table table-bordered table-sm border-dark" style="width: 60%; margin-bottom: 20px;">
                        <thead class="table-light border-dark">
                            <tr>
                                <th>Jenis Klien</th>
                                <th class="text-center">Total Ritase</th>
                                <th class="text-end">Netto (kg)</th>
                                <th class="text-end">Tipping (Rp)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $rekapPerJenis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rj): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($rj->jenis); ?></td>
                                <td class="text-center"><?php echo e(number_format($rj->total_ritase, 0, ',', '.')); ?></td>
                                <td class="text-end"><?php echo e(number_format($rj->total_netto, 2, ',', '.')); ?></td>
                                <td class="text-end"><?php echo e(number_format($rj->total_tipping, 0, ',', '.')); ?></td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                        <tfoot class="fw-bold">
                            <tr>
                                <td>TOTAL</td>
                                <td class="text-center"><?php echo e(number_format($grandTotals->total_ritase ?? 0, 0, ',', '.')); ?></td>
                                <td class="text-end"><?php echo e(number_format($grandTotals->total_netto ?? 0, 2, ',', '.')); ?></td>
                                <td class="text-end"><?php echo e(number_format($grandTotals->total_tipping ?? 0, 0, ',', '.')); ?></td>
                            </tr>
                        </tfoot>
                    </table>

                    
                    <h6 class="fw-bold mb-2">Rekap Harian</h6>
                    <table class="table table-bordered border-dark table-sm" style="font-size: 10px;">
                        <thead class="table-light border-dark">
                            <tr>
                                <th rowspan="2" class="align-middle text-center">Tanggal</th>
                                <?php $__currentLoopData = $jenisTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $jt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <th colspan="3" class="text-center border-start"><?php echo e($jt); ?></th>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <th colspan="3" class="text-center border-start">Total</th>
                            </tr>
                            <tr>
                                <?php $__currentLoopData = $jenisTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $jt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <th class="text-center border-start">Rit</th>
                                <th class="text-end">Netto</th>
                                <th class="text-end">Tipping</th>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <th class="text-center border-start">Rit</th>
                                <th class="text-end">Netto</th>
                                <th class="text-end">Tipping</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $pivotData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td class="text-center"><?php echo e(\Carbon\Carbon::parse($row['tanggal'])->format('d/m/Y')); ?></td>
                                <?php $__currentLoopData = $jenisTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $jt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php $cell = $row['jenis'][$jt] ?? null; ?>
                                <td class="text-center border-start"><?php echo e($cell ? number_format($cell['total_ritase'], 0, ',', '.') : '-'); ?></td>
                                <td class="text-end"><?php echo e($cell ? number_format($cell['total_netto'], 2, ',', '.') : '-'); ?></td>
                                <td class="text-end"><?php echo e($cell ? number_format($cell['total_tipping'], 0, ',', '.') : '-'); ?></td>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <td class="text-center border-start fw-bold"><?php echo e(number_format($row['total_ritase'], 0, ',', '.')); ?></td>
                                <td class="text-end fw-bold"><?php echo e(number_format($row['total_netto'], 2, ',', '.')); ?></td>
                                <td class="text-end fw-bold"><?php echo e(number_format($row['total_tipping'], 0, ',', '.')); ?></td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                        <tfoot class="fw-bold border-dark">
                            <tr class="table-light">
                                <td class="text-center">TOTAL</td>
                                <?php $__currentLoopData = $jenisTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $jt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php $jtRekap = $rekapPerJenis->firstWhere('jenis', $jt); ?>
                                <td class="text-center border-start"><?php echo e($jtRekap ? number_format($jtRekap->total_ritase, 0, ',', '.') : '-'); ?></td>
                                <td class="text-end"><?php echo e($jtRekap ? number_format($jtRekap->total_netto, 2, ',', '.') : '-'); ?></td>
                                <td class="text-end"><?php echo e($jtRekap ? number_format($jtRekap->total_tipping, 0, ',', '.') : '-'); ?></td>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <td class="text-center border-start"><?php echo e(number_format($grandTotals->total_ritase ?? 0, 0, ',', '.')); ?></td>
                                <td class="text-end"><?php echo e(number_format($grandTotals->total_netto ?? 0, 2, ',', '.')); ?></td>
                                <td class="text-end"><?php echo e(number_format($grandTotals->total_tipping ?? 0, 0, ',', '.')); ?></td>
                            </tr>
                        </tfoot>
                    </table>

                    
                    <h6 class="fw-bold mb-2 mt-4">Detail per Klien</h6>
                    <table class="table table-bordered border-dark table-sm">
                        <thead class="table-light border-dark">
                            <tr>
                                <th class="text-center" style="width: 40px;">No</th>
                                <th>Nama Klien</th>
                                <th>Jenis</th>
                                <th class="text-center">Ritase</th>
                                <th class="text-end">Netto (kg)</th>
                                <th class="text-end">Tipping (Rp)</th>
                                <th class="text-end">Avg Netto/Rit</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $rekapPerKlien; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $rk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td class="text-center"><?php echo e($index + 1); ?></td>
                                <td><?php echo e($rk->nama_klien); ?></td>
                                <td><?php echo e($rk->jenis); ?></td>
                                <td class="text-center"><?php echo e(number_format($rk->total_ritase, 0, ',', '.')); ?></td>
                                <td class="text-end"><?php echo e(number_format($rk->total_netto, 2, ',', '.')); ?></td>
                                <td class="text-end"><?php echo e(number_format($rk->total_tipping, 0, ',', '.')); ?></td>
                                <td class="text-end"><?php echo e($rk->total_ritase > 0 ? number_format($rk->total_netto / $rk->total_ritase, 2, ',', '.') : '-'); ?></td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                        <tfoot class="fw-bold">
                            <tr class="table-light border-dark">
                                <td colspan="3" class="text-end">TOTAL</td>
                                <td class="text-center"><?php echo e(number_format($grandTotals->total_ritase ?? 0, 0, ',', '.')); ?></td>
                                <td class="text-end"><?php echo e(number_format($grandTotals->total_netto ?? 0, 2, ',', '.')); ?></td>
                                <td class="text-end"><?php echo e(number_format($grandTotals->total_tipping ?? 0, 0, ',', '.')); ?></td>
                                <td class="text-end"><?php echo e(($grandTotals->total_ritase ?? 0) > 0 ? number_format(($grandTotals->total_netto ?? 0) / $grandTotals->total_ritase, 2, ',', '.') : '-'); ?></td>
                            </tr>
                        </tfoot>
                    </table>

                    <div class="row mt-5">
                        <div class="col-8"></div>
                        <div class="col-4 text-center">
                            <p class="mb-5">Dicetak pada: <?php echo e(now()->format('d/m/Y H:i')); ?></p>
                            <div class="mt-5">
                                <p class="fw-bold mb-0">( ____________________ )</p>
                                <p class="text-secondary small">&nbsp;</p>
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
        body { 
            overflow: visible !important; 
            height: auto !important; 
            background: white !important;
        }
        .sidebar, .header, .mobile-bottom-nav, .modal-backdrop, .breadcrumb, .page-header, .card, form, .no-print, .d-print-none {
            display: none !important;
        }
        .wrapper { padding: 0 !important; margin: 0 !important; }
        .body { padding: 0 !important; margin: 0 !important; }
        .container-fluid { padding: 0 !important; margin: 0 !important; }
        .modal {
            display: block !important;
            position: static !important;
            left: 0 !important;
            top: 0 !important;
            width: 100% !important;
            opacity: 1 !important;
            visibility: visible !important;
            background: white !important;
            overflow: visible !important;
            height: auto !important;
        }
        .modal-dialog {
            max-width: 100% !important;
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            overflow: visible !important;
            height: auto !important;
        }
        .modal-content, .modal-body {
            display: block !important;
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
            background: white !important;
            visibility: visible !important;
            opacity: 1 !important;
            overflow: visible !important;
            height: auto !important;
            max-height: none !important;
        }
        #printArea {
            visibility: visible !important;
            opacity: 1 !important;
            display: block !important;
            padding: 0 !important;
            margin: 0 !important;
            max-width: 100% !important;
            min-height: auto !important;
            box-shadow: none !important;
        }
        #printArea * {
            visibility: visible !important;
            opacity: 1 !important;
        }
        @page {
            size: landscape;
        }
    }
</style>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECT_HERD\tpst-app\resources\views/admin/laporan/rekap-ritase.blade.php ENDPATH**/ ?>