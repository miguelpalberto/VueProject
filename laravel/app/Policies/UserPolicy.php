<?php

namespace App\Policies;

use App\Models\AuthUser;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->user_type === 'A';
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(AuthUser $authUser): bool
    {
        return $authUser->user_type === 'A';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(AuthUser $authUser, User $model): bool
    {
        return $authUser->user_type === 'A';
    }
}
