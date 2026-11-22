<?php

declare(strict_types=1);

/**
 * Weekly doctor schedule (FR-DOC-04): recurring slots per doctor and ISO weekday.
 * A slot repeats every week, so nothing here is dated.
 */

/** ISO weekday numbers, 1 = Senin .. 7 = Minggu, matching EXTRACT(ISODOW). */
const SCHEDULE_DAYS = [
    1 => 'Senin',
    2 => 'Selasa',
    3 => 'Rabu',
    4 => 'Kamis',
    5 => 'Jumat',
    6 => 'Sabtu',
    7 => 'Minggu',
];

/**
 * Active doctors with their slots grouped per ISO weekday. Deactivated doctors
 * are skipped; their rows stay in the table.
 *
 * @return list<array<string, mixed>>
 */
function list_weekly_schedule(): array
{
    $rows = db()->query(
        'SELECT d.id, d.full_name, s.id AS service_id, s.name AS service_name,
                sc.id AS slot_id, sc.day_of_week, sc.start_time, sc.end_time
         FROM doctors d
         JOIN services s ON s.id = d.service_id
         LEFT JOIN doctor_schedules sc ON sc.doctor_id = d.id
         WHERE d.is_active
         ORDER BY s.id, d.full_name, sc.day_of_week, sc.start_time'
    )->fetchAll();

    $doctors = [];

    foreach ($rows as $row) {
        $id = (string) $row['id'];

        if (!isset($doctors[$id])) {
            $doctors[$id] = [
                'id' => $id,
                'full_name' => (string) $row['full_name'],
                'service_id' => (int) $row['service_id'],
                'service_name' => (string) $row['service_name'],
                'slots' => [],
            ];
        }

        if (null === $row['slot_id']) {
            continue;
        }

        $doctors[$id]['slots'][(int) $row['day_of_week']][] = [
            'id' => (string) $row['slot_id'],
            'start_time' => substr((string) $row['start_time'], 0, 5),
            'end_time' => substr((string) $row['end_time'], 0, 5),
        ];
    }

    return array_values($doctors);
}

/** doctor_schedules.id is UUID: a forged id raises 22P02 instead of missing. */
function valid_schedule_id(string $slotId): bool
{
    return 1 === preg_match(
        '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i',
        $slotId
    );
}

/** Zero-padded 24-hour HH:MM, the only shape an <input type="time"> submits. */
function valid_schedule_time(string $time): bool
{
    return 1 === preg_match('/^([01][0-9]|2[0-3]):[0-5][0-9]$/', $time);
}

/** Pure input validation; doctor existence is checked by create_schedule_slot(). */
function schedule_slot_error(string $doctorId, int $dayOfWeek, string $startTime, string $endTime): ?string
{
    if (!valid_medic_id($doctorId)) {
        return 'Tenaga medis tidak ditemukan.';
    }

    if (!isset(SCHEDULE_DAYS[$dayOfWeek])) {
        return 'Hari tidak valid.';
    }

    if (!valid_schedule_time($startTime) || !valid_schedule_time($endTime)) {
        return 'Jam harus dalam format HH:MM.';
    }

    if ($endTime <= $startTime) {
        return 'Jam selesai harus setelah jam mulai.';
    }

    return null;
}

/**
 * Only an existing, active doctor can be scheduled.
 *
 * @return array{ok: bool, error: ?string}
 */
function create_schedule_slot(
    string $doctorId,
    int $dayOfWeek,
    string $startTime,
    string $endTime,
    string $actorId,
): array {
    $error = schedule_slot_error($doctorId, $dayOfWeek, $startTime, $endTime);

    if (null !== $error) {
        return ['ok' => false, 'error' => $error];
    }

    $pdo = db();
    $pdo->beginTransaction();

    try {
        // FOR UPDATE: a concurrent deactivation must not slip past this check.
        $active = $pdo->prepare('SELECT is_active FROM doctors WHERE id = :id FOR UPDATE');
        $active->execute(['id' => $doctorId]);

        if (!(bool) $active->fetchColumn()) {
            $pdo->rollBack();

            return ['ok' => false, 'error' => 'Tenaga medis tidak ditemukan atau nonaktif.'];
        }

        $insert = $pdo->prepare(
            'INSERT INTO doctor_schedules (doctor_id, day_of_week, start_time, end_time)
             VALUES (:doctor, :day, :start, :end)
             RETURNING id'
        );
        $insert->execute([
            'doctor' => $doctorId,
            'day' => $dayOfWeek,
            'start' => $startTime,
            'end' => $endTime,
        ]);
        $slotId = (string) $insert->fetchColumn();

        audit_log('doctor_schedules', $slotId, 'INSERT', $actorId, null, json_encode([
            'doctor_id' => $doctorId,
            'day_of_week' => $dayOfWeek,
            'start_time' => $startTime,
            'end_time' => $endTime,
        ]) ?: null);

        $pdo->commit();
    } catch (Throwable $exception) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        throw $exception;
    }

    return ['ok' => true, 'error' => null];
}

/**
 * Hard delete; the audit row is the only record that the slot existed.
 *
 * @return array{ok: bool, error: ?string}
 */
function delete_schedule_slot(string $slotId, string $actorId): array
{
    if (!valid_schedule_id($slotId)) {
        return ['ok' => false, 'error' => 'Jadwal tidak ditemukan.'];
    }

    $pdo = db();
    $pdo->beginTransaction();

    try {
        $find = $pdo->prepare(
            'SELECT doctor_id, day_of_week, start_time, end_time
             FROM doctor_schedules WHERE id = :id FOR UPDATE'
        );
        $find->execute(['id' => $slotId]);
        $old = $find->fetch();

        if (!is_array($old)) {
            $pdo->rollBack();

            return ['ok' => false, 'error' => 'Jadwal tidak ditemukan.'];
        }

        $pdo->prepare('DELETE FROM doctor_schedules WHERE id = :id')->execute(['id' => $slotId]);

        audit_log('doctor_schedules', $slotId, 'DELETE', $actorId, json_encode([
            'doctor_id' => (string) $old['doctor_id'],
            'day_of_week' => (int) $old['day_of_week'],
            'start_time' => substr((string) $old['start_time'], 0, 5),
            'end_time' => substr((string) $old['end_time'], 0, 5),
        ]) ?: null, null);

        $pdo->commit();
    } catch (Throwable $exception) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        throw $exception;
    }

    return ['ok' => true, 'error' => null];
}
