<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;

trait TenantTrait
{
    /**
     * Boot the TenantTrait.
     */
    public static function bootTenantTrait(): void
    {
        static::creating(function (Model $model) {
            if (auth()->check() && empty($model->tenant_id)) {
                $model->tenant_id = auth()->user()->getEffectiveTenantId();
            }
        });
    }
}
