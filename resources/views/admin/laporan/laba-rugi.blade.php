@extends('layouts.admin')
@section('title', 'Laporan Laba Rugi')

@section('content')
<div class="d-none d-print-block">
    <x-kop-surat />
</div>

<div class="page-header d-print-none flex-wrap d-flex justify-content-between align-items-center">
    <div><h1 class="mb-0">Laporan Laba Rugi</h1></div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary" onclick="window.print()"><i class="cil-print me-1"></i> Print</button>
        <form action="{{ url()->current() }}" method="GET" class="d-inline">
            <input type="hidden" name="dari" value="{{ request('dari', $dari) }}">
            <input type="hidden" name="sampai" value="{{ request('sampai', $sampai) }}">
            <input type="hidden" name="export" value="pdf">
            <button type="submit" class="btn btn-outline-danger"><i class="cil-file me-1"></i> PDF</button>
        </form>
        <form action="{{ url()->current() }}" method="GET" class="d-inline">
            <input type="hidden" name="dari" value="{{ request('dari', $dari) }}">
            <input type="hidden" name="sampai" value="{{ request('sampai', $sampai) }}">
            <input type="hidden" name="export" value="excel">
            <button type="submit" class="btn btn-outline-success"><i class="cil-spreadsheet me-1"></i> Excel</button>
        </form>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-auto"><label class="form-label mb-0 small text-body-secondary">Dari</label><input type="date" name="dari" class="form-control" value="{{ $dari }}"></div>
            <div class="col-auto"><label class="form-label mb-0 small text-body-secondary">Sampai</label><input type="date" name="sampai" class="form-control" value="{{ $sampai }}"></div>
            <div class="col-auto"><button class="btn btn-primary" type="submit"><i class="cil-filter me-1"></i> Filter</button></div>
        </form>
    </div>
</div>

<div class="card" id="printable">
    <div class="card-body">
        <div class="text-center mb-4 print-header">
            <h5 class="fw-bold mb-1">LAPORAN LABA RUGI</h5>
            <p class="text-body-secondary mb-0">Periode: {{ \Carbon\Carbon::parse($dari)->format('d M Y') }} - {{ \Carbon\Carbon::parse($sampai)->format('d M Y') }}</p>
        </div>

        <h6 class="fw-bold text-success mb-2"><i class="cil-arrow-circle-top me-1"></i> PENDAPATAN</h6>
        <table class="table table-sm mb-3">
            <tbody>
                @foreach($pendapatan as $item)
                <tr><td>{{ $item->kode_akun }} - {{ $item->nama_akun }}</td><td class="text-end">Rp {{ number_format($item->saldo, 0, ',', '.') }}</td></tr>
                @endforeach
            </tbody>
            <tfoot class="border-top"><tr class="fw-bold"><td>Total Pendapatan</td><td class="text-end text-success">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</td></tr></tfoot>
        </table>

        <h6 class="fw-bold text-danger mb-2"><i class="cil-arrow-circle-bottom me-1"></i> BEBAN</h6>
        <table class="table table-sm mb-3">
            <tbody>
                @foreach($beban as $item)
                <tr><td>{{ $item->kode_akun }} - {{ $item->nama_akun }}</td><td class="text-end">Rp {{ number_format($item->saldo, 0, ',', '.') }}</td></tr>
                @endforeach
            </tbody>
            <tfoot class="border-top"><tr class="fw-bold"><td>Total Beban</td><td class="text-end text-danger">Rp {{ number_format($totalBeban, 0, ',', '.') }}</td></tr></tfoot>
        </table>

        <div class="border-top border-2 pt-3">
            <table class="table table-sm mb-0">
                <tr class="fw-bold fs-5"><td>LABA/RUGI BERSIH</td><td class="text-end {{ $labaRugiBersih >= 0 ? 'text-success' : 'text-danger' }}">Rp {{ number_format($labaRugiBersih, 0, ',', '.') }}</td></tr>
            </table>
        </div>
    </div>
</div>
@endsection
