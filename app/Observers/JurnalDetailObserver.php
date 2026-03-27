<?php

namespace App\Observers;

use App\Models\BukuPembantu;
use App\Models\JurnalDetail;
use App\Models\Coa;

class JurnalDetailObserver
{
    /**
     * Handle the JurnalDetail "saved" event.
     */
    public function saved(JurnalDetail $detail): void
    {
        // Only process if it has a contactable
        if (!$detail->contactable_id || !$detail->contactable_type) {
            return;
        }

        $coa = $detail->coa;
        $tipe = null;

        // Check if accounts are AR or AP
        // Based on previous research: 1103-1105 are Piutang, 2101-2105 are Utang
        if (str_starts_with($coa->kode_akun, '1103') || str_starts_with($coa->kode_akun, '1104') || str_starts_with($coa->kode_akun, '1105')) {
            $tipe = 'piutang';
        } elseif (str_starts_with($coa->kode_akun, '21')) { // 21 is usually Payables
            $tipe = 'utang';
        }

        if (!$tipe) {
            return;
        }

        $jumlah = ($tipe === 'piutang') ? ($detail->debit - $detail->kredit) : ($detail->kredit - $detail->debit);

        if ($jumlah == 0) {
            return;
        }

        // Logic split: Addition vs Reduction
        if ($jumlah > 0) {
            // Addition: New receivable/payable or increase
            BukuPembantu::updateOrCreate(
                [
                    'tenant_id' => $detail->jurnalHeader->tenant_id,
                    'jurnal_header_id' => $detail->jurnal_header_id,
                    'contactable_type' => $detail->contactable_type,
                    'contactable_id' => $detail->contactable_id,
                    'tipe' => $tipe,
                ],
                [
                    'tanggal' => $detail->jurnalHeader->tanggal,
                    'tanggal_jatuh_tempo' => $detail->jurnalHeader->tanggal->addDays(30),
                    'jumlah' => abs($jumlah),
                    'keterangan' => $detail->jurnalHeader->deskripsi,
                    'status' => 'pending',
                ]
            );
        } else {
            // Reduction: Payment or correction
            $amountToSettle = abs($jumlah);
            
            // Find oldest pending entries for this contact and type
            // Pending means jumlah > terbayar
            $pendingEntries = BukuPembantu::where('tenant_id', $detail->jurnalHeader->tenant_id)
                ->where('contactable_type', $detail->contactable_type)
                ->where('contactable_id', $detail->contactable_id)
                ->where('tipe', $tipe)
                ->whereColumn('jumlah', '>', 'terbayar')
                ->orderBy('tanggal', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            foreach ($pendingEntries as $entry) {
                if ($amountToSettle <= 0) break;

                $remainingToPay = $entry->jumlah - $entry->terbayar;
                $paymentForThisEntry = min($amountToSettle, $remainingToPay);

                $entry->increment('terbayar', $paymentForThisEntry);
                $entry->update(['settled_by_jurnal_header_id' => $detail->jurnal_header_id]);
                
                if ($entry->terbayar >= $entry->jumlah) {
                    $entry->update(['status' => 'lunas']);

                    // Sync status to source document (Invoice or Ritase)
                    if ($entry->jurnalHeader && $entry->jurnalHeader->referensi) {
                        $ref = $entry->jurnalHeader->referensi;
                        if ($ref instanceof \App\Models\Invoice) {
                            $ref->update(['status' => 'Paid']);
                        } elseif ($ref instanceof \App\Models\Ritase) {
                            $ref->update(['status_invoice' => 'Paid']);
                            // If Ritase belongs to an Invoice, check if we should update Invoice status too
                            if ($ref->invoice) {
                                $allPaid = $ref->invoice->ritase()->where('status_invoice', '!=', 'Paid')->count() == 0;
                                if ($allPaid) {
                                    $ref->invoice->update(['status' => 'Paid']);
                                }
                            }
                        } elseif ($ref instanceof \App\Models\Penjualan) {
                            $ref->update(['status_invoice' => 'Paid']);
                        }
                    }
                }

                $amountToSettle -= $paymentForThisEntry;
            }

            // Optional: Handle overpayment as a negative entry
            if ($amountToSettle > 0) {
                 BukuPembantu::create([
                    'tenant_id' => $detail->jurnalHeader->tenant_id,
                    'jurnal_header_id' => $detail->jurnal_header_id,
                    'contactable_type' => $detail->contactable_type,
                    'contactable_id' => $detail->contactable_id,
                    'tipe' => $tipe,
                    'tanggal' => $detail->jurnalHeader->tanggal,
                    'jumlah' => -$amountToSettle,
                    'terbayar' => 0,
                    'keterangan' => "Lebih Bayar dari JV-{$detail->jurnalHeader->nomor_referensi}",
                    'status' => 'pending',
                ]);
            }
        }
    }

    /**
     * Handle the JurnalDetail "deleted" event.
     */
    public function deleted(JurnalDetail $detail): void
    {
        // 1. Delete associated BP entries (Additions)
        BukuPembantu::where('jurnal_header_id', $detail->jurnal_header_id)
            ->where('contactable_type', $detail->contactable_type)
            ->where('contactable_id', $detail->contactable_id)
            ->delete();

        // 2. Reverse settlement effects (Reductions)
        // Find BP entries that were marked as settled by this header
        // Since we don't know the exact amount per detail without more complex logs,
        // this reversal is best done at the header level or by recalculating.
        // For now, we reset statuses of entries settled by this header.
        $settledEntries = BukuPembantu::where('settled_by_jurnal_header_id', $detail->jurnal_header_id)->get();
        foreach ($settledEntries as $entry) {
            // Reverse sync to source document
            if ($entry->jurnalHeader && $entry->jurnalHeader->referensi) {
                $ref = $entry->jurnalHeader->referensi;
                if ($ref instanceof \App\Models\Invoice) {
                    $ref->update(['status' => 'Sent']); 
                } elseif ($ref instanceof \App\Models\Ritase) {
                    $ref->update(['status_invoice' => ($ref->invoice_id ? 'Sent' : 'Draft')]);
                } elseif ($ref instanceof \App\Models\Penjualan) {
                    $ref->update(['status_invoice' => 'Sent']);
                }
            }

            $entry->update([
                'status' => 'pending',
                'terbayar' => 0, 
                'settled_by_jurnal_header_id' => null
            ]);
        }
    }
}
