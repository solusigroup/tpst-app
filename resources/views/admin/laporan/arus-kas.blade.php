@extends('layouts.admin')
@section('title', 'Laporan Arus Kas')

@section('content')
<div class="d-none d-print-block">
    <x-kop-surat />
</div>

<div class="page-header d-print-none flex-wrap d-flex justify-content-between align-items-center">
    <div><h1 class="mb-0">Laporan Arus Kas</h1></div>
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

<div class="card mb-4"><div class="card-body py-3">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-auto"><label class="form-label mb-0 small text-body-secondary">Dari</label><input type="date" name="dari" class="form-control" value="{{ $dari }}"></div>
        <div class="col-auto"><label class="form-label mb-0 small text-body-secondary">Sampai</label><input type="date" name="sampai" class="form-control" value="{{ $sampai }}"></div>
        <div class="col-auto"><button class="btn btn-primary" type="submit"><i class="cil-filter me-1"></i> Filter</button></div>
    </form>
</div></div>

<div class="card" id="printable"><div class="card-body">
    <div class="text-center mb-4"><h5 class="fw-bold mb-1">LAPORAN ARUS KAS</h5><p class="text-body-secondary mb-0">Metode Langsung<br>Periode: {{ \Carbon\Carbon::parse($dari)->format('d M Y') }} - {{ \Carbon\Carbon::parse($sampai)->format('d M Y') }}</p></div>

    <table class="table table-sm mb-4">
        <tbody>
            <tr><td class="fw-bold" colspan="2">Arus Kas dari Aktivitas Operasi</td></tr>
            <tr><td class="ps-4">Penerimaan Kas</td><td class="text-end text-success">Rp {{ number_format($totalKasMasuk, 0, ',', '.') }}</td></tr>
            <tr><td class="ps-4">Pengeluaran Kas</td><td class="text-end text-danger">(Rp {{ number_format($totalKasKeluar, 0, ',', '.') }})</td></tr>
            <tr class="fw-bold border-top"><td>Kas Bersih dari Aktivitas Operasi</td><td class="text-end">Rp {{ number_format($totalKasBersih, 0, ',', '.') }}</td></tr>
        </tbody>
    </table>

    <div class="border-top border-2 pt-3">
        <table class="table table-sm mb-0">
            <tr><td class="fw-semibold">Saldo Kas Awal Periode</td><td class="text-end">Rp {{ number_format($saldoAwal, 0, ',', '.') }}</td></tr>
            <tr><td class="fw-semibold">Kenaikan/(Penurunan) Kas Bersih</td><td class="text-end {{ $totalKasBersih >= 0 ? 'text-success' : 'text-danger' }}">Rp {{ number_format($totalKasBersih, 0, ',', '.') }}</td></tr>
            <tr class="fw-bold fs-5 border-top"><td>SALDO KAS AKHIR PERIODE</td><td class="text-end">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</td></tr>
        </table>
    </div>
</div></div>
@endsection
