<?php

namespace App\Models;

use App\Scopes\TenantScope;
use App\Traits\TenantTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProduksiHarian extends Model
{
    use TenantTrait;

    protected $table = 'produksi_harian';

    protected $fillable = [
        'tenant_id',
        'tanggal',
        'total_input_sampah',
        'hasil_rdf',
        'hasil_plastik',
        'hasil_kompos',
        'residu_tpa',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'total_input_sampah' => 'decimal:2',
        'hasil_rdf' => 'decimal:2',
        'hasil_plastik' => 'decimal:2',
        'hasil_kompos' => 'decimal:2',
        'residu_tpa' => 'decimal:2',
    ];

    /**
     * Boot the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new TenantScope());
    }

    /**
     * Get the tenant this produksi harian belongs to.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
