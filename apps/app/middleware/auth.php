<?php

declare(strict_types=1);

/**
 * Auth guard (demonstrator).
 *
 * Stands in for the future database-backed session: reads the X-Demo-User
 * header, stores it on the request, and rejects unauthenticated requests.
 */
Middleware::register('auth', static function (Request $request, callable $next): Response {
    $user = $_SERVER['HTTP_X_DEMO_USER'] ?? null;

    if (!is_string($user) || $user === '') {
        return new Response('401 Unauthorized', 401);
    }

    $request->attributes['user'] = $user;

    return $next($request);
});
