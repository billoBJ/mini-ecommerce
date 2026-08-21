<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\MeController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Customers\DeleteCustomerController;
use App\Http\Controllers\Api\Customers\ListCustomerController;
use App\Http\Controllers\Api\Customers\ShowCustomerController;
use App\Http\Controllers\Api\Customers\StoreCustomerController;
use App\Http\Controllers\Api\Customers\UpdateCustomerController;
use App\Http\Controllers\Api\Orders\ListOrderController;
use App\Http\Controllers\Api\Orders\ShowOrderController;
use App\Http\Controllers\Api\Orders\StoreOrderController;
use App\Http\Controllers\Api\Orders\UpdateOrderStatusController;
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

Route::prefix('customers')->middleware('auth:sanctum')->group(function () {
    Route::get('/', ListCustomerController::class);
    Route::post('/', StoreCustomerController::class);
    Route::get('/{customer}', ShowCustomerController::class)->whereNumber('customer');
    Route::put('/{customer}', UpdateCustomerController::class)->whereNumber('customer');
    Route::delete('/{customer}', DeleteCustomerController::class)->whereNumber('customer');
});

Route::prefix('orders')->middleware('auth:sanctum')->group(function () {
    Route::get('/', ListOrderController::class);
    Route::post('/', StoreOrderController::class);
    Route::get('/{order}', ShowOrderController::class)->whereNumber('order');
    Route::patch('/{order}/status', UpdateOrderStatusController::class)->whereNumber('order');
});
