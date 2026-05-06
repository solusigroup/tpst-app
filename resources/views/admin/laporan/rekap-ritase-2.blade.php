@extends('layouts.admin')

@section('title', 'Laporan Rekap Ritase II')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Laporan Rekap Ritase II</h1>
    </div>
    <div>
        <a href="{{ route('admin.laporan-operasional.rekap-ritase-2', array_merge(request()->all(), ['export' => 'pdf'])) }}" class="btn btn-danger">
            <i class="cil-file text-white"></i> Export PDF
        </a>
        <a href="{{ route('admin.laporan-operasional.rekap-ritase-2', array_merge(request()->all(), ['export' => 'excel'])) }}" class="btn btn-success">
            <i class="cil-spreadsheet text-white"></i> Export Excel
        </a>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header bg-white py-3">
        <h6 class="m-0 font-weight-bold text-primary">Filter Laporan</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.laporan-operasional.rekap-ritase-2') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label for="klien_id" class="form-label">Klien</label>
                <select name="klien_id" id="klien_id" class="form-select ts-select">
                    <option value="">Semua Klien</option>
                    @foreach($kliens as $k)
                        <option value="{{ $k->id }}" {{ $klienId == $k->id ? 'selected' : '' }}>
                            {{ $k->nama_klien }} ({{ $k->jenis }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="bulan" class="form-label">Bulan</label>
                <select name="bulan" id="bulan" class="form-select">
                    @for($i=1; $i<=12; $i++)
                        <option value="{{ sprintf('%02d', $i) }}" {{ $bulan == sprintf('%02d', $i) ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-3">
                <label for="tahun" class="form-label">Tahun</label>
                <select name="tahun" id="tahun" class="form-select">
                    @for($i=date('Y'); $i>=2020; $i--)
                        <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="cil-filter"></i> Filter
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Preview Data Rekap</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th style="font-weight: bold;">KLIEN</th>
                        <th colspan="2">{{ $klien ? $klien->nama_klien : 'Semua Klien' }}</th>
                    </tr>
                    <tr>
                        <th style="font-weight: bold;">JENIS KLIEN</th>
                        <th colspan="2">{{ $klien ? $klien->jenis : '-' }}</th>
                    </tr>
                    <tr>
                        <th style="font-weight: bold;">BULAN</th>
                        <th>{{ \Carbon\Carbon::create()->month((int)$bulan)->translatedFormat('F') }}</th>
                        <th style="font-weight: bold;">Tahun: {{ $tahun }}</th>
                    </tr>
                    <tr>
                        <th colspan="3" class="border-0"></th>
                    </tr>
                    <tr>
                        <th>Row Labels</th>
                        <th>Count of Berat Netto (kg)</th>
                        <th>Sum of Berat Netto (kg)2</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rekapHarian as $row)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}</td>
                        <td>{{ $row->total_ritase }}</td>
                        <td>{{ number_format($row->total_netto, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="text-center">Tidak ada data untuk periode ini</td>
                    </tr>
                    @endforelse
                </tbody>
                @if($rekapHarian->count() > 0)
                <tfoot>
                    <tr class="fw-bold bg-light">
                        <td>Grand Total</td>
                        <td>{{ $grandTotalRitase }}</td>
                        <td>{{ number_format($grandTotalNetto, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.4.1/dist/js/tom-select.complete.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        new TomSelect('.ts-select', {
            create: false,
            sortField: {
                field: "text",
                direction: "asc"
            }
        });
    });
</script>
@endpush
