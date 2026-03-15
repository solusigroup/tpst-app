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
                $userTenantId = auth()->user()->tenant_id;
                // Only auto-assign if the user actually belongs to a tenant (not a global superadmin)
                if ($userTenantId) {
                    $model->tenant_id = $userTenantId;
                }
            }
        });
    }
}
