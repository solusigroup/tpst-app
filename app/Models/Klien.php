<?php

namespace App\Models;

use App\Scopes\TenantScope;
use App\Traits\TenantTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Klien extends Model
{
    use TenantTrait;

    protected $table = 'klien';

    protected $fillable = [
        'tenant_id',
        'nama_klien',
        'jenis',
        'kontak',
        'alamat',
    ];

    protected $casts = [
        'jenis' => 'string',
    ];

    /**
     * Boot the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new TenantScope());
    }

    /**
     * Get the tenant this klien belongs to.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get all armada for this klien.
     */
    public function armada(): HasMany
    {
        return $this->hasMany(Armada::class);
    }

    /**
     * Get all ritase for this klien.
     */
    public function ritase(): HasMany
    {
        return $this->hasMany(Ritase::class);
    }

    /**
     * Get all penjualan for this klien.
     */
    public function penjualan(): HasMany
    {
        return $this->hasMany(Penjualan::class);
    }

    /**
     * Get all invoices for this klien.
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}
