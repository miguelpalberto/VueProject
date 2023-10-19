<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VCard;

class VCardController extends Controller
{
    public function index()
    {
        $vCards = VCard::all();
        return response()->json([
            'success' => true,
            'message' => 'Successfully retrieved vcards',
            'data' => $vCards
        ], 200);
    }
}
