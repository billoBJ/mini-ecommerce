<?php

namespace App\Policies\Products;

use App\Models\User;

class ProductPolicy
{
    /**
     * Products have no ownership/role scoping yet — any authenticated
     * user may manage the catalog. Centralizing that decision here
     * (instead of inlining `true` in each FormRequest) means a future
     * role check only has to change in one place.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user): bool
    {
        return true;
    }

    public function delete(User $user): bool
    {
        return true;
    }

    /**
     * Products aren't soft-deletable — these are unreachable in practice.
     */
    public function restore(User $user): bool
    {
        return false;
    }

    public function forceDelete(User $user): bool
    {
        return false;
    }
}
