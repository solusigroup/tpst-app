<?php

namespace App\Models;

use App\Scopes\TenantScope;
use App\Traits\TenantTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Vendor extends Model
{
    use TenantTrait, SoftDeletes, \Spatie\Activitylog\Traits\LogsActivity;

    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        return \Spatie\Activitylog\LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected $fillable = [
        'tenant_id',
        'nama_vendor',
        'kontak',
        'alamat',
    ];

    /**
     * Boot the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new TenantScope());
    }

    /**
     * Get the tenant this vendor belongs to.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get all buku pembantu entries for this vendor.
     */
    public function bukuPembantu(): MorphMany
    {
        return $this->morphMany(BukuPembantu::class, 'contactable');
    }
}
