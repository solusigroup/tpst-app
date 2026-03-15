@extends('layouts.admin')
@section('title', 'Posisi Keuangan')

@section('content')
<div class="d-none d-print-block">
    <x-kop-surat />
</div>

<div class="page-header d-print-none flex-wrap d-flex justify-content-between align-items-center">
    <div><h1 class="mb-0">Laporan Posisi Keuangan</h1></div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary" onclick="window.print()"><i class="cil-print me-1"></i> Print</button>
        <form action="{{ url()->current() }}" method="GET" class="d-inline">
            <input type="hidden" name="sampai" value="{{ request('sampai', $sampai) }}">
            <input type="hidden" name="export" value="pdf">
            <button type="submit" class="btn btn-outline-danger"><i class="cil-file me-1"></i> PDF</button>
        </form>
        <form action="{{ url()->current() }}" method="GET" class="d-inline">
            <input type="hidden" name="sampai" value="{{ request('sampai', $sampai) }}">
            <input type="hidden" name="export" value="excel">
            <button type="submit" class="btn btn-outline-success"><i class="cil-spreadsheet me-1"></i> Excel</button>
        </form>
    </div>
</div>

<div class="card mb-4"><div class="card-body py-3">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-auto"><label class="form-label mb-0 small text-body-secondary">Per Tanggal</label><input type="date" name="sampai" class="form-control" value="{{ $sampai }}"></div>
        <div class="col-auto"><button class="btn btn-primary" type="submit"><i class="cil-filter me-1"></i> Filter</button></div>
    </form>
</div></div>

<div class="row g-4" id="printable">
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header bg-primary text-white"><h6 class="mb-0 fw-bold">ASET</h6></div>
            <div class="card-body">
                <h6 class="fw-semibold text-body-secondary">Aset Lancar</h6>
                <table class="table table-sm">
                    @foreach($asetLancar as $item)<tr><td>{{ $item->kode_akun }} - {{ $item->nama_akun }}</td><td class="text-end">{{ number_format($item->saldo, 0, ',', '.') }}</td></tr>@endforeach
                    <tr class="fw-bold border-top"><td>Total Aset Lancar</td><td class="text-end">{{ number_format($totalAsetLancar, 0, ',', '.') }}</td></tr>
                </table>
                <h6 class="fw-semibold text-body-secondary mt-3">Aset Tidak Lancar</h6>
                <table class="table table-sm">
                    @foreach($asetTidakLancar as $item)<tr><td>{{ $item->kode_akun }} - {{ $item->nama_akun }}</td><td class="text-end">{{ number_format($item->saldo, 0, ',', '.') }}</td></tr>@endforeach
                    <tr class="fw-bold border-top"><td>Total Aset Tidak Lancar</td><td class="text-end">{{ number_format($totalAsetTidakLancar, 0, ',', '.') }}</td></tr>
                </table>
                <div class="border-top border-2 pt-2 mt-3"><table class="table table-sm mb-0"><tr class="fw-bold fs-5"><td>TOTAL ASET</td><td class="text-end">Rp {{ number_format($totalAset, 0, ',', '.') }}</td></tr></table></div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header bg-warning text-dark"><h6 class="mb-0 fw-bold">LIABILITAS & EKUITAS</h6></div>
            <div class="card-body">
                <h6 class="fw-semibold text-body-secondary">Liabilitas Jangka Pendek</h6>
                <table class="table table-sm">
                    @foreach($liabilitasJP as $item)<tr><td>{{ $item->kode_akun }} - {{ $item->nama_akun }}</td><td class="text-end">{{ number_format($item->saldo, 0, ',', '.') }}</td></tr>@endforeach
                    <tr class="fw-bold border-top"><td>Total Liabilitas JP</td><td class="text-end">{{ number_format($totalLiabilitasJP, 0, ',', '.') }}</td></tr>
                </table>
                <h6 class="fw-semibold text-body-secondary mt-3">Liabilitas Jangka Panjang</h6>
                <table class="table table-sm">
                    @foreach($liabilitasJPj as $item)<tr><td>{{ $item->kode_akun }} - {{ $item->nama_akun }}</td><td class="text-end">{{ number_format($item->saldo, 0, ',', '.') }}</td></tr>@endforeach
                    <tr class="fw-bold border-top"><td>Total Liabilitas JPj</td><td class="text-end">{{ number_format($totalLiabilitasJPj, 0, ',', '.') }}</td></tr>
                </table>
                <h6 class="fw-semibold text-body-secondary mt-3">Ekuitas</h6>
                <table class="table table-sm">
                    @foreach($ekuitas as $item)<tr><td>{{ $item->kode_akun }} - {{ $item->nama_akun }}</td><td class="text-end">{{ number_format($item->saldo, 0, ',', '.') }}</td></tr>@endforeach
                    <tr class="fw-bold border-top"><td>Total Ekuitas</td><td class="text-end">{{ number_format($totalEkuitas, 0, ',', '.') }}</td></tr>
                </table>
                <div class="border-top border-2 pt-2 mt-3"><table class="table table-sm mb-0"><tr class="fw-bold fs-5"><td>TOTAL LIABILITAS + EKUITAS</td><td class="text-end {{ abs($totalAset - $totalLiabilitasEkuitas) < 0.01 ? 'text-success' : 'text-danger' }}">Rp {{ number_format($totalLiabilitasEkuitas, 0, ',', '.') }}</td></tr></table></div>
            </div>
        </div>
    </div>
</div>
@endsection
