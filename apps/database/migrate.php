<?php

declare(strict_types=1);

/**
 * Migration runner (PostgreSQL).
 *
 * Applies database/migrations/*.sql in lexical order, each inside its own
 * transaction, and records the applied version plus a SHA-256 checksum in the
 * `schema_migrations` table. A session-level advisory lock serialises runs so
 * two concurrent invocations (developer + CI) cannot apply the same file twice.
 *
 * Usage: php database/migrate.php [up|status|rollback|fresh]
 *
 *   up        apply all pending migrations (default)
 *   status    list every migration and whether it is applied or pending
 *   rollback  revert the last applied migration (requires a *.down.sql file)
 *   fresh     drop the public schema and re-apply everything (development only)
 */

require_once __DIR__ . '/../app/lib/config.php';
require_once __DIR__ . '/../app/lib/db.php';

const MIGRATIONS_DIR = __DIR__ . '/migrations';
const MIGRATIONS_TABLE = 'schema_migrations';

/** Fixed, arbitrary key used for the session-level advisory lock. */
const ADVISORY_LOCK_KEY = 728461;

/**
 * Every up-migration file, sorted lexically (so 0002 runs after 0001).
 *
 * @return list<string>
 */
function migration_files(string $dir): array
{
    $files = glob($dir . '/*.sql') ?: [];

    $files = array_values(array_filter(
        $files,
        static fn (string $file): bool => !str_ends_with($file, '.down.sql'),
    ));

    sort($files, SORT_STRING);

    return $files;
}

/** The stable version identifier for a migration file (filename without .sql). */
function version_of(string $file): string
{
    return basename($file, '.sql');
}

/** SHA-256 of a file's contents, used to detect edits to applied migrations. */
function checksum_of(string $file): string
{
    return hash_file('sha256', $file) ?: '';
}

/** Create the tracking table on first run. */
function ensure_table(PDO $pdo): void
{
    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS ' . MIGRATIONS_TABLE . ' ('
        . ' version TEXT PRIMARY KEY,'
        . ' checksum TEXT NOT NULL,'
        . ' applied_at TIMESTAMPTZ NOT NULL DEFAULT now(),'
        . ' execution_ms INTEGER NOT NULL DEFAULT 0'
        . ')'
    );
}

/**
 * Map of version => checksum for everything already applied.
 *
 * @return array<string, string>
 */
function applied_migrations(PDO $pdo): array
{
    $rows = $pdo->query(
        'SELECT version, checksum FROM ' . MIGRATIONS_TABLE . ' ORDER BY version'
    )->fetchAll();

    $map = [];

    foreach ($rows as $row) {
        $map[(string) $row['version']] = (string) $row['checksum'];
    }

    return $map;
}

function lock(PDO $pdo): void
{
    $pdo->exec('SELECT pg_advisory_lock(' . ADVISORY_LOCK_KEY . ')');
}

function unlock(PDO $pdo): void
{
    $pdo->exec('SELECT pg_advisory_unlock(' . ADVISORY_LOCK_KEY . ')');
}

/**
 * Applied migrations whose file was edited since it ran.
 *
 * @param list<string>          $files
 * @param array<string, string> $applied
 *
 * @return list<string>
 */
function drift_of(array $files, array $applied): array
{
    $drift = [];

    foreach ($files as $file) {
        $version = version_of($file);

        if (isset($applied[$version]) && $applied[$version] !== checksum_of($file)) {
            $drift[] = $version;
        }
    }

    return $drift;
}

/** Apply one migration file and record it. Returns the execution time in ms. */
function apply_migration(PDO $pdo, string $file): int
{
    $version = version_of($file);
    $start = microtime(true);

    $pdo->beginTransaction();

    try {
        $pdo->exec((string) file_get_contents($file));

        $elapsed = (int) round((microtime(true) - $start) * 1000);

        $pdo->prepare(
            'INSERT INTO ' . MIGRATIONS_TABLE . ' (version, checksum, execution_ms) VALUES (?, ?, ?)'
        )->execute([$version, checksum_of($file), $elapsed]);

        $pdo->commit();
    } catch (Throwable $exception) {
        $pdo->rollBack();
        throw $exception;
    }

    return $elapsed;
}

