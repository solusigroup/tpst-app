<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Evaluasi Kinerja (KPI) TPST Megilan</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11pt;
            color: #333;
            line-height: 1.4;
            margin: 0;
            padding: 15px;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header-title {
            text-align: center;
        }
        .header-title h2 {
            margin: 0;
            font-size: 16pt;
            text-transform: uppercase;
            color: #1a4d2e;
        }
        .header-title h4 {
            margin: 4px 0 0 0;
            font-size: 11pt;
            font-weight: normal;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 15px;
            font-size: 10pt;
        }
        .meta-table td {
            padding: 3px 0;
        }
        .kpi-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 9.5pt;
        }
        .kpi-table th, .kpi-table td {
            border: 1px solid #444;
            padding: 6px 8px;
        }
        .kpi-table th {
            background-color: #e8f5e9;
            color: #1b5e20;
            text-align: center;
        }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .fw-bold { font-weight: bold; }
        .notes-box {
            border: 1px solid #999;
            background-color: #f9f9f9;
            padding: 10px;
            margin-bottom: 25px;
            font-size: 9.5pt;
        }
        .signatures-table {
            width: 100%;
            margin-top: 30px;
            page-break-inside: avoid;
            font-size: 10pt;
        }
        .signatures-table td {
            text-align: center;
            vertical-align: top;
            width: 33.33%;
        }
        .signature-space {
            height: 65px;
        }
    </style>
</head>
<body>
    {{-- Header Kop Surat --}}
    <table class="header-table">
        <tr>
            <td class="header-title">
                <h2>TPST MEGILAN LAMONGAN</h2>
                <h4>SISTEM MANAJEMEN KINERJA OPERASIONAL (PERFORMANCE MANAGEMENT SYSTEM)</h4>
                <div style="font-size: 9pt; color: #555; margin-top: 4px;">
                    Operator: PT Tata Bumi Adilimbah &bull; Supervisi: PT Pinastika Bhakti Semesta &bull; DLH Kab. Lamongan
                </div>
            </td>
        </tr>
    </table>

    {{-- Meta Dokumen --}}
    <table class="meta-table">
        <tr>
            <td style="width: 20%;"><strong>Target Penilaian:</strong></td>
            <td style="width: 40%;">{{ strtoupper(str_replace('_', ' ', $kpiEvaluation->jabatan)) }}</td>
            <td style="width: 20%;"><strong>Status Verifikasi:</strong></td>
            <td style="width: 20%; font-weight: bold; color: {{ $kpiEvaluation->status === 'approved' ? '#2e7d32' : '#d32f2f' }};">
                {{ strtoupper($kpiEvaluation->status) }}
            </td>
        </tr>
        <tr>
            <td><strong>Siklus Periode:</strong></td>
            <td>{{ strtoupper($kpiEvaluation->periode_tipe) }}</td>
            <td><strong>Total Skor Akhir:</strong></td>
            <td style="font-weight: bold; font-size: 12pt; color: #1565c0;">
                {{ $kpiEvaluation->total_skor }}% ({{ $kpiEvaluation->predikat }})
            </td>
        </tr>
        <tr>
            <td><strong>Rentang Tanggal:</strong></td>
            <td>{{ $kpiEvaluation->periode_mulai->format('d/m/Y') }} s/d {{ $kpiEvaluation->periode_selesai->format('d/m/Y') }}</td>
            <td><strong>Tanggal Terbit:</strong></td>
            <td>{{ now()->format('d/m/Y') }}</td>
        </tr>
    </table>

    {{-- Tabel Rincian Skor KPI --}}
    <table class="kpi-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 30%;">Indikator KPI</th>
                <th style="width: 18%;">Target</th>
                <th style="width: 25%;">Realisasi Aktual</th>
                <th style="width: 7%;">Nilai (%)</th>
                <th style="width: 7%;">Bobot</th>
                <th style="width: 8%;">Skor</th>
            </tr>
        </thead>
        <tbody>
            @if(!empty($kpiEvaluation->rincian_kpi))
                @foreach($kpiEvaluation->rincian_kpi as $idx => $item)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="fw-bold">{{ $item['nama'] }}</td>
                    <td>{{ $item['target'] }}</td>
                    <td>{{ $item['realisasi'] }}</td>
                    <td class="text-center">{{ $item['nilai'] }}%</td>
                    <td class="text-center">{{ $item['bobot'] }}%</td>
                    <td class="text-end fw-bold">{{ $item['skor'] }}</td>
                </tr>
                @endforeach
            @endif
        </tbody>
        <tfoot>
            <tr style="background-color: #f1f8e9;">
                <td colspan="5" class="text-end fw-bold">TOTAL SKOR TERTINJAU:</td>
                <td class="text-center fw-bold">100%</td>
                <td class="text-end fw-bold" style="color: #1b5e20; font-size: 11pt;">{{ $kpiEvaluation->total_skor }}%</td>
            </tr>
        </tfoot>
    </table>

    {{-- Catatan & Rekomendasi Supervisi PT PBS --}}
    <div class="notes-box">
        <strong>Catatan & Rekomendasi Supervisi Teknis (PT Pinastika Bhakti Semesta):</strong><br>
        <p style="margin: 5px 0 0 0; font-style: italic;">
            {{ $kpiEvaluation->supervisor_notes ?? 'Pencapaian operasional telah diverifikasi sesuai target kebersihan, kontinuitas mesin, tonase intake, dan kepuasan stakeholder.' }}
        </p>
    </div>

    {{-- Lembar Tanda Tangan 3 Pihak --}}
    <table class="signatures-table">
        <tr>
            <td>
                Disusun Oleh,<br>
                <strong>Site Manager TPST Megilan</strong><br>
                PT Tata Bumi Adilimbah
                <div class="signature-space"></div>
                <strong><u>BUDI SUCAHYO</u></strong><br>
                Site Manager
            </td>
            <td>
                Disetujui Oleh,<br>
                <strong>Supervisi Teknis</strong><br>
                PT Pinastika Bhakti Semesta
                <div class="signature-space"></div>
                <strong><u>{{ $kpiEvaluation->approved_by_name ?? 'SUPERVISI TEKNIS' }}</u></strong><br>
                Technical Supervisor
            </td>
            <td>
                Mengetahui,<br>
                <strong>Dinas Lingkungan Hidup</strong><br>
                Kabupaten Lamongan
                <div class="signature-space"></div>
                <strong><u>KEPALA BIDANG PENGELOLAAN SAMPAH</u></strong><br>
                DLH Kab. Lamongan
            </td>
        </tr>
    </table>
</body>
</html>
