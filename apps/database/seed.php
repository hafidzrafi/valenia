<?php

declare(strict_types=1);

/**
 * Reference seeder (development bootstrap).
 *
 * Inserts master poli and a single admin account. Idempotent: safe to run
 * repeatedly (upserts use ON CONFLICT DO NOTHING).
 *
 * Run after migrations:
 *   php database/seed.php
 *
 * Production bootstrap (explicit opt-in):
 *   SEED_ADMIN_PASSWORD='...' php database/seed.php --force
 *
 * Reference data (poli, doctors, schedules) also ships in migration 0030, so a
 * plain migrate is enough for the public form. This seeder additionally creates
 * the admin account, whose password must be hashed in PHP.
 *
 * The admin password comes from the SEED_ADMIN_PASSWORD environment variable
 * (default below) and is stored hashed; the account must change it on first
 * login (must_change_password = TRUE).
 */

require_once __DIR__.'/../app/lib/config.php';

require_once __DIR__.'/../app/lib/db.php';

const SEED_SERVICES = ['Poli Umum' => 'PU', 'Poli Gigi' => 'PG', 'Konseling/Psikologi' => 'PK'];

/**
 * Placeholder doctors used for the attendance panel and schedules.
 * These are fictional names, not real Polinema staff; replace when the clinic
 * provides the real roster.
 *
 * @var array<string, array{service: string, name: string}>
 */
const SEED_DOCTORS = [
    'umum' => ['service' => 'Poli Umum', 'name' => 'dr. Ayu Pramesti'],
    'gigi' => ['service' => 'Poli Gigi', 'name' => 'drg. Bagas Wardana'],
    'konseling' => ['service' => 'Konseling/Psikologi', 'name' => 'Rani Kusuma, M.Psi.'],
];

/**
 * Weekly schedule per seeded doctor. day_of_week uses ISO numbering (1 = Monday).
 *
 * @var array<string, list<array{day: int, start: string, end: string}>>
 */
const SEED_SCHEDULES = [
    'umum' => [
        ['day' => 1, 'start' => '08:00', 'end' => '15:00'],
        ['day' => 2, 'start' => '08:00', 'end' => '15:00'],
        ['day' => 3, 'start' => '08:00', 'end' => '15:00'],
        ['day' => 4, 'start' => '08:00', 'end' => '15:00'],
        ['day' => 5, 'start' => '08:00', 'end' => '15:00'],
    ],
    'gigi' => [
        ['day' => 1, 'start' => '08:00', 'end' => '15:00'],
        ['day' => 2, 'start' => '08:00', 'end' => '15:00'],
        ['day' => 3, 'start' => '08:00', 'end' => '15:00'],
        ['day' => 4, 'start' => '08:00', 'end' => '15:00'],
        ['day' => 5, 'start' => '08:00', 'end' => '15:00'],
    ],
    'konseling' => [
        ['day' => 2, 'start' => '09:00', 'end' => '13:00'],
        ['day' => 5, 'start' => '09:00', 'end' => '13:00'],
    ],
];
const SEED_ADMIN_USERNAME = 'admin';
const SEED_ADMIN_FULL_NAME = 'Administrator';
const SEED_ADMIN_ROLE = 'admin';
const SEED_ADMIN_PASSWORD_DEFAULT = 'admin12345';

$force = in_array('--force', $argv ?? [], true);
$isProduction = 'production' === env('APP_ENV');

if ($isProduction && !$force) {
    fwrite(STDERR, "Seeder ditolak pada lingkungan produksi.\n");
    fwrite(STDERR, "Jalankan ulang dengan --force dan set SEED_ADMIN_PASSWORD untuk melanjutkan.\n");

    exit(1);
}

if ($isProduction) {
    $adminPassword = env('SEED_ADMIN_PASSWORD');

    if (!is_string($adminPassword) || '' === $adminPassword) {
        fwrite(STDERR, "Menolak seeding produksi tanpa SEED_ADMIN_PASSWORD.\n");

        exit(1);
    }
}

$resetAdmin = in_array(
    strtolower(trim((string) env('SEED_ADMIN_RESET_PASSWORD', '0'))),
    ['1', 'true', 'yes'],
    true,
);

try {
    $pdo = db();
} catch (Throwable $exception) {
    fwrite(STDERR, 'Gagal terhubung ke basis data: '.$exception->getMessage()."\n");

    exit(1);
}

