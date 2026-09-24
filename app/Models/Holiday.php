<?php

namespace App\Models;

use App\Scopes\TenantScope;
use App\Traits\TenantTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Holiday extends Model
{
    use TenantTrait;

    protected $table = 'holidays';

    protected $fillable = [
        'tenant_id',
        'tanggal',
        'nama',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new TenantScope());
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get all holiday dates for a tenant within a date range.
     */
    public static function getHolidayDates($startDate, $endDate, $tenantId = null): array
    {
        $query = self::whereBetween('tanggal', [$startDate, $endDate]);
        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }
        return $query->pluck('tanggal')->map(fn($d) => $d->toDateString())->toArray();
    }
}
