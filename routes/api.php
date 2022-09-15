<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PartnerController;
use App\Http\Controllers\Api\ApplicationController;

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

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});
Route::prefix('v1')->group(function () {
    Route::get('partners/order', [PartnerController::class, 'getOrders']);
    Route::post('set-status', [ApplicationController::class, 'setStatus']);
    Route::post('order/create', [ApplicationController::class, 'create']);
    Route::get('order/{id}/stickers', [ApplicationController::class, 'getSticker']);
    Route::get('order/get-status', [ApplicationController::class, 'getOrderStatus']);
    Route::post('order/status-history', [ApplicationController::class, 'statusHistory']);
});

