<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Ritase;
use Illuminate\Auth\Access\HandlesAuthorization;

class RitasePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Ritase');
    }

    public function view(AuthUser $authUser, Ritase $ritase): bool
    {
        return $authUser->can('View:Ritase');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Ritase');
    }

    public function update(AuthUser $authUser, Ritase $ritase): bool
    {
        return $authUser->can('Update:Ritase');
    }

    public function delete(AuthUser $authUser, Ritase $ritase): bool
    {
        return $authUser->can('Delete:Ritase');
    }

    public function restore(AuthUser $authUser, Ritase $ritase): bool
    {
        return $authUser->can('Restore:Ritase');
    }

    public function forceDelete(AuthUser $authUser, Ritase $ritase): bool
    {
        return $authUser->can('ForceDelete:Ritase');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Ritase');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Ritase');
    }

    public function replicate(AuthUser $authUser, Ritase $ritase): bool
    {
        return $authUser->can('Replicate:Ritase');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Ritase');
    }

}