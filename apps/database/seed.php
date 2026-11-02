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

    // 3) Admin user. The no-op DO UPDATE makes RETURNING id work whether the
    //    row is newly inserted or already present.
    $upsertUser = $pdo->prepare(
        'INSERT INTO users (username, full_name, role) VALUES (?, ?, ?)
         ON CONFLICT (username) DO UPDATE SET username = EXCLUDED.username
         RETURNING id'
    );
    $upsertUser->execute([SEED_ADMIN_USERNAME, SEED_ADMIN_FULL_NAME, SEED_ADMIN_ROLE]);
    $userId = (string) $upsertUser->fetchColumn();

    // 4) Credential (hashed; never overwritten on re-run).
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
    'Seeding selesai: '.count(SEED_SERVICES).' layanan, '.count(SEED_DOCTORS)." dokter, dan akun admin ({$userId}) disiapkan.\n"
);

exit(0);
