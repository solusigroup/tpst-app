<?php

namespace App\Models;

use App\Scopes\TenantScope;
use App\Traits\TenantTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JurnalHeader extends Model
{
    use TenantTrait, \Spatie\Activitylog\Traits\LogsActivity;

    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected $table = 'jurnal_header';

    protected $fillable = [
        'tenant_id',
        'nomor_referensi',
        'tanggal',
        'referensi_type',
        'referensi_id',
        'deskripsi',
        'status',
        'bukti_transaksi',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    /**
     * Boot the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new TenantScope());

        static::creating(function (JurnalHeader $header) {
            if (empty($header->nomor_referensi)) {
                $prefix = 'JV-' . now()->format('Ym') . '-';
                
                // Get the last reference number for this month
                $lastHeader = self::withoutGlobalScope(TenantScope::class)
                    ->where('tenant_id', $header->tenant_id)
                    ->where('nomor_referensi', 'like', $prefix . '%')
                    ->orderBy('nomor_referensi', 'desc')
                    ->first();
                
                $nextSequence = 1;
                if ($lastHeader) {
                    $lastSequence = (int) substr($lastHeader->nomor_referensi, -4);
                    $nextSequence = $lastSequence + 1;
                }
                
                $header->nomor_referensi = $prefix . str_pad($nextSequence, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    /**
     * Get the tenant this jurnal header belongs to.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the reference model (Ritase or Penjualan) this jurnal header belongs to.
     */
    public function referensi()
    {
        return $this->morphTo();
    }

    /**
     * Get all jurnal details for this header.
     */
    public function jurnalDetails(): HasMany
    {
        return $this->hasMany(JurnalDetail::class);
    }
}
