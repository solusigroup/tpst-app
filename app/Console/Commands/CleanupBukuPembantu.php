<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BukuPembantu;

class CleanupBukuPembantu extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cleanup:buku-pembantu';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Membersihkan data Buku Pembantu yang nyangkut (yatim/orphaned) akibat penghapusan jurnal sebelumnya.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Mencari data Buku Pembantu yang nyangkut...');

        // Disable global scopes in case tenant_id is needed, though for CLI it should run without tenant scope since auth is not set.
        $orphans = BukuPembantu::withoutGlobalScopes()->whereDoesntHave('jurnalHeader')->get();
        $count = $orphans->count();

        if ($count === 0) {
            $this->info('Tidak ditemukan data yang nyangkut. Database sudah bersih!');
            return;
        }

        $this->warn("Ditemukan {$count} data Buku Pembantu yang nyangkut.");

        if ($this->confirm('Apakah Anda yakin ingin menghapus data-data tersebut?')) {
            $deletedCount = 0;
            foreach ($orphans as $orphan) {
                $orphan->delete();
                $deletedCount++;
            }
            $this->info("Berhasil menghapus {$deletedCount} data Buku Pembantu yang nyangkut.");
        } else {
            $this->info('Proses dibatalkan.');
        }
    }
}
