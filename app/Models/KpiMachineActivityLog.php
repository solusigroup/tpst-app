<?php

namespace App\Models;

use App\Traits\TenantTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KpiMachineActivityLog extends Model
{
    use HasFactory, TenantTrait;

    protected $table = 'kpi_machine_activity_logs';

    protected $fillable = [
        'tenant_id',
        'machine_id',
        'nama_alat',
        'tanggal',
        'jam_start',
        'jam_stop',
        'jam_operasi',
        'jam_downtime',
        'bbm_liter',
        'status_alat',
        'catatan_kendala',
        'operator_id',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jam_operasi' => 'decimal:2',
        'jam_downtime' => 'decimal:2',
        'bbm_liter' => 'decimal:2',
    ];

    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }

    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
