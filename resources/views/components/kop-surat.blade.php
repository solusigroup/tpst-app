@props(['tenant' => null])
@php
    $tenant = $tenant ?? auth()->user()->tenant;
@endphp
<div class="mb-8 border-b-2 border-slate-900 pb-6 text-center">
    <h1 class="text-3xl font-black uppercase tracking-tighter text-slate-900">
        {{ !empty($tenant->name) ? $tenant->name : 'PT Tatabumi Adilimbah' }}
    </h1>
    <p class="text-xs text-slate-500 mt-2 font-medium leading-relaxed">
        {{ $tenant->address ?? '' }}<br>
        @if($tenant->email) Email: {{ $tenant->email }} @endif
        @if($tenant->bank_name) 
            <span class="mx-2 text-slate-300">|</span> 
            Bank: {{ $tenant->bank_name }} - {{ $tenant->bank_account_number }} 
            ({{ $tenant->bank_account_name }})
        @endif
    </p>
</div>
