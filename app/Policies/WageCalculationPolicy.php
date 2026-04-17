<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WageCalculation;

class WageCalculationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['manajemen', 'hrd', 'keuangan', 'super_admin']);
    }

    public function view(User $user, WageCalculation $calculation): bool
    {
        return $user->tenant_id === $calculation->tenant_id &&
               $user->hasRole(['manajemen', 'hrd', 'keuangan', 'super_admin']);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['manajemen', 'hrd', 'super_admin']);
    }

    public function update(User $user, WageCalculation $calculation): bool
    {
        return $user->tenant_id === $calculation->tenant_id &&
               $user->hasRole(['manajemen', 'super_admin']);
    }

    public function approve(User $user, WageCalculation $calculation): bool
    {
        return $user->tenant_id === $calculation->tenant_id &&
               $user->hasRole(['manajemen', 'super_admin']);
    }

    public function delete(User $user, WageCalculation $calculation): bool
    {
        return $user->tenant_id === $calculation->tenant_id &&
               $user->hasRole(['manajemen', 'super_admin']);
    }
}
