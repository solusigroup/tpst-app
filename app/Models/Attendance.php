<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'attendance_date',
        'check_in',
        'check_out',
        'status',
        'overtime_pay',
        'notes',
    ];

    protected $casts = [
        'attendance_date' => 'date',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getWorkHoursAttribute(): ?string
    {
        if ($this->check_in && $this->check_out) {
            $checkIn = \Carbon\Carbon::createFromTimeString($this->check_in);
            $checkOut = \Carbon\Carbon::createFromTimeString($this->check_out);
            $hours = $checkOut->diffInMinutes($checkIn) / 60;
            return number_format($hours, 2);
        }
        return null;
    }
}
