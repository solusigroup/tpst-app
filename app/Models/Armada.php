<?php

namespace App\Models;

use App\Scopes\TenantScope;
use App\Traits\TenantTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Armada extends Model
{
    use TenantTrait, SoftDeletes;

    protected $table = 'armada';

    protected $fillable = [
        'tenant_id',
        'klien_id',
        'plat_nomor',
        'kapasitas_maksimal',
    ];

    protected $casts = [
        'kapasitas_maksimal' => 'decimal:2',
    ];

    /**
     * Boot the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new TenantScope());
    }

    /**
     * Get the tenant this armada belongs to.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the klien this armada belongs to.
     */
    public function klien(): BelongsTo
    {
        return $this->belongsTo(Klien::class);
    }

    /**
     * Get all ritase for this armada.
     */
    public function ritase(): HasMany
    {
        return $this->hasMany(Ritase::class);
    }
}
