@props(['title', 'subtitle' => null, 'date' => null, 'periode' => null])

<div class="report-header text-center mb-6 pb-4 border-b-2 border-gray-800 dark:border-gray-200">
    <h1 class="text-lg font-bold uppercase tracking-wide text-gray-900 dark:text-white">
        {{ auth()->user()->tenant->name ?? 'Perusahaan' }}
    </h1>
    <h2 class="text-base font-bold uppercase mt-1 text-gray-800 dark:text-gray-100">{{ $title }}</h2>
    @if($date)
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-0.5">Per {{ \Carbon\Carbon::parse($date)->translatedFormat('d F Y') }}</p>
    @endif
    @if($periode)
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-0.5">{{ $periode }}</p>
    @endif
    @if($subtitle)
        <p class="text-xs text-gray-500 dark:text-gray-500 italic mt-0.5">{{ $subtitle }}</p>
    @endif
</div>
