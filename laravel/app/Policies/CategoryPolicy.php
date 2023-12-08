<?php

namespace App\Policies;

use App\Models\AuthUser;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;

class CategoryPolicy
{
    /**
     * Determine whether the user can create models.
     */
    public function create(AuthUser $authUser): bool
    {
        return !Auth::guest() && $authUser->user_type != 'A';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(AuthUser $authUser, Category $category): bool
    {
        return $authUser->userType != 'A' &&  $authUser->username == $category->vcard;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(AuthUser $authUser, Category $category): bool
    {
        return $authUser->userType != 'A' &&  $authUser->username == $category->vcard;
    }
}
