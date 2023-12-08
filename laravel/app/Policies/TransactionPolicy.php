<?php

namespace App\Policies;

use App\Models\VCard;
use App\Models\AuthUser;
use App\Models\Transaction;
use Illuminate\Auth\Access\Response;

class TransactionPolicy
{
    public function view(AuthUser $authUser, Transaction $transaction): bool
    {
        return $authUser->username == $transaction->vcard;
    }

    public function create(AuthUser $authUser): bool
    {
        return true;
    }

    public function update(AuthUser $authUser, Transaction $transaction): bool
    {
        return $authUser->username == $transaction->vcard;
    }
}
