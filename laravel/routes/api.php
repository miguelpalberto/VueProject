<?php

use App\Http\Controllers\auth\AuthController;
use App\Http\Controllers\AuthUserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DefaultCategoryController;
use App\Http\Controllers\VCardController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


//AUTH PASSPORT
Route::post('auth/login', [AuthController::class, 'login']);
Route::post('vcards', [VCardController::class, 'store']);


Route::middleware('auth:api')->group(
    function () {
        // CATEGORIES
        Route::get('vcards/{vcard}/categories', [CategoryController::class, 'getVCardCategories']);
        Route::apiResource('categories', CategoryController::class)->except(['show']); //G4.1, G2.1

        // DEFAULT CATEGORIES
        Route::apiResource('defaultCategories', DefaultCategoryController::class)->except(['show']);

        // AUTH USERS
        Route::get('authUsers/me', [AuthUserController::class, 'me']);
        Route::get('authUsers', [AuthUserController::class, 'index']);
        Route::patch('authUsers/changePassword', [AuthUserController::class, 'changePassword']);

        // TRANSACTIONS
        //Route::get('vcards/{vcard}/transactions', [TransactionController::class, 'getVCardTransactions']);//descomentar
        Route::get('transactions/{transaction}', [TransactionController::class, 'show']);
        Route::post('transactions', [TransactionController::class, 'store']);

        // USERS
        Route::apiResource('users', UserController::class); //G4.2, G4.4, ...
        // USERS VERIFICAR JOAO SE ESTÁ BEM!!!!!!!!!!***!!!!!!!!***!!!!!*!!** VER SER AS ROUTAS /USER/MANAGE-ADMIN ESTAO A LIGAR CORRETAMENTE
        Route::put('/users/manage-administrators', [UserController::class, 'manageAdministrators']);//Policies ja fazem isto: nao é preciso

        // VCARD
        Route::get('vcards/{vcard}/transactions', [TransactionController::class, 'getVCardTransactions']); //remove
        Route::put('vcards/{vcard}', [VCardController::class, 'getVCardStats']);
        Route::patch('vcards/{vcard}/block', [VCardController::class, 'block']);
        Route::patch('vcards/{vcard}/unblock', [VCardController::class, 'unblock']);
        Route::patch('vcards/{vcard}/changeConfirmationCode', [VCardController::class, 'changeConfirmationCode']);
        Route::get('vcards', [VCardController::class, 'index']);
        Route::get('vcards/{vcard}', [VCardController::class, 'show']);
        Route::put('vcards/{vcard}', [VCardController::class, 'update']);
        Route::delete('vcards/{vcard}', [VCardController::class, 'destroy']);


        // AUTH PASSPORT LOGOUT
        Route::post('auth/logout', [AuthController::class, 'logout']);
    }
);

