<?php

namespace App\Policies;

use App\Models\User;
use App\Models\EmployeeOutput;

class EmployeeOutputPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_employee_output');
    }

    public function view(User $user, EmployeeOutput $output): bool
    {
        return $user->tenant_id === $output->tenant_id &&
               $user->can('view_employee_output');
    }

    public function create(User $user): bool
    {
        return $user->can('create_employee_output');
    }

    public function update(User $user, EmployeeOutput $output): bool
    {
        return $user->tenant_id === $output->tenant_id &&
               $user->can('update_employee_output');
    }

    public function delete(User $user, EmployeeOutput $output): bool
    {
        return $user->tenant_id === $output->tenant_id &&
               $user->can('delete_employee_output');
    }
}
