<?php

namespace App\Models;

use App\Traits\TenantTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KpiMaster extends Model
{
    use HasFactory, TenantTrait;

    protected $table = 'kpi_masters';

    protected $fillable = [
        'tenant_id',
        'kode_kpi',
        'nama_kpi',
        'level',
        'jabatan_target',
        'kategori',
        'metric_type',
        'default_target',
        'satuan',
        'bobot',
        'formula_type',
        'deskripsi',
        'is_active',
    ];

    protected $casts = [
        'default_target' => 'decimal:2',
        'bobot' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForJabatan($query, $jabatan)
    {
        return $query->where('jabatan_target', $jabatan);
    }
}
