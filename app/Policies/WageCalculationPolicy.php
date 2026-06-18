<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WageCalculation;

class WageCalculationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_wage_calculation');
    }

    public function view(User $user, WageCalculation $calculation): bool
    {
        return $user->tenant_id === $calculation->tenant_id &&
               $user->can('view_wage_calculation');
    }

    public function create(User $user): bool
    {
        return $user->can('create_wage_calculation');
    }

    public function update(User $user, WageCalculation $calculation): bool
    {
        return $user->tenant_id === $calculation->tenant_id &&
               $user->can('update_wage_calculation');
    }

    public function approve(User $user, WageCalculation $calculation): bool
    {
        return $user->tenant_id === $calculation->tenant_id &&
               $user->can('update_wage_calculation');
    }

    public function delete(User $user, WageCalculation $calculation): bool
    {
        return $user->tenant_id === $calculation->tenant_id &&
               $user->can('delete_wage_calculation');
    }
}
