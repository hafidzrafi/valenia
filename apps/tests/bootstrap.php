<?php

declare(strict_types=1);

/**
 * PHPUnit bootstrap.
 *
 * Loads the procedural application library once. Integration tests opt in via
 * RUN_DB_TESTS=1; unit tests never need PostgreSQL.
 */
define('VALENIA_TEST_ROOT', dirname(__DIR__));

require_once VALENIA_TEST_ROOT.'/vendor/autoload.php';

if (!defined('VALENIA_BOOTSTRAPPED')) {
    define('VALENIA_BOOTSTRAPPED', true);

    // app/bootstrap.php returns a Router; discard it for tests.
    require_once VALENIA_TEST_ROOT.'/app/bootstrap.php';
}

/**
 * True when integration tests are explicitly enabled.
 */
function test_database_enabled(): bool
{
    return '1' === getenv('RUN_DB_TESTS');
}
