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
    ];

    protected $casts = [
        'tanggal' => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function handledBy()
    {
        return $this->belongsTo(User::class, 'handled_by_id');
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
