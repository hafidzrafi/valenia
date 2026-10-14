<?php

declare(strict_types=1);

/**
 * Forced password-change gate (FR-AUTH-08). Use after `auth`.
 *
 * Accounts flagged must_change_password may only visit /ganti-sandi and
 * /logout until they set a new password. Attach it to guarded routes; leave it
 * off the ganti-sandi and logout routes themselves.
 */

Middleware::register('password', static function (Request $request, callable $next): Response {
    if (($request->attribute('must_change') ?? false) === true) {
        return redirect('/ganti-sandi');
    }

    return $next($request);
});
