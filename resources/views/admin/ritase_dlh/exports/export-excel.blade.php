<table>
    <thead>
        <tr>
            <th>No</th>
            <th>No. Tiket</th>
            <th>Tanggal</th>
            <th>Klien</th>
            <th>Armada</th>
            <th>Asal Sampah</th>
            <th>Bruto (kg)</th>
            <th>Tarra (kg)</th>
            <th>Netto (kg)</th>
            <th>Netto (ton)</th>
            <th>Biaya Tipping (Rp)</th>
            <th>Status Invoice</th>
        </tr>
    </thead>
    <tbody>
        @foreach($ritase as $index => $item)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $item->nomor_tiket ?? '-' }}</td>
            <td>{{ $item->waktu_masuk ? \Carbon\Carbon::parse($item->waktu_masuk)->format('d/m/Y H:i') : '-' }}</td>
            <td>{{ $item->klien->nama_klien ?? '-' }}</td>
            <td>{{ $item->armada->plat_nomor ?? '-' }}</td>
            <td>{{ $item->jenis_sampah ?? '-' }}</td>
            <td>{{ $item->berat_bruto }}</td>
            <td>{{ $item->berat_tarra }}</td>
            <td>{{ $item->berat_netto }}</td>
            <td>{{ round($item->berat_netto / 1000, 4) }}</td>
            <td>{{ $item->biaya_tipping }}</td>
            <td>{{ $item->status_invoice ?? 'Unbilled' }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="8" style="text-align: right; font-weight: bold;">TOTAL</td>
            <td style="font-weight: bold;">{{ $totalBeratNetto }}</td>
            <td style="font-weight: bold;">{{ round($totalBeratNetto / 1000, 4) }}</td>
            <td style="font-weight: bold;">{{ $totalBiayaTipping }}</td>
            <td></td>
        </tr>
    </tfoot>
</table>
