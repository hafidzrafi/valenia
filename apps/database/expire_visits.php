<?php

declare(strict_types=1);

/**
 * Mark previous-day waiting or served visits as expired.
 *
 * Run once per day after midnight Asia/Jakarta, for example from cron:
 *   5 0 * * * cd /srv && php database/expire_visits.php
 *
 * The SQL function is idempotent, so a repeated run is safe.
 */

require_once __DIR__.'/../app/lib/config.php';

require_once __DIR__.'/../app/lib/db.php';

try {
    $count = (int) db()->query('SELECT expire_stale_visits()')->fetchColumn();
    fwrite(STDOUT, 'Expired visits: '.$count."\n");

    exit(0);
} catch (Throwable $exception) {
    fwrite(STDERR, 'Gagal menjalankan expiry: '.$exception->getMessage()."\n");

    exit(1);
}
