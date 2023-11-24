<?php

namespace App\Http\Controllers;

use App\Models\VCard;
use Illuminate\Http\Request;
use App\Models\DefaultCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\VCardRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

class VCardController extends Controller
{
    // esta função só deve ser chamada por um administrador (falta implementar a autorização)
    public function index()
    {
        return VCard::all();
    }

    public function show($phoneNumber)
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

    public function store(VCardRequest $request)
    {
        $validRequest = $request->validated();

        $vCard = DB::transaction(function () use ($validRequest, $request) {
            $newVCard = new VCard();
            $newVCard->phone_number = $validRequest['phone_number'];
            $newVCard->name = $validRequest['name'];
            $newVCard->email = $validRequest['email'];
            $newVCard->confirmation_code = Hash::make($validRequest['confirmation_code']);
            $newVCard->password = Hash::make($validRequest['password']);
            $newVCard->blocked = 0;
            $newVCard->balance = 0;
            $newVCard->max_debit = 5000;
            $newVCard->custom_options = $validRequest['custom_options'] ?? null;
            $newVCard->custom_data = $validRequest['custom_data'] ?? null;


            if ($request->hasFile('photo_file')) {
                $path = $request->photo_file->store('public/fotos');
                $newVCard->photo_url = basename($path);
            }

            $newVCard->save();
            $phoneNumber = $validRequest['phone_number'];
            // Manually insert associations with default categories
            $categories = DefaultCategory::all()->map(function ($defaultCategory) use ($phoneNumber) {
                return [
                    'vcard' => $phoneNumber,
                    'type' => $defaultCategory->type,
                    'name' => $defaultCategory->name,
                    'custom_options' => $defaultCategory->custom_options,
                    'custom_data' => $defaultCategory->custom_data
                ];
            })->toArray();

            DB::table('categories')->insert($categories);

            return $newVCard;
        });

        request()->request->add([
            'username' => $validRequest['phone_number'],
            'password' => $validRequest['password']
        ]);

        $request = Request::create(env('PASSPORT_URL') . '/api/auth/login', 'POST');
        $response = Route::dispatch($request);
        return json_decode((string) $response->content(), true);
    }

    public function update(VCard $vCard, VCardRequest $request)
    {
        $validRequest = $request->validated();

        $vCard->name = $validRequest['name'];
        $vCard->email = $validRequest['email'];
        $vCard->confirmation_code = $validRequest['confirmation_code'];
        $vCard->password = Hash::make($validRequest['password']);
        $vCard->blocked = $validRequest['blocked'];
        $vCard->balance = $validRequest['balance'];
        $vCard->max_debit = $validRequest['max_debit'];
        $vCard->customOptions = $validRequest['custom_options'] ?? null;
        $vCard->customData = $validRequest['custom_data'] ?? null;

        if ($request->hasFile('photo_file')) {
            $path = $request->photo_file->store('public/photos');
            $vCard->photo_url = basename($path);
        }

        $vCard->save();

        return $vCard;
    }


    public function updateCategories(VCard $vCard, VCardRequest $request): JsonResponse
    {
        $validRequest = $request->validated();

        $vCard->name = $validRequest['name'];
        $vCard->email = $validRequest['email'];
        $vCard->confirmation_code = $validRequest['confirmation_code'];
        $vCard->password = Hash::make($validRequest['password']);
        $vCard->blocked = $validRequest['blocked'];
        $vCard->balance = $validRequest['balance'];
        $vCard->max_debit = $validRequest['max_debit'];
        $vCard->customOptions = $validRequest['custom_options'] ?? null;
        $vCard->customData = $validRequest['custom_data'] ?? null;

        if ($request->hasFile('photo_file')) {
            $path = $request->photo_file->store('public/photos');
            $vCard->photo_url = basename($path);
        }

        $vCard->save();

        return response()->json([
            'success' => true,
            'message' => 'Successfully updated vcard',
            'data' => $vCard
        ], 200);
    }


    public function block(VCard $vcard)
    {
        //falta implementar a autorização
        if ($vcard->blocked) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, vCard is already blocked'
            ], 400);
        }

        $vcard->blocked = true;
        $vcard->save();
        return $vcard;
    }

    public function unblock(VCard $vcard)
    {
        //falta implementar a autorização
        if (!$vcard->blocked) {
            return response()->json([
                'success' => false,
                'message' => 'vCard is already unblocked'
            ], 400);
        }

        $vcard->blocked = false;
        $vcard->save();
        return $vcard;
    }

    //ESTATISTICAS
    //Ver biblioteca vue-chartjs

    //todo - incompleto
    //Para User apenas (nao admin):
    public function getVCardStats(VCard $vcard)
    {
        //ver media dinheiro gasto por mes (no presente ano) (bar chart)
        $sql = "SELECT SUM([value]) FROM transactions WHERE [type] = 'D' AND vcard = @vcardNumber   AND YEAR([date]) = YEAR(CURRENT_DATE()) GROUP BY MONTH([date]) ORDER BY MONTH([date]);"; //todo fiqeui aqui


        //ver % dinheiro gasto por categoria (pie chart)
        $sql2 = "SELECT SUM(t.[value]) FROM transactions t JOIN categories c ON c.vcard=t.vcard WHERE t.[type] = 'D' AND t.vcard = @vcardNumber GROUP BY c.name ORDER BY c.name;";
    }
}
