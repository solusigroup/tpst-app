<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\JurnalHeader;
use Illuminate\Auth\Access\HandlesAuthorization;

class JurnalHeaderPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:JurnalHeader');
    }

    public function view(AuthUser $authUser, JurnalHeader $jurnalHeader): bool
    {
        return $authUser->can('View:JurnalHeader');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:JurnalHeader');
    }

    public function update(AuthUser $authUser, JurnalHeader $jurnalHeader): bool
    {
        return $authUser->can('Update:JurnalHeader');
    }

    public function delete(AuthUser $authUser, JurnalHeader $jurnalHeader): bool
    {
        return $authUser->can('Delete:JurnalHeader');
    }

    public function restore(AuthUser $authUser, JurnalHeader $jurnalHeader): bool
    {
        return $authUser->can('Restore:JurnalHeader');
    }

    public function forceDelete(AuthUser $authUser, JurnalHeader $jurnalHeader): bool
    {
        return $authUser->can('ForceDelete:JurnalHeader');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:JurnalHeader');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:JurnalHeader');
    }

    public function replicate(AuthUser $authUser, JurnalHeader $jurnalHeader): bool
    {
        return $authUser->can('Replicate:JurnalHeader');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:JurnalHeader');
    }

}