<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Klien;
use App\Models\Ritase;
use App\Models\Invoice;
use App\Models\Penjualan;
use Illuminate\Support\Facades\DB;

class RecalculateSwastaInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:recalculate-swasta-invoices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate all tipping fees and invoice totals for Swasta clients based on current tariff settings';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting recalculation for Swasta clients...');

        $kliens = Klien::where('jenis', 'Swasta')->get();

        if ($kliens->isEmpty()) {
            $this->warn('No Swasta clients found.');
            return;
        }

        $this->info('Found ' . $kliens->count() . ' Swasta clients.');

        foreach ($kliens as $klien) {
            $this->info("Processing Klien: {$klien->nama_klien} (Tarif: {$klien->jenis_tarif} - Rp {$klien->besaran_tarif})");

            DB::transaction(function () use ($klien) {
                // 1. Recalculate all Ritase for this client
                $ritases = Ritase::where('klien_id', $klien->id)->get();
                $this->comment("  - Updating {$ritases->count()} ritase records...");
                foreach ($ritases as $ritase) {
                    $ritase->save(); // Triggers the saving hook in model
                }

                // 2. Recalculate all Invoices for this client
                $invoices = Invoice::where('klien_id', $klien->id)->get();
                $this->comment("  - Updating {$invoices->count()} invoices...");
                foreach ($invoices as $invoice) {
                    $totalRitase = Ritase::where('invoice_id', $invoice->id)->sum('biaya_tipping');
                    $totalPenjualan = Penjualan::where('invoice_id', $invoice->id)->sum('total_harga');
                    $totalUangMuka = Penjualan::where('invoice_id', $invoice->id)->sum('jumlah_bayar');

                    $feeBulanan = ($klien->jenis_tarif === 'Bulanan') 
                        ? ($klien->besaran_tarif ?? 0) 
                        : 0;

                    $invoice->update([
                        'total_tagihan' => $totalRitase + $totalPenjualan + $feeBulanan,
                        'uang_muka' => $totalUangMuka,
                    ]);
                }
            });
        }

        $this->info('Recalculation completed successfully!');
    }
}
