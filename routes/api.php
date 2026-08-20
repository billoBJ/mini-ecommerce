<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\MeController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Products\DeleteProductController;
use App\Http\Controllers\Api\Products\ListProductController;
use App\Http\Controllers\Api\Products\ShowProductController;
use App\Http\Controllers\Api\Products\StoreProductController;
use App\Http\Controllers\Api\Products\UpdateProductController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', RegisterController::class);
    Route::post('/login', LoginController::class);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', LogoutController::class);
        Route::get('/me', MeController::class);
    });
});

Route::prefix('products')->group(function () {
    Route::get('/', ListProductController::class);
    Route::get('/{product}', ShowProductController::class)->whereNumber('product');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/', StoreProductController::class);
        Route::put('/{product}', UpdateProductController::class)->whereNumber('product');
        Route::delete('/{product}', DeleteProductController::class)->whereNumber('product');
    });
});
