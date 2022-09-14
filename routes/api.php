<?php

use App\Http\Controllers\Api\PhotoApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\Api\AuthApiController;

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


Route::post('/register',[AuthApiController::class,'register'])->name('api.register');
Route::post('/login',[AuthApiController::class,'logIn'])->name('api.login');
Route::get('/users',[AuthApiController::class,'users'])->name('api.users');



Route::middleware('auth:sanctum')->group(function (){
    Route::post('/logout',[AuthApiController::class,'logout'])->name('api.logout');
    Route::post('/logout-all',[AuthApiController::class,'logoutAll'])->name('api.logoutAll');
    Route::get('/tokens',[AuthApiController::class,'tokens'])->name('api.tokens');
    Route::apiResource('/products',ProductApiController::class);
    Route::apiResource('/photos',PhotoApiController::class);
    Route::get('/user', function (Request $request) {
    return $request->user();
});


});
//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::get('/agent', function () {
    return request()->userAgent();
});
