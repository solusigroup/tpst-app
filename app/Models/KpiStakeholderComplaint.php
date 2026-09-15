<?php

namespace App\Models;

use App\Traits\TenantTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KpiStakeholderComplaint extends Model
{
    use HasFactory, TenantTrait;

    protected $table = 'kpi_stakeholder_complaints';

    protected $fillable = [
        'tenant_id',
        'tanggal',
        'stakeholder_type',
        'nama_pelapor',
        'kontak_pelapor',
        'isi_keluhan',
        'tingkat_urgensi',
        'status_penanganan',
        'tindakan_perbaikan',
        'tanggal_selesai',
        'handled_by_id',
        'is_approved',
        'approved_by_id',
        'approved_at',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'tanggal_selesai' => 'date',
        'is_approved' => 'boolean',
        'approved_at' => 'datetime',
    ];

    public function handledBy()
    {
        return $this->belongsTo(User::class, 'handled_by_id');
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
