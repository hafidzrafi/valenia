<?php

declare(strict_types=1);

/**
 * Auth guard: database-backed session (FR-AUTH-02/03).
 *
 * Runs globally. Always tries to resolve the session's user and publishes it on
 * the request and on Auth::$user (for the layout). Non-public paths require a
 * valid session, otherwise the server logs a 401 and redirects to /login.
 */

Middleware::register('auth', static function (Request $request, callable $next): Response {
    start_app_session();

    $raw = $_SESSION['user_id'] ?? null;
    $user = is_string($raw) && $raw !== '' ? load_session_user($raw) : null;

    if ($user !== null) {
        Auth::$user = $user;

        $request->attributes['user_id'] = $user['id'];
        $request->attributes['user'] = $user['username'];
        $request->attributes['role'] = $user['role'];
        $request->attributes['must_change'] = $user['must_change'];
    }

    if ($user === null && !is_public_path($request->path)) {
        $where = '/' . ltrim($request->path, '/');
        $ip = log_safe((string) ($_SERVER['REMOTE_ADDR'] ?? '-'));
        $ua = log_safe((string) ($_SERVER['HTTP_USER_AGENT'] ?? '-'));
        app_log('warning', "401 Unauthorized {$request->method} {$where} ip={$ip} ua={$ua} (no valid session)");

        return redirect('/login');
    }

    return $next($request);
});
