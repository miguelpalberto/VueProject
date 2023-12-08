<?php

namespace App\Policies;

use App\Models\AuthUser;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class TransactionPolicy
{
    public function view(AuthUser $authUser, Transaction $transaction): bool
    {
        return $authUser->username == $transaction->vcard;
    }

    public function create(AuthUser $authUser): bool
    {
        return !Auth::guest();
    }

    public function update(AuthUser $authUser, Transaction $transaction): bool
    {
        return $authUser->username == $transaction->vcard;
    }
}
