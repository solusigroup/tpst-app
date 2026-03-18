<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WageCalculation extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'week_start',
        'week_end',
        'total_quantity',
        'total_wage',
        'status',
        'paid_date',
        'notes',
    ];

    protected $casts = [
        'week_start' => 'date',
        'week_end' => 'date',
        'total_quantity' => 'decimal:2',
        'total_wage' => 'decimal:2',
        'paid_date' => 'date',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function calculateForEmployee(int $userId, \DateTime $weekStart, ?int $tenantId = null): self
    {
        $tenantId = $tenantId ?? auth()->user()->tenant_id;
        $weekEnd = $weekStart->copy()->addDays(6);

        $outputs = EmployeeOutput::where('user_id', $userId)
            ->where('tenant_id', $tenantId)
            ->whereBetween('output_date', [$weekStart->format('Y-m-d'), $weekEnd->format('Y-m-d')])
            ->get();

        $user = User::find($userId);

        $totalQuantity = $outputs->sum('quantity');
        
        if ($user && $user->salary_type === 'bulanan') {
            $totalWage = $user->monthly_salary ?? 0;
        } else {
            $totalWage = $outputs->sum(fn($output) => $output->getWageForThisOutput());
        }

        $calculation = self::firstOrCreate(
            [
                'tenant_id' => $tenantId,
                'user_id' => $userId,
                'week_start' => $weekStart,
            ],
            [
                'week_end' => $weekEnd,
                'total_quantity' => $totalQuantity,
                'total_wage' => $totalWage,
                'status' => 'pending',
            ]
        );

        $calculation->update([
            'total_quantity' => $totalQuantity,
            'total_wage' => $totalWage,
        ]);

        return $calculation;
    }

    /**
     * Get associated jurnal headers.
     */
    public function jurnalHeaders()
    {
        return $this->morphMany(JurnalHeader::class, 'referensi');
    }
}
