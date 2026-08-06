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

class RekonsiliasiBankMatchedSheet implements FromArray, WithTitle, WithHeadings, ShouldAutoSize, WithStyles
{
    protected array $matched;

    public function __construct(array $matched)
    {
        $this->matched = $matched;
    }

    public function title(): string
    {
        return 'Cocok Sempurna';
    }

    public function headings(): array
    {
        return [
            // Bank side
            'Tgl Bank',
            'Keterangan Bank',
            'Debit Bank (Rp)',
            'Kredit Bank (Rp)',
            // spacer
            '',
            // Buku side
            'Tgl Buku',
            'No. Referensi',
            'Deskripsi Buku',
            'Debit Buku (Rp)',
            'Kredit Buku (Rp)',
        ];
    }

    public function array(): array
    {
        $rows = [];
        foreach ($this->matched as $match) {
            $bank = $match['bank'];
            $buku = $match['buku'];
            $rows[] = [
                $bank['tanggal'],
                $bank['keterangan'],
                $bank['debit'] > 0 ? $bank['debit'] : '',
                $bank['kredit'] > 0 ? $bank['kredit'] : '',
                '',
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
        $lastRow = count($this->matched) + 1;

        // Header row
        $sheet->getStyle('A1:J1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF16a34a']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Borders on data
        if ($lastRow > 1) {
            $sheet->getStyle("A1:J{$lastRow}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['argb' => 'FFd1fae5'],
                    ],
                ],
            ]);
        }

        // Number format for debit/kredit columns (C, D, I, J)
        if ($lastRow > 1) {
            foreach (['C', 'D', 'I', 'J'] as $col) {
                $sheet->getStyle("{$col}2:{$col}{$lastRow}")
                    ->getNumberFormat()->setFormatCode('#,##0');
            }
        }

        return [];
    }
}
