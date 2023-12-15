<?php

namespace App\Policies;

use App\Models\AuthUser;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use App\Models\VCard;

class TransactionPolicy
{
    public function view(AuthUser $authUser, Transaction $transaction): bool
    {
        return $authUser->username == $transaction->vcard;
    }

    public function create(AuthUser $authUser): bool
    {
        return !Auth::guest() && $authUser->blocked == false;
    }

    public function update(AuthUser $authUser, Transaction $transaction): bool
    {
        return  $authUser->blocked == false && $authUser->userType != 'A' &&  $authUser->username == $transaction->vcard;
    }

    // public function getAllTransactionStatistics(AuthUser $authUser): bool
    // {
    //     //return true;
    //     return $authUser->user_type == 'A';
    // }
}
