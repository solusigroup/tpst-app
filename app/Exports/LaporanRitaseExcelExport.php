<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class LaporanRitaseExcelExport implements FromView, WithEvents
{
    protected array $data;
    protected array $requestParams;

    public function __construct(array $data, array $requestParams = [])
    {
        $this->data = array_merge($data, ['isExport' => true]);
        $this->requestParams = $requestParams;
    }

    public function view(): View
    {
        return view('admin.laporan.exports.ritase-export', $this->data);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $rows = $this->data['rows'] ?? [];

                // Find header row for main table by inspecting Column A / B
                $startDataRow = 0;
                $highestRow = $sheet->getHighestRow();
                for ($r = 1; $r <= $highestRow; $r++) {
                    $cellVal = trim((string) $sheet->getCell('A' . $r)->getValue());
                    $cellBVal = trim((string) $sheet->getCell('B' . $r)->getValue());
                    if ($cellVal === 'No' || $cellBVal === 'Tanggal') {
                        $startDataRow = $r + 1;
                        break;
                    }
                }

                if ($startDataRow <= 0) {
                    return;
                }

                // Determine image dimensions in mm or px from request parameters
                // Default width: 35mm (~132px), Default height: 30mm (~113px)
                $widthMm = (float) ($this->requestParams['foto_w'] ?? 35);
                $heightMm = (float) ($this->requestParams['foto_h'] ?? 30);

                // Convert mm to pixels at standard 96 DPI (1 mm = 3.7795 px)
                $targetWidthPx = (int) round($widthMm * 3.7795);
                $targetHeightPx = (int) round($heightMm * 3.7795);

                // Calculate Excel column width & row height in points
                // Approx 1 Excel column width unit = 7.5 to 8 pixels
                $colWidthUnits = max(15, (float) round($targetWidthPx / 7.5, 1));
                // 1 pixel = 0.75 points for Excel row height
                $rowHeightPt = max(35, (float) round($targetHeightPx * 0.75, 1));

                // Set column dimensions for photo columns O, P, Q
                $sheet->getColumnDimension('O')->setWidth($colWidthUnits);
                $sheet->getColumnDimension('P')->setWidth($colWidthUnits);
                $sheet->getColumnDimension('Q')->setWidth($colWidthUnits);

                foreach ($rows as $index => $item) {
                    $currentRow = $startDataRow + $index;
                    $hasAnyPhoto = false;

                    $photoFields = [
                        'O' => $item->foto_tiket_bruto,
                        'P' => $item->foto_tiket_tarra,
                        'Q' => $item->foto_tiket,
                    ];

                    foreach ($photoFields as $col => $relativePath) {
                        if ($relativePath && Storage::disk('public')->exists($relativePath)) {
                            $fullPath = Storage::disk('public')->path($relativePath);
                            if (file_exists($fullPath)) {
                                $hasAnyPhoto = true;
                                $drawing = new Drawing();
                                $drawing->setName('Foto ' . $col . ' ' . ($index + 1));
                                $drawing->setDescription('Foto Ritase');
                                $drawing->setPath($fullPath);
                                $drawing->setCoordinates($col . $currentRow);

                                // Set dimensions
                                if ($heightMm > 0) {
                                    $drawing->setHeight($targetHeightPx);
                                }
                                if ($widthMm > 0 && isset($this->requestParams['foto_w'])) {
                                    $drawing->setWidth($targetWidthPx);
                                }

                                $drawing->setOffsetY(4);
                                $drawing->setOffsetX(4);
                                $drawing->setWorksheet($sheet);
                            }
                        }
                    }

                    if ($hasAnyPhoto) {
                        $sheet->getRowDimension($currentRow)->setRowHeight($rowHeightPt);
                    } else {
                        $sheet->getRowDimension($currentRow)->setRowHeight(25);
                    }
                }

                // Center align all data cells vertically
                $lastDataRow = $startDataRow + count($rows) - 1;
                if ($lastDataRow >= $startDataRow) {
                    $sheet->getStyle("A{$startDataRow}:Q{$lastDataRow}")
                        ->getAlignment()
                        ->setVertical(Alignment::VERTICAL_CENTER);
                }
            },
        ];
    }
}
