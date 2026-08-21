<?php

namespace App\Providers;

use App\Domain\Customer\CustomerRepositoryInterface;
use App\Domain\Order\OrderRepositoryInterface;
use App\Domain\Product\ProductRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\EloquentCustomerRepository;
use App\Infrastructure\Persistence\Eloquent\EloquentOrderRepository;
use App\Infrastructure\Persistence\Eloquent\EloquentProductRepository;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ProductRepositoryInterface::class, EloquentProductRepository::class);
        $this->app->bind(CustomerRepositoryInterface::class, EloquentCustomerRepository::class);
        $this->app->bind(OrderRepositoryInterface::class, EloquentOrderRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // API-only app: never redirect an unauthenticated request to a
        // "login" route (there isn't one) — always fall through to a
        // 401 JSON response instead.
        Authenticate::redirectUsing(fn () => null);
    }
}
