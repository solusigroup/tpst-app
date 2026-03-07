<div class="custom-bottom-nav">
    <div class="nav-container">
        <a href="{{ route('filament.admin.pages.dashboard') }}" class="nav-item {{ request()->routeIs('filament.admin.pages.dashboard') ? 'active' : '' }}">
            <x-heroicon-o-home style="width: 24px; height: 24px; margin-bottom: 2px" />
            <span>Home</span>
        </a>
        <a href="{{ route('filament.admin.resources.ritases.index') }}" class="nav-item {{ request()->routeIs('filament.admin.resources.ritases.*') ? 'active' : '' }}">
            <x-heroicon-o-truck style="width: 24px; height: 24px; margin-bottom: 2px" />
            <span>Ritase</span>
        </a>
        
        <!-- Center Action Button -->
        <div class="nav-center-item">
            <a href="{{ route('filament.admin.resources.jurnal-kas.create') }}" class="fab-button">
                <x-heroicon-o-plus style="width: 28px; height: 28px;" />
            </a>
            <span>Kas Baru</span>
        </div>

        <a href="{{ route('filament.admin.resources.jurnals.index') }}" class="nav-item {{ request()->routeIs('filament.admin.resources.jurnals.*') ? 'active' : '' }}">
            <x-heroicon-o-document-duplicate style="width: 24px; height: 24px; margin-bottom: 2px" />
            <span>Jurnal Umum</span>
        </a>
        <a href="{{ route('filament.admin.pages.laporan-laba-rugi') }}" class="nav-item {{ request()->routeIs('filament.admin.pages.*') && !request()->routeIs('filament.admin.pages.dashboard') ? 'active' : '' }}">
            <x-heroicon-o-chart-bar style="width: 24px; height: 24px; margin-bottom: 2px" />
            <span>Laporan</span>
        </a>
    </div>
</div>

<style>
    /* Only show custom bottom nav on mobile screens (max-width: 768px) */
    .custom-bottom-nav {
        display: none;
    }

    @media (max-width: 768px) {
        .custom-bottom-nav {
            display: block;
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 64px;
            background-color: #ffffff;
            border-top: 1px solid #e5e7eb;
            z-index: 50;
            box-shadow: 0 -4px 6px -1px rgba(0, 0, 0, 0.1);
            padding-bottom: env(safe-area-inset-bottom);
        }

        /* Dark mode support */
        :is(.dark .custom-bottom-nav) {
            background-color: #111827;
            border-color: #1f2937;
        }

        .nav-container {
            display: flex;
            justify-content: space-between;
            align-items: stretch;
            height: 100%;
            max-width: 32rem; /* 512px */
            margin: 0 auto;
        }

        .nav-item, .nav-center-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            flex: 1;
            text-decoration: none;
            padding: 0 4px;
            color: #6b7280; /* text-gray-500 */
        }

        :is(.dark .nav-item), :is(.dark .nav-center-item) {
            color: #9ca3af; /* text-gray-400 */
        }

        .nav-item:hover {
            background-color: #f9fafb;
        }

        :is(.dark .nav-item:hover) {
            background-color: #1f2937;
        }

        .nav-item.active {
            color: #d97706; /* primary color / amber-600 */
            font-weight: 700;
        }

        :is(.dark .nav-item.active) {
            color: #f59e0b; /* amber-500 */
        }

        .nav-item span, .nav-center-item span {
            font-size: 10px;
            width: 100%;
            text-align: center;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Centered Floating Action Button */
        .nav-center-item {
            position: relative;
        }

        .nav-center-item span {
            margin-top: 28px;
        }

        .fab-button {
            position: absolute;
            top: -24px;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 56px;
            height: 56px;
            background-color: #d97706; /* primary / amber */
            color: #ffffff;
            border-radius: 9999px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            border: 4px solid #ffffff;
            transition: transform 0.2s ease-in-out;
        }

        :is(.dark .fab-button) {
            border-color: #111827;
        }

        .fab-button:hover {
            background-color: #b45309;
            transform: scale(1.05);
        }
        
        .fab-button:active {
            transform: scale(0.95);
        }

        /* Prevent content from hiding behind bottom nav */
        .fi-layout > main, body > main, main.fi-main {
            padding-bottom: 5rem !important;
        }
    }
</style>
