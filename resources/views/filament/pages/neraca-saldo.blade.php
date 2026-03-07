@php 
    if(!isset($data)) $data = $this->getReportData();
    $tenant = auth()->user()->tenant;
@endphp

@if(!($isExport ?? false))
<x-filament-panels::page>
    <div class="space-y-4">

        {{-- Filter --}}
        <div class="report-filter no-print fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-4">
            <div class="flex items-end gap-4">
                <div>
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Dari</label>
                    <input type="date" wire:model.live="dari" class="mt-1 block w-48 rounded-lg border-gray-300 shadow-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white text-sm focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Sampai</label>
                    <input type="date" wire:model.live="sampai" class="mt-1 block w-48 rounded-lg border-gray-300 shadow-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white text-sm focus:ring-primary-500 focus:border-primary-500">
                </div>
            </div>
        </div>

        @include('filament.pages.partials.neraca-saldo-content', ['data' => $data, 'dari' => $dari, 'sampai' => $sampai, 'tenant' => $tenant])

    </div>
</x-filament-panels::page>
@else
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Neraca Saldo</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #333; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .text-2xl { font-size: 1.5rem; }
        .mb-2 { margin-bottom: 0.5rem; }
        .mb-8 { margin-bottom: 2rem; }
        .pb-4 { padding-bottom: 1rem; }
        .border-b-2 { border-bottom: 2px solid #333; }
        .uppercase { text-transform: uppercase; }
        table { w-full; border-collapse: collapse; width: 100%; margin-top: 1rem; }
        th, td { padding: 6px; text-align: left; }
        .border-b { border-bottom: 1px solid #ddd; }
        .text-right { text-align: right; }
        .bg-gray-100 { background-color: #f3f4f6; }
        .font-semibold { font-weight: 600; }
        .text-green-700 { color: #15803d; }
        .text-red-700 { color: #b91c1c; }
        .w-1-6 { width: 16.666667%; }
        .w-1-2 { width: 50%; }
        .font-normal { font-weight: normal; }
    </style>
</head>
<body>
    @include('filament.pages.partials.neraca-saldo-content', ['data' => $data, 'dari' => $dari, 'sampai' => $sampai, 'tenant' => $tenant])
</body>
</html>
@endif
