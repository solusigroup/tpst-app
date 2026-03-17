<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WasteCategory;

class WasteCategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['manajemen', 'hrd', 'super_admin']);
    }

    public function view(User $user, WasteCategory $category): bool
    {
        return $user->tenant_id === $category->tenant_id;
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['manajemen', 'hrd', 'super_admin']);
    }

    public function update(User $user, WasteCategory $category): bool
    {
        return $user->tenant_id === $category->tenant_id &&
               $user->hasRole(['manajemen', 'hrd', 'super_admin']);
    }

    public function delete(User $user, WasteCategory $category): bool
    {
        return $user->tenant_id === $category->tenant_id &&
               $user->hasRole(['manajemen', 'super_admin']);
    }
}
