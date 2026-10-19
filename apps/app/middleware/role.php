<?php

declare(strict_types=1);

/**
 * Role guard. Use as `role:admin`.
 *
 * Compares the real users.role published by the `auth` guard and logs denied
 * access (NFR-REL-03), then returns 403 (FR-AUTH-03).
 */

Middleware::register('role', static function (Request $request, callable $next, string $role): Response {
    if ($request->attribute('role') === $role) {
        return $next($request);
    }

    $where = '/' . ltrim($request->path, '/');
    $who = log_safe((string) ($request->attribute('user') ?? '-'));
    $have = log_safe((string) ($request->attribute('role') ?? '-'));
    app_log('warning', "403 Forbidden {$request->method} {$where} user={$who} role={$have} required={$role}");

    return new Response('403 Forbidden', 403);
});
