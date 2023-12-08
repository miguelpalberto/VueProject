<?php

namespace App\Policies;

use App\Models\AuthUser;
use App\Models\DefaultCategory;
use Illuminate\Auth\Access\Response;

class DefaultCategoryPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->user_type == 'A';
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(AuthUser $authUser): bool
    {
        return $authUser->user_type == 'A';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(AuthUser $authUser, DefaultCategory $defaultCategory): bool
    {
        return $authUser->user_type == 'A';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(AuthUser $authUser, DefaultCategory $defaultCategory): bool
    {
        return $authUser->user_type == 'A';
    }
}
