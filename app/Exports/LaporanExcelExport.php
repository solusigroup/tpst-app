<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class LaporanExcelExport implements FromView, ShouldAutoSize
{
    protected string $viewName;
    protected array $data;

    public function __construct(string $viewName, array $data)
    {
        $this->viewName = $viewName;
        // set isExport to true so the blade view knows it's being rendered for Excel
        $this->data = array_merge($data, ['isExport' => true]);
    }

    public function view(): View
    {
        return view($this->viewName, $this->data);
    }
}
