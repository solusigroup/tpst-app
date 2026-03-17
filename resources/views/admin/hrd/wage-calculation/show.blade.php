@extends('layouts.admin')
@section('title', 'Detail Perhitungan Upah')

@section('content')
<div class="page-header">
    <div>
        <h1>Detail Perhitungan Upah</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li><li class="breadcrumb-item"><a href="{{ route('admin.hrd.wage-calculation.index') }}">Perhitungan Upah</a></li><li class="breadcrumb-item active">Detail</li></ol></nav>
    </div>
    <div>
        <a href="{{ route('admin.hrd.wage-calculation.export-slip', $wageCalculation) }}" target="_blank" class="btn btn-danger text-white"><i class="cil-print me-1"></i> Cetak Slip Gaji (PDF)</a>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header bg-white"><strong>Informasi Perhitungan</strong></div>
            <div class="card-body">
                <table class="table table-borderless table-sm mb-0">
                    <tr><td class="text-body-secondary">Karyawan</td><td>: <strong>{{ $wageCalculation->user->name ?? '-' }}</strong></td></tr>
                    <tr><td class="text-body-secondary">Periode</td><td>: 
                        @if($wageCalculation->user->salary_type === 'bulanan')
                            Bulan {{ \Carbon\Carbon::parse($wageCalculation->week_start)->translatedFormat('F Y') }}
                        @else
                            {{ \Carbon\Carbon::parse($wageCalculation->week_start)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($wageCalculation->week_end)->format('d/m/Y') }}
                        @endif
                    </td></tr>
                    <tr><td class="text-body-secondary">Total Output</td><td>: {{ number_format($wageCalculation->total_output, 2, ',', '.') }} kg</td></tr>
                    <tr><td class="text-body-secondary">Status</td><td>: 
                        @if($wageCalculation->status == 'pending') <span class="badge bg-warning">Pending</span>
                        @elseif($wageCalculation->status == 'approved') <span class="badge bg-info">Disetujui</span>
                        @elseif($wageCalculation->status == 'paid') <span class="badge bg-success">Dibayar ({{ \Carbon\Carbon::parse($wageCalculation->paid_date)->format('d M Y') }})</span>
                        @endif
                    </td></tr>
                </table>
            </div>
            <div class="card-footer text-center bg-white">
                <h4 class="mb-0 text-primary">Total: Rp {{ number_format($wageCalculation->total_wage, 2, ',', '.') }}</h4>
            </div>
            
            @if($wageCalculation->status == 'pending')
            <div class="card-body border-top">
                <form action="{{ route('admin.hrd.wage-calculation.approve', $wageCalculation) }}" method="POST" onsubmit="return confirm('Setujui perhitungan ini?')">
                    @csrf
                    <button class="btn btn-info w-100 text-white"><i class="cil-check-circle me-1"></i> Setujui Upah</button>
                </form>
            </div>
            @endif

            @if($wageCalculation->status == 'approved')
            <div class="card-body border-top">
                <form action="{{ route('admin.hrd.wage-calculation.pay', $wageCalculation) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Tanggal Pembayaran</label>
                        <input type="date" name="paid_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <button class="btn btn-success w-100 text-white"><i class="cil-money me-1"></i> Tandai Dibayar</button>
                </form>
            </div>
            @endif
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-white"><strong>Rincian Output Karyawan</strong></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr><th>Tanggal</th><th>Kategori Sampah</th><th class="text-end">Jumlah</th></tr>
                        </thead>
                        <tbody>
                            @forelse($outputs as $out)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($out->output_date)->format('d/m/Y') }}</td>
                                <td><span class="badge bg-secondary">{{ $out->wasteCategory->name }}</span></td>
                                <td class="text-end">{{ number_format($out->quantity, 2, ',', '.') }} {{ $out->unit }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center py-4 text-body-secondary">Tidak ada catatan output pada periode ini.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
