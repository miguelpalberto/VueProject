<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;

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
}
