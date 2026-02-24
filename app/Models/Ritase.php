<?php

namespace App\Models;

use App\Scopes\TenantScope;
use App\Traits\TenantTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ritase extends Model
{
    use TenantTrait;

    protected $table = 'ritase';

    protected $fillable = [
        'tenant_id',
        'armada_id',
        'klien_id',
        'nomor_tiket',
        'waktu_masuk',
        'waktu_keluar',
        'berat_bruto',
        'berat_tarra',
        'berat_netto',
        'jenis_sampah',
        'biaya_tipping',
        'status',
    ];

    protected $casts = [
        'waktu_masuk' => 'datetime',
        'waktu_keluar' => 'datetime',
        'berat_bruto' => 'decimal:2',
        'berat_tarra' => 'decimal:2',
        'berat_netto' => 'decimal:2',
        'biaya_tipping' => 'decimal:2',
        'status' => 'string',
    ];

    /**
     * Boot the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new TenantScope());

        static::saving(function (Ritase $ritase) {
            $ritase->berat_netto = ($ritase->berat_bruto ?? 0) - ($ritase->berat_tarra ?? 0);
        });
    }

    /**
     * Get the tenant this ritase belongs to.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the armada this ritase belongs to.
     */
    public function armada(): BelongsTo
    {
        return $this->belongsTo(Armada::class);
    }

    /**
     * Get the klien this ritase belongs to.
     */
    public function klien(): BelongsTo
    {
        return $this->belongsTo(Klien::class);
    }

    /**
     * Get associated jurnal headers.
     */
    public function jurnalHeaders(): HasMany
    {
        return $this->hasMany(JurnalHeader::class, 'nomor_referensi', 'nomor_tiket');
    }
}
