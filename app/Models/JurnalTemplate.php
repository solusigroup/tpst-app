<?php

namespace App\Models;

use App\Scopes\TenantScope;
use App\Traits\TenantTrait;
use Illuminate\Database\Eloquent\Model;

class JurnalTemplate extends Model
{
    use TenantTrait;

    protected $table = 'jurnal_templates';

    protected $fillable = [
        'tenant_id',
        'nama',
        'deskripsi',
        'details',
    ];

    protected $casts = [
        'details' => 'array',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new TenantScope());
    }
}
