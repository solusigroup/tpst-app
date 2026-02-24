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
            if (auth()->check() && !$model->tenant_id) {
                $model->tenant_id = auth()->user()->tenant_id;
            }
        });
    }
}
