<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequestToken
{
    /**
     * Handle an incoming request.
     *
     * Checks for a valid static token in the X-Service-Token header before allowing access.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get the X-Service-Token header
        $token = $request->header('X-Service-Token');

        // Check if token is present
        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Missing X-Service-Token header.',
            ], 401);
        }

        // Compare token with the static token in config/services.php
        $validToken = config('services.api.token');

        if (empty($validToken)) {
            return response()->json([
                'success' => false,
                'message' => 'Server configuration error: Missing static API token.',
            ], 500);
        }

        if (!hash_equals($validToken, $token)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Invalid token.',
            ], 401);
        }

        // Token is valid — allow request to continue
        return $next($request);
    }
}
