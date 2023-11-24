<?php

namespace App\Http\Controllers;

use DateTime;
use App\Models\VCard;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\TransactionRequest;
use App\Http\Resources\TransactionResource;

class TransactionController extends Controller
{
    public function index(){
        return Transaction::all();
    }

    public function getVCardTransactions(VCard $vcard){
        
        $paginatedResult = Transaction::where('vcard', $vcard->phone_number)
        ->with('category')
        ->orderBy('created_at', 'desc')
        ->paginate(10);

        //$paginatedResult['data'] = TransactionResource::collection($paginatedResult->data);
        //return $paginatedResult;
        return TransactionResource::collection($paginatedResult);
    }

    public function show(Transaction $transaction){
        return $transaction;
    }

    //to do: testar
    public function store(TransactionRequest $request){
        //falta autorização
        $validRequest = $request->validated();
        $vcard = VCard::find($validRequest['vcard']);
        $isDebitTransaction = $validRequest['type'] == 'D';

        if ($isDebitTransaction && $vcard->balance < $validRequest['value']) {
            return response()->json([
                'errors' => [
                    'value' => [
                        'Sorry, you do not have enough balance to make this transaction'
                    ]
                ]
            ], 422);
        }

        if ($validRequest['value'] > $vcard->max_debit) {
            return response()->json([
                'errors' => [
                    'value' => [
                        "Sorry, you cannot make a transaction with a value higher than your maximum debit ($vcard->max_debit" . "€)"
                    ]
                ]
            ], 422);
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
}
