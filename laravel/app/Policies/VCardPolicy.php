<?php

namespace App\Policies;

use App\Models\AuthUser;
use App\Models\VCard;
use Illuminate\Auth\Access\Response;

class VCardPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(AuthUser $authUser): bool
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(AuthUser $authUser, VCard $vCard): bool
    {
        //
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(AuthUser $authUser): bool
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(AuthUser $authUser, VCard $vCard): bool
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(AuthUser $authUser, VCard $vCard): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(AuthUser $authUser, VCard $vCard): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(AuthUser $authUser, VCard $vCard): bool
    {
        //
    }


    public function getVCardTransactions(AuthUser $authUser, VCard $vcard): bool
    {
        return $authUser->username == $vcard->phone_number;
    }
}
