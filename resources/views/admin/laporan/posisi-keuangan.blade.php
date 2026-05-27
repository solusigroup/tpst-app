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
            <input type="hidden" name="penyajian" value="{{ request('penyajian', $penyajian ?? 'single') }}">
            @if(isset($penyajian) && $penyajian == 'komparatif')
            <input type="hidden" name="sampai_pembanding" value="{{ request('sampai_pembanding', $sampaiPembanding ?? '') }}">
            @endif
            <input type="hidden" name="export" value="pdf">
            <button type="submit" class="btn btn-outline-danger"><i class="cil-file me-1"></i> PDF</button>
        </form>
        <form action="{{ url()->current() }}" method="GET" class="d-inline">
            <input type="hidden" name="sampai" value="{{ request('sampai', $sampai) }}">
            <input type="hidden" name="penyajian" value="{{ request('penyajian', $penyajian ?? 'single') }}">
            @if(isset($penyajian) && $penyajian == 'komparatif')
            <input type="hidden" name="sampai_pembanding" value="{{ request('sampai_pembanding', $sampaiPembanding ?? '') }}">
            @endif
            <input type="hidden" name="export" value="excel">
            <button type="submit" class="btn btn-outline-success"><i class="cil-spreadsheet me-1"></i> Excel</button>
        </form>
    </div>
</div>

<div class="card mb-4"><div class="card-body py-3">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-auto">
            <label class="form-label mb-0 small text-body-secondary">Penyajian</label>
            <select name="penyajian" class="form-select" id="penyajianSelect" onchange="toggleKomparatif()">
                <option value="single" {{ (isset($penyajian) && $penyajian == 'single') ? 'selected' : '' }}>Single Period</option>
                <option value="komparatif" {{ (isset($penyajian) && $penyajian == 'komparatif') ? 'selected' : '' }}>Komparatif</option>
            </select>
        </div>
        <div class="col-auto"><label class="form-label mb-0 small text-body-secondary">Per Tanggal</label><input type="date" name="sampai" class="form-control" value="{{ $sampai }}"></div>
        
        <div class="col-auto komparatif-input"><label class="form-label mb-0 small text-body-secondary">Per Tanggal (Pembanding)</label><input type="date" name="sampai_pembanding" class="form-control" value="{{ isset($sampaiPembanding) ? $sampaiPembanding : '' }}"></div>

        <div class="col-auto"><button class="btn btn-primary" type="submit"><i class="cil-filter me-1"></i> Filter</button></div>
    </form>

    <script>
        function toggleKomparatif() {
            var val = document.getElementById('penyajianSelect').value;
            var inputs = document.querySelectorAll('.komparatif-input');
            inputs.forEach(function(el) {
                el.style.display = (val === 'komparatif') ? 'block' : 'none';
            });
        }
        document.addEventListener('DOMContentLoaded', toggleKomparatif);
    </script>
</div></div>

