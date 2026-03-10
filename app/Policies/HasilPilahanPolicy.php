<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\HasilPilahan;
use Illuminate\Auth\Access\HandlesAuthorization;

class HasilPilahanPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:HasilPilahan');
    }

    public function view(AuthUser $authUser, HasilPilahan $hasilPilahan): bool
    {
        return $authUser->can('View:HasilPilahan');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:HasilPilahan');
    }

    public function update(AuthUser $authUser, HasilPilahan $hasilPilahan): bool
    {
        return $authUser->can('Update:HasilPilahan');
    }

    public function delete(AuthUser $authUser, HasilPilahan $hasilPilahan): bool
    {
        return $authUser->can('Delete:HasilPilahan');
    }

    public function restore(AuthUser $authUser, HasilPilahan $hasilPilahan): bool
    {
        return $authUser->can('Restore:HasilPilahan');
    }

    public function forceDelete(AuthUser $authUser, HasilPilahan $hasilPilahan): bool
    {
        return $authUser->can('ForceDelete:HasilPilahan');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:HasilPilahan');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:HasilPilahan');
    }

    public function replicate(AuthUser $authUser, HasilPilahan $hasilPilahan): bool
    {
        return $authUser->can('Replicate:HasilPilahan');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:HasilPilahan');
    }

}