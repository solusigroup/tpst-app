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

class RekonsiliasiBankUnmatchedBankSheet implements FromArray, WithTitle, WithHeadings, ShouldAutoSize, WithStyles
{
    protected array $unmatchedBank;

    public function __construct(array $unmatchedBank)
    {
        $this->unmatchedBank = $unmatchedBank;
    }

    public function title(): string
    {
        return 'Unmatched Bank';
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Keterangan Mutasi',
            'Tipe',
            'Debit / Keluar (Rp)',
            'Kredit / Masuk (Rp)',
        ];
    }

    public function array(): array
    {
        $rows = [];
        foreach ($this->unmatchedBank as $bank) {
            $rows[] = [
                $bank['tanggal'],
                $bank['keterangan'],
                $bank['kredit'] > 0 ? 'Setoran / Masuk' : 'Tarikan / Keluar',
                $bank['debit'] > 0 ? $bank['debit'] : '',
                $bank['kredit'] > 0 ? $bank['kredit'] : '',
            ];
        }
        return $rows;
    }

    public function styles(Worksheet $sheet): array
    {
        $lastRow = count($this->unmatchedBank) + 1;

        // Header row
        $sheet->getStyle('A1:E1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFdc2626']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        if ($lastRow > 1) {
            $sheet->getStyle("A1:E{$lastRow}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['argb' => 'FFfee2e2'],
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
