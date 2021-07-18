<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminPublicController;
use App\Http\Controllers\AdminAuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/save-admin', [AdminPublicController::class, 'signupAuth']);

Route::get('/check-shop-open', [AdminAuthController::class, 'checkShopOpen'])->middleware('auth:sanctum');
Route::get('/all-countries', [AdminAuthController::class, 'getAllCountry'])->middleware('auth:sanctum');
Route::get('/all-currencies', [AdminAuthController::class, 'getAllCurrency'])->middleware('auth:sanctum');

Route::group([
    'prefix'=>'auth'
], function(){
    Route::post('/login', [AdminPublicController::class, 'login']);
    Route::post('/logout', [AdminPublicController::class, 'logout']);
});

Route::group([
    'middleware' => 'auth:sanctum',
], function(){
    Route::post('/logout', [AdminPublicController::class, 'logout']);
});
