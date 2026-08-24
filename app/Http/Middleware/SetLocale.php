<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /** @var list<string> */
    private const SUPPORTED = ['en', 'es'];

    public function handle(Request $request, Closure $next): Response
    {
        $locale = config('app.locale', 'en');

        if ($request->headers->has('Accept-Language')) {
            $locale = $request->getPreferredLanguage(self::SUPPORTED) ?? $locale;
        }

        if (! in_array($locale, self::SUPPORTED, true)) {
            $locale = config('app.fallback_locale', 'en');
        }

        app()->setLocale($locale);

        /** @var Response $response */
        $response = $next($request);
        $response->headers->set('Content-Language', $locale);

        return $response;
    }
}
