<?php

declare(strict_types=1);

/**
 * Shared PDO connection.
 *
 * Uses PostgreSQL when DB_DSN is a pgsql DSN; otherwise falls back to SQLite
 * (DB_FILE or apps/database/app.sqlite) so the app runs without Docker.
 * The SQLite branch applies database/schema.sql on first connection.
 */

function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];

    $dsn = env('DB_DSN');

    if (is_string($dsn) && str_starts_with($dsn, 'pgsql:')) {
        $pdo = new PDO($dsn, env('DB_USER'), env('DB_PASSWORD'), $options);

        return $pdo;
    }

    $file = env('DB_FILE') ?: app_path('database/app.sqlite');
    $pdo = new PDO('sqlite:' . $file, options: $options);
    $pdo->exec((string) file_get_contents(app_path('database/schema.sql')));

    return $pdo;
}
