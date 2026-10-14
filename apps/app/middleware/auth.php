<?php

declare(strict_types=1);

/**
 * Auth guard: database-backed session (FR-AUTH-02/03).
 *
 * Loads the session's user via load_session_user() and publishes the user_id,
 * username, role, and must_change flag on the request. Without a valid session
 * the server logs a 401 and redirects to /login.
 */

Middleware::register('auth', static function (Request $request, callable $next): Response {
    start_app_session();

    $raw = $_SESSION['user_id'] ?? null;
    $user = is_string($raw) && $raw !== '' ? load_session_user($raw) : null;

    if ($user === null) {
        $where = '/' . ltrim($request->path, '/');
        $ip = log_safe((string) ($_SERVER['REMOTE_ADDR'] ?? '-'));
        $ua = log_safe((string) ($_SERVER['HTTP_USER_AGENT'] ?? '-'));
        app_log('warning', "401 Unauthorized {$request->method} {$where} ip={$ip} ua={$ua} (no valid session)");
        app_log('info', "Redirecting unauthenticated request to /login");

        return redirect('/login');
    }

    $request->attributes['user_id'] = $user['id'];
    $request->attributes['user'] = $user['username'];
    $request->attributes['role'] = $user['role'];
    $request->attributes['must_change'] = $user['must_change'];

    return $next($request);
});
