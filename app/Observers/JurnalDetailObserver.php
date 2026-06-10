<?php

namespace App\Observers;

use App\Models\BukuPembantu;
use App\Models\JurnalDetail;
use App\Models\Coa;
use Illuminate\Support\Facades\DB;

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

        // Check if accounts are AR or AP dynamically
        if ($coa->tipe === 'Asset' && (in_array($coa->kategori_buku_pembantu, ['piutang_dlh', 'piutang_swasta', 'piutang_offtaker']) || (str_contains(strtolower($coa->nama_akun), 'piutang') && !str_contains(strtolower($coa->nama_akun), 'uang muka')))) {
            $tipe = 'piutang';
        } elseif ($coa->tipe === 'Liability' && ($coa->kategori_buku_pembantu === 'utang_usaha' || str_contains(strtolower($coa->nama_akun), 'utang usaha') || str_contains(strtolower($coa->nama_akun), 'hutang usaha') || str_starts_with($coa->kode_akun, '2101'))) {
            $tipe = 'utang';
        }

        if (!$tipe) {
            return;
        }

        $jumlah = ($tipe === 'piutang') ? ($detail->debit - $detail->kredit) : ($detail->kredit - $detail->debit);

        if ($jumlah == 0) {
            return;
        }

        DB::transaction(function () use ($detail, $tipe, $jumlah) {
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
                    ->lockForUpdate() // Mencegah race condition (double submit)
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
        });
    }

    /**
     * Handle the JurnalDetail "deleted" event.
     */
    public function deleted(JurnalDetail $detail): void
    {
        if (!$detail->contactable_id || !$detail->contactable_type) {
            return;
        }

        $coa = $detail->coa;
        $tipe = null;

        if ($coa->tipe === 'Asset' && (in_array($coa->kategori_buku_pembantu, ['piutang_dlh', 'piutang_swasta', 'piutang_offtaker']) || (str_contains(strtolower($coa->nama_akun), 'piutang') && !str_contains(strtolower($coa->nama_akun), 'uang muka')))) {
            $tipe = 'piutang';
        } elseif ($coa->tipe === 'Liability' && ($coa->kategori_buku_pembantu === 'utang_usaha' || str_contains(strtolower($coa->nama_akun), 'utang usaha') || str_contains(strtolower($coa->nama_akun), 'hutang usaha') || str_starts_with($coa->kode_akun, '2101'))) {
            $tipe = 'utang';
        }

        if (!$tipe) {
            return;
        }

        $jumlah = ($tipe === 'piutang') ? ($detail->debit - $detail->kredit) : ($detail->kredit - $detail->debit);

        if ($jumlah == 0) {
            return;
        }

        DB::transaction(function () use ($detail, $tipe, $jumlah) {
            if ($jumlah > 0) {
                // 1. Delete associated BP entries (Additions)
                BukuPembantu::where('jurnal_header_id', $detail->jurnal_header_id)
                    ->where('contactable_type', $detail->contactable_type)
                    ->where('contactable_id', $detail->contactable_id)
                    ->delete();
            } else {
                // 2. Reverse settlement effects (Reductions)
                $amountToReverse = abs($jumlah);

                // Hapus entry "Lebih Bayar" jika ada yang berasal dari pembayaran ini
                $lebihBayar = BukuPembantu::where('jurnal_header_id', $detail->jurnal_header_id)
                    ->where('contactable_type', $detail->contactable_type)
                    ->where('contactable_id', $detail->contactable_id)
                    ->where('tipe', $tipe)
                    ->where('jumlah', '<', 0)
                    ->first();

                if ($lebihBayar) {
                    $amountToReverse -= abs($lebihBayar->jumlah);
                    $lebihBayar->delete();
                }

                if ($amountToReverse <= 0) {
                    return;
                }

                // Kurangi terbayar pada entry yang sudah dibayar secara LIFO
                $paidEntries = BukuPembantu::where('contactable_type', $detail->contactable_type)
                    ->where('contactable_id', $detail->contactable_id)
                    ->where('tipe', $tipe)
                    ->where('terbayar', '>', 0)
                    ->orderBy('id', 'desc')
                    ->lockForUpdate()
                    ->get();

                foreach ($paidEntries as $entry) {
                    if ($amountToReverse <= 0) break;

                    $reduction = min($amountToReverse, $entry->terbayar);
                    $entry->decrement('terbayar', $reduction);
                    
                    if ($entry->terbayar < $entry->jumlah) {
                        $entry->update([
                            'status' => 'pending', 
                            'settled_by_jurnal_header_id' => null
                        ]);
                        
                        // Reverse sync to source document
                        if ($entry->jurnalHeader && $entry->jurnalHeader->referensi) {
                            $ref = $entry->jurnalHeader->referensi;
                            if ($ref instanceof \App\Models\Invoice) {
                                $ref->update(['status' => 'Sent']); 
                            } elseif ($ref instanceof \App\Models\Ritase) {
                                $ref->update(['status_invoice' => ($ref->invoice_id ? 'Sent' : 'Draft')]);
                                if ($ref->invoice) {
                                    $ref->invoice->update(['status' => 'Sent']);
                                }
                            } elseif ($ref instanceof \App\Models\Penjualan) {
                                $ref->update(['status_invoice' => 'Sent']);
                            }
                        }
                    }
                    $amountToReverse -= $reduction;
                }
            }
        });
    }
}
