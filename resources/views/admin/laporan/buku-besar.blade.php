@extends('layouts.admin')
@section('title', 'Buku Besar')

@section('content')
<div class="d-none d-print-block">
    <x-kop-surat />
</div>

<div class="page-header d-print-none flex-wrap d-flex justify-content-between align-items-center">
    <div><h1 class="mb-0">Buku Besar</h1></div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary" onclick="window.print()"><i class="cil-print me-1"></i> Print</button>
        <form action="{{ url()->current() }}" method="GET" class="d-inline">
            <input type="hidden" name="dari" value="{{ request('dari', $dari) }}">
            <input type="hidden" name="sampai" value="{{ request('sampai', $sampai) }}">
            @if($coaId)<input type="hidden" name="coa_id" value="{{ request('coa_id', $coaId) }}">@endif
            <input type="hidden" name="export" value="pdf">
            <button type="submit" class="btn btn-outline-danger"><i class="cil-file me-1"></i> PDF</button>
        </form>
        <form action="{{ url()->current() }}" method="GET" class="d-inline">
            <input type="hidden" name="dari" value="{{ request('dari', $dari) }}">
            <input type="hidden" name="sampai" value="{{ request('sampai', $sampai) }}">
            @if($coaId)<input type="hidden" name="coa_id" value="{{ request('coa_id', $coaId) }}">@endif
            <input type="hidden" name="export" value="excel">
            <button type="submit" class="btn btn-outline-success"><i class="cil-spreadsheet me-1"></i> Excel</button>
        </form>
    </div>
</div>

<div class="card mb-4"><div class="card-body py-3">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-auto"><label class="form-label mb-0 small text-body-secondary">Dari</label><input type="date" name="dari" class="form-control" value="{{ $dari }}"></div>
        <div class="col-auto"><label class="form-label mb-0 small text-body-secondary">Sampai</label><input type="date" name="sampai" class="form-control" value="{{ $sampai }}"></div>
        <div class="col-auto">
            <label class="form-label mb-0 small text-body-secondary">Akun COA</label>
            <select name="coa_id" class="form-select">
                <option value="">-- Semua Akun --</option>
                @foreach($coas as $c)<option value="{{ $c->id }}" {{ $coaId == $c->id ? 'selected' : '' }}>{{ $c->kode_akun }} - {{ $c->nama_akun }}</option>@endforeach
            </select>
        </div>
        <div class="col-auto"><button class="btn btn-primary" type="submit"><i class="cil-filter me-1"></i> Filter</button></div>
    </form>
</div></div>

<div class="card" id="printable">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light"><tr><th>Tanggal</th><th>Kode Akun</th><th>Nama Akun</th><th>Keterangan</th><th class="text-end">Debit</th><th class="text-end">Kredit</th><th class="text-end">Saldo</th></tr></thead>
                <tbody>
                    @php 
                        $runningSaldo = $pageSaldoAwal ?? 0;
                        $isDebitNormal = $selectedCoa ? in_array($selectedCoa->tipe, ['Asset', 'Expense']) : true;
                    @endphp

                    @if($coaId && $rows->currentPage() == 1)
                    <tr class="table-light italic">
                        <td colspan="4"><strong>SALDO AWAL (Per {{ \Carbon\Carbon::parse($dari)->format('d M Y') }})</strong></td>
                        <td class="text-end">-</td>
                        <td class="text-end">-</td>
                        <td class="text-end fw-bold text-primary">{{ number_format($saldoAwal, 0, ',', '.') }}</td>
                    </tr>
                    @endif

                    @forelse($rows as $r)
                    @php 
                        if ($selectedCoa) {
                            if ($isDebitNormal) {
                                $runningSaldo += ($r->debit - $r->kredit);
                            } else {
                                $runningSaldo += ($r->kredit - $r->debit);
                            }
                        }
                    @endphp
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($r->tanggal)->format('d M Y') }}</td>
                        <td><strong>{{ $r->kode_akun }}</strong></td>
                        <td>{{ $r->nama_akun }}</td>
                        <td style="font-size: 0.85rem; max-width: 300px; white-space: normal; word-wrap: break-word;">{{ $r->deskripsi }}</td>
                        <td class="text-end">{{ $r->debit > 0 ? number_format($r->debit, 0, ',', '.') : '-' }}</td>
                        <td class="text-end">{{ $r->kredit > 0 ? number_format($r->kredit, 0, ',', '.') : '-' }}</td>
                        <td class="text-end fw-bold {{ $runningSaldo < 0 ? 'text-danger' : 'text-primary' }}">
                            @if($coaId)
                                {{ number_format($runningSaldo, 0, ',', '.') }}
                            @else
                                <span class="text-muted small">N/A</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-4 text-body-secondary">Belum ada data jurnal untuk periode ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($rows->hasPages()) <div class="card-footer bg-white">{{ $rows->links() }}</div> @endif
</div>
@endsection
