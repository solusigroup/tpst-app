<?php $__env->startSection('title', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div class="d-flex align-items-center gap-4">
            <div>
                <h1>Dashboard</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </nav>
            </div>

            
            <form action="<?php echo e(route('admin.dashboard')); ?>" method="GET" class="period-selector-container d-none d-md-flex">
                <div class="period-selector shadow-sm">
                    <i class="cil-calendar text-primary me-2"></i>
                    <select name="month" onchange="this.form.submit()">
                        <?php $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e(sprintf('%02d', $m)); ?>" <?php echo e($selectedMonth == $m ? 'selected' : ''); ?>><?php echo e($name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <div class="divider"></div>
                    <select name="year" onchange="this.form.submit()">
                        <?php $__currentLoopData = $years; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $y): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($y); ?>" <?php echo e($selectedYear == $y ? 'selected' : ''); ?>><?php echo e($y); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </form>
        </div>
        
        <div class="d-flex flex-wrap align-items-center gap-3">
            
            <form action="<?php echo e(route('admin.dashboard')); ?>" method="GET" class="d-flex d-md-none align-items-center">
                <div class="period-selector shadow-sm" style="padding: 0.3rem 0.75rem;">
                    <select name="month" onchange="this.form.submit()" style="font-size: 0.8rem;">
                        <?php $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e(sprintf('%02d', $m)); ?>" <?php echo e($selectedMonth == $m ? 'selected' : ''); ?>><?php echo e($name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <div class="divider"></div>
                    <select name="year" onchange="this.form.submit()" style="font-size: 0.8rem;">
                        <?php $__currentLoopData = $years; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $y): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($y); ?>" <?php echo e($selectedYear == $y ? 'selected' : ''); ?>><?php echo e($y); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </form>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view_buku_pembantu')): ?>
                <form action="<?php echo e(route('admin.buku-pembantu.sync-status')); ?>" method="POST" class="d-inline">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn btn-outline-info btn-lg shadow-sm" title="Sinkronisasi Status Piutang">
                        <i class="cil-sync me-1"></i> Sync Piutang
                    </button>
                </form>
            <?php endif; ?>

            <div class="dropdown">
                <button class="btn btn-primary btn-lg shadow-sm dropdown-toggle" type="button" data-coreui-toggle="dropdown"
                    aria-expanded="false">
                    <i class="cil-plus me-1"></i> Tambah Transaksi
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create_machine_log')): ?>
                        <li><a class="dropdown-item py-2" href="<?php echo e(route('admin.machine-logs.create')); ?>"><i
                                     class="cil-memory me-2 text-primary"></i> Tambah Log Mesin</a></li>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create_ritase')): ?>
                        <li><a class="dropdown-item py-2" href="<?php echo e(route('admin.ritase.create')); ?>"><i
                                     class="cil-truck me-2 text-primary"></i> Tambah Ritase</a></li>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create_hasil_pilahan')): ?>
                        <li><a class="dropdown-item py-2" href="<?php echo e(route('admin.hasil-pilahan.create')); ?>"><i
                                     class="cil-filter me-2 text-primary"></i> Catat Hasil Pilah</a></li>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create_pengangkutan_residu')): ?>
                        <li><a class="dropdown-item py-2" href="<?php echo e(route('admin.pengangkutan-residu.create')); ?>"><i
                                     class="cil-trash me-2 text-primary"></i> Catat Residu</a></li>
                    <?php endif; ?>
                    <div class="dropdown-divider"></div>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create_jurnal_kas')): ?>
                        <li><a class="dropdown-item py-2" href="<?php echo e(route('admin.jurnal-kas.create')); ?>"><i
                                     class="cil-money me-2 text-success"></i> Catat Jurnal Kas</a></li>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create_attendance')): ?>
                        <li><a class="dropdown-item py-2" href="<?php echo e(route('admin.hrd.attendance.create')); ?>"><i
                                     class="cil-calendar-check me-2 text-info"></i> Catat Kehadiran Karyawan</a></li>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create_penjualan')): ?>
                        <li><a class="dropdown-item py-2" href="<?php echo e(route('admin.penjualan.create')); ?>"><i
                                     class="cil-cart me-2 text-warning"></i> Catat Penjualan</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>

    
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card stat-success">
                <div class="card-body d-flex align-items-center">
                    <div class="stat-icon bg-success-light me-3">
                        <i class="cil-balance-scale"></i>
                    </div>
                    <div>
                        <div class="text-body-secondary text-uppercase fw-semibold small">Tonase Hari Ini</div>
                        <div class="fs-4 fw-bold"><?php echo e(number_format($tonaseHariIni, 2, ',', '.')); ?> kg</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card stat-success">
                <div class="card-body d-flex align-items-center">
                    <div class="stat-icon bg-success-light me-3">
                        <i class="cil-balance-scale"></i>
                    </div>
                    <div>
                        <div class="text-body-secondary text-uppercase fw-semibold small">Tonase <?php echo e($months[intval($selectedMonth)]); ?> <?php echo e($selectedYear); ?></div>
                        <div class="fs-4 fw-bold"><?php echo e(number_format($tonaseBulanIni, 2, ',', '.')); ?> kg</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card stat-primary">
                <div class="card-body d-flex align-items-center">
                    <div class="stat-icon bg-primary-light me-3">
                        <i class="cil-truck"></i>
                    </div>
                    <div>
                        <div class="text-body-secondary text-uppercase fw-semibold small">Ritase Hari Ini</div>
                        <div class="fs-4 fw-bold"><?php echo e($jumlahRitaseHariIni); ?> unit</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card stat-primary">
                <div class="card-body d-flex align-items-center">
                    <div class="stat-icon bg-primary-light me-3">
                        <i class="cil-truck"></i>
                    </div>
                    <div>
                        <div class="text-body-secondary text-uppercase fw-semibold small">Ritase <?php echo e($months[intval($selectedMonth)]); ?> <?php echo e($selectedYear); ?></div>
                        <div class="fs-4 fw-bold"><?php echo e($jumlahRitaseBulanIni); ?> unit</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card stat-success">
                <div class="card-body d-flex align-items-center">
                    <div class="stat-icon bg-success-light me-3">
                        <i class="cil-chart-pie"></i>
                    </div>
                    <div>
                        <div class="text-body-secondary text-uppercase fw-semibold small">Reduce Akumulatif</div>
                        <div class="fs-4 fw-bold"><?php echo e(number_format($kemampuanReduceKeseluruhan, 1, ',', '.')); ?>%</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card stat-success">
                <div class="card-body d-flex align-items-center">
                    <div class="stat-icon bg-success-light me-3">
                        <i class="cil-filter"></i>
                    </div>
                    <div>
                        <div class="text-body-secondary text-uppercase fw-semibold small">Prosentase Terpilah</div>
                        <div class="fs-4 fw-bold"><?php echo e(number_format($kemampuanReducePilahan, 1, ',', '.')); ?>%</div>
                    </div>
                </div>
            </div>
        </div>

        <?php if(!auth()->user()->hasRole('ritase_only')): ?>
            <div class="col-sm-6 col-xl-3">
                <div class="card stat-card stat-info">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-info-light me-3">
                            <i class="cil-money"></i>
                        </div>
                        <div>
                            <div class="text-body-secondary text-uppercase fw-semibold small">Pendapatan Tipping</div>
                            <div class="fs-4 fw-bold">Rp <?php echo e(number_format($pendapatanTipping, 0, ',', '.')); ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card stat-card stat-warning">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-warning-light me-3">
                            <i class="cil-chart"></i>
                        </div>
                        <div>
                            <div class="text-body-secondary text-uppercase fw-semibold small">Pendapatan <?php echo e($months[intval($selectedMonth)]); ?> <?php echo e($selectedYear); ?></div>
                            <div class="fs-4 fw-bold">Rp <?php echo e(number_format($penjualanBulanIni, 0, ',', '.')); ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card stat-card stat-danger">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-danger-light me-3">
                            <i class="cil-wallet"></i>
                        </div>
                        <div>
                            <div class="text-body-secondary text-uppercase fw-semibold small">Biaya <?php echo e($months[intval($selectedMonth)]); ?> <?php echo e($selectedYear); ?></div>
                            <div class="fs-4 fw-bold">Rp <?php echo e(number_format($biayaBulanIni, 0, ',', '.')); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    
    <div class="row g-4 mb-4">
        <div class="<?php echo e(auth()->user()->hasRole('ritase_only') ? 'col-xl-12' : 'col-xl-8'); ?>">
            <div class="card">
                <div class="card-header bg-white border-bottom-0 pt-4">
                    <h5 class="card-title mb-0 fw-semibold">Tonase Harian (<?php echo e($months[intval($selectedMonth)]); ?> <?php echo e($selectedYear); ?>)</h5>
                </div>
                <div class="card-body">
                    <canvas id="dailyTonnageChart"
                        height="<?php echo e(auth()->user()->hasRole('ritase_only') ? '80' : '100'); ?>"></canvas>
                </div>
            </div>
        </div>
        <?php if(!auth()->user()->hasRole('ritase_only')): ?>
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-header bg-white border-bottom-0 pt-4">
                        <h5 class="card-title mb-0 fw-semibold">Revenue vs Biaya</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="financialChart" height="200"></canvas>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
    <style>
        .period-selector-container {
            transition: all 0.3s ease;
        }
        .period-selector {
            display: flex;
            align-items: center;
            background: #ffffff;
            padding: 0.4rem 1.25rem;
            border-radius: 50px;
            border: 1px solid rgba(0, 0, 0, 0.08);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .period-selector:hover {
            background: #fff;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
            transform: translateY(-1px);
            border-color: var(--cui-primary);
        }
        .period-selector select {
            border: none;
            background: transparent;
            font-weight: 700;
            color: #334155;
            cursor: pointer;
            outline: none;
            padding: 0.25rem 0.5rem;
            font-size: 0.9rem;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
        }
        .period-selector .divider {
            width: 1px;
            height: 18px;
            background: rgba(0, 0, 0, 0.1);
            margin: 0 0.5rem;
        }
        
        [data-coreui-theme="dark"] .period-selector {
            background: #1e293b;
            border-color: rgba(255, 255, 255, 0.1);
        }
        [data-coreui-theme="dark"] .period-selector select {
            color: #f1f5f9;
        }
        [data-coreui-theme="dark"] .period-selector .divider {
            background: rgba(255, 255, 255, 0.1);
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Daily Tonnage Chart
            const dailyData = <?php echo json_encode($dailyTonnage, 15, 512) ?>;
            new Chart(document.getElementById('dailyTonnageChart'), {
                type: 'bar',
                data: {
                    labels: dailyData.map(d => d.date),
                    datasets: [{
                        label: 'Tonase (kg)',
                        data: dailyData.map(d => d.tonnage),
                        backgroundColor: 'rgba(59, 130, 246, 0.2)',
                        borderColor: 'rgba(59, 130, 246, 1)',
                        borderWidth: 2,
                        borderRadius: 6,
                        barPercentage: 0.6,
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(0,0,0,0.05)' },
                            ticks: { callback: v => v.toLocaleString('id-ID') + ' kg' }
                        },
                        x: { grid: { display: false } }
                    }
                }
            });

            <?php if(!auth()->user()->hasRole('ritase_only')): ?>
                // Financial Comparison Chart
                const financialData = <?php echo json_encode($monthlyFinancials, 15, 512) ?>;
                new Chart(document.getElementById('financialChart'), {
                    type: 'bar',
                    data: {
                        labels: financialData.map(d => d.month),
                        datasets: [
                            {
                                label: 'Pendapatan',
                                data: financialData.map(d => d.revenue),
                                backgroundColor: '#3b82f6',
                                borderRadius: 4,
                            },
                            {
                                label: 'Biaya',
                                data: financialData.map(d => d.expense),
                                backgroundColor: '#ef4444',
                                borderRadius: 4,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20 } },
                            tooltip: {
                                callbacks: {
                                    label: ctx => ctx.dataset.label + ': Rp ' + ctx.parsed.y.toLocaleString('id-ID')
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: v => 'Rp ' + (v >= 1000000 ? (v / 1000000) + 'jt' : v.toLocaleString('id-ID'))
                                }
                            }
                        }
                    }
                });
            <?php endif; ?>
    });
    </script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECT_HERD\tpst-app\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>