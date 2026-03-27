<?php

namespace App\Models;

use App\Scopes\TenantScope;
use App\Traits\TenantTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JurnalKas extends Model
{
    use TenantTrait, \Spatie\Activitylog\Traits\LogsActivity;

    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected $table = 'jurnal_kas';

    protected $fillable = [
        'tenant_id',
        'tipe',
        'tanggal',
        'coa_kas_id',
        'coa_lawan_id',
        'nominal',
        'deskripsi',
        'bukti_transaksi',
        'status',
        'contactable_type',
        'contactable_id',
    ];

    /**
     * Get the parent contactable model (Klien or Vendor).
     */
    public function contactable(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }

    protected $casts = [
        'tanggal' => 'date',
        'nominal' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new TenantScope());

        static::created(function (JurnalKas $jurnalKas) {
            $jurnalKas->createJurnalAkuntansi();
        });

        static::updated(function (JurnalKas $jurnalKas) {
            // Re-create or update JurnalAkuntansi
            // Since it's polymorphic, we'll delete and re-create for simplicity
            $jurnalKas->deleteJurnalAkuntansi();
            $jurnalKas->createJurnalAkuntansi();
        });

        static::deleted(function (JurnalKas $jurnalKas) {
            $jurnalKas->deleteJurnalAkuntansi();
        });
    }

    public function createJurnalAkuntansi()
    {
        $jurnalHeader = new JurnalHeader();
        $jurnalHeader->tenant_id = $this->tenant_id;
        $jurnalHeader->tanggal = $this->tanggal;
        $jurnalHeader->deskripsi = $this->deskripsi ?: "Jurnal Kas: {$this->tipe}";
        $jurnalHeader->referensi_type = self::class;
        $jurnalHeader->referensi_id = $this->id;
        $jurnalHeader->status = $this->status ?? 'draft';
        $jurnalHeader->bukti_transaksi = $this->bukti_transaksi;
        $jurnalHeader->save();

        // Details
        if ($this->tipe === 'Penerimaan') {
            // Kas bertambah (Debit), Lawan bertambah (Kredit - usually Pendapatan)
            $jurnalHeader->jurnalDetails()->create([
                'coa_id' => $this->coa_kas_id,
                'debit' => $this->nominal,
                'kredit' => 0,
            ]);
            $jurnalHeader->jurnalDetails()->create([
                'coa_id' => $this->coa_lawan_id,
                'debit' => 0,
                'kredit' => $this->nominal,
                'contactable_type' => $this->contactable_type,
                'contactable_id' => $this->contactable_id,
            ]);
        } else {
            // Pengeluaran
            // Lawan bertambah (Debit - usually Biaya), Kas berkurang (Kredit)
            $jurnalHeader->jurnalDetails()->create([
                'coa_id' => $this->coa_lawan_id,
                'debit' => $this->nominal,
                'kredit' => 0,
                'contactable_type' => $this->contactable_type,
                'contactable_id' => $this->contactable_id,
            ]);
            $jurnalHeader->jurnalDetails()->create([
                'coa_id' => $this->coa_kas_id,
                'debit' => 0,
                'kredit' => $this->nominal,
            ]);
        }
    }

    public function deleteJurnalAkuntansi()
    {
        JurnalHeader::where('referensi_type', self::class)
            ->where('referensi_id', $this->id)
            ->delete();
    }

    public function coaKas(): BelongsTo
    {
        return $this->belongsTo(Coa::class, 'coa_kas_id');
    }

    public function coaLawan(): BelongsTo
    {
        return $this->belongsTo(Coa::class, 'coa_lawan_id');
    }
}
