<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class NeracaSaldoExport implements FromView, ShouldAutoSize
{
    protected array $data;
    protected ?string $dari;
    protected ?string $sampai;

    public function __construct(array $data, ?string $dari, ?string $sampai)
    {
        $this->data = $data;
        $this->dari = $dari;
        $this->sampai = $sampai;
    }

    public function view(): View
    {
        return view('filament.pages.neraca-saldo', [
            'data' => $this->data,
            'dari' => $this->dari,
            'sampai' => $this->sampai,
            'isExport' => true,
        ]);
    }
}
