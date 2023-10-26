<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\VCard;

class TransactionController extends Controller
{
    public function index(){
        $transactions = Transaction::all();
        return response()->json([
            'success' => true,
            'message' => 'Successfully retrieved transactions',
            'data' => $transactions
        ], 200);
    }

    public function showByPhoneNumber($phoneNumber){
        if (VCard::find($phoneNumber) == null) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, VCard with phone number "' . $phoneNumber . '" cannot be found'
            ], 400);
        }

        $transactions = Transaction::where('vcard', $phoneNumber)
            ->orWhere('pair_vcard', $phoneNumber)
            ->with('category')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Successfully retrieved transactions',
            'data' => $transactions
        ], 200);
    }
}
