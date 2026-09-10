<?php

namespace App\Models;

use App\Traits\TenantTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KpiEvaluation extends Model
{
    use HasFactory, TenantTrait;

    protected $table = 'kpi_evaluations';

    protected $fillable = [
        'tenant_id',
        'periode_tipe',
        'periode_mulai',
        'periode_selesai',
        'target_tipe',
        'user_id',
        'jabatan',
        'total_skor',
        'predikat',
        'rincian_kpi',
        'status',
        'supervisor_institution',
        'approved_by_id',
        'approved_by_name',
        'submitted_at',
        'approved_at',
        'supervisor_notes',
    ];

    protected $casts = [
        'periode_mulai' => 'date',
        'periode_selesai' => 'date',
        'total_skor' => 'decimal:2',
        'rincian_kpi' => 'array',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
