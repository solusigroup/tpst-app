@extends('layouts.admin')
@section('title', 'Neraca Saldo')

@section('content')
<div class="d-none d-print-block">
    <x-kop-surat />
</div>

<div class="page-header d-print-none flex-wrap d-flex justify-content-between align-items-center">
    <div><h1 class="mb-0">Neraca Saldo</h1></div>
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
    <div class="text-center mb-4"><h5 class="fw-bold mb-1">NERACA SALDO</h5><p class="text-body-secondary mb-0">Periode: {{ \Carbon\Carbon::parse($dari)->format('d M Y') }} - {{ \Carbon\Carbon::parse($sampai)->format('d M Y') }}</p></div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr><th>Kode Akun</th><th>Nama Akun</th><th>Tipe</th><th class="text-end">Debit</th><th class="text-end">Kredit</th><th class="text-end">Saldo</th></tr></thead>
            <tbody>
                @foreach($rows as $r)
                <tr>
                    <td><strong>{{ $r->kode_akun }}</strong></td>
                    <td>{{ $r->nama_akun }}</td>
                    <td><span class="badge bg-secondary">{{ $r->tipe }}</span></td>
                    <td class="text-end">{{ number_format($r->total_debit, 0, ',', '.') }}</td>
                    <td class="text-end">{{ number_format($r->total_kredit, 0, ',', '.') }}</td>
                    <td class="text-end fw-bold {{ $r->saldo >= 0 ? '' : 'text-danger' }}">{{ number_format($r->saldo, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="border-top border-2 fw-bold">
                <tr><td colspan="3">TOTAL</td><td class="text-end">Rp {{ number_format($totalDebit, 0, ',', '.') }}</td><td class="text-end">Rp {{ number_format($totalKredit, 0, ',', '.') }}</td><td class="text-end {{ $totalDebit == $totalKredit ? 'text-success' : 'text-danger' }}">{{ $totalDebit == $totalKredit ? 'BALANCE' : 'IMBALANCE' }}</td></tr>
            </tfoot>
        </table>
    </div>
</div></div>
@endsection
