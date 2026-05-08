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
        'user_id',
        'waste_category_id',
        'tanggal',
        'kategori',
        'jenis',
        'tonase',
        'jml_bal',
        'officer',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'tonase' => 'decimal:2',
        'jml_bal' => 'integer',
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

    /**
     * Get the user (petugas pemilah).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the waste category (jenis sampah).
     */
    public function wasteCategory(): BelongsTo
    {
        return $this->belongsTo(WasteCategory::class);
    }

    /**
     * Get the active wage rate for this record.
     */
    public function getWageRate(): ?WageRate
    {
        return WageRate::getActiveRate($this->waste_category_id, $this->tanggal);
    }

    /**
     * Calculate total wage for this record.
     */
    public function getTotalWage(): float
    {
        $rate = $this->getWageRate();
        return $rate ? (float) ($this->tonase * $rate->rate_per_unit) : 0;
    }
}
