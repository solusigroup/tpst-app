<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Attendance;

class AttendancePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_attendance');
    }

    public function view(User $user, Attendance $attendance): bool
    {
        return $user->tenant_id === $attendance->tenant_id &&
               $user->can('view_attendance');
    }

    public function create(User $user): bool
    {
        return $user->can('create_attendance');
    }

    public function update(User $user, Attendance $attendance): bool
    {
        return $user->tenant_id === $attendance->tenant_id &&
               $user->can('update_attendance');
    }

    public function delete(User $user, Attendance $attendance): bool
    {
        return $user->tenant_id === $attendance->tenant_id &&
               $user->can('delete_attendance');
    }

    public function quickCheckIn(User $user): bool
    {
        return $user->can('create_attendance');
    }

    public function quickCheckOut(User $user): bool
    {
        return $user->can('create_attendance');
    }
}
