<?php

declare(strict_types=1);

/**
 * Migration runner (minimal).
 *
 * Applies database/schema.sql through db(). For the SQLite dev fallback the
 * schema is idempotent; PostgreSQL migrations are scheduled for Sprint 1.
 */

require_once __DIR__ . '/../app/lib/config.php';
require_once __DIR__ . '/../app/lib/db.php';

$pdo = db();
$driver = (string) $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);

if ($driver === 'sqlite') {
    $pdo->exec((string) file_get_contents(__DIR__ . '/schema.sql'));
    fwrite(STDOUT, "Migrasi selesai (sqlite).\n");
} else {
    fwrite(STDOUT, "Driver {$driver}: migrasi sungguhan dijadwalkan pada Sprint 1.\n");
}

exit(0);
