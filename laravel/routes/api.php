<?php

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// DEFAULT CATEGORIES
Route::apiResource('defaultCategories', DefaultCategoryController::class)->except(['show']);

// CATEGORIES
Route::get('vcards/{vcard}/categories', [CategoryController::class, 'getVCardCategories']);
Route::apiResource('categories', CategoryController::class)->except(['show']);

// AUTH USERS
Route::resource('authUsers', AuthUserController::class);

// TRANSACTIONS
Route::get('vcards/{vcard}/transactions', [TransactionController::class, 'getVCardTransactions']);
Route::post('transactions', [TransactionController::class, 'store']);
Route::get('transactions', [TransactionController::class, 'index']);

// USERS
Route::apiResource('users', UserController::class);

// VCARDs
Route::put('vcards/{vcard}', [VCardController::class, 'getVCardStats']);
Route::patch('vcards/{vcard}/block', [VCardController::class, 'block']);
Route::apiResource("vcards", VCardController::class);