<div class="row g-4" id="printable">
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header bg-primary text-white"><h6 class="mb-0 fw-bold">ASET</h6></div>
            <div class="card-body">
                <h6 class="fw-semibold text-body-secondary">Aset Lancar</h6>
                <table class="table table-sm">
                    <thead><tr><th>Akun</th><th class="text-end">Berjalan</th>@if(isset($penyajian) && $penyajian == 'komparatif')<th class="text-end text-muted">Pembanding</th>@endif</tr></thead>
                    <tbody>
                    @foreach($asetLancar as $item)<tr>
                        <td>{{ $item->kode_akun }} - {{ $item->nama_akun }}</td>
                        <td class="text-end">{{ number_format($item->saldo, 0, ',', '.') }}</td>
                        @if(isset($penyajian) && $penyajian == 'komparatif')<td class="text-end text-muted">{{ number_format($item->saldo_pembanding, 0, ',', '.') }}</td>@endif
                    </tr>@endforeach
                    </tbody>
                    <tfoot class="fw-bold border-top"><tr>
                        <td>Total Aset Lancar</td>
                        <td class="text-end">{{ number_format($totalAsetLancar, 0, ',', '.') }}</td>
                        @if(isset($penyajian) && $penyajian == 'komparatif')<td class="text-end text-muted">{{ number_format($totalAsetLancarPembanding, 0, ',', '.') }}</td>@endif
                    </tr></tfoot>
                </table>
                <h6 class="fw-semibold text-body-secondary mt-3">Aset Tidak Lancar</h6>
                <table class="table table-sm">
                    <thead><tr><th>Akun</th><th class="text-end">Berjalan</th>@if(isset($penyajian) && $penyajian == 'komparatif')<th class="text-end text-muted">Pembanding</th>@endif</tr></thead>
                    <tbody>
                    @foreach($asetTidakLancar as $item)<tr>
                        <td>{{ $item->kode_akun }} - {{ $item->nama_akun }}</td>
                        <td class="text-end">{{ number_format($item->saldo, 0, ',', '.') }}</td>
                        @if(isset($penyajian) && $penyajian == 'komparatif')<td class="text-end text-muted">{{ number_format($item->saldo_pembanding, 0, ',', '.') }}</td>@endif
                    </tr>@endforeach
                    </tbody>
                    <tfoot class="fw-bold border-top"><tr>
                        <td>Total Aset Tidak Lancar</td>
                        <td class="text-end">{{ number_format($totalAsetTidakLancar, 0, ',', '.') }}</td>
                        @if(isset($penyajian) && $penyajian == 'komparatif')<td class="text-end text-muted">{{ number_format($totalAsetTidakLancarPembanding, 0, ',', '.') }}</td>@endif
                    </tr></tfoot>
                </table>
                <div class="border-top border-2 pt-2 mt-3"><table class="table table-sm mb-0"><tr class="fw-bold fs-5">
                    <td>TOTAL ASET</td>
                    <td class="text-end">Rp {{ number_format($totalAset, 0, ',', '.') }}</td>
                    @if(isset($penyajian) && $penyajian == 'komparatif')<td class="text-end text-muted">Rp {{ number_format($totalAsetPembanding, 0, ',', '.') }}</td>@endif
                </tr></table></div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header bg-warning text-dark"><h6 class="mb-0 fw-bold">LIABILITAS & EKUITAS</h6></div>
            <div class="card-body">
                <h6 class="fw-semibold text-body-secondary">Liabilitas Jangka Pendek</h6>
                <table class="table table-sm">
                    <thead><tr><th>Akun</th><th class="text-end">Berjalan</th>@if(isset($penyajian) && $penyajian == 'komparatif')<th class="text-end text-muted">Pembanding</th>@endif</tr></thead>
                    <tbody>
                    @foreach($liabilitasJP as $item)<tr>
                        <td>{{ $item->kode_akun }} - {{ $item->nama_akun }}</td>
                        <td class="text-end">{{ number_format($item->saldo, 0, ',', '.') }}</td>
                        @if(isset($penyajian) && $penyajian == 'komparatif')<td class="text-end text-muted">{{ number_format($item->saldo_pembanding, 0, ',', '.') }}</td>@endif
                    </tr>@endforeach
                    </tbody>
                    <tfoot class="fw-bold border-top"><tr>
                        <td>Total Liabilitas JP</td>
                        <td class="text-end">{{ number_format($totalLiabilitasJP, 0, ',', '.') }}</td>
                        @if(isset($penyajian) && $penyajian == 'komparatif')<td class="text-end text-muted">{{ number_format($totalLiabilitasJPPembanding, 0, ',', '.') }}</td>@endif
                    </tr></tfoot>
                </table>
                <h6 class="fw-semibold text-body-secondary mt-3">Liabilitas Jangka Panjang</h6>
                <table class="table table-sm">
                    <thead><tr><th>Akun</th><th class="text-end">Berjalan</th>@if(isset($penyajian) && $penyajian == 'komparatif')<th class="text-end text-muted">Pembanding</th>@endif</tr></thead>
                    <tbody>
                    @foreach($liabilitasJPj as $item)<tr>
                        <td>{{ $item->kode_akun }} - {{ $item->nama_akun }}</td>
                        <td class="text-end">{{ number_format($item->saldo, 0, ',', '.') }}</td>
                        @if(isset($penyajian) && $penyajian == 'komparatif')<td class="text-end text-muted">{{ number_format($item->saldo_pembanding, 0, ',', '.') }}</td>@endif
                    </tr>@endforeach
                    </tbody>
                    <tfoot class="fw-bold border-top"><tr>
                        <td>Total Liabilitas JPj</td>
                        <td class="text-end">{{ number_format($totalLiabilitasJPj, 0, ',', '.') }}</td>
                        @if(isset($penyajian) && $penyajian == 'komparatif')<td class="text-end text-muted">{{ number_format($totalLiabilitasJPjPembanding, 0, ',', '.') }}</td>@endif
                    </tr></tfoot>
                </table>
                <h6 class="fw-semibold text-body-secondary mt-3">Ekuitas</h6>
                <table class="table table-sm">
                    <thead><tr><th>Akun</th><th class="text-end">Berjalan</th>@if(isset($penyajian) && $penyajian == 'komparatif')<th class="text-end text-muted">Pembanding</th>@endif</tr></thead>
                    <tbody>
                    @foreach($ekuitas as $item)<tr>
                        <td>{{ $item->kode_akun }} - {{ $item->nama_akun }}</td>
                        <td class="text-end">{{ number_format($item->saldo, 0, ',', '.') }}</td>
                        @if(isset($penyajian) && $penyajian == 'komparatif')<td class="text-end text-muted">{{ number_format($item->saldo_pembanding, 0, ',', '.') }}</td>@endif
                    </tr>@endforeach
                    <tr>
                        <td>Laba/Rugi Berjalan</td>
                        <td class="text-end">{{ number_format($labaRugi ?? 0, 0, ',', '.') }}</td>
                        @if(isset($penyajian) && $penyajian == 'komparatif')<td class="text-end text-muted">{{ number_format($labaRugiPembanding ?? 0, 0, ',', '.') }}</td>@endif
                    </tr>
                    </tbody>
                    <tfoot class="fw-bold border-top"><tr>
                        <td>Total Ekuitas</td>
                        <td class="text-end">{{ number_format($totalEkuitas, 0, ',', '.') }}</td>
                        @if(isset($penyajian) && $penyajian == 'komparatif')<td class="text-end text-muted">{{ number_format($totalEkuitasPembanding, 0, ',', '.') }}</td>@endif
                    </tr></tfoot>
                </table>
                <div class="border-top border-2 pt-2 mt-3"><table class="table table-sm mb-0"><tr class="fw-bold fs-5">
                    <td>TOTAL LIABILITAS + EKUITAS</td>
                    <td class="text-end {{ abs($totalAset - $totalLiabilitasEkuitas) < 0.01 ? 'text-success' : 'text-danger' }}">Rp {{ number_format($totalLiabilitasEkuitas, 0, ',', '.') }}</td>
                    @if(isset($penyajian) && $penyajian == 'komparatif')<td class="text-end text-muted">Rp {{ number_format($totalLiabilitasEkuitasPembanding, 0, ',', '.') }}</td>@endif
                </tr></table></div>
            </div>
        </div>
    </div>
</div>
@endsection
