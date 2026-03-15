<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'Laporan' }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 11px; margin: 0; padding: 15px; }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .text-start { text-align: left; }
        .fw-bold { font-weight: bold; }
        .mb-1 { margin-bottom: 0.25rem; }
        .mb-2 { margin-bottom: 0.5rem; }
        .mb-3 { margin-bottom: 1rem; }
        .mb-4 { margin-bottom: 1.5rem; }
        .pt-3 { padding-top: 1rem; }
        .pb-3 { padding-bottom: 1rem; }
        .w-100 { width: 100%; }
        .text-success { color: #198754; }
        .text-danger { color: #dc3545; }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1rem;
        }
        th, td {
            padding: 0.5rem;
            border: 1px solid #dee2e6;
            vertical-align: middle;
        }
        th {
            background-color: #f8f9fa;
        }
        .table-borderless th, .table-borderless td {
            border: none;
        }
        .border-top { border-top: 1px solid #dee2e6; }
        .header-section { margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header-title { font-size: 18px; margin: 0; text-transform: uppercase; font-weight: bold; }
        .header-subtitle { font-size: 11px; margin-top: 5px; color: #555; }
    </style>
</head>
<body>
    <div class="header-section text-center">
        @php
            $tenant = auth()->user() ? auth()->user()->tenant : null;
        @endphp
        <h1 class="header-title">{{ !empty($tenant?->name) ? $tenant->name : 'PT Tatabumi Adilimbah' }}</h1>
        <p class="header-subtitle">
            {{ $tenant?->address ?? '' }}<br>
            @if(!empty($tenant?->email)) Email: {{ $tenant->email }} @endif
            @if(!empty($tenant?->bank_name)) 
                | Bank: {{ $tenant?->bank_name }} - {{ $tenant?->bank_account_number }} ({{ $tenant?->bank_account_name }})
            @endif
        </p>
    </div>

    @yield('content')
</body>
</html>
