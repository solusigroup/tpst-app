<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeOutput extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'waste_category_id',
        'output_date',
        'quantity',
        'unit',
        'notes',
    ];

    protected $casts = [
        'output_date' => 'date',
        'quantity' => 'decimal:2',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function wasteCategory(): BelongsTo
    {
        return $this->belongsTo(WasteCategory::class);
    }

    public function getWageForThisOutput(): float
    {
        $rate = WageRate::getActiveRate($this->waste_category_id, $this->output_date);
        if (!$rate) {
            return 0;
        }
        return $this->quantity * $rate->rate_per_unit;
    }
}
