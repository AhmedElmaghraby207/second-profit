<?php

use App\Http\Controllers\ApiCustomersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => '/customers'], function(){
    Route::post('/store', [ApiCustomersController::class, 'store']);
    Route::post('/generate-auth-token', [ApiCustomersController::class, 'generate_auth_token']);
    Route::post('/save-status', [ApiCustomersController::class, 'save_status']);
});