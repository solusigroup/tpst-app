@extends('layouts.admin')
@section('title', 'Perubahan Ekuitas')

@section('content')
<div class="d-none d-print-block">
    <x-kop-surat />
</div>

<div class="page-header d-print-none flex-wrap d-flex justify-content-between align-items-center">
    <div><h1 class="mb-0">Laporan Perubahan Ekuitas</h1></div>
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
    <div class="text-center mb-4"><h5 class="fw-bold mb-1">LAPORAN PERUBAHAN EKUITAS</h5><p class="text-body-secondary mb-0">Periode: {{ \Carbon\Carbon::parse($dari)->format('d M Y') }} - {{ \Carbon\Carbon::parse($sampai)->format('d M Y') }}</p></div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr><th>Akun Ekuitas</th><th class="text-end">Saldo Awal</th><th class="text-end">Penambahan</th><th class="text-end">Pengurangan</th><th class="text-end">Saldo Akhir</th></tr></thead>
            <tbody>
                @foreach($rows as $r)
                <tr>
                    <td>{{ $r['kode_akun'] }} - {{ $r['nama_akun'] }}</td>
                    <td class="text-end">{{ number_format($r['saldoAwal'], 0, ',', '.') }}</td>
                    <td class="text-end text-success">{{ number_format($r['penambahan'], 0, ',', '.') }}</td>
                    <td class="text-end text-danger">{{ number_format($r['pengurangan'], 0, ',', '.') }}</td>
                    <td class="text-end fw-bold">{{ number_format($r['saldoAkhir'], 0, ',', '.') }}</td>
                </tr>
                @endforeach
                <tr class="bg-light">
                    <td>Laba / (Rugi) Bersih Periode Berjalan</td>
                    <td class="text-end">-</td>
                    <td class="text-end {{ $labaRugi >= 0 ? 'text-success' : '' }}">{{ $labaRugi >= 0 ? number_format($labaRugi, 0, ',', '.') : '-' }}</td>
                    <td class="text-end {{ $labaRugi < 0 ? 'text-danger' : '' }}">{{ $labaRugi < 0 ? number_format(abs($labaRugi), 0, ',', '.') : '-' }}</td>
                    <td class="text-end fw-bold {{ $labaRugi >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($labaRugi, 0, ',', '.') }}</td>
                </tr>
            </tbody>
            <tfoot class="border-top border-2 fw-bold fs-6">
                <tr><td>TOTAL EKUITAS</td><td class="text-end">Rp {{ number_format($totalSaldoAwal, 0, ',', '.') }}</td><td class="text-end text-success">Rp {{ number_format($totalPenambahan + ($labaRugi >= 0 ? $labaRugi : 0), 0, ',', '.') }}</td><td class="text-end text-danger">Rp {{ number_format($totalPengurangan + ($labaRugi < 0 ? abs($labaRugi) : 0), 0, ',', '.') }}</td><td class="text-end">Rp {{ number_format($totalSaldoAkhir, 0, ',', '.') }}</td></tr>
            </tfoot>
        </table>
    </div>
</div></div>
@endsection
