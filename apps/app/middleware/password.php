<?php

declare(strict_types=1);

/**
 * Forced password-change gate (FR-AUTH-08). Runs globally, after `auth`.
 *
 * Accounts flagged must_change_password may only visit /ganti-sandi and /logout
 * until they set a new password. Anonymous visitors have no must_change
 * attribute, so public paths pass straight through.
 */

Middleware::register('password', static function (Request $request, callable $next): Response {
    $exempt = ['ganti-sandi', 'logout'];

    if (($request->attribute('must_change') ?? false) === true && !in_array($request->path, $exempt, true)) {
        return redirect('/ganti-sandi');
    }

    return $next($request);
});
