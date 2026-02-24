<?php

namespace App\Models;

use App\Scopes\TenantScope;
use App\Traits\TenantTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JurnalHeader extends Model
{
    use TenantTrait;

    protected $table = 'jurnal_header';

    protected $fillable = [
        'tenant_id',
        'tanggal',
        'nomor_referensi',
        'deskripsi',
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
    }

    /**
     * Get the tenant this jurnal header belongs to.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get all jurnal details for this header.
     */
    public function jurnalDetails(): HasMany
    {
        return $this->hasMany(JurnalDetail::class);
    }
}
