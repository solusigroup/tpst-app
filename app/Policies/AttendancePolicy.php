<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Attendance;

class AttendancePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['manajemen', 'hrd', 'super_admin']);
    }

    public function view(User $user, Attendance $attendance): bool
    {
        return $user->tenant_id === $attendance->tenant_id &&
               $user->hasRole(['manajemen', 'hrd', 'super_admin']);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['manajemen', 'hrd', 'super_admin']);
    }

    public function update(User $user, Attendance $attendance): bool
    {
        return $user->tenant_id === $attendance->tenant_id &&
               $user->hasRole(['manajemen', 'hrd', 'super_admin']);
    }

    public function delete(User $user, Attendance $attendance): bool
    {
        return $user->tenant_id === $attendance->tenant_id &&
               $user->hasRole(['manajemen', 'super_admin']);
    }

    public function quickCheckIn(User $user): bool
    {
        return $user->hasRole(['manajemen', 'hrd', 'super_admin']);
    }

    public function quickCheckOut(User $user): bool
    {
        return $user->hasRole(['manajemen', 'hrd', 'super_admin']);
    }
}
