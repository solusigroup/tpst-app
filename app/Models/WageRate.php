<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WageRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'waste_category_id',
        'rate_per_unit',
        'effective_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'rate_per_unit' => 'decimal:2',
        'effective_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function wasteCategory(): BelongsTo
    {
        return $this->belongsTo(WasteCategory::class);
    }

    public static function getActiveRate(int $wasteCategoryId, \DateTime $date = null): ?self
    {
        $date = $date ?? now();
        return self::where('waste_category_id', $wasteCategoryId)
            ->where('effective_date', '<=', $date)
            ->where(function ($query) use ($date) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', $date);
            })
            ->where('is_active', true)
            ->latest('effective_date')
            ->first();
    }
}
