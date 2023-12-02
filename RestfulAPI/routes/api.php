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
Route::resource('defaultCategories', DefaultCategoryController::class)->except(['create', 'edit', 'show']);

// CATEGORIES
Route::post('vcards/{vcard}/categories', [CategoryController::class, 'createByVCard']);
Route::get('vcards/{vcard}/categories', [CategoryController::class, 'getByVCard']);
Route::delete('vcards/{vcard}/categories/{category}', [CategoryController::class, 'deleteByVCard']);
Route::put('vcards/{vcard}/categories/{category}', [CategoryController::class, 'updateByVCard']);

// AUTH USERS
Route::resource('authUsers', AuthUserController::class);

// TRANSACTIONS
Route::put('vcards/{phoneNumber}/transactions', [TransactionController::class, 'getByPhoneNumber']);

Route::resource('transactions', TransactionController::class);



// USERS VERIFICAR JOAO SE ESTÁ BEM!!!!!!!!!!***!!!!!!!!***!!!!!*!!** VER SER AS ROUTAS /USER/MANAGE-ADMIN ESTAO A LIGAR
//CORRETAMENTE

Route::resource('users', UserController::class)->except(['create', 'edit', 'show']);
Route::put('/users/manage-administrators', [UserController::class, 'manageAdministrators']);

//Route::put('vcards/{vcard}/customUpdate', [VCardController::class, 'customUpdate']);



// VCARDs
Route::patch('vcards/{vcard}/block', [VCardController::class, 'block']);
Route::resource("vcards", VCardController::class)->except(['create', 'edit', 'show']);

