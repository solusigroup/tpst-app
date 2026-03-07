<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ArusKasExport implements FromView, ShouldAutoSize
{
    protected array $data;
    protected string $tahun;

    public function __construct(array $data, string $tahun)
    {
        $this->data = $data;
        $this->tahun = $tahun;
    }

    public function view(): View
    {
        return view('filament.pages.arus-kas', [
            'data' => $this->data,
            'tahun' => $this->tahun,
            'isExport' => true,
        ]);
    }
}