try {
    $pdo->beginTransaction();

    // 1) Master poli. The prefix is written explicitly: relying on the 'XX'
    //    column default would violate services_queue_prefix_idx on a fresh
    //    database (every new row would default to 'XX'). DO UPDATE also heals
    //    rows that were seeded before this fix.
    $upsertService = $pdo->prepare(
        'INSERT INTO services (name, queue_prefix) VALUES (?, ?)
         ON CONFLICT (name) DO UPDATE SET queue_prefix = EXCLUDED.queue_prefix'
    );

    foreach (SEED_SERVICES as $serviceName => $queuePrefix) {
        $upsertService->execute([$serviceName, $queuePrefix]);
    }

    // 2) Doctors and weekly schedules. Idempotent: a doctor is matched by name
    //    and service, and a schedule is skipped when that doctor already has
    //    the weekday registered.
    $findService = $pdo->prepare('SELECT id FROM services WHERE name = ?');
    $findDoctor = $pdo->prepare('SELECT id FROM doctors WHERE full_name = ? AND service_id = ?');
    $insertDoctor = $pdo->prepare('INSERT INTO doctors (full_name, service_id) VALUES (?, ?) RETURNING id');
    $hasSchedule = $pdo->prepare('SELECT 1 FROM doctor_schedules WHERE doctor_id = ? AND day_of_week = ?');
    $insertSchedule = $pdo->prepare(
        'INSERT INTO doctor_schedules (doctor_id, day_of_week, start_time, end_time) VALUES (?, ?, ?, ?)'
    );

    foreach (SEED_DOCTORS as $key => $doctor) {
        $findService->execute([$doctor['service']]);
        $serviceId = $findService->fetchColumn();

        if (false === $serviceId) {
            throw new RuntimeException('Layanan tidak ditemukan: '.$doctor['service']);
        }

        $findDoctor->execute([$doctor['name'], $serviceId]);
        $doctorId = $findDoctor->fetchColumn();

        if (false === $doctorId) {
            $insertDoctor->execute([$doctor['name'], $serviceId]);
            $doctorId = $insertDoctor->fetchColumn();
        }

        foreach (SEED_SCHEDULES[$key] as $schedule) {
            $hasSchedule->execute([$doctorId, $schedule['day']]);

            if (false === $hasSchedule->fetchColumn()) {
                $insertSchedule->execute([$doctorId, $schedule['day'], $schedule['start'], $schedule['end']]);
            }
        }
    }

    // 3) Admin user. An email is only written when SEED_ADMIN_EMAIL is
    //    provided. A normal seed fills a missing address but never overwrites
    //    one; a reset seed replaces the address when a new one is provided.
    $adminEmail = env('SEED_ADMIN_EMAIL');
    $adminEmail = is_string($adminEmail) && '' !== trim($adminEmail) ? strtolower(trim($adminEmail)) : null;

    if (null !== $adminEmail && false === filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) {
        throw new RuntimeException('SEED_ADMIN_EMAIL tidak valid.');
    }

    $emailUpdate = $resetAdmin && null !== $adminEmail
        ? 'email = EXCLUDED.email'
        : 'email = COALESCE(users.email, EXCLUDED.email)';

    $upsertUser = $pdo->prepare(
        'INSERT INTO users (username, full_name, role, email) VALUES (?, ?, ?, ?)
         ON CONFLICT (username) DO UPDATE
             SET username = EXCLUDED.username,
                 '.$emailUpdate.'
         RETURNING id'
    );
    $upsertUser->execute([SEED_ADMIN_USERNAME, SEED_ADMIN_FULL_NAME, SEED_ADMIN_ROLE, $adminEmail]);
    $userId = (string) $upsertUser->fetchColumn();

    // 4) Credential (hashed). A normal run never overwrites an existing
    //    password; SEED_ADMIN_RESET_PASSWORD=1 replaces it and revokes sessions.
    $algorithm = defined('PASSWORD_ARGON2ID') ? PASSWORD_ARGON2ID : PASSWORD_DEFAULT;
    $seedPassword = env('SEED_ADMIN_PASSWORD', SEED_ADMIN_PASSWORD_DEFAULT) ?? SEED_ADMIN_PASSWORD_DEFAULT;
    $passwordHash = password_hash($seedPassword, $algorithm);

    $credentialSql = 'INSERT INTO credentials (user_id, password_hash, must_change_password)
                      VALUES (?, ?, TRUE)
                      ON CONFLICT (user_id, provider) ';

    $credentialSql .= $resetAdmin
        ? 'DO UPDATE SET password_hash = EXCLUDED.password_hash,
                       must_change_password = TRUE,
                       failed_attempts = 0,
                       locked_until = NULL,
                       updated_at = now()'
        : 'DO NOTHING';

    $pdo->prepare($credentialSql)->execute([$userId, $passwordHash]);

    if ($resetAdmin) {
        $pdo->prepare('DELETE FROM sessions WHERE user_id = ?')->execute([$userId]);
    }

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
    'Seeding selesai: '.count(SEED_SERVICES).' layanan, '.count(SEED_DOCTORS)." dokter, dan akun admin ({$userId}) disiapkan.\n"
);

exit(0);
