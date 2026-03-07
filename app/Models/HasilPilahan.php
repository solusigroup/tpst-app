<?php

namespace App\Models;

use App\Scopes\TenantScope;
use App\Traits\TenantTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HasilPilahan extends Model
{
    use TenantTrait;

    protected $table = 'hasil_pilahan';

    protected $fillable = [
        'tenant_id',
        'tanggal',
        'kategori',
        'jenis',
        'tonase',
        'officer',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'tonase' => 'decimal:2',
    ];

    /**
     * Boot the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new TenantScope());
    }

    /**
     * Get the tenant.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
