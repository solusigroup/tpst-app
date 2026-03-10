<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Klien;
use Illuminate\Auth\Access\HandlesAuthorization;

class KlienPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Klien');
    }

    public function view(AuthUser $authUser, Klien $klien): bool
    {
        return $authUser->can('View:Klien');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Klien');
    }

    public function update(AuthUser $authUser, Klien $klien): bool
    {
        return $authUser->can('Update:Klien');
    }

    public function delete(AuthUser $authUser, Klien $klien): bool
    {
        return $authUser->can('Delete:Klien');
    }

    public function restore(AuthUser $authUser, Klien $klien): bool
    {
        return $authUser->can('Restore:Klien');
    }

    public function forceDelete(AuthUser $authUser, Klien $klien): bool
    {
        return $authUser->can('ForceDelete:Klien');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Klien');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Klien');
    }

    public function replicate(AuthUser $authUser, Klien $klien): bool
    {
        return $authUser->can('Replicate:Klien');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Klien');
    }

}