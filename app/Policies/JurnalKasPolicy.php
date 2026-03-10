<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\JurnalKas;
use Illuminate\Auth\Access\HandlesAuthorization;

class JurnalKasPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:JurnalKas');
    }

    public function view(AuthUser $authUser, JurnalKas $jurnalKas): bool
    {
        return $authUser->can('View:JurnalKas');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:JurnalKas');
    }

    public function update(AuthUser $authUser, JurnalKas $jurnalKas): bool
    {
        return $authUser->can('Update:JurnalKas');
    }

    public function delete(AuthUser $authUser, JurnalKas $jurnalKas): bool
    {
        return $authUser->can('Delete:JurnalKas');
    }

    public function restore(AuthUser $authUser, JurnalKas $jurnalKas): bool
    {
        return $authUser->can('Restore:JurnalKas');
    }

    public function forceDelete(AuthUser $authUser, JurnalKas $jurnalKas): bool
    {
        return $authUser->can('ForceDelete:JurnalKas');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:JurnalKas');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:JurnalKas');
    }

    public function replicate(AuthUser $authUser, JurnalKas $jurnalKas): bool
    {
        return $authUser->can('Replicate:JurnalKas');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:JurnalKas');
    }

}