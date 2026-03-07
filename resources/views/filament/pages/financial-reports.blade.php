<x-filament-panels::page>
    <style>
        .report-card-icon svg {
            width: 3rem !important;
            height: 3rem !important;
        }
    </style>

    <div class="space-y-8">
        <form wire:submit.prevent="submit">
            {{ $this->form }}
        </form>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mt-6">
            <!-- Laba Rugi Card -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 flex flex-col items-center text-center">
                <div class="report-card-icon mb-4">
                    <x-filament::icon 
                        icon="heroicon-o-document-text" 
                        class="w-12 h-12 text-primary-500 mx-auto" 
                    />
                </div>
                
                <h3 class="text-lg font-bold">Laba Rugi</h3>
                <p class="text-sm text-gray-500 mt-2 mb-4">
                    Analisis pendapatan dan beban operasional perusahaan.
                </p>
                <div class="w-full mt-auto">
                    <x-filament::button 
                        tag="a" 
                        href="{{ route('reports.laba-rugi') }}?month={{ $data['month'] }}&year={{ $data['year'] }}" 
                        target="_blank"
                        color="success"
                        class="w-full"
                    >
                        Buka Laporan
                    </x-filament::button>
                </div>
            </div>

            <!-- Posisi Keuangan Card -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 flex flex-col items-center text-center">
                <div class="report-card-icon mb-4">
                    <x-filament::icon 
                        icon="heroicon-o-scale" 
                        class="w-12 h-12 text-success-500 mx-auto" 
                    />
                </div>
                
                <h3 class="text-lg font-bold">Posisi Keuangan</h3>
                <p class="text-sm text-gray-500 mt-2 mb-4">
                    Laporan neraca (Aset, Liabilitas, Ekuitas).
                </p>
                <div class="w-full mt-auto">
                    <x-filament::button 
                        tag="a" 
                        href="{{ route('reports.posisi-keuangan') }}?month={{ $data['month'] }}&year={{ $data['year'] }}" 
                        target="_blank"
                        color="success"
                        class="w-full"
                    >
                        Buka Laporan
                    </x-filament::button>
                </div>
            </div>

            <!-- Arus Kas Card -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 flex flex-col items-center text-center">
                <div class="report-card-icon mb-4">
                    <x-filament::icon 
                        icon="heroicon-o-currency-dollar" 
                        class="w-12 h-12 text-danger-500 mx-auto" 
                    />
                </div>
                
                <h3 class="text-lg font-bold">Arus Kas</h3>
                <p class="text-sm text-gray-500 mt-2 mb-4">
                    Aliran kas masuk dan keluar metode langsung.
                </p>
                <div class="w-full mt-auto">
                    <x-filament::button 
                        tag="a" 
                        href="{{ route('reports.arus-kas') }}?month={{ $data['month'] }}&year={{ $data['year'] }}" 
                        target="_blank"
                        color="success"
                        class="w-full"
                    >
                        Buka Laporan
                    </x-filament::button>
                </div>
            </div>

            <!-- Perubahan Ekuitas Card -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 flex flex-col items-center text-center">
                <div class="report-card-icon mb-4">
                    <x-filament::icon 
                        icon="heroicon-o-chart-pie" 
                        class="w-12 h-12 text-warning-500 mx-auto" 
                    />
                </div>
                
                <h3 class="text-lg font-bold">Perubahan Ekuitas</h3>
                <p class="text-sm text-gray-500 mt-2 mb-4">
                    Laporan perubahan modal pemilik.
                </p>
                <div class="w-full mt-auto">
                    <x-filament::button 
                        tag="a" 
                        href="{{ route('reports.perubahan-ekuitas') }}?month={{ $data['month'] }}&year={{ $data['year'] }}" 
                        target="_blank"
                        color="success"
                        class="w-full"
                    >
                        Buka Laporan
                    </x-filament::button>
                </div>
            </div>

            <!-- Neraca Saldo Card -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 flex flex-col items-center text-center">
                <div class="report-card-icon mb-4">
                    <x-filament::icon 
                        icon="heroicon-o-document-magnifying-glass" 
                        class="w-12 h-12 text-info-500 mx-auto" 
                    />
                </div>
                
                <h3 class="text-lg font-bold">Neraca Saldo</h3>
                <p class="text-sm text-gray-500 mt-2 mb-4">
                    Daftar saldo debit dan kredit buku besar.
                </p>
                <div class="w-full mt-auto">
                    <x-filament::button 
                        tag="a" 
                        href="{{ route('reports.neraca-saldo') }}?month={{ $data['month'] }}&year={{ $data['year'] }}" 
                        target="_blank"
                        color="success"
                        class="w-full"
                    >
                        Buka Laporan
                    </x-filament::button>
                </div>
            </div>

        </div>
    </div>
</x-filament-panels::page>
