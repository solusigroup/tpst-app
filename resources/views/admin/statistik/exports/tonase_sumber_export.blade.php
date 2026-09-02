<html>
<head>
    <meta charset="utf-8">
    <title>Tonase per Sumber — {{ $sumberLabel[$sumber] ?? 'Semua' }}</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 11px; color: #333; }
        h2 { text-align: center; margin-bottom: 4px; font-size: 16px; }
        .subtitle { text-align: center; margin-bottom: 16px; font-size: 12px; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; }
        th { background-color: #f0f4f8; text-align: center; font-weight: 700; font-size: 10px; text-transform: uppercase; }
        td { font-size: 11px; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .text-success { color: #198754; }
        .text-danger { color: #dc3545; }
        .text-primary { color: #0d6efd; }
        .font-monospace { font-family: 'Courier New', monospace; }
        tfoot td { font-weight: 700; background-color: #f8f9fa; }
        .meta-info { margin-bottom: 12px; font-size: 10px; color: #888; text-align: right; }
    </style>
</head>
<body>
    <h2>Laporan Tonase per Sumber</h2>
    <div class="subtitle">
        Sumber: {{ $sumberLabel[$sumber] ?? $sumber }}
        @if($compareYear)
            — Perbandingan Tahun {{ $selectedYear }} vs {{ $compareYear }}
        @else
            — Tahun {{ $selectedYear }}
        @endif
    </div>
    <div class="meta-info">Dicetak: {{ date('d/m/Y H:i') }}</div>

    @if($compareYear)
        <table>
            <thead>
                <tr>
                    <th style="width: 40px;">No</th>
                    <th>Bulan</th>
                    <th class="text-end">Tonase {{ $selectedYear }} (kg)</th>
                    <th class="text-end">Tonase {{ $selectedYear }} (Ton)</th>
                    <th class="text-end">Tonase {{ $compareYear }} (kg)</th>
                    <th class="text-end">Tonase {{ $compareYear }} (Ton)</th>
                    <th class="text-end">Selisih (kg)</th>
                    <th class="text-center">Pertumbuhan (%)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($chartData as $index => $row)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $row['month_name'] }}</td>
                        <td class="text-end font-monospace">{{ number_format($row['tonase_kg'], 2, ',', '.') }}</td>
                        <td class="text-end font-monospace">{{ number_format($row['tonase_ton'], 3, ',', '.') }}</td>
                        <td class="text-end font-monospace">{{ number_format($row['compare_kg'], 2, ',', '.') }}</td>
                        <td class="text-end font-monospace">{{ number_format($row['compare_ton'], 3, ',', '.') }}</td>
                        <td class="text-end font-monospace {{ $row['diff'] >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ $row['diff'] >= 0 ? '+' : '' }}{{ number_format($row['diff'], 2, ',', '.') }}
                        </td>
                        <td class="text-center {{ $row['diff_percent'] >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ $row['diff_percent'] >= 0 ? '+' : '' }}{{ number_format($row['diff_percent'], 1, ',', '.') }}%
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" class="text-center">TOTAL</td>
                    <td class="text-end font-monospace">{{ number_format($totalTonase, 2, ',', '.') }}</td>
                    <td class="text-end font-monospace">{{ number_format($totalTonase / 1000, 3, ',', '.') }}</td>
                    <td class="text-end font-monospace">{{ number_format($totalCompareTonase, 2, ',', '.') }}</td>
                    <td class="text-end font-monospace">{{ number_format($totalCompareTonase / 1000, 3, ',', '.') }}</td>
                    <td class="text-end font-monospace {{ $totalDiff >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ $totalDiff >= 0 ? '+' : '' }}{{ number_format($totalDiff, 2, ',', '.') }}
                    </td>
                    <td class="text-center {{ $totalDiffPercent >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ $totalDiffPercent >= 0 ? '+' : '' }}{{ number_format($totalDiffPercent, 1, ',', '.') }}%
                    </td>
                </tr>
            </tfoot>
        </table>
    @else
        <table>
            <thead>
                <tr>
                    <th style="width: 40px;">No</th>
                    <th>Bulan</th>
                    <th class="text-end">Tonase (kg)</th>
                    <th class="text-end">Tonase (Ton)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($chartData as $index => $row)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $row['month_name'] }}</td>
                        <td class="text-end font-monospace">{{ number_format($row['tonase_kg'], 2, ',', '.') }}</td>
                        <td class="text-end font-monospace">{{ number_format($row['tonase_ton'], 3, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" class="text-center">TOTAL</td>
                    <td class="text-end font-monospace">{{ number_format($totalTonase, 2, ',', '.') }}</td>
                    <td class="text-end font-monospace">{{ number_format($totalTonase / 1000, 3, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    @endif
</body>
</html>
