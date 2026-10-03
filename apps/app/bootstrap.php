<?php

declare(strict_types=1);

/**
 * Application bootstrap: load the library, register the middleware guards, and
 * return a Router configured with the routes directory and global middleware.
 */

require_once __DIR__ . '/lib/config.php';
require_once __DIR__ . '/lib/request.php';
require_once __DIR__ . '/lib/response.php';
require_once __DIR__ . '/lib/middleware.php';
require_once __DIR__ . '/lib/view.php';
require_once __DIR__ . '/lib/db.php';
require_once __DIR__ . '/lib/router.php';

require_once __DIR__ . '/middleware/auth.php';
require_once __DIR__ . '/middleware/role.php';
require_once __DIR__ . '/middleware/csrf.php';

return new Router(__DIR__ . '/routes', ['csrf']);
