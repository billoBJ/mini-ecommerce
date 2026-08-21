<?php

use App\Domain\Auth\InvalidCredentialsException;
use App\Domain\Customer\CustomerNotFoundException;
use App\Domain\Order\EmptyOrderException;
use App\Domain\Order\InvalidOrderStatusTransitionException;
use App\Domain\Order\OrderNotFoundException;
use App\Domain\Product\InsufficientStockException;
use App\Domain\Product\ProductNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        $exceptions->render(function (ProductNotFoundException $e, Request $request) {
            return response()->json(['message' => $e->getMessage()], 404);
        });

        $exceptions->render(function (CustomerNotFoundException $e, Request $request) {
            return response()->json(['message' => $e->getMessage()], 404);
        });

        $exceptions->render(function (InvalidCredentialsException $e, Request $request) {
            return response()->json(['message' => $e->getMessage()], 401);
        });

        $exceptions->render(function (OrderNotFoundException $e, Request $request) {
            return response()->json(['message' => $e->getMessage()], 404);
        });

        $exceptions->render(function (EmptyOrderException|InsufficientStockException $e, Request $request) {
            return response()->json(['message' => $e->getMessage()], 422);
        });

        // 409 Conflict, not 422: the request body itself was well-formed
        // (a valid status value), it's the order's CURRENT state that
        // conflicts with the move being requested.
        $exceptions->render(function (InvalidOrderStatusTransitionException $e, Request $request) {
            return response()->json(['message' => $e->getMessage()], 409);
        });
    })->create();
