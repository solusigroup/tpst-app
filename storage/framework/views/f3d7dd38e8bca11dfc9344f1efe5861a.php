<?php $__env->startSection('title', 'Profil Karyawan - ' . $employee->name); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Profil Karyawan</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?php echo e(route('admin.hrd.employee.index')); ?>">Manajemen Karyawan</a></li>
                <li class="breadcrumb-item active">Profil</li>
            </ol>
        </nav>
    </div>
    <div class="btn-group">
        <a href="<?php echo e(route('admin.hrd.employee.index')); ?>" class="btn btn-outline-secondary">
            <i class="cil-arrow-left"></i> Kembali
        </a>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update_employee')): ?>
            <a href="<?php echo e(route('admin.hrd.employee.edit', $employee->id)); ?>" class="btn btn-warning">
                <i class="cil-pencil"></i> Edit Profil
            </a>
        <?php endif; ?>
    </div>
</div>

<div class="row">
    <!-- Profile Sidebar / Focus Photo -->
    <div class="col-md-4">
        <div class="card shadow-sm mb-4 border-0">
            <div class="card-body text-center p-4">
                <div class="mb-4">
                    <?php if($employee->photo): ?>
                        <img src="<?php echo e(Storage::url($employee->photo)); ?>" alt="Foto <?php echo e($employee->name); ?>" 
                             class="img-fluid rounded-circle shadow-lg border border-4 border-white" 
                             style="width: 200px; height: 200px; object-fit: cover;">
                    <?php else: ?>
                        <div class="bg-gradient-secondary text-white d-flex align-items-center justify-content-center mx-auto rounded-circle shadow" 
                             style="width: 200px; height: 200px; font-size: 80px;">
                            <?php echo e(substr($employee->name, 0, 1)); ?>

                        </div>
                    <?php endif; ?>
                </div>
                <h4 class="mb-1 font-weight-bold"><?php echo e($employee->name); ?></h4>
                <p class="text-primary mb-3"><?php echo e($employee->position ?: 'Staf'); ?></p>
                <div class="d-flex justify-content-center gap-2 mb-3">
                    <span class="badge bg-<?php echo e($employee->salary_type == 'bulanan' ? 'info' : ($employee->salary_type == 'harian' ? 'success' : 'secondary')); ?> py-2 px-3">
                        Skema: <?php echo e(ucfirst($employee->salary_type ?: 'Borongan')); ?>

                    </span>
                    <span class="badge bg-<?php echo e($employee->bpjs_status == 'Aktif' ? 'success' : 'danger'); ?> py-2 px-3">
                        BPJS: <?php echo e($employee->bpjs_status); ?>

                    </span>
                </div>
                <hr>
                <div class="text-start">
                    <small class="text-muted d-block mb-1">Username / ID</small>
                    <p class="font-weight-medium"><?php echo e($employee->username); ?></p>
                    <small class="text-muted d-block mb-1">Email</small>
                    <p class="font-weight-medium"><?php echo e($employee->email); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Details Column -->
    <div class="col-md-8">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white font-weight-bold py-3">
                <i class="cil-contact me-1 color-primary"></i> Informasi Pribadi & Kepegawaian
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="small text-muted mb-1">Nomor KTP</label>
                        <p class="mb-0 h6"><?php echo e($employee->ktp_number ?: '-'); ?></p>
                    </div>
                    <div class="col-md-6">
                        <label class="small text-muted mb-1">Jenis Kelamin</label>
                        <p class="mb-0 h6"><?php echo e($employee->gender ?: '-'); ?></p>
                    </div>
                    <div class="col-md-12">
                        <label class="small text-muted mb-1">Alamat Domisili</label>
                        <p class="mb-0 h6"><?php echo e($employee->address ?: '-'); ?></p>
                    </div>
                    <div class="col-md-6 border-top pt-3">
                        <label class="small text-muted mb-1">Tanggal Mulai Kerja</label>
                        <p class="mb-0 h6 text-success"><?php echo e($employee->joined_at ? \Carbon\Carbon::parse($employee->joined_at)->format('d F Y') : '-'); ?></p>
                    </div>
                    <div class="col-md-6 border-top pt-3">
                        <label class="small text-muted mb-1">Tanggal Akhir Kerja</label>
                        <p class="mb-0 h6 text-danger"><?php echo e($employee->ended_at ? \Carbon\Carbon::parse($employee->ended_at)->format('d F Y') : '-'); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white font-weight-bold py-3">
                        <i class="cil-money me-1 color-success"></i> Kompensasi
                    </div>
                    <div class="card-body">
                        <?php if($employee->salary_type == 'bulanan'): ?>
                            <label class="small text-muted mb-1">Gaji Bulanan</label>
                            <h4 class="text-success mb-0 font-weight-bold">Rp <?php echo e(number_format($employee->monthly_salary, 0, ',', '.')); ?></h4>
                        <?php elseif($employee->salary_type == 'harian'): ?>
                            <label class="small text-muted mb-1">Upah Harian</label>
                            <h4 class="text-success mb-0 font-weight-bold">Rp <?php echo e(number_format($employee->daily_wage, 0, ',', '.')); ?> <small class="text-muted">/hari</small></h4>
                            <label class="small text-muted mt-3 mb-1">Frekuensi Pembayaran</label>
                            <p class="mb-0 h6"><?php echo e($employee->payment_frequency ?: 'Mingguan'); ?></p>
                        <?php else: ?>
                            <label class="small text-muted mb-1">Tipe Upah</label>
                            <h4 class="text-secondary mb-0">Borongan</h4>
                            <p class="small text-muted mt-2">Dihitung berdasarkan jumlah output pilahan sampah.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white font-weight-bold py-3">
                        <i class="cil-shield-alt me-1 color-info"></i> BPJS & Jaminan
                    </div>
                    <div class="card-body">
                        <label class="small text-muted mb-1">Status Kepesertaan</label>
                        <div class="mb-3">
                            <?php if($employee->bpjs_status == 'Aktif'): ?>
                                <span class="badge bg-success py-1 px-3">Terdaftar & Aktif</span>
                            <?php else: ?>
                                <span class="badge bg-danger py-1 px-3">Tidak Aktif / Belum Terdaftar</span>
                            <?php endif; ?>
                        </div>
                        <label class="small text-muted mb-1">Nomor BPJS</label>
                        <p class="mb-0 h5 font-weight-bold letter-spacing-1"><?php echo e($employee->bpjs_number ?: '-'); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-gradient-secondary {
        background: linear-gradient(135deg, #8e9eab 0%, #eef2f3 100%);
    }
    .letter-spacing-1 {
        letter-spacing: 1px;
    }
    .font-weight-medium {
        font-weight: 500;
    }
    .color-primary { color: #321fdb; }
    .color-success { color: #2eb85c; }
    .color-info { color: #39f; }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\PROJECT_HERD\tpst-app\resources\views\admin\hrd\employee\show.blade.php ENDPATH**/ ?>