@props(['tenant' => null])
@php
    $tenant = $tenant ?? auth()->user()->tenant;
@endphp
<div class="mb-4 border-bottom border-dark pb-3 text-center">
    <h1 class="display-6 fw-bold text-uppercase text-dark mb-1" style="letter-spacing: -1px;">
        {{ !empty($tenant?->name) ? $tenant->name : 'PT Tatabumi Adilimbah' }}
    </h1>
    <p class="small text-secondary fw-medium mb-0" style="line-height: 1.6;">
        {{ $tenant?->address ?? '' }}<br>
        @if($tenant?->email) Email: {{ $tenant?->email }} @endif
        @if($tenant?->bank_name) 
            <span class="mx-2 text-black-50 fw-light">|</span> 
            Bank: {{ $tenant?->bank_name }} - {{ $tenant?->bank_account_number }} 
            ({{ $tenant?->bank_account_name }})
        @endif
    </p>
</div>
