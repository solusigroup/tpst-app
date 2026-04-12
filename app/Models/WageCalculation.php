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
        $user = User::find($userId);
        
        // Determine pay period based on frequency
        $daysToAdd = ($user && $user->payment_frequency === 'Dua Mingguan') ? 13 : 6;
        $weekEnd = $weekStart->copy()->addDays($daysToAdd);

        $outputs = EmployeeOutput::where('user_id', $userId)
            ->where('tenant_id', $tenantId)
            ->whereBetween('output_date', [$weekStart->format('Y-m-d'), $weekEnd->format('Y-m-d')])
            ->get();

        $totalQuantity = $outputs->sum('quantity');
        
        $totalWage = 0;
        if ($user) {
            if ($user->salary_type === 'bulanan') {
                $totalWage = $user->monthly_salary ?? 0;
            } elseif ($user->salary_type === 'harian') {
                // Count unique attendance days in this period
                $attendanceCount = Attendance::where('user_id', $userId)
                    ->where('tenant_id', $tenantId)
                    ->whereBetween('attendance_date', [$weekStart->format('Y-m-d'), $weekEnd->format('Y-m-d')])
                    ->whereIn('status', ['present']) // Use lowercase as per AttendanceController
                    ->count();
                
                $totalWage = $attendanceCount * ($user->daily_wage ?? 0);
            } else {
                // Default to borongan (performance based)
                $totalWage = $outputs->sum(fn($output) => $output->getWageForThisOutput());
            }
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
            'week_end' => $weekEnd,
            'total_quantity' => $totalQuantity,
            'total_wage' => $totalWage,
        ]);

        return $calculation;
    }

    /**
     * Get total_output as an alias for total_quantity for backward compatibility with views.
     */
    public function getTotalOutputAttribute()
    {
        return $this->total_quantity;
    }

    /**
     * Get associated jurnal headers.
     */
    public function jurnalHeaders()
    {
        return $this->morphMany(JurnalHeader::class, 'referensi');
    }
}
