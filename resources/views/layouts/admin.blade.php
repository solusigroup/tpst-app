<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - {{ config('app.name', 'TPST App') }}</title>

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

    {{-- CoreUI CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/@coreui/coreui@5.3.1/dist/css/coreui.min.css" rel="stylesheet">
    {{-- CoreUI Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/@coreui/icons@3.0.1/css/all.min.css" rel="stylesheet">
    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    {{-- Chart.js --}}
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
    @stack('styles')
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
            {{-- Dashboard --}}
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                    <i class="nav-icon cil-speedometer"></i> Dashboard
                </a>
            </li>

            {{-- Operasional --}}
            @canany(['view_ritase', 'view_klien', 'view_armada', 'view_hasil_pilahan', 'view_penjualan'])
            <li class="nav-title">Operasional</li>
            @endcan
            @can('view_ritase')
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.ritase.*') ? 'active' : '' }}" href="{{ route('admin.ritase.index') }}">
                    <i class="nav-icon cil-truck"></i> Ritase
                </a>
            </li>
            @endcan
            @can('view_klien')
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.klien.*') ? 'active' : '' }}" href="{{ route('admin.klien.index') }}">
                    <i class="nav-icon cil-people"></i> Klien
                </a>
            </li>
            @endcan
            @can('view_armada')
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.armada.*') ? 'active' : '' }}" href="{{ route('admin.armada.index') }}">
                    <i class="nav-icon cil-car-alt"></i> Armada
                </a>
            </li>
            @endcan
            @can('view_hasil_pilahan')
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.hasil-pilahan.*') ? 'active' : '' }}" href="{{ route('admin.hasil-pilahan.index') }}">
                    <i class="nav-icon cil-filter"></i> Hasil Pilahan
                </a>
            </li>
            @endcan
            @can('view_penjualan')
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.penjualan.*') ? 'active' : '' }}" href="{{ route('admin.penjualan.index') }}">
                    <i class="nav-icon cil-cart"></i> Penjualan
                </a>
            </li>
            @endcan

            {{-- Keuangan --}}
            @canany(['view_coa', 'view_jurnal', 'view_jurnal_kas', 'view_invoice'])
            <li class="nav-title">Keuangan</li>
            @endcan
            @can('view_coa')
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.coa.*') ? 'active' : '' }}" href="{{ route('admin.coa.index') }}">
                    <i class="nav-icon cil-book"></i> Chart of Account
                </a>
            </li>
            @endcan
            @can('view_jurnal')
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.jurnal.*') ? 'active' : '' }}" href="{{ route('admin.jurnal.index') }}">
                    <i class="nav-icon cil-file"></i> Jurnal
                </a>
            </li>
            @endcan
            @can('view_jurnal_kas')
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.jurnal-kas.*') ? 'active' : '' }}" href="{{ route('admin.jurnal-kas.index') }}">
                    <i class="nav-icon cil-money"></i> Jurnal Kas
                </a>
            </li>
            @endcan
            @can('view_invoice')
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.invoice.*') ? 'active' : '' }}" href="{{ route('admin.invoice.index') }}">
                    <i class="nav-icon cil-description"></i> Invoice
                </a>
            </li>
            @endcan

            {{-- Laporan --}}
            @canany(['view_laporan_keuangan', 'view_laporan_operasional'])
            <li class="nav-title">Laporan</li>
            @endcan
            @can('view_laporan_keuangan')
            <li class="nav-group {{ request()->routeIs('admin.laporan.*') ? 'show' : '' }}">
                <a class="nav-link nav-group-toggle" href="#">
                    <i class="nav-icon cil-chart"></i> Laporan Keuangan
                </a>
                <ul class="nav-group-items compact">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.laporan.laba-rugi') ? 'active' : '' }}" href="{{ route('admin.laporan.laba-rugi') }}"><span class="nav-icon"><span class="nav-icon-bullet"></span></span> Laba Rugi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.laporan.neraca-saldo') ? 'active' : '' }}" href="{{ route('admin.laporan.neraca-saldo') }}"><span class="nav-icon"><span class="nav-icon-bullet"></span></span> Neraca Saldo</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.laporan.posisi-keuangan') ? 'active' : '' }}" href="{{ route('admin.laporan.posisi-keuangan') }}"><span class="nav-icon"><span class="nav-icon-bullet"></span></span> Posisi Keuangan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.laporan.arus-kas') ? 'active' : '' }}" href="{{ route('admin.laporan.arus-kas') }}"><span class="nav-icon"><span class="nav-icon-bullet"></span></span> Arus Kas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.laporan.perubahan-ekuitas') ? 'active' : '' }}" href="{{ route('admin.laporan.perubahan-ekuitas') }}"><span class="nav-icon"><span class="nav-icon-bullet"></span></span> Perubahan Ekuitas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.laporan.buku-besar') ? 'active' : '' }}" href="{{ route('admin.laporan.buku-besar') }}"><span class="nav-icon"><span class="nav-icon-bullet"></span></span> Buku Besar</a>
                    </li>
                </ul>
            </li>
            @endcan
            @can('view_laporan_operasional')
            <li class="nav-group {{ request()->routeIs('admin.laporan-operasional.*') ? 'show' : '' }}">
                <a class="nav-link nav-group-toggle" href="#">
                    <i class="nav-icon cil-clipboard"></i> Laporan Operasional
                </a>
                <ul class="nav-group-items compact">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.laporan-operasional.ritase') ? 'active' : '' }}" href="{{ route('admin.laporan-operasional.ritase') }}"><span class="nav-icon"><span class="nav-icon-bullet"></span></span> Laporan Ritase</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.laporan-operasional.penjualan') ? 'active' : '' }}" href="{{ route('admin.laporan-operasional.penjualan') }}"><span class="nav-icon"><span class="nav-icon-bullet"></span></span> Laporan Penjualan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.laporan-operasional.hasil-pilahan') ? 'active' : '' }}" href="{{ route('admin.laporan-operasional.hasil-pilahan') }}"><span class="nav-icon"><span class="nav-icon-bullet"></span></span> Laporan Hasil Pilahan</a>
                    </li>
                </ul>
            </li>
            @endcan

            {{-- HRD (Sumber Daya Manusia) --}}
            @hasanyrole('manajemen|hrd|keuangan|super_admin')
            <li class="nav-title">S D M</li>
            
            @hasanyrole('manajemen|hrd|super_admin')
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.hrd.employee.*') ? 'active' : '' }}" href="{{ route('admin.hrd.employee.index') }}">
                    <i class="nav-icon cil-people"></i> Karyawan
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.hrd.attendance.*') ? 'active' : '' }}" href="{{ route('admin.hrd.attendance.index') }}">
                    <i class="nav-icon cil-calendar-check"></i> Kehadiran
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.hrd.output.*') ? 'active' : '' }}" href="{{ route('admin.hrd.output.index') }}">
                    <i class="nav-icon cil-chart-pie"></i> Output Pemilah
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.hrd.waste-category.*') ? 'active' : '' }}" href="{{ route('admin.hrd.waste-category.index') }}">
                    <i class="nav-icon cil-tags"></i> Kategori Sampah
                </a>
            </li>
            @endhasanyrole

            @hasanyrole('manajemen|hrd|keuangan|super_admin')
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.hrd.wage-rate.*') ? 'active' : '' }}" href="{{ route('admin.hrd.wage-rate.index') }}">
                    <i class="nav-icon cil-dollar"></i> Tarif Upah
                </a>
            </li>
            @endhasanyrole

            @hasanyrole('manajemen|keuangan|super_admin')
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.hrd.wage-calculation.*') ? 'active' : '' }}" href="{{ route('admin.hrd.wage-calculation.index') }}">
                    <i class="nav-icon cil-calculator"></i> Perhitungan Upah
                </a>
            </li>
            @endhasanyrole
            @endhasanyrole

            {{-- Administrasi --}}
            @canany(['view_users', 'view_company_settings', 'view_activity_log'])
            <li class="nav-title">Administrasi</li>
            @endcan
            @can('view_users')
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}" href="{{ route('admin.roles.index') }}">
                    <i class="nav-icon cil-lock-locked"></i> Manajemen Role
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                    <i class="nav-icon cil-user"></i> Users
                </a>
            </li>
            @endcan
            @can('view_company_settings')
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.company-settings') ? 'active' : '' }}" href="{{ route('admin.company-settings') }}">
                    <i class="nav-icon cil-building"></i> Pengaturan Perusahaan
                </a>
            </li>
            @endcan
            @can('view_activity_log')
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.activities.*') ? 'active' : '' }}" href="{{ route('admin.activities.index') }}">
                    <i class="nav-icon cil-history"></i> Log Aktivitas
                </a>
            </li>
            @endcan

            {{-- Panduan / User Guide --}}
            <li class="nav-title">Bantuan</li>
            <li class="nav-item">
                <a class="nav-link text-info" href="/panduan.html" target="_blank">
                    <i class="nav-icon cil-book text-info"></i> Panduan Aplikasi
                </a>
            </li>

            @if(auth()->user() && auth()->user()->is_super_admin)
            <!-- CENTRAL PANEL -->
            <li class="nav-title text-danger">CENTRAL PANEL (SUPERADMIN)</li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('central.dashboard') }}">
                    <i class="nav-icon cil-speedometer text-danger"></i> Central Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('central.tenants.index') }}">
                    <i class="nav-icon cil-building text-danger"></i> Manajemen Tenant
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('central.users.index') }}">
                    <i class="nav-icon cil-people text-danger"></i> Semua User (Central)
                </a>
            </li>
            @endif
        </ul>
        <button class="sidebar-toggler" type="button" data-coreui-toggle="unfoldable"></button>
    </div>

    <div class="wrapper d-flex flex-column min-vh-100">
        {{-- Header --}}
        <header class="header header-sticky p-0 mb-4 shadow-sm bg-body">
            <div class="container-fluid px-4">
                <button class="header-toggler" type="button"
                    onclick="document.getElementById('sidebar').classList.toggle('hide'); document.getElementById('sidebar').classList.toggle('show');"
                    style="margin-inline-start: -14px;">
                    <i class="cil-menu" style="font-size: 1.25rem;"></i>
                </button>

                <ul class="header-nav ms-auto flex-row align-items-center">
                    {{-- RTL Toggle --}}
                    <li class="nav-item">
                        <button class="nav-link" type="button" onclick="toggleRTL()" title="Toggle RTL">
                            <i class="cil-swap-horizontal fs-5"></i>
                        </button>
                    </li>
                    
                    {{-- Theme Toggle --}}
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
                                {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end pt-0">
                            <div class="dropdown-header bg-body-tertiary text-body-secondary fw-semibold rounded-top mb-2 py-3 px-4">
                                <div class="fw-semibold">{{ auth()->user()->name ?? 'User' }}</div>
                                <small class="text-body-secondary">{{ auth()->user()->email ?? '' }}</small>
                            </div>
                            <a class="dropdown-item" href="{{ route('admin.company-settings') }}">
                                <i class="cil-settings me-2"></i> Pengaturan
                            </a>
                            <div class="dropdown-divider"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="cil-account-logout me-2"></i> Logout
                                </button>
                            </form>
                        </div>
                    </li>
                </ul>
            </div>
        </header>

        {{-- Content --}}
        <div class="body flex-grow-1">
            <div class="container-fluid px-4">
                {{-- Flash Messages --}}
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="cil-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-coreui-dismiss="alert"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="cil-x-circle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-coreui-dismiss="alert"></button>
                    </div>
                @endif
                @if(session('warning'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <i class="cil-warning me-2"></i>{{ session('warning') }}
                        <button type="button" class="btn-close" data-coreui-dismiss="alert"></button>
                    </div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="cil-x-circle me-2"></i>
                        <strong>Terjadi kesalahan:</strong>
                        <ul class="mb-0 mt-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-coreui-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>

        {{-- Footer --}}
        <footer class="footer px-4 mt-auto">
            <div class="text-body-secondary small">
                &copy; {{ date('Y') }} TPST App. All rights reserved.
            </div>
        </footer>
    </div>

    {{-- Mobile Bottom Nav --}}
    <div class="mobile-bottom-nav">
        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="cil-speedometer"></i>
            <span>Home</span>
        </a>
        <a href="{{ route('admin.ritase.index') }}" class="nav-link {{ request()->routeIs('admin.ritase.*') ? 'active' : '' }}">
            <i class="cil-truck"></i>
            <span>Ritase</span>
        </a>
        <a href="{{ route('admin.penjualan.index') }}" class="nav-link {{ request()->routeIs('admin.penjualan.*') ? 'active' : '' }}">
            <i class="cil-cart"></i>
            <span>Jual</span>
        </a>
        <a href="{{ route('admin.jurnal.index') }}" class="nav-link {{ request()->routeIs('admin.jurnal.*') ? 'active' : '' }}">
            <i class="cil-file"></i>
            <span>Jurnal</span>
        </a>
        <a href="#" class="nav-link" onclick="document.getElementById('sidebar').classList.toggle('show')">
            <i class="cil-menu"></i>
            <span>Menu</span>
        </a>
    </div>

    {{-- Overlay for mobile sidebar --}}
    <div id="sidebar-overlay" class="d-none" style="position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1031;"></div>

    {{-- CoreUI JS --}}
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
    @stack('scripts')
</body>
</html>
