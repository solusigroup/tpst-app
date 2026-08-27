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
        $klienId   = $request->input('klien_id');
        $invoiceId = $request->input('invoice_id'); // If editing
        $bulan     = $request->input('periode_bulan');
        $tahun     = $request->input('periode_tahun');
        $showAll   = $request->boolean('show_all', false);

        if (!$klienId) {
            return response()->json(['ritase' => [], 'penjualan' => []]);
        }

        // Fetch unbilled Ritase (or ritase belonging to the current invoice being edited)
        // When period filter is active, only show items matching the selected month/year
        $ritaseQuery = Ritase::where('klien_id', $klienId)
            ->where('is_approved', 1)
            ->where(function ($q) use ($invoiceId, $bulan, $tahun, $showAll) {
                // Always show items already attached to this invoice (when editing)
                if ($invoiceId) {
                    $q->where('invoice_id', $invoiceId);
                }

                // Add unbilled items, optionally filtered by period
                $q->orWhere(function ($unbilled) use ($bulan, $tahun, $showAll) {
                    $unbilled->whereNull('invoice_id');
                    if (!$showAll && $bulan && $tahun) {
                        $unbilled->whereMonth('waktu_masuk', $bulan)
                                 ->whereYear('waktu_masuk', $tahun);
                    }
                });
            })
            ->orderBy('waktu_masuk', 'asc')
            ->select('id', 'nomor_tiket', 'waktu_masuk', 'berat_netto', 'biaya_tipping', 'invoice_id');

        $ritase = $ritaseQuery->get()->map(function ($item) use ($bulan, $tahun, $showAll) {
            $inPeriod = true;
            if (!$showAll && $bulan && $tahun) {
                $inPeriod = ((int) $item->waktu_masuk->format('m') === (int) $bulan
                          && (int) $item->waktu_masuk->format('Y') === (int) $tahun);
            }
            return [
                'id' => $item->id,
                'label' => "{$item->nomor_tiket} (" . $item->waktu_masuk->format('d/m/Y') . ") - {$item->berat_netto} kg - Rp " . number_format($item->biaya_tipping, 0, ',', '.'),
                'price' => $item->biaya_tipping,
                'selected' => $item->invoice_id !== null,
                'in_period' => $inPeriod,
            ];
        });

        // Fetch unbilled Penjualan (or penjualan belonging to the current invoice being edited)
        $penjualanQuery = Penjualan::where('klien_id', $klienId)
            ->where(function ($q) use ($invoiceId, $bulan, $tahun, $showAll) {
                if ($invoiceId) {
                    $q->where('invoice_id', $invoiceId);
                }

                $q->orWhere(function ($unbilled) use ($bulan, $tahun, $showAll) {
                    $unbilled->whereNull('invoice_id');
                    if (!$showAll && $bulan && $tahun) {
                        $unbilled->whereMonth('tanggal', $bulan)
                                 ->whereYear('tanggal', $tahun);
                    }
                });
            })
            ->orderBy('tanggal', 'asc')
            ->select('id', 'tanggal', 'jenis_produk', 'berat_kg', 'total_harga', 'jumlah_bayar', 'invoice_id');

        $penjualan = $penjualanQuery->get()->map(function ($item) use ($bulan, $tahun, $showAll) {
            $inPeriod = true;
            if (!$showAll && $bulan && $tahun) {
                $inPeriod = ((int) $item->tanggal->format('m') === (int) $bulan
                          && (int) $item->tanggal->format('Y') === (int) $tahun);
            }
            return [
                'id' => $item->id,
                'label' => "Penjualan: {$item->jenis_produk} (" . $item->tanggal->format('d/m/Y') . ") - {$item->berat_kg} kg - Rp " . number_format($item->total_harga, 0, ',', '.'),
                'price' => $item->total_harga,
                'dp' => $item->jumlah_bayar,
                'selected' => $item->invoice_id !== null,
                'in_period' => $inPeriod,
            ];
        });

        return response()->json([
            'ritase' => $ritase,
            'penjualan' => $penjualan,
        ]);
    }
}
