<?php

namespace App\Http\Controllers;

use DateTime;
use App\Models\VCard;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\TransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Policies\TransactionPolicy;

class TransactionController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Transaction::class, 'transaction');
    }

    public function getVCardTransactions(VCard $vcard, Request $request){
        $this->authorize('getVCardTransactions', $vcard);

        if ($vcard->blocked) {
            return response()->json([
                'success' => false,
                'message' => 'This vCard is blocked'
            ], 400);
        }

        $queryable = Transaction::query()->where('vcard', $vcard->phone_number);

        $orderBy = $request->query('orderBy');
        $filterByType = $request->query('filterByType');
        $filterByCategory = $request->query('filterByCategory');
        $filterByDate = $request->query('filterByDate');
        $filterByPaymentType = $request->query('filterByPaymentType');
        $search = $request->query('search');

        if ($orderBy) {
            $orderAsc = $request->query('asc') != null ? 'asc' : 'desc';
            if ($orderBy == 'date') {
                $queryable->orderBy('created_at', $orderAsc);
            } else if ($orderBy == 'value') {
                $queryable->orderBy('value', $orderAsc);
            }
        }
        else {
            $queryable->orderBy('created_at', 'desc');
        }

        if ($filterByType && ($filterByType == 'D' || $filterByType == 'C')) {
            $queryable->where('type', $filterByType);
        }

        if ($filterByPaymentType) {
            $payment_types = ['VISA', 'MB', 'IBAN', 'PAYPAL', 'MBWAY', 'VCARD'];
            if (in_array($filterByPaymentType, $payment_types)) {
                $queryable->where('payment_type', $filterByPaymentType);
            }
        }

        if ($filterByCategory) {
            $queryable->where('category_id', $filterByCategory);
        }

        if ($filterByDate) {
            $queryable->where('date', $filterByDate);
        }

        if ($search) {
            $queryable->where('payment_reference', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        }
        
        $paginatedResult = $queryable->paginate(10);

        return TransactionResource::collection($paginatedResult);
    }

    public function show(Transaction $transaction){
        return $transaction;
    }

    public function store(TransactionRequest $request){

        $validRequest = $request->validated();
        $vcard = VCard::find($validRequest['vcard']);
        $isDebitTransaction = $validRequest['type'] == 'D';

        if ($isDebitTransaction && $request->user()->username != $vcard->phone_number) {
            return response()->json([
                'errors' => [
                    'vcard' => [
                        'You can only make debit transactions from your own vCard'
                    ]
                ]
            ], 422);
        }

        if ($isDebitTransaction && !Hash::check($validRequest['confirmation_code'], $vcard->confirmation_code)) {
            return response()->json([
                'errors' => [
                    'confirmation_code' => [
                        'The confirmation code is incorrect'
                    ]
                ]
            ], 422);
        }

        if ($isDebitTransaction && $vcard->balance < $validRequest['value']) {
            return response()->json([
                'errors' => [
                    'value' => [
                        'Sorry, you do not have enough balance to make this transaction'
                    ]
                ]
            ], 422);
        }

        if ($isDebitTransaction && $validRequest['value'] > $vcard->max_debit) {
            return response()->json([
                'errors' => [
                    'value' => [
                        "Sorry, you cannot make a transaction with a value higher than your maximum debit ($vcard->max_debit" . "€)"
                    ]
                ]
            ], 422);
        }
    
        if (isset($validRequest['category_id'])) {
            $category = $vcard->categories->where('id', $validRequest['category_id'])->first();

            if (!$category) {
                return response()->json([
                    'errors' => [
                        'category_id' => [
                            'Category was not found'
                        ]
                    ]
                ], 422);
            }

            if ($category->type != $validRequest['type']) {
                return response()->json([
                    'errors' => [
                        'category_id' => [
                            'Category type must be the same as the transaction type'
                        ]
                    ]
                ], 422);
            }
        }
        
        $transaction = DB::transaction(function () use ($validRequest, $vcard, $isDebitTransaction) {
            $utcDatetimeNow = new DateTime('now', new \DateTimeZone('UTC'));
            
            $transaction = new Transaction();
            $transaction->vcard = $vcard->phone_number;
            $transaction->date = $utcDatetimeNow->format('Y-m-d');
            $transaction->datetime = $utcDatetimeNow;
            $transaction->type = $validRequest['type'];
            $transaction->value = $validRequest['value'];
            $transaction->payment_type = $validRequest['payment_type'];
            $transaction->payment_reference = $validRequest['payment_reference'];
            $transaction->old_balance = $vcard->balance;
            $transaction->new_balance = $isDebitTransaction ? $vcard->balance - $validRequest['value'] : $vcard->balance + $validRequest['value'];
            $transaction->category_id = $validRequest['category_id'] ?? null;
            $transaction->description = $validRequest['description'] ?? null;
            $transaction->pair_vcard = $validRequest['pair_vcard'] ?? null;
            $transaction->custom_options = $validRequest['custom_options'] ?? null;
            $transaction->custom_data = $validRequest['custom_data'] ?? null;
            $transaction->save();

            if ($transaction->payment_type == 'VCARD'){
                $pairVCard = VCard::find($validRequest['pair_vcard']);

                if (!$isDebitTransaction && $validRequest['payment_type'] == 'VCARD' && $pairVCard->balance < $validRequest['value']) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Sorry, the destination vCard does not have enough balance to receive this transaction'
                    ], 400);
                }

                $pairTransaction = new Transaction();
                $pairTransaction->vcard = $validRequest['pair_vcard'];
                $pairTransaction->pair_vcard = $vcard->phone_number;
                $pairTransaction->date = $utcDatetimeNow->format('Y-m-d');
                $pairTransaction->datetime = $utcDatetimeNow;
                $pairTransaction->type = $isDebitTransaction ? 'C' : 'D';
                $pairTransaction->value = $validRequest['value'];
                $pairTransaction->payment_type = 'VCARD';
                $pairTransaction->payment_reference = $validRequest['vcard'];
                $pairTransaction->old_balance = $pairVCard->balance;
                $pairTransaction->new_balance = $isDebitTransaction ? $pairVCard->balance + $validRequest['value'] : $pairVCard->balance - $validRequest['value'];
                $pairTransaction->description = $validRequest['description'] ?? null;
                $pairTransaction->custom_options = $validRequest['custom_options'] ?? null;
                $pairTransaction->custom_data = $validRequest['custom_data'] ?? null;
                $pairTransaction->save();

                $pairVCard->balance = $pairTransaction->new_balance;
                $pairVCard->save();
            }

            $vcard->balance = $transaction->new_balance;
            $vcard->save();

            return $transaction;
        });

        return $transaction;
    }

    public function update(UpdateTransactionRequest $request, Transaction $transaction){
        $validRequest = $request->validated();
        
        if (isset($validRequest['category_id'])) {
            $category = $transaction->vCard->categories->where('id', $validRequest['category_id'])->first();

            if (!$category) {
                return response()->json([
                    'errors' => [
                        'category_id' => [
                            'Category was not found'
                        ]
                    ]
                ], 422);
            }

            if ($category->type != $transaction->type) {
                return response()->json([
                    'errors' => [
                        'category_id' => [
                            'Category type must be the same as the transaction type'
                        ]
                    ]
                ], 422);
            }
        }

        $transaction->category_id = $validRequest['category_id'] ?? null;
        $transaction->description = $validRequest['description'] ?? null;

        $transaction->save();

        return new TransactionResource($transaction);
    }
}
