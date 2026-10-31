<?php

declare(strict_types=1);

/**
 * Minimal application logger.
 *
 * Writes timestamped lines to STDERR. Under Docker (PHP-FPM) this lands in
 * `docker compose logs app`, which is where server-side 401/403 events and
 * errors are observed (NFR-REL-03).
 */
function log_safe(string $value): string
{
    // Strip control characters so user-controlled values (paths, user agents)
    // cannot inject extra lines into the log.
    return (string) preg_replace('/[\x00-\x1F\x7F]+/', '?', $value);
}

function app_log(string $level, string $message): void
{
    // error_log() works in every SAPI (CLI and PHP-FPM); STDERR as a constant
    // exists only in CLI. Under Docker the FPM error log is the container's
    // stderr, so these lines show up in `docker compose logs app`.
    $time = (new DateTimeImmutable('now'))->format('Y-m-d H:i:s');
    error_log("[{$time}] [".strtoupper($level).'] '.$message);
}
