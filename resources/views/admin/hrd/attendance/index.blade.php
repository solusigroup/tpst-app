@extends('layouts.admin')
@section('title', 'Kehadiran')

@section('content')
<div class="page-header">
    <div>
        <h1>Kehadiran</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li><li class="breadcrumb-item active">Kehadiran</li></ol></nav>
    </div>
    @hasanyrole('manajemen|hrd|super_admin')
    <a href="{{ route('admin.hrd.attendance.create') }}" class="btn btn-primary"><i class="cil-plus me-1"></i> Tambah Kehadiran</a>
    @endhasanyrole
</div>

@if(Auth::user()->salary_type === 'bulanan')
<div class="card mb-4">
    <div class="card-header bg-white py-3">
        <h5 class="mb-3">Quick Check-in / Check-out</h5>
        <div class="row g-2 align-items-center">
            <div class="col-md-4">
                <p class="mb-0">Anda dapat melakukan check-in dan check-out sendiri.</p>
            </div>
            <div class="col-auto">
                <form method="GET" action="{{ route('attendance.check-in') }}">
                    <button type="submit" class="btn btn-success text-white"><i class="cil-account-login me-1"></i> Check In</button>
                </form>
            </div>
            <div class="col-auto">
                <form method="GET" action="{{ route('attendance.check-out') }}">
                    <button type="submit" class="btn btn-warning text-white"><i class="cil-account-logout me-1"></i> Check Out</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

<div class="card">
    <div class="card-header bg-white py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Karyawan</label>
                @if(auth()->check() && auth()->user()->hasRole('karyawan'))
                    <input type="text" class="form-control bg-light" value="{{ auth()->user()->name }}" readonly>
                    <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                @else
                    <select name="user_id" class="form-select">
                        <option value="">Semua Karyawan</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                        @endforeach
                    </select>
                @endif
            </div>
            <div class="col-md-2">
                <label class="form-label">Dari Tanggal</label>
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Sampai Tanggal</label>
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="present" {{ request('status') == 'present' ? 'selected' : '' }}>Hadir</option>
                    <option value="absent" {{ request('status') == 'absent' ? 'selected' : '' }}>Mangkir</option>
                    <option value="sick" {{ request('status') == 'sick' ? 'selected' : '' }}>Sakit</option>
                    <option value="leave" {{ request('status') == 'leave' ? 'selected' : '' }}>Izin</option>
                </select>
            </div>
            <div class="col-auto"><button class="btn btn-outline-primary" type="submit"><i class="cil-search me-1"></i> Filter</button></div>
            @if(request()->hasAny(['user_id','date_from','date_to','status']))
                <div class="col-auto"><a href="{{ route('admin.hrd.attendance.index') }}" class="btn btn-outline-secondary">Reset</a></div>
            @endif
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>Tanggal</th><th>Karyawan</th><th>Check In</th><th>Check Out</th><th>Status</th><th class="text-end">Aksi</th></tr>
                </thead>
                <tbody>
                    @forelse($attendances as $item)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($item->attendance_date)->format('d/m/Y') }}</td>
                        <td><strong>{{ $item->user->name }}</strong></td>
                        <td>{{ $item->check_in ? \Carbon\Carbon::parse($item->check_in)->format('H:i') : '-' }}</td>
                        <td>{{ $item->check_out ? \Carbon\Carbon::parse($item->check_out)->format('H:i') : '-' }}</td>
                        <td>
                            @if($item->status == 'present') <span class="badge bg-success">Hadir</span>
                            @elseif($item->status == 'absent') <span class="badge bg-danger">Mangkir</span>
                            @elseif($item->status == 'sick') <span class="badge bg-warning">Sakit</span>
                            @elseif($item->status == 'leave') <span class="badge bg-info">Izin</span>
                            @else <span class="badge bg-secondary">{{ $item->status }}</span> @endif
                        </td>
                        <td class="text-end">
                            @hasanyrole('manajemen|hrd|super_admin')
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.hrd.attendance.edit', $item) }}" class="btn btn-outline-primary"><i class="cil-pencil"></i></a>
                                <form method="POST" action="{{ route('admin.hrd.attendance.destroy', $item) }}" class="d-inline" >
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Yakin ingin menghapus data ini?')"><i class="cil-trash"></i></button>
                                </form>
                            </div>
                            @endhasanyrole
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-4 text-body-secondary">Belum ada data kehadiran.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($attendances->hasPages()) <div class="card-footer bg-white">{{ $attendances->links() }}</div> @endif
</div>

@push('scripts')
<script>
    function submitQuick(action) {
        let userId = document.getElementById('quickUserSelect').value;
        if(!userId) {
            alert('Silakan pilih karyawan terlebih dahulu!');
            return;
        }
        let form = document.getElementById('quickActionForm');
        if(action === 'check-in') {
            form.action = "{{ url('admin/hrd/attendance') }}/" + userId + "/check-in";
        } else {
            form.action = "{{ url('admin/hrd/attendance') }}/" + userId + "/check-out";
        }
        form.submit();
    }
</script>
@endpush
@endsection
