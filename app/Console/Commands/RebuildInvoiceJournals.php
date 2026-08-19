<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Invoice;
use App\Models\JurnalHeader;
use App\Models\JurnalDetail;
use App\Models\BukuPembantu;
use App\Observers\InvoiceObserver;
use App\Observers\JurnalDetailObserver;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RebuildInvoiceJournals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:rebuild-invoice-journals 
                            {--tenant= : Filter berdasarkan ID tenant}
                            {--invoice= : Filter invoice tertentu berdasarkan ID}
                            {--force : Jalankan langsung tanpa konfirmasi}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Membangun dan memperbaiki ulang seluruh jurnal invoice (Sent & Paid) serta buku pembantu sesuai aturan COA terbaru.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->warn('================================================================');
        $this->warn(' REBUILD JURNAL INVOICE & SINKRONISASI BUKU PEMBANTU');
        $this->warn('================================================================');
        $this->info('Perintah ini akan:');
        $this->info('1. Memulihkan status invoice yang sebelumnya sudah PAID.');
        $this->info('2. Memperbarui jurnal untuk seluruh invoice berstatus SENT (Piutang ↔ Pendapatan).');
        $this->info('3. Memperbarui jurnal kedua untuk invoice berstatus PAID (Bank Jatim ↔ Piutang).');
        $this->info('4. Membangun ulang Buku Pembantu piutang agar sinkron 100% dengan jurnal.');
        $this->newLine();

        if (!$this->option('force') && !$this->confirm('Apakah Anda yakin ingin melanjutkan proses rebuild ini?')) {
            $this->info('Proses dibatalkan.');
            return 0;
        }

        // 1. Restore known and verified Paid invoices that were accidentally changed to Sent
        $knownPaidIds = [6, 8, 18, 32, 34, 35, 36, 42, 57, 58, 61, 62, 63, 66, 67, 70, 73, 74, 75, 76, 77, 78, 79, 81, 83, 87, 88];
        Invoice::withoutGlobalScopes()
            ->whereIn('id', $knownPaidIds)
            ->update(['status' => 'Paid']);

        // Also restore any invoices whose linked items are marked Paid
        Invoice::withoutGlobalScopes()
            ->where('status', '!=', 'Paid')
            ->where(function($q) {
                $q->whereHas('ritase', function($r) {
                    $r->where('status_invoice', 'Paid');
                })->orWhereHas('penjualan', function($p) {
                    $p->where('status_invoice', 'Paid');
                });
            })
            ->update(['status' => 'Paid']);

        $query = Invoice::withoutGlobalScopes()->with(['klien', 'ritase', 'penjualan']);

        if ($tenantId = $this->option('tenant')) {
            $query->where('tenant_id', $tenantId);
        }

        if ($invoiceId = $this->option('invoice')) {
            $query->where('id', $invoiceId);
        }

        $invoices = $query->orderBy('tanggal_invoice', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $totalInvoices = $invoices->count();

        if ($totalInvoices === 0) {
            $this->warn('Tidak ada invoice yang ditemukan.');
            return 0;
        }

        $this->info("Ditemukan {$totalInvoices} invoice untuk diproses.");
        $progressBar = $this->output->createProgressBar($totalInvoices);
        $progressBar->start();

        $observer = new InvoiceObserver();
        $processed = 0;
        $errors = 0;

        foreach ($invoices as $invoice) {
            try {
                // Trigger the observer to cleanly recreate journals according to new rules
                $observer->saved($invoice);
                $processed++;
            } catch (\Exception $e) {
                $errors++;
                $this->error("\nError pada Invoice ID {$invoice->id} ({$invoice->nomor_invoice}): " . $e->getMessage());
            }
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        // Rebuild Buku Pembantu to ensure 100% chronological settlement consistency
        $this->info('Menyinkronkan ulang Buku Pembantu dari Jurnal Detail terbaru...');
        $this->rebuildBukuPembantu();

        $this->info("================================================================");
        $this->info(" REBUILD SELESAI!");
        $this->info(" Total Invoice Diproses : {$processed}");
        if ($errors > 0) {
            $this->warn(" Total Error            : {$errors}");
        }
        $this->info("================================================================");

        return 0;
    }

    /**
     * Rebuild Buku Pembantu ledger entries from all Jurnal Details.
     */
    private function rebuildBukuPembantu(): void
    {
        Schema::disableForeignKeyConstraints();
        BukuPembantu::truncate();
        Schema::enableForeignKeyConstraints();

        $details = JurnalDetail::withoutGlobalScopes()
            ->join('jurnal_header', 'jurnal_detail.jurnal_header_id', '=', 'jurnal_header.id')
            ->orderBy('jurnal_header.tanggal', 'asc')
            ->orderBy('jurnal_detail.id', 'asc')
            ->select('jurnal_detail.*')
            ->get();

        $observer = new JurnalDetailObserver();

        DB::transaction(function () use ($details, $observer) {
            foreach ($details as $detail) {
                $detail->loadMissing(['jurnalHeader', 'coa']);
                $observer->saved($detail);
            }
        });
    }
}
