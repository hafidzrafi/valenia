<?php

declare(strict_types=1);

/**
 * Shared PDO connection (PostgreSQL only).
 *
 * DB_DSN must be a pgsql DSN. The former SQLite fallback was removed when the
 * PostgreSQL migration runner landed; local development runs the database
 * through Docker Compose (see docs). Schema changes go through
 * database/migrate.php, never through an auto-applied schema file.
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

    if (!is_string($dsn) || !str_starts_with($dsn, 'pgsql:')) {
        throw new RuntimeException(
            'DB_DSN harus berupa DSN PostgreSQL (contoh: pgsql:host=db;port=5432;dbname=valenia). '
            . 'Jalankan basis data melalui Docker Compose.'
        );
    }

    return $pdo = new PDO($dsn, env('DB_USER'), env('DB_PASSWORD'), $options);
}
