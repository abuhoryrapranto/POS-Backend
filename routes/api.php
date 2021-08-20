<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminPublicController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\StaffsController;
use App\Http\Controllers\ProductController;

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

Route::get('/check-shop-open', [AdminAuthController::class, 'checkShopOpen'])->middleware('auth:sanctum');
Route::get('/all-countries', [AdminAuthController::class, 'getAllCountry'])->middleware('auth:sanctum');
Route::get('/all-currencies', [AdminAuthController::class, 'getAllCurrency'])->middleware('auth:sanctum');
Route::get('/all-timezones', [AdminAuthController::class, 'getAllTimezone'])->middleware('auth:sanctum');

Route::group([
    'prefix'=>'auth'
], function(){
    Route::post('/register', [AdminPublicController::class, 'signupAuth']);
    Route::post('/login', [AdminPublicController::class, 'login']);
    Route::post('/logout', [AdminPublicController::class, 'logout']);
});

Route::group([
    'middleware' => 'auth:sanctum',
], function(){
    Route::post('/logout', [AdminPublicController::class, 'logout']);
});

Route::group([
    'middleware' => 'auth:sanctum',
    'prefix' => 'shop'
], function(){
    Route::post('/open-shop', [ShopController::class, 'openShop']);
});

Route::group([
    'middleware' => 'auth:sanctum',
    'prefix' => 'admins'
], function(){
    Route::post('/save-new-admin', [AdminAuthController::class, 'saveNewAdmin']);
    Route::get('/admin-active-toggle/{uuid}', [AdminAuthController::class, 'adminActiveToggle']);
    Route::get('/my-country', [AdminAuthController::class, 'getMyCountry']);
    Route::get('/all-admins', [AdminAuthController::class, 'adminList']);
    Route::get('/profile/{uuid}', [AdminAuthController::class, 'getAdminprofile']);
    Route::put('/update-profile/{uuid}', [AdminAuthController::class, 'updateAdminProfile']);
});

Route::group([
    'middleware' => 'auth:sanctum',
    'prefix' => 'staffs'
], function(){
    Route::post('/save-new-staff', [StaffsController::class, 'saveStaff']);
    Route::get('/all-staffs', [StaffsController::class, 'getAllStaffs']);
});

Route::group([
    'middleware' => 'auth:sanctum',
    'prefix' => 'brands'
], function(){
    Route::post('/save-new', [ProductController::class, 'saveBrand']);
    Route::get('/all-brands', [ProductController::class, 'getAllBrands']);
    Route::get('/toggle-brand/{uuid}', [ProductController::class, 'brandActiveChange']);
    Route::put('/update-brand/{uuid}', [ProductController::class, 'updateBrand']);
    Route::delete('/delete-brand/{uuid}', [ProductController::class, 'deleteBrand']);
    Route::delete('/force-delete-brand/{uuid}', [ProductController::class, 'forceDeleteBrand']);
});

Route::group([
    'middleware' => 'auth:sanctum',
    'prefix' => 'types'
], function(){
    Route::post('/save-new', [ProductController::class, 'saveType']);
    Route::get('/all-types', [ProductController::class, 'getAllTypes']);
    Route::get('/toggle-type/{uuid}', [ProductController::class, 'typeActiveChange']);
    Route::put('/update-type/{uuid}', [ProductController::class, 'updateType']);
    Route::delete('/delete-type/{uuid}', [ProductController::class, 'deleteType']);
    Route::delete('/force-delete-type/{uuid}', [ProductController::class, 'forceDeleteType']);
});
