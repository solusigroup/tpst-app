<div class="fixed bottom-0 left-0 z-50 w-full h-16 bg-white border-t border-gray-200 dark:bg-gray-900 dark:border-gray-800 md:hidden pb-safe shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.1)]">
    <div class="grid h-full max-w-lg grid-cols-5 mx-auto font-medium">
        <a href="{{ route('filament.admin.pages.dashboard') }}" class="inline-flex flex-col items-center justify-center px-1 hover:bg-gray-50 dark:hover:bg-gray-800 {{ request()->routeIs('filament.admin.pages.dashboard') ? 'text-primary-600 dark:text-primary-500 font-bold' : 'text-gray-500 dark:text-gray-400' }}">
            <x-heroicon-o-home class="w-6 h-6 mb-1"/>
            <span class="text-[10px] truncate w-full text-center">Home</span>
        </a>
        <a href="{{ route('filament.admin.resources.ritases.index') }}" class="inline-flex flex-col items-center justify-center px-1 hover:bg-gray-50 dark:hover:bg-gray-800 {{ request()->routeIs('filament.admin.resources.ritases.*') ? 'text-primary-600 dark:text-primary-500 font-bold' : 'text-gray-500 dark:text-gray-400' }}">
            <x-heroicon-o-truck class="w-6 h-6 mb-1"/>
            <span class="text-[10px] truncate w-full text-center">Ritase</span>
        </a>
        <!-- Add "+" Button in the Middle for Jurnal Kas -->
        <div class="relative flex items-center justify-center px-1 h-full">
            <a href="{{ route('filament.admin.resources.jurnal-kas.create') }}" class="absolute -top-6 flex items-center justify-center w-14 h-14 bg-primary-600 dark:bg-primary-500 text-white rounded-full shadow-lg hover:bg-primary-700 dark:hover:bg-primary-600 ring-4 ring-white dark:ring-gray-900 transition-transform hover:scale-105 active:scale-95">
                <x-heroicon-o-plus class="w-8 h-8"/>
            </a>
            <span class="text-[10px] text-gray-500 dark:text-gray-400 mt-8 truncate w-full text-center">Kas Baru</span>
        </div>
        <a href="{{ route('filament.admin.resources.jurnals.index') }}" class="inline-flex flex-col items-center justify-center px-1 hover:bg-gray-50 dark:hover:bg-gray-800 {{ request()->routeIs('filament.admin.resources.jurnals.*') ? 'text-primary-600 dark:text-primary-500 font-bold' : 'text-gray-500 dark:text-gray-400' }}">
            <x-heroicon-o-document-duplicate class="w-6 h-6 mb-1"/>
            <span class="text-[10px] truncate w-full text-center">Jurnal Umum</span>
        </a>
        <a href="{{ route('filament.admin.pages.laba-rugi') }}" class="inline-flex flex-col items-center justify-center px-1 hover:bg-gray-50 dark:hover:bg-gray-800 {{ request()->routeIs('filament.admin.pages.*') && !request()->routeIs('filament.admin.pages.dashboard') ? 'text-primary-600 dark:text-primary-500 font-bold' : 'text-gray-500 dark:text-gray-400' }}">
            <x-heroicon-o-chart-bar class="w-6 h-6 mb-1"/>
            <span class="text-[10px] truncate w-full text-center">Laporan</span>
        </a>
    </div>
</div>

<style>
    /* Add padding to the bottom of the main content on mobile so the bottom bar does not overlap content */
    @media (max-width: 768px) {
        .fi-layout > main, body > main, main.fi-main {
            padding-bottom: 5rem !important;
        }
        
        /* Attempt to fix some bottom action overlaps in Filament */
        .fi-page-actions {
            padding-bottom: 2rem !important;
        }
    }
</style>
