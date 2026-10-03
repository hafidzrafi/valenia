<?php

declare(strict_types=1);

/** Environment and path helpers. Functions only, no side effects. */

/**
 * Absolute path inside the application root (`apps/`).
 */
function app_path(string $path = ''): string
{
    $base = dirname(__DIR__, 2);

    return $path === '' ? $base : $base . '/' . ltrim($path, '/');
}

/**
 * Read an environment value, loading `.env` through phpdotenv when available.
 */
function env(string $key, ?string $default = null): ?string
{
    static $loaded = false;

    if ($loaded === false) {
        $loaded = true;
        $autoload = app_path('vendor/autoload.php');

        if (is_file($autoload)) {
            require_once $autoload;

            if (class_exists(\Dotenv\Dotenv::class) && is_file(app_path('.env'))) {
                \Dotenv\Dotenv::createImmutable(app_path())->safeLoad();
            }
        }
    }

    $value = $_ENV[$key] ?? getenv($key);

    if ($value === false || $value === null || $value === '') {
        return $default;
    }

    return (string) $value;
}
