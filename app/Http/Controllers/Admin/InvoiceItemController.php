<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ritase;
use App\Models\Penjualan;
use Illuminate\Http\Request;

class InvoiceItemController extends Controller
{
    /**
     * Get billable items (Ritase & Penjualan) for a specific client that are not yet invoiced.
     */
    public function getPendingItems(Request $request)
    {
        $klienId = $request->input('klien_id');
        $invoiceId = $request->input('invoice_id'); // If editing

        if (!$klienId) {
            return response()->json(['ritase' => [], 'penjualan' => []]);
        }

        // Fetch unbilled Ritase (or ritase belonging to the current invoice being edited)
        $ritaseQuery = Ritase::where('klien_id', $klienId)
            ->where(function ($q) use ($invoiceId) {
                $q->whereNull('invoice_id');
                if ($invoiceId) {
                    $q->orWhere('invoice_id', $invoiceId);
                }
            })
            // Optional: You might want to filter only specific operational statuses here like 'Selesai'
            ->orderBy('waktu_masuk', 'asc')
            ->select('id', 'nomor_tiket', 'waktu_masuk', 'berat_netto', 'biaya_tipping', 'invoice_id');

        $ritase = $ritaseQuery->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'label' => "{$item->nomor_tiket} (" . $item->waktu_masuk->format('d/m/Y') . ") - {$item->berat_netto} kg - Rp " . number_format($item->biaya_tipping, 0, ',', '.'),
                'price' => $item->biaya_tipping,
                'selected' => $item->invoice_id !== null,
            ];
        });

        // Fetch unbilled Penjualan (or penjualan belonging to the current invoice being edited)
        $penjualanQuery = Penjualan::where('klien_id', $klienId)
            ->where(function ($q) use ($invoiceId) {
                $q->whereNull('invoice_id');
                if ($invoiceId) {
                    $q->orWhere('invoice_id', $invoiceId);
                }
            })
            ->orderBy('tanggal', 'asc')
            ->select('id', 'tanggal', 'jenis_produk', 'berat_kg', 'total_harga', 'jumlah_bayar', 'invoice_id');

        $penjualan = $penjualanQuery->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'label' => "Penjualan: {$item->jenis_produk} (" . $item->tanggal->format('d/m/Y') . ") - {$item->berat_kg} kg - Rp " . number_format($item->total_harga, 0, ',', '.'),
                'price' => $item->total_harga,
                'dp' => $item->jumlah_bayar,
                'selected' => $item->invoice_id !== null,
            ];
        });

        return response()->json([
            'ritase' => $ritase,
            'penjualan' => $penjualan,
        ]);
    }
}
