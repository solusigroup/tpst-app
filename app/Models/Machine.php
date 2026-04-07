<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Scopes\TenantScope;
use App\Traits\TenantTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

class Machine extends Model
{
    use TenantTrait, SoftDeletes;

    protected $fillable = [
        'nomor_mesin',
        'nama_mesin',
        'tenant_id',
    ];

    /**
     * Boot the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new TenantScope());
    }

    public function logs()
    {
        return $this->hasMany(MachineLog::class);
    }
}
