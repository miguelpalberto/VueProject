<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VCard;

class VCardController extends Controller
{
    // esta função só deve ser chamada por um administrador (falta implementar a autorização)
    public function index()
    {
        $vCards = VCard::all();
        return response()->json([
            'success' => true,
            'message' => 'Successfully retrieved vcards',
            'data' => $vCards
        ], 200);
    }

    public function show($phoneNumber)
    {
        $vCard = VCard::find($phoneNumber);

        if (!$vCard) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, vcard with phone number "' . $phoneNumber . '" cannot be found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Successfully retrieved vcard',
            'data' => $vCard
        ], 200);
    }
}
