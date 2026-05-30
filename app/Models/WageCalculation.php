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
        'details',
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
        'details' => 'array',
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

    public static function calculateForEmployee(int $userId, \DateTime $weekStart, ?int $tenantId = null, ?\DateTime $weekEnd = null): self
    {
        $tenantId = $tenantId ?? auth()->user()->tenant_id;
        $user = User::find($userId);
        
        // Ensure we have a Carbon instance for easier date manipulation
        $carbonWeekStart = Carbon::instance($weekStart)->startOfDay();
        
        if ($weekEnd) {
            $carbonWeekEnd = Carbon::instance($weekEnd)->endOfDay();
        } else {
            // Determine pay period based on salary type and frequency
            if ($user && $user->salary_type === 'bulanan') {
                $carbonWeekStart = $carbonWeekStart->copy()->startOfMonth();
                $carbonWeekEnd = $carbonWeekStart->copy()->endOfMonth()->endOfDay();
            } else {
                $daysToAdd = ($user && $user->payment_frequency === 'Dua Mingguan') ? 13 : 6;
                $carbonWeekEnd = $carbonWeekStart->copy()->addDays($daysToAdd)->endOfDay();
            }
        }

        // 1. Revert previous calculation if it exists to avoid double-counting paid_quantity
        $oldCalculation = self::where('user_id', $userId)
            ->where('tenant_id', $tenantId)
            ->where('week_start', $carbonWeekStart->toDateString())
            ->first();

        if ($oldCalculation && $oldCalculation->details) {
            foreach ($oldCalculation->details as $item) {
                if (isset($item['employee_output_id'])) {
                    $output = EmployeeOutput::find($item['employee_output_id']);
                    if ($output) {
                        $output->decrement('paid_quantity', $item['quantity_paid']);
                    }
                }
            }
        }

        $totalQuantity = 0;
        $totalWage = 0;
        $calculationDetails = [];
        
        if ($user) {
            if ($user->salary_type === 'bulanan') {
                $totalWage = $user->monthly_salary ?? 0;
                // For reporting, we can still sum quantity but it doesn't affect wage
                $totalQuantity = EmployeeOutput::where('user_id', $userId)
                    ->where('tenant_id', $tenantId)
                    ->whereBetween('output_date', [$carbonWeekStart->toDateString(), $carbonWeekEnd->toDateString()])
                    ->sum('quantity');
            } elseif ($user->salary_type === 'harian') {
                $attendanceCount = Attendance::where('user_id', $userId)
                    ->where('tenant_id', $tenantId)
                    ->whereBetween('attendance_date', [$carbonWeekStart->toDateString(), $carbonWeekEnd->toDateString()])
                    ->whereIn('status', ['present'])
                    ->count();
                
                $totalWage = $attendanceCount * ($user->daily_wage ?? 0);
                $totalQuantity = EmployeeOutput::where('user_id', $userId)
                    ->where('tenant_id', $tenantId)
                    ->whereBetween('output_date', [$carbonWeekStart->toDateString(), $carbonWeekEnd->toDateString()])
                    ->sum('quantity');
            } else {
                // BORONGAN: Pay only for what has been sold (FIFO)
                // Look for all unpaid outputs within the calculation period [carbonWeekStart, carbonWeekEnd]
                $unpaidOutputs = EmployeeOutput::where('user_id', $userId)
                    ->where('tenant_id', $tenantId)
                    ->whereColumn('paid_quantity', '<', 'quantity')
                    ->whereBetween('output_date', [$carbonWeekStart->toDateString(), $carbonWeekEnd->toDateString()])
                    ->orderBy('output_date')
                    ->get();

                foreach ($unpaidOutputs as $output) {
                    $remainingToPay = $output->quantity - $output->paid_quantity;
                    if ($remainingToPay <= 0) continue;

                    // Calculate Global Available Sales for this category up to carbonWeekEnd
                    $totalSold = Penjualan::where('waste_category_id', $output->waste_category_id)
                        ->where('tenant_id', $tenantId)
                        ->where('tanggal', '<=', $carbonWeekEnd->toDateString())
                        ->sum('berat_kg');

                    $totalPaidGlobally = EmployeeOutput::where('waste_category_id', $output->waste_category_id)
                        ->where('tenant_id', $tenantId)
                        ->sum('paid_quantity');

                    $availableToPay = max(0, $totalSold - $totalPaidGlobally);
                    $canPay = min($remainingToPay, $availableToPay);

                    if ($canPay > 0) {
                        $rate = WageRate::getActiveRate($output->waste_category_id, $output->output_date);
                        $wageForThis = $canPay * ($rate ? $rate->rate_per_unit : 0);

                        // Update output
                        $output->increment('paid_quantity', $canPay);

                        // Track totals
                        $totalQuantity += $canPay;
                        $totalWage += $wageForThis;

                        // Store in details
                        $calculationDetails[] = [
                            'employee_output_id' => $output->id,
                            'date' => $output->output_date->toDateString(),
                            'category' => $output->wasteCategory->name ?? 'Unknown',
                            'quantity_paid' => (float)$canPay,
                            'rate' => (float)($rate ? $rate->rate_per_unit : 0),
                            'subtotal' => (float)$wageForThis,
                        ];
                    }
                }
            }
        }

        $overtimePay = Attendance::where('user_id', $userId)
            ->where('tenant_id', $tenantId)
            ->whereBetween('attendance_date', [$carbonWeekStart->toDateString(), $carbonWeekEnd->toDateString()])
            ->sum('overtime_pay');

        $calculation = self::updateOrCreate(
            [
                'tenant_id' => $tenantId,
                'user_id' => $userId,
                'week_start' => $carbonWeekStart->toDateString(),
            ],
            [
                'week_end' => $carbonWeekEnd->toDateString(),
                'total_quantity' => $totalQuantity,
                'total_wage' => $totalWage,
                'overtime_pay' => $overtimePay,
                'details' => $calculationDetails,
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
