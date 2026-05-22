<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Ritase;
use Illuminate\Http\Request;
use Carbon\Carbon;

class InvoiceController extends Controller
{
    public function print(Invoice $invoice)
    {
        $invoice->load(['klien', 'tenant', 'ritase', 'penjualan']);
        
        $totalTonnageRitase = $invoice->ritase->sum('berat_netto');
        $totalTonnagePenjualan = $invoice->penjualan->sum('berat_kg');
        
        $periodeLabel = \App\Helpers\DateHelper::indonesianMonthName($invoice->periode_bulan) . ' ' . $invoice->periode_tahun;

        return view('invoices.print', compact('invoice', 'totalTonnageRitase', 'totalTonnagePenjualan', 'periodeLabel'));
    }
}
