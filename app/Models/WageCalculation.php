<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use App\Models\Attendance;
use App\Models\EmployeeOutput;
use Carbon\Carbon;

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
        'overtime_pay',
        'status',
        'approved_by_id',
        'approved_at',
        'paid_date',
        'notes',
    ];

    protected $casts = [
        'week_start' => 'date',
        'week_end' => 'date',
        'total_quantity' => 'decimal:2',
        'total_wage' => 'decimal:2',
        'overtime_pay' => 'decimal:2',
        'paid_date' => 'date',
        'approved_at' => 'datetime',
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
        
        // Ensure we have a Carbon instance for easier date manipulation
        $carbonWeekStart = Carbon::instance($weekStart)->startOfDay();
        
        // Determine pay period based on frequency
        $daysToAdd = ($user && $user->payment_frequency === 'Dua Mingguan') ? 13 : 6;
        $weekEnd = $carbonWeekStart->copy()->addDays($daysToAdd)->endOfDay();

        $outputs = EmployeeOutput::where('user_id', $userId)
            ->where('tenant_id', $tenantId)
            ->whereBetween('output_date', [$carbonWeekStart->toDateString(), $weekEnd->toDateString()])
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
                    ->whereBetween('attendance_date', [$carbonWeekStart->toDateString(), $weekEnd->toDateString()])
                    ->whereIn('status', ['present'])
                    ->count();
                
                $totalWage = $attendanceCount * ($user->daily_wage ?? 0);
            } else {
                // Default to borongan (performance based)
                $totalWage = $outputs->sum(fn($output) => $output->getWageForThisOutput());
            }
        }

        $overtimePay = Attendance::where('user_id', $userId)
            ->where('tenant_id', $tenantId)
            ->whereBetween('attendance_date', [$carbonWeekStart->toDateString(), $weekEnd->toDateString()])
            ->sum('overtime_pay');

        $calculation = self::updateOrCreate(
            [
                'tenant_id' => $tenantId,
                'user_id' => $userId,
                'week_start' => $carbonWeekStart->toDateString(),
            ],
            [
                'week_end' => $weekEnd->toDateString(),
                'total_quantity' => $totalQuantity,
                'total_wage' => $totalWage,
                'overtime_pay' => $overtimePay,
                'status' => 'pending',
            ]
        );

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
     * Approve the calculation.
     */
    public function approve(int $approvedById): bool
    {
        if ($this->status !== 'pending') {
            return false;
        }

        return $this->update([
            'status' => 'approved',
            'approved_by_id' => $approvedById,
            'approved_at' => now(),
        ]);
    }

    /**
     * Get associated approved by user.
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }

    /**
     * Get associated jurnal headers.
     */
    public function jurnalHeaders()
    {
        return $this->morphMany(JurnalHeader::class, 'referensi');
    }
}
