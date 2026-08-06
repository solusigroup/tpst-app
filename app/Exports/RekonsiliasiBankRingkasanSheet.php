<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RekonsiliasiBankRingkasanSheet implements FromArray, WithTitle, ShouldAutoSize, WithStyles
{
    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function title(): string
    {
        return 'Ringkasan';
    }

    public function array(): array
    {
        $stats  = $this->data['stats']  ?? [];
        $coa    = $this->data['coa']    ?? null;
        $dari   = $this->data['dari']   ?? null;
        $sampai = $this->data['sampai'] ?? null;

        $coaNama    = $coa ? ($coa['kode_akun'] . ' - ' . $coa['nama_akun']) : '-';
        $periodeStr = ($dari ?? '-') . ' s/d ' . ($sampai ?? '-');

        return [
            ['REKONSILIASI BANK JATIM', ''],
            ['Akun (COA)', $coaNama],
            ['Periode', $periodeStr],
            ['Tanggal Export', now()->format('d/m/Y H:i')],
            ['', ''],
            ['STATISTIK', 'NILAI'],
            ['Total Mutasi Bank (Transaksi)', $stats['total_bank_transaksi'] ?? 0],
            ['Total Jurnal Buku (Transaksi)', $stats['total_buku_transaksi'] ?? 0],
            ['Total Debit Bank', 'Rp ' . number_format($stats['total_bank_debit'] ?? 0, 0, ',', '.')],
            ['Total Kredit Bank', 'Rp ' . number_format($stats['total_bank_kredit'] ?? 0, 0, ',', '.')],
            ['Total Debit Buku', 'Rp ' . number_format($stats['total_buku_debit'] ?? 0, 0, ',', '.')],
            ['Total Kredit Buku', 'Rp ' . number_format($stats['total_buku_kredit'] ?? 0, 0, ',', '.')],
            ['', ''],
            ['Transaksi Cocok Sempurna', $stats['matched_count'] ?? 0],
            ['Transaksi Selisih Tanggal', $stats['partial_count'] ?? 0],
            ['Transaksi Unmatched (Bank)', $stats['unmatched_bank_count'] ?? 0],
            ['Transaksi Unmatched (Buku)', $stats['unmatched_buku_count'] ?? 0],
            ['Tingkat Kecocokan (%)', ($stats['match_rate'] ?? 0) . '%'],
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        // Title row
        $sheet->mergeCells('A1:B1');
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 14, 'color' => ['argb' => 'FFFFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1E40AF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Section header row: STATISTIK | NILAI (row 6)
        $sheet->getStyle('A6:B6')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1E40AF']],
        ]);

        // Info rows A2:A4
        $sheet->getStyle('A2:A4')->getFont()->setBold(true);

        // Alternating light blue for stats rows 7–18
        foreach ([7, 9, 11, 14, 16, 18] as $row) {
            $sheet->getStyle("A{$row}:B{$row}")->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFdbeafe']],
            ]);
        }

        // Thin border on data area
        $sheet->getStyle('A6:B18')->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['argb' => 'FFbfdbfe'],
                ],
            ],
        ]);

        return [];
    }
}
