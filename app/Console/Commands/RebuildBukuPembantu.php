<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BukuPembantu;
use App\Models\JurnalDetail;
use App\Observers\JurnalDetailObserver;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class RebuildBukuPembantu extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rebuild:buku-pembantu';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Membangun ulang data Buku Pembantu (Piutang & Utang) secara kronologis dari detail jurnal yang ada.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->warn('PERINGATAN: Perintah ini akan mengosongkan tabel buku_pembantu dan mengisinya kembali dari awal berdasarkan data Jurnal Detail.');
        
        if (!$this->confirm('Apakah Anda yakin ingin melanjutkan proses rebuild ini?')) {
            $this->info('Proses dibatalkan.');
            return;
        }

        $this->info('Mengosongkan tabel buku_pembantu...');
        Schema::disableForeignKeyConstraints();
        BukuPembantu::truncate();
        Schema::enableForeignKeyConstraints();

        $this->info('Mengambil data Jurnal Detail secara kronologis...');
        // Query details without tenant scope to rebuild all tenants if multi-tenant
        $details = JurnalDetail::withoutGlobalScopes()
            ->join('jurnal_header', 'jurnal_detail.jurnal_header_id', '=', 'jurnal_header.id')
            ->orderBy('jurnal_header.tanggal', 'asc')
            ->orderBy('jurnal_detail.id', 'asc')
            ->select('jurnal_detail.*')
            ->get();

        $count = $details->count();
        $this->info("Memproses {$count} data Jurnal Detail...");

        $observer = new JurnalDetailObserver();
        $processed = 0;

        DB::transaction(function () use ($details, $observer, &$processed) {
            foreach ($details as $detail) {
                // Ensure relations are loaded
                $detail->loadMissing(['jurnalHeader', 'coa']);
                $observer->saved($detail);
                $processed++;
            }
        });

        $this->info("Sukses! Berhasil membangun ulang Buku Pembantu dari {$processed} baris jurnal.");
    }
}
