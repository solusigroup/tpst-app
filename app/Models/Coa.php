<?php

namespace App\Models;

use App\Scopes\TenantScope;
use App\Traits\TenantTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coa extends Model
{
    use TenantTrait;

    protected $table = 'coa';

    protected $fillable = [
        'tenant_id',
        'kode_akun',
        'nama_akun',
        'tipe',
        'klasifikasi',
    ];

    protected $casts = [
        'tipe' => 'string',
    ];

    /**
     * Boot the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new TenantScope());
    }

    /**
     * Get the tenant this COA belongs to.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get all jurnal details for this account.
     */
    public function jurnalDetails(): HasMany
    {
        return $this->hasMany(JurnalDetail::class);
    }
}
