<?php

namespace App\Models;

use App\Scopes\TenantScope;
use App\Traits\TenantTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class BukuPembantu extends Model
{
    use TenantTrait, \Spatie\Activitylog\Traits\LogsActivity;

    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        return \Spatie\Activitylog\LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected $table = 'buku_pembantu';

    protected $fillable = [
        'tenant_id',
        'jurnal_header_id',
        'settled_by_jurnal_header_id',
        'contactable_type',
        'contactable_id',
        'tipe',
        'tanggal',
        'tanggal_jatuh_tempo',
        'jumlah',
        'terbayar',
        'keterangan',
        'bukti_transaksi',
        'status',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'tanggal_jatuh_tempo' => 'date',
        'jumlah' => 'decimal:2',
        'terbayar' => 'decimal:2',
    ];

    /**
     * Boot the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new TenantScope());
    }

    /**
     * Get the tenant this buku pembantu entry belongs to.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the jurnal header this entry belongs to.
     */
    public function jurnalHeader(): BelongsTo
    {
        return $this->belongsTo(JurnalHeader::class);
    }

    /**
     * Get the parent contactable model (Klien or Vendor).
     */
    public function contactable(): MorphTo
    {
        return $this->morphTo();
    }
}
