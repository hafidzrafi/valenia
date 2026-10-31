<?php

declare(strict_types=1);

/**
 * Reference seeder (development bootstrap).
 *
 * Inserts master poli and a single admin account. Idempotent: safe to run
 * repeatedly (upserts use ON CONFLICT DO NOTHING). Refuses to run when
 * APP_ENV=production.
 *
 * Run after migrations:
 *   php database/seed.php
 *
 * The admin password comes from the SEED_ADMIN_PASSWORD environment variable
 * (default below) and is stored hashed; the account must change it on first
 * login (must_change_password = TRUE).
 */

require_once __DIR__.'/../app/lib/config.php';

require_once __DIR__.'/../app/lib/db.php';

const SEED_SERVICES = ['Poli Umum', 'Poli Gigi', 'Konseling/Psikologi'];
const SEED_ADMIN_USERNAME = 'admin';
const SEED_ADMIN_FULL_NAME = 'Administrator';
const SEED_ADMIN_ROLE = 'admin';
const SEED_ADMIN_PASSWORD_DEFAULT = 'admin12345';

if ('production' === env('APP_ENV')) {
    fwrite(STDERR, "Seeder ditolak pada lingkungan produksi.\n");

    exit(1);
}

try {
    $pdo = db();
} catch (Throwable $exception) {
    fwrite(STDERR, 'Gagal terhubung ke basis data: '.$exception->getMessage()."\n");

    exit(1);
}

try {
    $pdo->beginTransaction();

    // 1) Master poli.
    $insertService = $pdo->prepare(
        'INSERT INTO services (name) VALUES (?) ON CONFLICT (name) DO NOTHING'
    );

    foreach (SEED_SERVICES as $serviceName) {
        $insertService->execute([$serviceName]);
    }

    // 2) Admin user. The no-op DO UPDATE makes RETURNING id work whether the
    //    row is newly inserted or already present.
    $upsertUser = $pdo->prepare(
        'INSERT INTO users (username, full_name, role) VALUES (?, ?, ?)
         ON CONFLICT (username) DO UPDATE SET username = EXCLUDED.username
         RETURNING id'
    );
    $upsertUser->execute([SEED_ADMIN_USERNAME, SEED_ADMIN_FULL_NAME, SEED_ADMIN_ROLE]);
    $userId = (string) $upsertUser->fetchColumn();

    // 3) Credential (hashed; never overwritten on re-run).
    $algorithm = defined('PASSWORD_ARGON2ID') ? PASSWORD_ARGON2ID : PASSWORD_DEFAULT;
    $passwordHash = password_hash(
        env('SEED_ADMIN_PASSWORD', SEED_ADMIN_PASSWORD_DEFAULT) ?? SEED_ADMIN_PASSWORD_DEFAULT,
        $algorithm,
    );

    $insertCredential = $pdo->prepare(
        'INSERT INTO credentials (user_id, password_hash, must_change_password)
         VALUES (?, ?, TRUE)
         ON CONFLICT (user_id, provider) DO NOTHING'
    );
    $insertCredential->execute([$userId, $passwordHash]);

    $pdo->commit();
} catch (Throwable $exception) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    fwrite(STDERR, 'Seeding gagal: '.$exception->getMessage()."\n");

    exit(1);
}

fwrite(
    STDOUT,
    'Seeding selesai: '.count(SEED_SERVICES)." layanan dan akun admin ({$userId}) disiapkan.\n"
);

exit(0);
