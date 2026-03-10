<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Armada;
use Illuminate\Auth\Access\HandlesAuthorization;

class ArmadaPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Armada');
    }

    public function view(AuthUser $authUser, Armada $armada): bool
    {
        return $authUser->can('View:Armada');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Armada');
    }

    public function update(AuthUser $authUser, Armada $armada): bool
    {
        return $authUser->can('Update:Armada');
    }

    public function delete(AuthUser $authUser, Armada $armada): bool
    {
        return $authUser->can('Delete:Armada');
    }

    public function restore(AuthUser $authUser, Armada $armada): bool
    {
        return $authUser->can('Restore:Armada');
    }

    public function forceDelete(AuthUser $authUser, Armada $armada): bool
    {
        return $authUser->can('ForceDelete:Armada');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Armada');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Armada');
    }

    public function replicate(AuthUser $authUser, Armada $armada): bool
    {
        return $authUser->can('Replicate:Armada');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Armada');
    }

}