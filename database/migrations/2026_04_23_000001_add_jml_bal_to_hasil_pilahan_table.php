<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('hasil_pilahan', function (Blueprint $table) {
            $table->integer('jml_bal')->nullable()->after('tonase');
        });

        // Migrate existing data: extract digits from 'keterangan' (e.g. '5 bal')
        $records = \Illuminate\Support\Facades\DB::table('hasil_pilahan')
            ->whereNotNull('keterangan')
            ->get();
            
        foreach ($records as $record) {
            if (preg_match('/^(\d+)/', trim($record->keterangan), $matches)) {
                \Illuminate\Support\Facades\DB::table('hasil_pilahan')
                    ->where('id', $record->id)
                    ->update(['jml_bal' => (int)$matches[1]]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hasil_pilahan', function (Blueprint $table) {
            $table->dropColumn('jml_bal');
        });
    }
};
