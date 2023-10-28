<?php

namespace App\Http\Controllers;

use App\Http\Requests\VCardRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\VCard;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

class VCardController extends Controller
{
    // esta função só deve ser chamada por um administrador (falta implementar a autorização)
    public function index() : JsonResponse
    {
        $vCards = VCard::all();
        return response()->json([
            'success' => true,
            'message' => 'Successfully retrieved vCards',
            'data' => $vCards
        ], 200);
    }

    public function show($phoneNumber) : JsonResponse
    {
        $vCard = VCard::find($phoneNumber);

        if (!$vCard) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, vCard with phone number "' . $phoneNumber . '" cannot be found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Successfully retrieved vCard',
            'data' => $vCard
        ], 200);
    }

    public function store(VCardRequest $request) : JsonResponse
    {
        $validRequest = $request->validated();

        $vCard = DB::transaction(function () use ($validRequest, $request) {
            $newVCard = new VCard();
            $newVCard->phone_number = strval($validRequest['phone_number']);
            $newVCard->name = $validRequest['name'];
            $newVCard->email = $validRequest['email'];
            $newVCard->confirmation_code = $validRequest['confirmation_code'];
            $newVCard->password = Hash::make($validRequest['password']);
            $newVCard->blocked = 0;
            $newVCard->balance = 0;
            $newVCard->max_debit = 5000;
            $newVCard->custom_options = $validRequest['custom_options'] ?? null;
            $newVCard->custom_data = $validRequest['custom_data'] ?? null;
            $newVCard->vcardCategory()->associate($validRequest['vcard_category_id']);//?????rever

            if ($request->hasFile('photo_file')) {
                $path = $request->photo_file->store('public/photos');
                $newVCard->photo_url = basename($path);
            }

            $newVCard->save();
            return $newVCard;
        });

        if (!$vCard) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, vCard could not be created'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Successfully created vCard',
            'data' => $vCard
        ], 201);
    }





    public function block(VCard $vcard){
        //falta implementar a autorização
        if ($vcard->blocked) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, vCard is already blocked'
            ], 400);
        }

        $vcard->blocked = true;
        $vcard->save();
        return response()->json([
            'success' => true,
            'message' => 'Successfully blocked vCard',
            'data' => $vcard

        ], 200);
    }
}
