<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PosisiKeuanganExport implements FromView, ShouldAutoSize
{
    protected array $data;
    protected string $sampai;

    public function __construct(array $data, string $sampai)
    {
        $this->data = $data;
        $this->sampai = $sampai;
    }

    public function view(): View
    {
        return view('filament.pages.posisi-keuangan', [
            'data' => $this->data,
            'sampai' => $this->sampai,
            'isExport' => true,
        ]);
    }
}
