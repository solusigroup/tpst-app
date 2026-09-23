<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Temuan Anomali Data</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #000000;
            padding: 6px 8px;
            font-family: Arial, sans-serif;
            font-size: 11px;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }
        .text-center { text-align: center; }
        .danger { color: #cc0000; font-weight: bold; }
        .warning { color: #d97706; font-weight: bold; }
    </style>
</head>
<body>
    <h3>LAPORAN TEMUAN ANOMALI ENTRY DATA OPERASIONAL TPST</h3>
    <p>Tanggal Cetak: {{ date('d/m/Y H:i') }} | Total Temuan: {{ count($anomalies) }}</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tingkat Keparahan</th>
                <th>Modul</th>
                <th>Tanggal</th>
                <th>No. Referensi / Tiket</th>
                <th>Judul Anomali</th>
                <th>Uraian & Penjelasan Anomali</th>
            </tr>
        </thead>
        <tbody>
            @foreach($anomalies as $index => $item)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td class="text-center {{ $item['severity'] === 'danger' ? 'danger' : 'warning' }}">
                        {{ strtoupper($item['severity']) }}
                    </td>
                    <td>{{ $item['module'] }}</td>
                    <td class="text-center">{{ $item['date'] }}</td>
                    <td>{{ $item['ref_number'] }}</td>
                    <td>{{ $item['title'] }}</td>
                    <td>{{ $item['description'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
