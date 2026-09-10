<?php

namespace App\Models;

use App\Traits\TenantTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KpiDailyChecklist extends Model
{
    use HasFactory, TenantTrait;

    protected $table = 'kpi_daily_checklists';

    protected $fillable = [
        'tenant_id',
        'tanggal',
        'area',
        'status_kebersihan',
        'ada_tumpukan_sampah',
        'status_bau',
        'skor_kebersihan',
        'skor_bau',
        'user_id',
        'foto_bukti',
        'catatan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'ada_tumpukan_sampah' => 'boolean',
        'skor_kebersihan' => 'integer',
        'skor_bau' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
