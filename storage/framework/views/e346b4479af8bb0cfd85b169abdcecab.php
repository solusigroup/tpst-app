<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'Admin'); ?> - <?php echo e(config('app.name', 'TPST App')); ?></title>

    <script>
        (function() {
            var theme = localStorage.getItem('theme');
            if (theme === 'auto' || !theme) {
                theme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            }
            document.documentElement.setAttribute('data-coreui-theme', theme);
            
            if (localStorage.getItem('rtl') === 'true') {
                document.documentElement.setAttribute('dir', 'rtl');
            }
        })();
    </script>

    
    <link href="https://cdn.jsdelivr.net/npm/@coreui/coreui@5.3.1/dist/css/coreui.min.css" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/@coreui/icons@3.0.1/css/all.min.css" rel="stylesheet">
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>

    <style>
        :root {
            --cui-body-font-family: 'Inter', sans-serif;
            --cui-primary: #3b7ddd;
            --cui-primary-rgb: 59, 125, 221;
        }
        body {
            font-family: 'Inter', sans-serif;
        }
        .sidebar {
            background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
        }
        .sidebar-brand {
            background: rgba(0,0,0,.15);
        }
        .sidebar-nav .nav-link.active {
            background: rgba(255,255,255,.1);
            border-left: 3px solid #3b82f6;
        }
        .sidebar-nav .nav-group-toggle::after {
            filter: brightness(0) invert(1);
        }
        .card {
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            transition: box-shadow 0.2s ease;
        }
        .card:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
        }
        .stat-card {
            border-left: 4px solid;
            border-radius: 0.75rem;
        }
        .stat-card.stat-primary { border-left-color: #3b82f6; }
        .stat-card.stat-success { border-left-color: #10b981; }
        .stat-card.stat-warning { border-left-color: #f59e0b; }
        .stat-card.stat-danger  { border-left-color: #ef4444; }
        .stat-card.stat-info    { border-left-color: #06b6d4; }
        .stat-card .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }
        .stat-card .stat-icon.bg-primary-light { background: rgba(59,130,246,.1); color: #3b82f6; }
        .stat-card .stat-icon.bg-success-light { background: rgba(16,185,129,.1); color: #10b981; }
        .stat-card .stat-icon.bg-warning-light { background: rgba(245,158,11,.1); color: #f59e0b; }
        .stat-card .stat-icon.bg-info-light    { background: rgba(6,182,212,.1); color: #06b6d4; }
        .table th {
            font-weight: 600;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #6b7280;
            border-bottom-width: 2px;
        }
        .table td {
            vertical-align: middle;
        }
        .badge-status {
            font-weight: 500;
            padding: 0.35em 0.65em;
            border-radius: 0.375rem;
        }
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
        }
        .page-header h1 {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
            color: #1e293b;
        }
        .breadcrumb {
            background: transparent;
            padding: 0;
            margin: 0;
            font-size: 0.875rem;
        }
        .btn {
            border-radius: 0.5rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        .btn:hover {
            transform: translateY(-1px);
        }
        .wrapper {
            min-height: 100vh;
        }
        .body {
            padding: 1.5rem 0;
        }
        /* Mobile bottom nav */
        .mobile-bottom-nav {
            display: none;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #fff;
            border-top: 1px solid #e2e8f0;
            padding: 0.5rem 0;
            z-index: 1030;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.05);
        }
        .mobile-bottom-nav .nav-link {
            display: flex;
            flex-direction: column;
            align-items: center;
            font-size: 0.7rem;
            color: #64748b;
            padding: 0.25rem;
        }
        .mobile-bottom-nav .nav-link.active {
            color: #3b82f6;
        }
        .mobile-bottom-nav .nav-link i {
            font-size: 1.25rem;
            margin-bottom: 0.15rem;
        }

        /* Sidebar & Wrapper Responsive Fixes */
        @media (min-width: 768px) {
            .wrapper {
                padding-left: var(--cui-sidebar-occupy-start, 256px);
                transition: padding-left 0.3s;
            }
            .sidebar-fixed {
                width: 256px;
            }
            #sidebar.hide + .wrapper {
                padding-left: 0;
            }
        }
        @media (max-width: 767.98px) {
            .sidebar-fixed {
                width: 256px;
                transform: translateX(-100%);
                transition: transform 0.3s;
            }
            #sidebar.show {
                transform: translateX(0);
            }
            .mobile-bottom-nav {
                display: flex;
                justify-content: space-around;
            }
            .body {
                padding-bottom: 5rem;
            }
        }

        .form-label {
            font-weight: 500;
            font-size: 0.875rem;
            color: #374151;
        }
        .form-control:focus, .form-select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 0.2rem rgba(59,130,246,.15);
        }

        /* Print Styles */
        @media print {
            .sidebar, .header, .mobile-bottom-nav, .btn, .breadcrumb {
                display: none !important;
            }
            .wrapper {
                padding-left: 0 !important;
                background: white !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .body {
                padding: 0 !important;
            }
            .card {
                border: none !important;
                box-shadow: none !important;
                margin-bottom: 0 !important;
            }
            .card-header, .card-footer {
                display: none !important;
            }
            .card-body {
                padding: 0 !important;
            }
            /* Reset colors to save ink and look readable */
            body {
                color: black !important;
                background: white !important;
            }
            * {
                box-shadow: none !important;
                text-shadow: none !important;
            }
            /* Optional A4 specific sizing if wanted */
            @page {
                size: A4;
                margin: 1.5cm;
            }
            /* Prevent breaking rows */
            tr, td, th {
                page-break-inside: avoid;
            }
        }
    </style>
    <style>
        /* Dark theme contrast fixes */
        [data-coreui-theme="dark"] body {
            color: #e6eef8;
            background-color: #0b1220;
        }
        [data-coreui-theme="dark"] .sidebar {
            background: linear-gradient(180deg, #0b1220 0%, #07121a 100%);
        }
        [data-coreui-theme="dark"] .bg-white {
            background: transparent !important;
        }
        [data-coreui-theme="dark"] .card,
        [data-coreui-theme="dark"] .card .card-body,
        [data-coreui-theme="dark"] .card .card-header,
        [data-coreui-theme="dark"] .card .card-footer {
            background-color: #0f1724;
            color: #e6eef8;
            border-color: rgba(255,255,255,0.03);
        }
        [data-coreui-theme="dark"] .breadcrumb,
        [data-coreui-theme="dark"] .breadcrumb a,
        [data-coreui-theme="dark"] .breadcrumb .active {
            color: #9fb3d7;
        }
        [data-coreui-theme="dark"] .form-label,
        [data-coreui-theme="dark"] .form-control,
        [data-coreui-theme="dark"] .form-select,
        [data-coreui-theme="dark"] .table th,
        [data-coreui-theme="dark"] .table td,
        [data-coreui-theme="dark"] .page-header h1,
        [data-coreui-theme="dark"] .nav-link,
        [data-coreui-theme="dark"] .stat-card {
            color: #e6eef8 !important;
        }
        [data-coreui-theme="dark"] .form-control,
        [data-coreui-theme="dark"] .form-select {
            background: #07121a;
            border-color: rgba(255,255,255,0.06);
            color: #e6eef8;
        }
        [data-coreui-theme="dark"] .table thead th {
            color: #d2e7ff;
            background: rgba(255,255,255,0.02);
        }
        [data-coreui-theme="dark"] .badge {
            color: #fff;
        }
        [data-coreui-theme="dark"] .btn-outline-secondary {
            color: #e6eef8;
            border-color: rgba(255,255,255,0.06);
        }
        /* Broad high-specificity overrides to ensure readable text in dark mode */
        [data-coreui-theme="dark"] body,
        [data-coreui-theme="dark"] .wrapper,
        [data-coreui-theme="dark"] .container-fluid,
        [data-coreui-theme="dark"] .card,
        [data-coreui-theme="dark"] .card *,
        [data-coreui-theme="dark"] .page-header h1,
        [data-coreui-theme="dark"] h1, [data-coreui-theme="dark"] h2, [data-coreui-theme="dark"] h3,
        [data-coreui-theme="dark"] h4, [data-coreui-theme="dark"] h5, [data-coreui-theme="dark"] h6,
        [data-coreui-theme="dark"] p, [data-coreui-theme="dark"] label, [data-coreui-theme="dark"] a,
        [data-coreui-theme="dark"] .nav-link, [data-coreui-theme="dark"] .dropdown-item,
        [data-coreui-theme="dark"] .breadcrumb, [data-coreui-theme="dark"] .breadcrumb a,
        [data-coreui-theme="dark"] .stat-card, [data-coreui-theme="dark"] .table th,
        [data-coreui-theme="dark"] .table td, [data-coreui-theme="dark"] .form-label,
        [data-coreui-theme="dark"] .list-group-item,
        [data-coreui-theme="dark"] .mobile-bottom-nav .nav-link,
        [data-coreui-theme="dark"] .badge,
        [data-coreui-theme="dark"] .text-body-secondary,
        [data-coreui-theme="dark"] .text-muted {
            color: #e6eef8 !important;
        }

        /* Slightly muted secondary text */
        [data-coreui-theme="dark"] .text-body-secondary,
        [data-coreui-theme="dark"] .text-muted {
            color: rgba(230,238,248,0.75) !important;
        }

        /* Form controls and placeholders */
        [data-coreui-theme="dark"] .form-control,
        [data-coreui-theme="dark"] .form-select,
        [data-coreui-theme="dark"] textarea {
            background: #07121a;
            border-color: rgba(255,255,255,0.06);
            color: #e6eef8 !important;
        }
        [data-coreui-theme="dark"] ::placeholder { color: rgba(230,238,248,0.6) !important; }

        /* Tables */
        [data-coreui-theme="dark"] .table {
            color: #e6eef8 !important;
        }

        /* Ensure buttons keep readable text */
        [data-coreui-theme="dark"] .btn,
        [data-coreui-theme="dark"] .btn * {
            color: inherit !important;
        }

        /* Links */
        [data-coreui-theme="dark"] a { color: #a9d1ff !important; }

        /* Sidebar items */
        [data-coreui-theme="dark"] .sidebar .nav-link,
        [data-coreui-theme="dark"] .sidebar .nav-title {
            color: #dbeeff !important;
        }
    </style>
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body>
    <div class="sidebar sidebar-dark sidebar-fixed" id="sidebar">
        <div class="sidebar-brand d-none d-md-flex">
            <span class="sidebar-brand-full fw-bold fs-5">
                <i class="cil-recycle me-2"></i>TPST App
            </span>
            <span class="sidebar-brand-narrow fw-bold">TP</span>
        </div>
        <ul class="sidebar-nav" data-coreui="navigation" data-simplebar>
            
            <?php if(!(auth()->check() && (auth()->user()->salary_type === 'bulanan' || auth()->user()->hasRole('karyawan')))): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo e(request()->routeIs('admin.dashboard') ? 'active' : ''); ?>" href="<?php echo e(route('admin.dashboard')); ?>">
                    <i class="nav-icon cil-speedometer"></i> Dashboard
                </a>
            </li>
            <?php else: ?>
            <li class="nav-item">
                <a class="nav-link <?php echo e(request()->is('admin/hrd/attendance*') ? 'active' : ''); ?>" href="<?php echo e(url('admin/hrd/attendance') . '?user_id=' . auth()->id()); ?>">
                    <i class="nav-icon cil-calendar-check"></i> Rekap Kehadiran Saya
                </a>
            </li>
            <?php endif; ?>

            
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['view_ritase', 'view_klien', 'view_armada', 'view_hasil_pilahan', 'view_penjualan'])): ?>
            <li class="nav-title">Operasional</li>
            <?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view_ritase')): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo e(request()->routeIs('admin.ritase.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.ritase.index')); ?>">
                    <i class="nav-icon cil-truck"></i> Ritase
                </a>
            </li>
            <?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view_klien')): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo e(request()->routeIs('admin.klien.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.klien.index')); ?>">
                    <i class="nav-icon cil-people"></i> Klien
                </a>
            </li>
            <?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view_armada')): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo e(request()->routeIs('admin.armada.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.armada.index')); ?>">
                    <i class="nav-icon cil-car-alt"></i> Armada
                </a>
            </li>
            <?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view_hasil_pilahan')): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo e(request()->routeIs('admin.hasil-pilahan.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.hasil-pilahan.index')); ?>">
                    <i class="nav-icon cil-filter"></i> Hasil Pilahan
                </a>
            </li>
            <?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view_penjualan')): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo e(request()->routeIs('admin.penjualan.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.penjualan.index')); ?>">
                    <i class="nav-icon cil-cart"></i> Penjualan
                </a>
            </li>
            <?php endif; ?>

            
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['view_coa', 'view_jurnal', 'view_jurnal_kas', 'view_invoice'])): ?>
            <li class="nav-title">Keuangan</li>
            <?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view_coa')): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo e(request()->routeIs('admin.coa.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.coa.index')); ?>">
                    <i class="nav-icon cil-book"></i> Chart of Account
                </a>
            </li>
            <?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view_jurnal')): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo e(request()->routeIs('admin.jurnal.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.jurnal.index')); ?>">
                    <i class="nav-icon cil-file"></i> Jurnal
                </a>
            </li>
            <?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view_jurnal_kas')): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo e(request()->routeIs('admin.jurnal-kas.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.jurnal-kas.index')); ?>">
                    <i class="nav-icon cil-money"></i> Jurnal Kas
                </a>
            </li>
            <?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view_invoice')): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo e(request()->routeIs('admin.invoice.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.invoice.index')); ?>">
                    <i class="nav-icon cil-description"></i> Invoice
                </a>
            </li>
            <?php endif; ?>

            
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['view_laporan_keuangan', 'view_laporan_operasional'])): ?>
            <li class="nav-title">Laporan</li>
            <?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view_laporan_keuangan')): ?>
            <li class="nav-group <?php echo e(request()->routeIs('admin.laporan.*') ? 'show' : ''); ?>">
                <a class="nav-link nav-group-toggle" href="#">
                    <i class="nav-icon cil-chart"></i> Laporan Keuangan
                </a>
                <ul class="nav-group-items compact">
                    <li class="nav-item">
                        <a class="nav-link <?php echo e(request()->routeIs('admin.laporan.laba-rugi') ? 'active' : ''); ?>" href="<?php echo e(route('admin.laporan.laba-rugi')); ?>"><span class="nav-icon"><span class="nav-icon-bullet"></span></span> Laba Rugi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo e(request()->routeIs('admin.laporan.neraca-saldo') ? 'active' : ''); ?>" href="<?php echo e(route('admin.laporan.neraca-saldo')); ?>"><span class="nav-icon"><span class="nav-icon-bullet"></span></span> Neraca Saldo</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo e(request()->routeIs('admin.laporan.posisi-keuangan') ? 'active' : ''); ?>" href="<?php echo e(route('admin.laporan.posisi-keuangan')); ?>"><span class="nav-icon"><span class="nav-icon-bullet"></span></span> Posisi Keuangan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo e(request()->routeIs('admin.laporan.arus-kas') ? 'active' : ''); ?>" href="<?php echo e(route('admin.laporan.arus-kas')); ?>"><span class="nav-icon"><span class="nav-icon-bullet"></span></span> Arus Kas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo e(request()->routeIs('admin.laporan.perubahan-ekuitas') ? 'active' : ''); ?>" href="<?php echo e(route('admin.laporan.perubahan-ekuitas')); ?>"><span class="nav-icon"><span class="nav-icon-bullet"></span></span> Perubahan Ekuitas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo e(request()->routeIs('admin.laporan.buku-besar') ? 'active' : ''); ?>" href="<?php echo e(route('admin.laporan.buku-besar')); ?>"><span class="nav-icon"><span class="nav-icon-bullet"></span></span> Buku Besar</a>
                    </li>
                </ul>
            </li>
            <?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view_laporan_operasional')): ?>
            <li class="nav-group <?php echo e(request()->routeIs('admin.laporan-operasional.*') ? 'show' : ''); ?>">
                <a class="nav-link nav-group-toggle" href="#">
                    <i class="nav-icon cil-clipboard"></i> Laporan Operasional
                </a>
                <ul class="nav-group-items compact">
                    <li class="nav-item">
                        <a class="nav-link <?php echo e(request()->routeIs('admin.laporan-operasional.ritase') ? 'active' : ''); ?>" href="<?php echo e(route('admin.laporan-operasional.ritase')); ?>"><span class="nav-icon"><span class="nav-icon-bullet"></span></span> Laporan Ritase</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo e(request()->routeIs('admin.laporan-operasional.penjualan') ? 'active' : ''); ?>" href="<?php echo e(route('admin.laporan-operasional.penjualan')); ?>"><span class="nav-icon"><span class="nav-icon-bullet"></span></span> Laporan Penjualan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo e(request()->routeIs('admin.laporan-operasional.hasil-pilahan') ? 'active' : ''); ?>" href="<?php echo e(route('admin.laporan-operasional.hasil-pilahan')); ?>"><span class="nav-icon"><span class="nav-icon-bullet"></span></span> Laporan Hasil Pilahan</a>
                    </li>
                </ul>
            </li>
            <?php endif; ?>

            
            <?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', 'manajemen|hrd|keuangan|super_admin')): ?>
            <li class="nav-title">S D M</li>
            
            <?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', 'manajemen|hrd|super_admin')): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo e(request()->routeIs('admin.hrd.employee.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.hrd.employee.index')); ?>">
                    <i class="nav-icon cil-people"></i> Karyawan
                </a>
            </li>
            <?php endif; ?>

            
            <?php if((auth()->user() && auth()->user()->salary_type === 'bulanan') || (auth()->user() && auth()->user()->hasAnyRole(['manajemen','hrd','super_admin']))): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo e(request()->routeIs('admin.hrd.attendance.*') || request()->routeIs('attendance.check-in') ? 'active' : ''); ?>" href="<?php echo e(auth()->user() && auth()->user()->salary_type === 'bulanan' ? route('attendance.check-in') : route('admin.hrd.attendance.index')); ?>">
                    <i class="nav-icon cil-calendar-check"></i> Kehadiran
                </a>
            </li>
            <?php endif; ?>

            <?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', 'manajemen|hrd|super_admin')): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo e(request()->routeIs('admin.hrd.output.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.hrd.output.index')); ?>">
                    <i class="nav-icon cil-chart-pie"></i> Output Pemilah
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo e(request()->routeIs('admin.hrd.waste-category.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.hrd.waste-category.index')); ?>">
                    <i class="nav-icon cil-tags"></i> Kategori Sampah
                </a>
            </li>
            <?php endif; ?>

            <?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', 'manajemen|hrd|keuangan|super_admin')): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo e(request()->routeIs('admin.hrd.wage-rate.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.hrd.wage-rate.index')); ?>">
                    <i class="nav-icon cil-dollar"></i> Tarif Upah
                </a>
            </li>
            <?php endif; ?>

            <?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', 'manajemen|keuangan|super_admin')): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo e(request()->routeIs('admin.hrd.wage-calculation.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.hrd.wage-calculation.index')); ?>">
                    <i class="nav-icon cil-calculator"></i> Perhitungan Upah
                </a>
            </li>
            <?php endif; ?>
            <?php endif; ?>

            
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['view_users', 'view_company_settings', 'view_activity_log'])): ?>
            <li class="nav-title">Administrasi</li>
            <?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view_users')): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo e(request()->routeIs('admin.roles.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.roles.index')); ?>">
                    <i class="nav-icon cil-lock-locked"></i> Manajemen Role
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo e(request()->routeIs('admin.users.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.users.index')); ?>">
                    <i class="nav-icon cil-user"></i> Users
                </a>
            </li>
            <?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view_company_settings')): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo e(request()->routeIs('admin.company-settings') ? 'active' : ''); ?>" href="<?php echo e(route('admin.company-settings')); ?>">
                    <i class="nav-icon cil-building"></i> Pengaturan Perusahaan
                </a>
            </li>
            <?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view_activity_log')): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo e(request()->routeIs('admin.activities.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.activities.index')); ?>">
                    <i class="nav-icon cil-history"></i> Log Aktivitas
                </a>
            </li>
            <?php endif; ?>

            
            <li class="nav-title">Bantuan</li>
            <li class="nav-item">
                <a class="nav-link text-info" href="/panduan.html" target="_blank">
                    <i class="nav-icon cil-book text-info"></i> Panduan Aplikasi
                </a>
            </li>

            <?php if(auth()->user() && auth()->user()->is_super_admin): ?>
            <!-- CENTRAL PANEL -->
            <li class="nav-title text-danger">CENTRAL PANEL (SUPERADMIN)</li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo e(route('central.dashboard')); ?>">
                    <i class="nav-icon cil-speedometer text-danger"></i> Central Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo e(route('central.tenants.index')); ?>">
                    <i class="nav-icon cil-building text-danger"></i> Manajemen Tenant
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo e(route('central.users.index')); ?>">
                    <i class="nav-icon cil-people text-danger"></i> Semua User (Central)
                </a>
            </li>
            <?php endif; ?>
        </ul>
        <button class="sidebar-toggler" type="button" data-coreui-toggle="unfoldable"></button>
    </div>

    <div class="wrapper d-flex flex-column min-vh-100">
        
        <header class="header header-sticky p-0 mb-4 shadow-sm bg-body">
            <div class="container-fluid px-4">
                <button class="header-toggler" type="button"
                    onclick="document.getElementById('sidebar').classList.toggle('hide'); document.getElementById('sidebar').classList.toggle('show');"
                    style="margin-inline-start: -14px;">
                    <i class="cil-menu" style="font-size: 1.25rem;"></i>
                </button>

                <ul class="header-nav ms-auto flex-row align-items-center">
                    
                    <li class="nav-item">
                        <button class="nav-link" type="button" onclick="toggleRTL()" title="Toggle RTL">
                            <i class="cil-swap-horizontal fs-5"></i>
                        </button>
                    </li>
                    
                    
                    <li class="nav-item dropdown">
                        <button class="nav-link" type="button" data-coreui-toggle="dropdown" aria-expanded="false" title="Toggle Theme">
                            <i class="cil-contrast fs-5" id="theme-icon"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><button class="dropdown-item d-flex align-items-center" type="button" onclick="setTheme('light')"><i class="cil-sun me-2"></i> Light</button></li>
                            <li><button class="dropdown-item d-flex align-items-center" type="button" onclick="setTheme('dark')"><i class="cil-moon me-2"></i> Dark</button></li>
                            <li><button class="dropdown-item d-flex align-items-center" type="button" onclick="setTheme('auto')"><i class="cil-devices me-2"></i> Auto</button></li>
                        </ul>
                    </li>

                    <li class="nav-item dropdown ms-2">
                        <a class="nav-link py-0 pe-0" data-coreui-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                            <div class="avatar avatar-md bg-primary text-white d-flex align-items-center justify-content-center" style="width:36px;height:36px;border-radius:50%;font-weight:600;font-size:0.875rem;">
                                <?php echo e(strtoupper(substr(auth()->user()->name ?? 'U', 0, 1))); ?>

                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end pt-0">
                            <div class="dropdown-header bg-body-tertiary text-body-secondary fw-semibold rounded-top mb-2 py-3 px-4">
                                <div class="fw-semibold"><?php echo e(auth()->user()->name ?? 'User'); ?></div>
                                <small class="text-body-secondary"><?php echo e(auth()->user()->email ?? ''); ?></small>
                            </div>
                            <a class="dropdown-item" href="<?php echo e(route('admin.company-settings')); ?>">
                                <i class="cil-settings me-2"></i> Pengaturan
                            </a>
                            <div class="dropdown-divider"></div>
                            <form method="POST" action="<?php echo e(route('logout')); ?>">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="cil-account-logout me-2"></i> Logout
                                </button>
                            </form>
                        </div>
                    </li>
                </ul>
            </div>
        </header>

        
        <div class="body flex-grow-1">
            <div class="container-fluid px-4">
                
                <?php if(session('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="cil-check-circle me-2"></i><?php echo e(session('success')); ?>

                        <button type="button" class="btn-close" data-coreui-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                <?php if(session('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="cil-x-circle me-2"></i><?php echo e(session('error')); ?>

                        <button type="button" class="btn-close" data-coreui-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                <?php if(session('warning')): ?>
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <i class="cil-warning me-2"></i><?php echo e(session('warning')); ?>

                        <button type="button" class="btn-close" data-coreui-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                <?php if($errors->any()): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="cil-x-circle me-2"></i>
                        <strong>Terjadi kesalahan:</strong>
                        <ul class="mb-0 mt-1">
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><?php echo e($error); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                        <button type="button" class="btn-close" data-coreui-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php echo $__env->yieldContent('content'); ?>
            </div>
        </div>

        
        <footer class="footer px-4 mt-auto">
            <div class="text-body-secondary small">
                &copy; <?php echo e(date('Y')); ?> TPST App. All rights reserved.
            </div>
        </footer>
    </div>

    
    <div class="mobile-bottom-nav">
        <a href="<?php echo e(route('admin.dashboard')); ?>" class="nav-link <?php echo e(request()->routeIs('admin.dashboard') ? 'active' : ''); ?>">
            <i class="cil-speedometer"></i>
            <span>Home</span>
        </a>
        <a href="<?php echo e(route('admin.ritase.index')); ?>" class="nav-link <?php echo e(request()->routeIs('admin.ritase.*') ? 'active' : ''); ?>">
            <i class="cil-truck"></i>
            <span>Ritase</span>
        </a>
        <a href="<?php echo e(route('admin.penjualan.index')); ?>" class="nav-link <?php echo e(request()->routeIs('admin.penjualan.*') ? 'active' : ''); ?>">
            <i class="cil-cart"></i>
            <span>Jual</span>
        </a>
        <a href="<?php echo e(route('admin.jurnal.index')); ?>" class="nav-link <?php echo e(request()->routeIs('admin.jurnal.*') ? 'active' : ''); ?>">
            <i class="cil-file"></i>
            <span>Jurnal</span>
        </a>
        <a href="#" class="nav-link" onclick="document.getElementById('sidebar').classList.toggle('show')">
            <i class="cil-menu"></i>
            <span>Menu</span>
        </a>
    </div>

    
    <div id="sidebar-overlay" class="d-none" style="position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1031;"></div>

    
    <script src="https://cdn.jsdelivr.net/npm/@coreui/coreui@5.3.1/dist/js/coreui.bundle.min.js"></script>
    <script>
        function setTheme(theme) {
            if (theme === 'auto') {
                const autoTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
                document.documentElement.setAttribute('data-coreui-theme', autoTheme);
                localStorage.setItem('theme', 'auto');
            } else {
                document.documentElement.setAttribute('data-coreui-theme', theme);
                localStorage.setItem('theme', theme);
            }
        }
        function toggleRTL() {
            const isRTL = document.documentElement.getAttribute('dir') === 'rtl';
            if (isRTL) {
                document.documentElement.removeAttribute('dir');
                localStorage.setItem('rtl', 'false');
            } else {
                document.documentElement.setAttribute('dir', 'rtl');
                localStorage.setItem('rtl', 'true');
            }
        }

        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
            if (localStorage.getItem('theme') === 'auto' || !localStorage.getItem('theme')) {
                setTheme('auto');
            }
        });

        document.querySelector('.header-toggler').addEventListener('click', function() {
            if (window.innerWidth < 768) {
                document.getElementById('sidebar-overlay').classList.toggle('d-none');
            }
        });
        document.querySelector('.mobile-bottom-nav .nav-link:last-child').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('sidebar-overlay').classList.remove('d-none');
        });
        document.getElementById('sidebar-overlay').addEventListener('click', function() {
            document.getElementById('sidebar').classList.remove('show');
            this.classList.add('d-none');
        });
    </script>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH D:\PROJECT_HERD\tpst-app\resources\views/layouts/admin.blade.php ENDPATH**/ ?>