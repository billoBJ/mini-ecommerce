<?php

use App\Http\Controllers\Api\Products\DeleteProductController;
use App\Http\Controllers\Api\Products\ListProductController;
use App\Http\Controllers\Api\Products\ShowProductController;
use App\Http\Controllers\Api\Products\StoreProductController;
use App\Http\Controllers\Api\Products\UpdateProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('products')->group(function () {
    Route::get('/', ListProductController::class);
    Route::post('/', StoreProductController::class);
    Route::get('/{product}', ShowProductController::class)->whereNumber('product');
    Route::put('/{product}', UpdateProductController::class)->whereNumber('product');
    Route::delete('/{product}', DeleteProductController::class)->whereNumber('product');
});
