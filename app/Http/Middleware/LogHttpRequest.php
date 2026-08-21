<?php

namespace App\Http\Middleware;

use App\Exceptions\ApiExceptionHandler;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class LogHttpRequest
{
    public function handle(Request $request, Closure $next): Response
    {
        $startedAt = hrtime(true);

        try {
            /** @var Response $response */
            $response = $next($request);
        } catch (Throwable $e) {
            $this->write(
                request: $request,
                status: ApiExceptionHandler::statusFor($e),
                durationMs: $this->durationMs($startedAt),
                exception: $e,
            );

            throw $e;
        }

        $this->write(
            request: $request,
            status: $response->getStatusCode(),
            durationMs: $this->durationMs($startedAt),
        );

        return $response;
    }

    private function write(Request $request, int $status, int $durationMs, ?Throwable $exception = null): void
    {
        $context = [
            'method' => $request->method(),
            'path' => '/'.$request->path(),
            'status' => $status,
            'duration_ms' => $durationMs,
            'user_id' => $request->user()?->id,
            'ip' => $request->ip(),
        ];

        if ($exception instanceof Throwable) {
            $context['exception'] = $exception::class;
            $context['error'] = $exception->getMessage();
        }

        $level = match (true) {
            $status >= 500 => 'error',
            $status >= 400 => 'warning',
            default => 'info',
        };

        Log::channel('observability')->log($level, 'http.request', $context);
    }

    private function durationMs(int $startedAt): int
    {
        return (int) ((hrtime(true) - $startedAt) / 1_000_000);
    }
}