function command_up(PDO $pdo): int
{
    $files = migration_files(MIGRATIONS_DIR);
    $applied = applied_migrations($pdo);

    $drift = drift_of($files, $applied);

    if ($drift !== []) {
        fwrite(STDERR, 'Drift: migrasi yang sudah diterapkan telah diubah: ' . implode(', ', $drift) . "\n");
        fwrite(STDERR, "Jangan mengubah berkas migrasi yang sudah diterapkan; buat migrasi baru.\n");

        return 1;
    }

    $pending = array_values(array_filter(
        $files,
        static fn (string $file): bool => !isset($applied[version_of($file)]),
    ));

    if ($pending === []) {
        fwrite(STDOUT, "Tidak ada migrasi baru. Basis data sudah terbaru.\n");

        return 0;
    }

    lock($pdo);

    try {
        foreach ($pending as $file) {
            $version = version_of($file);

            try {
                $milliseconds = apply_migration($pdo, $file);
            } catch (Throwable $exception) {
                fwrite(STDERR, "  GAGAL {$version}: " . $exception->getMessage() . "\n");

                return 1;
            }

            fwrite(STDOUT, "  OK {$version} ({$milliseconds} ms)\n");
        }
    } finally {
        unlock($pdo);
    }

    fwrite(STDOUT, 'Migrasi selesai: ' . count($pending) . " berkas diterapkan.\n");

    return 0;
}

function command_status(PDO $pdo): int
{
    $files = migration_files(MIGRATIONS_DIR);
    $applied = applied_migrations($pdo);

    $known = array_map('version_of', $files);

    fwrite(STDOUT, sprintf("%-36s %-10s %s\n", 'VERSI', 'STATUS', 'DITERAPKAN'));

    foreach ($files as $file) {
        $version = version_of($file);
        $status = isset($applied[$version]) ? 'applied' : 'pending';

        $at = '';

        if ($status === 'applied') {
            $row = $pdo->prepare('SELECT applied_at FROM ' . MIGRATIONS_TABLE . ' WHERE version = ?');
            $row->execute([$version]);
            $at = (string) ($row->fetchColumn() ?: '');
        }

        fwrite(STDOUT, sprintf("%-36s %-10s %s\n", $version, $status, $at));
    }

    foreach (array_keys($applied) as $version) {
        if (!in_array($version, $known, true)) {
            fwrite(STDOUT, sprintf("%-36s %-10s %s\n", $version, 'missing', ''));
        }
    }

    return 0;
}

function command_rollback(PDO $pdo): int
{
    $version = $pdo->query(
        'SELECT version FROM ' . MIGRATIONS_TABLE . ' ORDER BY version DESC LIMIT 1'
    )->fetchColumn();

    if ($version === false) {
        fwrite(STDOUT, "Tidak ada migrasi untuk dibatalkan.\n");

        return 0;
    }

    $version = (string) $version;
    $down = MIGRATIONS_DIR . '/' . $version . '.down.sql';

    if (!is_file($down)) {
        fwrite(STDERR, "Berkas rollback tidak ditemukan: {$version}.down.sql\n");

        return 1;
    }

    lock($pdo);

    try {
        $pdo->beginTransaction();

        try {
            $pdo->exec((string) file_get_contents($down));
            $pdo->prepare('DELETE FROM ' . MIGRATIONS_TABLE . ' WHERE version = ?')->execute([$version]);
            $pdo->commit();
        } catch (Throwable $exception) {
            $pdo->rollBack();
            fwrite(STDERR, "Rollback {$version} gagal: " . $exception->getMessage() . "\n");

            return 1;
        }
    } finally {
        unlock($pdo);
    }

    fwrite(STDOUT, "Rollback selesai: {$version} dibatalkan.\n");

    return 0;
}

function command_fresh(PDO $pdo): int
{
    if (env('APP_ENV') === 'production') {
        fwrite(STDERR, "Perintah fresh dilarang pada lingkungan produksi.\n");

        return 1;
    }

    fwrite(STDOUT, "Menghapus skema public dan menerapkan ulang seluruh migrasi...\n");

    lock($pdo);

    try {
        $pdo->exec('DROP SCHEMA IF EXISTS public CASCADE');
        $pdo->exec('CREATE SCHEMA public');
    } finally {
        unlock($pdo);
    }

    ensure_table($pdo);

    return command_up($pdo);
}

function usage(): int
{
    fwrite(STDERR, "Penggunaan: php database/migrate.php [up|status|rollback|fresh]\n");

    return 1;
}

$command = $argv[1] ?? 'up';

try {
    $pdo = db();
} catch (Throwable $exception) {
    fwrite(STDERR, 'Gagal terhubung ke basis data: ' . $exception->getMessage() . "\n");
    exit(1);
}

try {
    ensure_table($pdo);

    exit(match ($command) {
        'up' => command_up($pdo),
        'status' => command_status($pdo),
        'rollback' => command_rollback($pdo),
        'fresh' => command_fresh($pdo),
        default => usage(),
    });
} catch (Throwable $exception) {
    fwrite(STDERR, 'Error: ' . $exception->getMessage() . "\n");
    exit(1);
}
