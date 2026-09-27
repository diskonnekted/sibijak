<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyMiddleware
{
    /**
     * Gate API requests with a shared static key (X-API-KEY header).
     *
     * If API_KEY is empty in .env, the API is open (useful for local dev).
     * Once a key is set, every consumer (inspektorat, mitra, dsb.) must
     * send it via header `X-API-KEY: <key>`, Bearer token, or ?api_key=.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $configured = trim((string) env('API_KEY', ''));

        if ($configured === '') {
            return $next($request);
        }

        $provided = (string) (
            $request->header('X-API-KEY')
            ?? $request->bearerToken()
            ?? $request->query('api_key')
            ?? ''
        );

        if (! hash_equals($configured, $provided)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: kunci API tidak valid atau tidak disertakan.',
            ], 401);
        }

        return $next($request);
    }
}