<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WageRate;

class WageRatePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['admin', 'hrd', 'keuangan', 'super_admin']);
    }

    public function view(User $user, WageRate $rate): bool
    {
        return $user->tenant_id === $rate->tenant_id;
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['admin', 'hrd', 'super_admin']);
    }

    public function update(User $user, WageRate $rate): bool
    {
        return $user->tenant_id === $rate->tenant_id &&
            $user->hasRole(['admin', 'hrd', 'super_admin']);
    }

    public function delete(User $user, WageRate $rate): bool
    {
        return $user->tenant_id === $rate->tenant_id &&
            $user->hasRole(['admin', 'super_admin']);
    }
}
