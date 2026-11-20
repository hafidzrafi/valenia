<?php

declare(strict_types=1);

/**
 * Application bootstrap: load the library, register the middleware guards, and
 * return a Router configured with the routes directory and global middleware.
 */

require_once __DIR__.'/lib/config.php';

// Keep PHP and PostgreSQL on the same zone: db.php applies the same value to
// the connection, and app_today() reads it. An unknown identifier falls back to
// Asia/Jakarta instead of leaving PHP on UTC.
$appTimezone = env('APP_TIMEZONE', 'Asia/Jakarta') ?: 'Asia/Jakarta';

try {
    new DateTimeZone($appTimezone);
} catch (Throwable $exception) {
    $appTimezone = 'Asia/Jakarta';
}

date_default_timezone_set($appTimezone);

require_once __DIR__.'/lib/log.php';

require_once __DIR__.'/lib/request.php';

require_once __DIR__.'/lib/response.php';

require_once __DIR__.'/lib/middleware.php';

require_once __DIR__.'/lib/view.php';

require_once __DIR__.'/lib/db.php';

require_once __DIR__.'/lib/session.php';

require_once __DIR__.'/lib/auth.php';

require_once __DIR__.'/lib/registration.php';

require_once __DIR__.'/lib/staff.php';

require_once __DIR__.'/lib/master.php';

require_once __DIR__.'/lib/reports.php';

require_once __DIR__.'/lib/reports_pdf.php';

require_once __DIR__.'/lib/router.php';

require_once __DIR__.'/middleware/auth.php';

require_once __DIR__.'/middleware/role.php';

require_once __DIR__.'/middleware/password.php';

require_once __DIR__.'/middleware/csrf.php';

return new Router(__DIR__.'/routes', ['csrf', 'auth', 'password']);
