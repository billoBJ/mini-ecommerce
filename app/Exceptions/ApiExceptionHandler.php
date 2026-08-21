<?php

namespace App\Exceptions;

use App\Domain\Auth\InvalidCredentialsException;
use App\Domain\Customer\CustomerNotFoundException;
use App\Domain\Order\EmptyOrderException;
use App\Domain\Order\InvalidOrderStatusTransitionException;
use App\Domain\Order\OrderNotFoundException;
use App\Domain\Product\InsufficientStockException;
use App\Domain\Product\ProductNotFoundException;
use DomainException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

final class ApiExceptionHandler
{
    public function register(Exceptions $exceptions): void
    {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request): bool => $this->wantsJson($request),
        );

        $exceptions->dontReport([
            AuthenticationException::class,
            AuthorizationException::class,
            ValidationException::class,
            DomainException::class,
            ModelNotFoundException::class,
        ]);

        $exceptions->context(function (): array {
            $request = request();

            return [
                'request_id' => $request?->headers->get('X-Request-Id'),
                'user_id' => $request?->user()?->id,
                'method' => $request?->method(),
                'path' => $request?->path(),
            ];
        });

        $exceptions->report(function (Throwable $e): void {
            Log::error('http.server_error: '.$e->getMessage(), [
                'exception' => $e,
                'status' => 500,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
        })->stop();

        $exceptions->render(function (DomainException $e, Request $request): ?JsonResponse {
            if (! $this->wantsJson($request)) {
                return null;
            }

            return $this->json($request, [
                'message' => $e->getMessage(),
            ], self::statusFor($e));
        });

        $exceptions->render(function (Throwable $e, Request $request): ?JsonResponse {
            if (! $this->wantsJson($request) || $this->isClientException($e)) {
                return null;
            }

            return $this->internalServerError($e, $request);
        });
    }

    public static function statusFor(Throwable $e): int
    {
        return match (true) {
            $e instanceof ProductNotFoundException,
            $e instanceof CustomerNotFoundException,
            $e instanceof OrderNotFoundException,
            $e instanceof ModelNotFoundException => 404,
            $e instanceof InvalidCredentialsException,
            $e instanceof AuthenticationException => 401,
            $e instanceof AuthorizationException => 403,
            $e instanceof EmptyOrderException,
            $e instanceof InsufficientStockException,
            $e instanceof ValidationException => 422,
            $e instanceof InvalidOrderStatusTransitionException => 409,
            $e instanceof HttpExceptionInterface => $e->getStatusCode(),
            default => 500,
        };
    }

    private function internalServerError(Throwable $e, Request $request): JsonResponse
    {
        $payload = [
            'message' => 'Internal server error.',
            'error' => 'internal_server_error',
            'request_id' => $request->headers->get('X-Request-Id'),
        ];

        if (config('app.debug')) {
            $payload['debug'] = [
                'exception' => $e::class,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ];
        }

        return $this->json($request, $payload, 500);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function json(Request $request, array $payload, int $status): JsonResponse
    {
        $payload['request_id'] ??= $request->headers->get('X-Request-Id');

        return response()->json($payload, $status);
    }

    private function wantsJson(Request $request): bool
    {
        return $request->is('api/*') || $request->expectsJson();
    }

    private function isClientException(Throwable $e): bool
    {
        if ($e instanceof ValidationException
            || $e instanceof AuthenticationException
            || $e instanceof AuthorizationException
            || $e instanceof ModelNotFoundException) {
            return true;
        }

        return $e instanceof HttpExceptionInterface && $e->getStatusCode() < 500;
    }
}
