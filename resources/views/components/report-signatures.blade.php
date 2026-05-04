@props(['tenant' => null])
@php
    $tenant = $tenant ?? auth()->user()->tenant;
    $date = now()->translatedFormat('d F Y');
@endphp

<div class="mt-12 grid grid-cols-3 gap-8 text-center text-sm">
    <div>
        <p class="mb-20">Mengetahui,</p>
        <div class="border-b border-gray-400 dark:border-gray-600 w-3/4 mx-auto pb-1 font-bold">
            {{ $tenant->director_name ?? '..........................' }}
        </div>
        <p class="text-xs text-gray-500 mt-1">&nbsp;</p>
    </div>
    
    <div>
        <p class="mb-20">Diperiksa Oleh,</p>
        <div class="border-b border-gray-400 dark:border-gray-600 w-3/4 mx-auto pb-1 font-bold">
            {{ $tenant->manager_name ?? '..........................' }}
        </div>
        <p class="text-xs text-gray-500 mt-1">&nbsp;</p>
    </div>

    <div>
        <p class="mb-20">Lamongan, {{ $date }}<br>Dibuat Oleh,</p>
        <div class="border-b border-gray-400 dark:border-gray-600 w-3/4 mx-auto pb-1 font-bold">
            {{ $tenant->finance_name ?? '..........................' }}
        </div>
        <p class="text-xs text-gray-500 mt-1">&nbsp;</p>
    </div>
</div>
