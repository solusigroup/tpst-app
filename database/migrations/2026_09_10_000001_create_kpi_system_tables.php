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
        Schema::dropIfExists('kpi_evaluations');
        Schema::dropIfExists('kpi_machine_activity_logs');
        Schema::dropIfExists('kpi_stakeholder_complaints');
        Schema::dropIfExists('kpi_daily_checklists');
        Schema::dropIfExists('kpi_masters');

        // 1. KPI Masters (Definitions, Weights, Target Formulas)
        Schema::create('kpi_masters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
            $table->string('kode_kpi', 50)->unique();
            $table->string('nama_kpi');
            $table->enum('level', ['global', 'jabatan', 'pemilah'])->default('jabatan');
            $table->string('jabatan_target')->nullable(); // e.g. 'site_manager', 'admin_commercial', 'equipment_logistik', 'operasional_qc', 'pemilah'
            $table->string('kategori')->default('operasional'); // operasional, keuangan, mesin, kebersihan, sdm, dll.
            $table->enum('metric_type', ['positif', 'negatif'])->default('positif');
            $table->decimal('default_target', 12, 2)->default(100.00);
            $table->string('satuan', 50)->default('%'); // ton/hari, Rp/bulan, %, jam, kasus
            $table->decimal('bobot', 5, 2)->default(10.00); // 0 - 100%
            $table->string('formula_type')->default('ratio'); // ratio, penalty, checklist, availability
            $table->text('deskripsi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Daily Checklist (Kebersihan & Bau Area TPST - Ana & Agung)
        Schema::create('kpi_daily_checklists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
            $table->date('tanggal');
            $table->string('area'); // Receiving, Conveyor, Sortir, Halaman/Luar
            $table->enum('status_kebersihan', ['Bersih', 'Kurang Bersih', 'Kotor'])->default('Bersih');
            $table->boolean('ada_tumpukan_sampah')->default(false);
            $table->enum('status_bau', ['Tidak Bau', 'Bau Ringan', 'Bau Berat'])->default('Tidak Bau');
            $table->integer('skor_kebersihan')->default(100); // 100, 70, 0
            $table->integer('skor_bau')->default(100); // 100 (Tidak Bau), 70 (Ringan), 0 (Berat)
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // Petugas PIC input
            $table->string('foto_bukti')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'tanggal']);
        });

        // 3. Stakeholder Complaint Log (DLH, Tossa/Penggerobak, Klien Swasta, Warga - Budi & Nita)
        Schema::create('kpi_stakeholder_complaints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
            $table->date('tanggal');
            $table->enum('stakeholder_type', ['DLH', 'Penggerobak/Desa', 'Klien Swasta', 'Warga/Masyarakat', 'Lainnya'])->default('DLH');
            $table->string('nama_pelapor')->nullable();
            $table->string('kontak_pelapor')->nullable();
            $table->text('isi_keluhan');
            $table->enum('tingkat_urgensi', ['Rendah', 'Sedang', 'Tinggi'])->default('Sedang');
            $table->enum('status_penanganan', ['Open', 'Proses', 'Resolved'])->default('Open');
            $table->text('tindakan_perbaikan')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->foreignId('handled_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['tenant_id', 'tanggal']);
        });

        // 4. Machine & Heavy Equipment Daily Log (Wheel Loader & Processing Units - Agung)
        Schema::create('kpi_machine_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('machine_id')->nullable()->constrained('machines')->nullOnDelete();
            $table->string('nama_alat'); // e.g. Wheel Loader, Mesin RDF, Conveyor Pemilahan, Mesin Press Residu, Separator
            $table->date('tanggal');
            $table->time('jam_start')->nullable();
            $table->time('jam_stop')->nullable();
            $table->decimal('jam_operasi', 5, 2)->default(8.00);
            $table->decimal('jam_downtime', 5, 2)->default(0.00);
            $table->decimal('bbm_liter', 8, 2)->default(0.00);
            $table->enum('status_alat', ['Siap Operasi', 'Dalam Perbaikan', 'Rusak Total'])->default('Siap Operasi');
            $table->text('catatan_kendala')->nullable();
            $table->foreignId('operator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['tenant_id', 'tanggal']);
        });

        // 5. Periodic KPI Evaluations & PBS Supervisor Approvals (Mingguan & Bulanan)
        Schema::create('kpi_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('periode_tipe', ['mingguan', 'bulanan'])->default('mingguan');
            $table->date('periode_mulai');
            $table->date('periode_selesai');
            $table->enum('target_tipe', ['global', 'individual'])->default('global');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // Nullable jika target global
            $table->string('jabatan')->nullable(); // site_manager, admin_commercial, etc.
            $table->decimal('total_skor', 6, 2)->default(0.00);
            $table->string('predikat', 50)->default('Baik'); // Sangat Baik (A), Baik (B), Cukup (C), Kurang (D)
            $table->json('rincian_kpi')->nullable(); // Detailed metrics breakdown with realisasi, target, nilai, bobot, score
            $table->enum('status', ['draft', 'submitted', 'approved', 'revision'])->default('draft');
            
            // Workflow Supervisi PT PBS
            $table->string('supervisor_institution')->default('PT Pinastika Bhakti Semesta');
            $table->foreignId('approved_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('approved_by_name')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('supervisor_notes')->nullable(); // Catatan perbaikan atau verifikasi dari supervisi PT PBS
            
            $table->timestamps();

            $table->index(['tenant_id', 'periode_tipe', 'periode_mulai', 'periode_selesai'], 'idx_kpi_eval_period');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kpi_evaluations');
        Schema::dropIfExists('kpi_machine_activity_logs');
        Schema::dropIfExists('kpi_stakeholder_complaints');
        Schema::dropIfExists('kpi_daily_checklists');
        Schema::dropIfExists('kpi_masters');
    }
};
