<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WasteCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'name',
        'description',
        'unit',
        'selling_price',
        'is_active',
    ];

    protected $casts = [
        'selling_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function wageRates(): HasMany
    {
        return $this->hasMany(WageRate::class);
    }

    public function employeeOutputs(): HasMany
    {
        return $this->hasMany(EmployeeOutput::class);
    }

    public function getActiveWageRateAttribute()
    {
        return $this->wageRates()
            ->where('is_active', true)
            ->where('effective_date', '<=', now())
            ->where(function ($query) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            })
            ->latest('effective_date')
            ->first();
    }
}
