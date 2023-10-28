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

Route::resource('defaultCategories', DefaultCategoryController::class)->except(['create', 'edit', 'show']);
Route::resource('authUsers', AuthUserController::class);
Route::resource('categories', CategoryController::class);
Route::get('vcards/{phoneNumber}/transactions', [TransactionController::class, 'getByPhoneNumber']);
Route::patch('vcards/{vcard}/block', [VCardController::class, 'block']);
Route::resource("vcards", VCardController::class);
Route::resource('transactions', TransactionController::class);
Route::resource('users', UserController::class);

