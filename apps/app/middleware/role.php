<?php

declare(strict_types=1);

/**
 * Role guard (demonstrator). Use as `role:admin`.
 *
 * Relies on the `auth` guard having set the `user` request attribute.
 */
Middleware::register('role', static function (Request $request, callable $next, string $role): Response {
    if ($request->attribute('user') !== $role) {
        return new Response('403 Forbidden', 403);
    }

    return $next($request);
});
