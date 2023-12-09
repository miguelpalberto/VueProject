<?php

namespace App\Policies;

use App\Models\VCard;
use App\Models\AuthUser;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Auth;

class VCardPolicy
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
    public function create(?AuthUser $authUser): bool
    {
        return $authUser == null;
    }

    public function changeMaxDebit(AuthUser $authUser, VCard $vCard): bool
    {
        return $authUser->user_type == 'A';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(AuthUser $authUser, VCard $vCard): bool
    {
        return $authUser->user_type == 'A' || $authUser->username == $vCard->phone_number;
    }

    public function block(AuthUser $authUser, VCard $vCard): bool
    {
        return $authUser->user_type == 'A';
    }

    public function unblock(AuthUser $authUser, VCard $vCard): bool
    {
        return $authUser->user_type == 'A';
    }

    public function changeConfirmationCode(AuthUser $authUser, VCard $vCard): bool
    {
        return $authUser->userType != 'A' &&  $authUser->username == $vCard->phone_number;
    }

    public function deletePhoto(AuthUser $authUser, VCard $vCard): bool
    {
        return $authUser->userType != 'A' &&  $authUser->username == $vCard->phone_number;
    }

    public function uploadPhoto(AuthUser $authUser, VCard $vCard): bool
    {
        return $authUser->userType != 'A' &&  $authUser->username == $vCard->phone_number;
    }

    public function getVCardTransactions(AuthUser $authUser, VCard $vCard): bool
    {
        return $authUser->userType != 'A' && $authUser->username == $vCard->phone_number;
    }

    public function getVCardCategories(AuthUser $authUser, VCard $vCard): bool
    {
        return $authUser->userType != 'A' &&  $authUser->username == $vCard->phone_number;
    }

}
