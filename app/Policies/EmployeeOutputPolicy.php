<?php

namespace App\Policies;

use App\Models\User;
use App\Models\EmployeeOutput;

class EmployeeOutputPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['manajemen', 'hrd', 'super_admin']);
    }

    public function view(User $user, EmployeeOutput $output): bool
    {
        return $user->tenant_id === $output->tenant_id &&
               $user->hasRole(['manajemen', 'hrd', 'super_admin']);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['manajemen', 'hrd', 'super_admin']);
    }

    public function update(User $user, EmployeeOutput $output): bool
    {
        return $user->tenant_id === $output->tenant_id &&
               $user->hasRole(['manajemen', 'hrd', 'super_admin']);
    }

    public function delete(User $user, EmployeeOutput $output): bool
    {
        return $user->tenant_id === $output->tenant_id &&
               $user->hasRole(['manajemen', 'super_admin']);
    }
}
