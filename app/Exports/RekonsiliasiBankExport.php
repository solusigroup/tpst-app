<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class RekonsiliasiBankExport implements WithMultipleSheets
{
    use Exportable;

    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function sheets(): array
    {
        return [
            new RekonsiliasiBankRingkasanSheet($this->data),
            new RekonsiliasiBankMatchedSheet($this->data['matched'] ?? []),
            new RekonsiliasiBankPartialSheet($this->data['matchedPartial'] ?? []),
            new RekonsiliasiBankUnmatchedBankSheet($this->data['unmatchedBank'] ?? []),
            new RekonsiliasiBankUnmatchedBukuSheet($this->data['unmatchedBuku'] ?? []),
        ];
    }
}
