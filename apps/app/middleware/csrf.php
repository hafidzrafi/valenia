<?php

declare(strict_types=1);

/**
 * CSRF guard (demonstrator): double-submit cookie for unsafe methods.
 *
 * A token is issued in a SameSite=Lax cookie; unsafe requests must echo it
 * back in the `_token` field or `X-CSRF-Token` header. Forms use csrf_token().
 */
final class Csrf
{
    public static string $token = '';
}

function csrf_token(): string
{
    return Csrf::$token;
}

Middleware::register('csrf', static function (Request $request, callable $next): Response {
    $token = $_COOKIE['csrf_token'] ?? '';

    if (!is_string($token) || $token === '') {
        $token = bin2hex(random_bytes(32));
        setcookie('csrf_token', $token, [
            'httponly' => true,
            'samesite' => 'Lax',
            'path' => '/',
        ]);
    }

    Csrf::$token = $token;

    if (in_array($request->method, ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
        $submitted = $_POST['_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');

        if (!is_string($submitted) || !hash_equals($token, $submitted)) {
            return new Response('419 Page Expired', 419);
        }
    }

    return $next($request);
});
