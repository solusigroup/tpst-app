<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Coa;
use Illuminate\Auth\Access\HandlesAuthorization;

class CoaPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Coa');
    }

    public function view(AuthUser $authUser, Coa $coa): bool
    {
        return $authUser->can('View:Coa');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Coa');
    }

    public function update(AuthUser $authUser, Coa $coa): bool
    {
        return $authUser->can('Update:Coa');
    }

    public function delete(AuthUser $authUser, Coa $coa): bool
    {
        return $authUser->can('Delete:Coa');
    }

    public function restore(AuthUser $authUser, Coa $coa): bool
    {
        return $authUser->can('Restore:Coa');
    }

    public function forceDelete(AuthUser $authUser, Coa $coa): bool
    {
        return $authUser->can('ForceDelete:Coa');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Coa');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Coa');
    }

    public function replicate(AuthUser $authUser, Coa $coa): bool
    {
        return $authUser->can('Replicate:Coa');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Coa');
    }

}