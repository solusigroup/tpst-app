<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WageRate;

class WageRatePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['admin', 'hrd', 'keuangan', 'superadmin']);
    }

    public function view(User $user, WageRate $rate): bool
    {
        return $user->tenant_id === $rate->tenant_id;
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['admin', 'hrd', 'superadmin']);
    }

    public function update(User $user, WageRate $rate): bool
    {
        return $user->tenant_id === $rate->tenant_id &&
            $user->hasRole(['admin', 'hrd', 'superadmin']);
    }

    public function delete(User $user, WageRate $rate): bool
    {
        return $user->tenant_id === $rate->tenant_id &&
            $user->hasRole(['admin', 'superadmin']);
    }
}
