@extends('layouts.admin')
@section('title', $title)

@section('content')
<div class="page-header">
    <div>
        <h1>{{ $title }}</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li><li class="breadcrumb-item active">{{ $title }}</li></ol></nav>
    </div>
</div>

<div class="card">
    <div class="card-body text-center py-5">
        <i class="cil-chart" style="font-size: 3rem; color: #94a3b8;"></i>
        <h4 class="mt-3 text-body-secondary">{{ $title }}</h4>
        <p class="text-body-secondary">Fitur laporan ini akan segera tersedia.</p>
    </div>
</div>
@endsection
