<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WasteCategory;

class WasteCategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_waste_category');
    }

    public function view(User $user, WasteCategory $category): bool
    {
        return $user->tenant_id === $category->tenant_id &&
               $user->can('view_waste_category');
    }

    public function create(User $user): bool
    {
        return $user->can('create_waste_category');
    }

    public function update(User $user, WasteCategory $category): bool
    {
        return $user->tenant_id === $category->tenant_id &&
               $user->can('update_waste_category');
    }

    public function delete(User $user, WasteCategory $category): bool
    {
        return $user->tenant_id === $category->tenant_id &&
               $user->can('delete_waste_category');
    }
}
