@extends('layouts.admin')
@section('title', 'Activity Log')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1>Activity Log</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Log Aktivitas</li>
            </ol>
        </nav>
    </div>
</div>

<div class="card mb-4"><div class="card-body py-3">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-4">
            <label class="form-label mb-0 small text-body-secondary">Cari Log</label>
            <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Deskripsi / Modul...">
        </div>
        <div class="col-auto"><button class="btn btn-secondary" type="submit"><i class="cil-search"></i> Cari</button></div>
    </form>
</div></div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th style="width: 15%">Waktu</th>
                        <th style="width: 15%">User</th>
                        <th style="width: 15%">Event</th>
                        <th style="width: 25%">Model</th>
                        <th style="width: 30%">Deskripsi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activities as $log)
                    <tr>
                        <td>{{ $log->created_at->format('d M Y H:i:s') }}</td>
                        <td>
                            @if($log->causer)
                                <strong>{{ $log->causer->name }}</strong><br>
                                <small class="text-body-secondary">{{ class_basename($log->causer_type) }}</small>
                            @else
                                <span class="text-body-secondary fst-italic">System</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $badgeColor = match($log->event) {
                                    'created' => 'success',
                                    'updated' => 'info',
                                    'deleted' => 'danger',
                                    default => 'secondary'
                                };
                            @endphp
                            <span class="badge bg-{{ $badgeColor }}">{{ strtoupper($log->event) }}</span>
                        </td>
                        <td>{{ class_basename($log->subject_type) ?? '-' }}<br><small class="text-body-secondary">ID: {{ $log->subject_id ?? '-' }}</small></td>
                        <td>{{ $log->description }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-body-secondary">Tidak ada log aktivitas.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($activities->hasPages())
    <div class="card-footer bg-white">
        {{ $activities->links() }}
    </div>
    @endif
</div>
@endsection
