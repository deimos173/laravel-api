<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;

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

Route::middleware([\App\Http\Middleware\CheckApiKey::class])->group(function () {
    Route::get('/orders', [ApiController::class, 'orders']);
    Route::get('/sales', [ApiController::class, 'sales']);
    Route::get('/orders', [ApiController::class, 'orders']);
    Route::get('/orders', [ApiController::class, 'orders']);
});
