<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WageRate;

class WageRatePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_wage_rate');
    }

    public function view(User $user, WageRate $rate): bool
    {
        return $user->tenant_id === $rate->tenant_id &&
               $user->can('view_wage_rate');
    }

    public function create(User $user): bool
    {
        return $user->can('create_wage_rate');
    }

    public function update(User $user, WageRate $rate): bool
    {
        return $user->tenant_id === $rate->tenant_id &&
               $user->can('update_wage_rate');
    }

    public function delete(User $user, WageRate $rate): bool
    {
        return $user->tenant_id === $rate->tenant_id &&
               $user->can('delete_wage_rate');
    }
}
