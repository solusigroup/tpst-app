<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RekonsiliasiBankUnmatchedBukuSheet implements FromArray, WithTitle, WithHeadings, ShouldAutoSize, WithStyles
{
    protected array $unmatchedBuku;

    public function __construct(array $unmatchedBuku)
    {
        $this->unmatchedBuku = $unmatchedBuku;
    }

    public function title(): string
    {
        return 'Unmatched Buku';
    }

    public function headings(): array
    {
        return [
            'Tanggal Jurnal',
            'No. Referensi',
            'Deskripsi',
            'Debit / Penambahan (Rp)',
            'Kredit / Pengurangan (Rp)',
        ];
    }

    public function array(): array
    {
        $rows = [];
        foreach ($this->unmatchedBuku as $buku) {
            $rows[] = [
                $buku['tanggal'],
                $buku['nomor_referensi'] ?? '',
                $buku['deskripsi'] ?? '',
                $buku['debit'] > 0 ? $buku['debit'] : '',
                $buku['kredit'] > 0 ? $buku['kredit'] : '',
            ];
        }
        return $rows;
    }

    public function styles(Worksheet $sheet): array
    {
        $lastRow = count($this->unmatchedBuku) + 1;

        // Header row
        $sheet->getStyle('A1:E1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF7c3aed']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        if ($lastRow > 1) {
            $sheet->getStyle("A1:E{$lastRow}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['argb' => 'FFede9fe'],
                    ],
                ],
            ]);

            // Number format
            foreach (['D', 'E'] as $col) {
                $sheet->getStyle("{$col}2:{$col}{$lastRow}")
                    ->getNumberFormat()->setFormatCode('#,##0');
            }
        }

        return [];
    }
}
