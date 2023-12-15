<?php

namespace App\Http\Controllers;

use stdClass;
use App\Models\VCard;
use Illuminate\Http\Request;
use App\Models\DefaultCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\VCardRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\VCardResource;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\DeleteVCardRequest;
use App\Http\Requests\UploadPhotoRequest;
use App\Http\Requests\ChangeMaxDebitRequest;
use App\Http\Requests\ChangeVCardConfirmationCodeRequest;

class VCardController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(VCard::class, 'vcard');
    }

    public function index(Request $request)
    {
        $queryable = VCard::query()->orderBy('name', 'asc');

        $searchFilter = $request->query('search');
        $filterByStatus = $request->query('status');

        if ($filterByStatus) {
            $statuses = ['blockedOnly', 'unblockedOnly'];
            if (in_array($filterByStatus, $statuses)) {
                $queryable->where('blocked', $filterByStatus == 'blockedOnly');
            }
        }

        if ($searchFilter) {
            $queryable->where(function ($query) use ($searchFilter) {
                $query->where('phone_number', 'like', "%{$searchFilter}%")
                    ->orWhere('name', 'like', "%{$searchFilter}%")
                    ->orWhere('email', 'like', "%{$searchFilter}%");
            });
        }

        $vCards = $queryable->paginate(10);

        return VCardResource::collection($vCards);
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

    public function changeMaxDebit(ChangeMaxDebitRequest $request, VCard $vcard)
    {
        $this->authorize('changeMaxDebit', $vcard);
        $validRequest = $request->validated();

        $vcard->max_debit = $validRequest['max_debit'];

        $vcard->save();

        return response()->json([
            'success' => true,
            'message' => 'Successfully updated vCard',
            'data' => $vcard
        ], 200);
    }

    public function block(VCard $vcard)
    {
        $this->authorize('block', $vcard);
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

    public function unblock(VCard $vcard)
    {
        $this->authorize('unblock', $vcard);
        if (!$vcard->blocked) {
            return response()->json([
                'success' => false,
                'message' => 'vCard is already unblocked'
            ], 400);
        }

        $vcard->blocked = false;
        $vcard->save();
        return response()->json([
            'success' => true,
            'message' => 'Successfully unblocked vCard',
            'data' => $vcard
        ], 200);
    }

    public function destroy(VCard $vcard, DeleteVCardRequest $request)
    {
        $user = $request->user();

        $validRequest = $request->validated();
        //if user is not admin, check if password and confirmation code are correct
        //else if user is admin, just delete the vcard
        if ($user->user_type != 'A') {
            if (!Hash::check($validRequest['password'], $vcard->password)) {
                return response()->json([
                    'errors' => [
                        'password' => [
                            'The password is incorrect'
                        ]
                    ]
                ], 422);
            }

            if (!Hash::check($validRequest['confirmation_code'], $vcard->confirmation_code)) {
                return response()->json([
                    'errors' => [
                        'confirmation_code' => [
                            'The confirmation code is incorrect'
                        ]
                    ]
                ], 422);
            }
        }

        if ($vcard->balance > 0) {
            return response()->json([
                'errors' => [
                    'balance' => [
                        'Cannot delete vCard with positive balance'
                    ]
                ]
            ], 422);
        }

        $hasTransactions = $vcard->transactions()->exists();

        if ($hasTransactions) {
            $vcard->delete();  // Soft delete
            $vcard->transactions()->delete();
            $vcard->categories()->delete();
        } else {
            $vcard->categories()->forceDelete();
            $vcard->forceDelete();  // Hard delete
        }

        return response()->noContent();
    }

    public function changeConfirmationCode(VCard $vcard, ChangeVCardConfirmationCodeRequest $request)
    {
        $this->authorize('changeConfirmationCode', $vcard);

        if (!Hash::check($request->password, $vcard->password)) {
            return response()->json([
                'errors' => [
                    'password' => [
                        'The password is incorrect'
                    ]
                ]
            ], 422);
        }

        if (Hash::check($request->confirmation_code, $vcard->confirmation_code)) {
            return response()->json([
                'errors' => [
                    'confirmation_code' => [
                        'The new confirmation code must be different from the current one'
                    ]
                ]
            ], 422);
        }

        $validRequest = $request->validated();

        $vcard->confirmation_code = bcrypt($validRequest['confirmation_code']);
        $vcard->save();

        return response()->json([
            'success' => true,
            'message' => 'Successfully changed confirmation code'
        ], 200);
    }

    public function deletePhoto(VCard $vcard)
    {

        $this->authorize('deletePhoto', $vcard);

        if ($vcard->photo_url != null) {
            Storage::delete('public/fotos/' . $vcard->photo_url);
        }

        $vcard->photo_url = null;
        $vcard->save();

        return response()->noContent();
    }

    public function uploadPhoto(VCard $vcard, UploadPhotoRequest $request)
    {
        $this->authorize('uploadPhoto', $vcard);
        $validRequest = $request->validated();

        //remove from storage
        if ($vcard->photo_url != null) {
            Storage::delete('public/fotos/' . $vcard->photo_url);
        }

        if ($request->hasFile('photo_file')) {
            $path = $request->photo_file->store('public/fotos');
            $vcard->photo_url = basename($path);
        }

        $vcard->save();

        return response()->json([
            'success' => true,
            'message' => 'Successfully uploaded photo',
        ], 200);
    }


    public function getVCardBalanceStatistics(VCard $vcard, Request $request)
    {
        $this->authorize('getVCardBalanceStatistics', $vcard);

        $filterByRange = $request->query('range');

        //get just balances and datetimes
        $ranges = ['30', '60', 'year', 'all'];

        $queryable = $vcard->transactions()->orderBy('datetime', 'asc');

        if ($filterByRange) {
            if (in_array($filterByRange, $ranges)) {
                if ($filterByRange == '30') {
                    $queryable->where('date', '>=', now()->subDays(30));
                } else if ($filterByRange == '60') {
                    $queryable->where('date', '>=', now()->subDays(60));
                } else if ($filterByRange == 'year') {
                    $queryable->where('date', '>=', now()->subYear());
                }
            }
        } else {
            $queryable->where('date', '>=', now()->subDays(30));
        }

        $chartData = new stdClass();
        $chartData->labels = [];
        $chartData->data = [];
        foreach ($queryable->get() as $transaction) {
            $chartData->labels[] = $transaction->datetime;
            $chartData->data[] = $transaction->new_balance;
        }

        return $chartData;
    }


    public function getVCardTransactionsStatistics(VCard $vcard, Request $request)
    {
        $this->authorize('getVCardTransactionsStatistics', $vcard);

        $filterByRange = $request->query('range');

        //get just balances and datetimes
        $ranges = ['30', '60', 'year', 'all'];

        $queryable = $vcard->transactions()->orderBy('datetime', 'asc');

        if ($filterByRange) {
            if (in_array($filterByRange, $ranges)) {
                if ($filterByRange == '30') {
                    $queryable->where('date', '>=', now()->subDays(30));
                } else if ($filterByRange == '60') {
                    $queryable->where('date', '>=', now()->subDays(60));
                } else if ($filterByRange == 'year') {
                    $queryable->where('date', '>=', now()->subYear());
                }
            }
        } else {
            $queryable->where('date', '>=', now()->subDays(30));
        }

        $chartData = new stdClass();
        $chartData->labels = [];
        $chartData->data = [];
        foreach ($queryable->get() as $transaction) {
            $chartData->labels[] = $transaction->datetime;
            // Check if transaction type is 'D' (debit) and apply the '-' prefix
            $value = ($transaction->type === 'D') ? ($transaction->value * -1) : $transaction->value;

            $chartData->data[] = $value;
        }

        return $chartData;
    }
    public function getVCardTransactionsCategoriesStatistics(VCard $vcard, Request $request)
    {
        $this->authorize('getVCardTransactionsCategoriesStatistics', $vcard);
    
        $filterByRange = $request->query('range');
        $type = $request->query('type');
    
        // get just balances and datetimes
        $ranges = ['30', '60', 'year', 'all'];
    
        $queryable = $vcard->transactions()->with('category')
            ->select('category_id', DB::raw('SUM(value) as total_value'))
            ->where('type', '=', $type)
            ->groupBy('category_id')
            ->orderBy('category_id', 'asc');
    
        if ($filterByRange) {
            if (in_array($filterByRange, $ranges)) {
                if ($filterByRange == '30') {
                    $queryable->where('date', '>=', now()->subDays(30));
                } else if ($filterByRange == '60') {
                    $queryable->where('date', '>=', now()->subDays(60));
                } else if ($filterByRange == 'year') {
                    $queryable->where('date', '>=', now()->subYear());
                }
            }
        } else {
            $queryable->where('date', '>=', now()->subDays(30));
        }
    
        $chartData = new stdClass();
        $chartData->labels = [];
        $chartData->data = [];
    
        foreach ($queryable->get() as $result) {
        //    if($transaction->type === 'D'){
            $chartData->labels[] = $result->category->name ?? 'No Category';
            $chartData->data[] = $result->total_value;
        }
    
        return $chartData;
    }


    public function getActiveVcardsStatistics(Request $request)
    {
        if (!Gate::allows('vcards-statistics')) {
            abort(403);
        }

        $activeVcardsCount = VCard::whereNull('deleted_at')->count();
    
        return $activeVcardsCount;
    }

    public function getGlobalBalanceStatistics(Request $request)
    {
        if (!Gate::allows('vcards-statistics')) {
            abort(403);
        }

        $totalGlobalBalance = VCard::sum('balance');

        return $totalGlobalBalance;
    }
}
