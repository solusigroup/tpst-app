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
        $invoice->load(['klien', 'tenant']);
        
        // Fetch related ritase for the client and period
        // Usually, we would filter by month/year of the ritase too
        $ritase = Ritase::where('klien_id', $invoice->klien_id)
            ->whereMonth('waktu_masuk', $invoice->periode_bulan)
            ->whereYear('waktu_masuk', $invoice->periode_tahun)
            ->get();

        $totalTonnage = $ritase->sum('berat_netto');
        
        $monthNames = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
            '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
            '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];
        
        $periodeLabel = $monthNames[$invoice->periode_bulan] . ' ' . $invoice->periode_tahun;

        return view('invoices.print', compact('invoice', 'ritase', 'totalTonnage', 'periodeLabel'));
    }
}
