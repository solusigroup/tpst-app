<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('jurnal_header', function (Blueprint $table) {
            $table->string('referensi_type')->nullable()->after('tanggal');
            $table->unsignedBigInteger('referensi_id')->nullable()->after('referensi_type');
            $table->index(['referensi_type', 'referensi_id']);
        });

        // Migrate existing data based on prefix
        DB::table('jurnal_header')->orderBy('id')->chunk(100, function ($headers) {
            foreach ($headers as $header) {
                if (str_starts_with($header->nomor_referensi, 'TIP-')) {
                    $nomorTiket = substr($header->nomor_referensi, 4);
                    $ritase = DB::table('ritase')->where('nomor_tiket', $nomorTiket)->first();
                    if ($ritase) {
                        DB::table('jurnal_header')
                            ->where('id', $header->id)
                            ->update([
                                'referensi_type' => 'App\Models\Ritase',
                                'referensi_id' => $ritase->id,
                            ]);
                    }
                } elseif (str_starts_with($header->nomor_referensi, 'SAL-')) {
                    $penjualanId = substr($header->nomor_referensi, 4);
                    DB::table('jurnal_header')
                        ->where('id', $header->id)
                        ->update([
                            'referensi_type' => 'App\Models\Penjualan',
                            'referensi_id' => $penjualanId,
                        ]);
                }
            }
        });

        Schema::table('jurnal_header', function (Blueprint $table) {
            $table->dropColumn('nomor_referensi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jurnal_header', function (Blueprint $table) {
            $table->string('nomor_referensi')->nullable()->after('tanggal');
        });

        // Reverse data migration
        DB::table('jurnal_header')
            ->where('referensi_type', 'App\Models\Ritase')
            ->orderBy('id')->chunk(100, function ($headers) {
                foreach ($headers as $header) {
                    $ritase = DB::table('ritase')->where('id', $header->referensi_id)->first();
                    if ($ritase) {
                        DB::table('jurnal_header')
                            ->where('id', $header->id)
                            ->update([
                                'nomor_referensi' => 'TIP-' . $ritase->nomor_tiket,
                            ]);
                    }
                }
            });

        DB::table('jurnal_header')
            ->where('referensi_type', 'App\Models\Penjualan')
            ->orderBy('id')->chunk(100, function ($headers) {
                foreach ($headers as $header) {
                    DB::table('jurnal_header')
                        ->where('id', $header->id)
                        ->update([
                            'nomor_referensi' => 'SAL-' . $header->referensi_id,
                        ]);
                }
            });

        Schema::table('jurnal_header', function (Blueprint $table) {
            $table->dropIndex(['referensi_type', 'referensi_id']);
            $table->dropColumn(['referensi_type', 'referensi_id']);
        });
    }
};
