<?php

declare(strict_types=1);

/** Master data: services (poli) and doctors. Counters and presence are derived, never stored. */

/** @return list<array<string, mixed>> */
function list_services_with_counts(): array
{
    return db()->query(
        'SELECT s.id, s.name, s.queue_prefix, s.is_active,
                COUNT(d.id) AS doctor_count,
                COALESCE(BOOL_OR(a.doctor_id IS NOT NULL), FALSE) AS recorded,
                COALESCE(BOOL_OR(COALESCE(a.is_present, FALSE)), FALSE) AS is_present
         FROM services s
         LEFT JOIN doctors d
                ON d.service_id = s.id AND d.is_active
         LEFT JOIN doctor_attendance a
                ON a.doctor_id = d.id AND a.attendance_date = app_today()
         GROUP BY s.id
         ORDER BY s.id'
    )->fetchAll();
}

/** @return list<array<string, mixed>> */
function list_medics(): array
{
    return db()->query(
        'SELECT d.id, d.full_name, d.str_number, d.is_active,
                s.id AS service_id, s.name AS service_name,
                COALESCE(a.is_present, FALSE) AS is_present,
                (a.doctor_id IS NOT NULL) AS recorded
         FROM doctors d
         JOIN services s ON s.id = d.service_id
         LEFT JOIN doctor_attendance a
                ON a.doctor_id = d.id AND a.attendance_date = app_today()
         ORDER BY d.is_active DESC, s.id, d.full_name'
    )->fetchAll();
}

/** services.id is SMALLINT: an id outside it raises 22003 instead of missing. */
function valid_service_id(int $serviceId): bool
{
    return $serviceId >= 1 && $serviceId <= 32767;
}

/**
 * The queue prefix is not editable: tickets are rebuilt from it on render.
 *
 * @return array{ok: bool, error: ?string}
 */
function update_service(int $serviceId, string $name): array
{
    if (!valid_service_id($serviceId)) {
        return ['ok' => false, 'error' => 'Poli tidak ditemukan.'];
    }

    $name = trim($name);

    if (strlen($name) < 2 || strlen($name) > 60) {
        return ['ok' => false, 'error' => 'Nama poli wajib diisi (2-60 karakter).'];
    }

    try {
        $stmt = db()->prepare('UPDATE services SET name = :name WHERE id = :id');
        $stmt->execute(['name' => $name, 'id' => $serviceId]);
    } catch (Throwable $exception) {
        if ($exception instanceof PDOException && '23505' === (string) $exception->getCode()) {
            return ['ok' => false, 'error' => 'Nama poli sudah dipakai.'];
        }

        throw $exception;
    }

    return $stmt->rowCount() > 0
        ? ['ok' => true, 'error' => null]
        : ['ok' => false, 'error' => 'Poli tidak ditemukan.'];
}

/**
 * Deactivation is the delete path: visits reference services with NO ACTION.
 *
 * @return array{ok: bool, error: ?string}
 */
function set_service_active(int $serviceId, bool $active): array
{
    if (!valid_service_id($serviceId)) {
        return ['ok' => false, 'error' => 'Poli tidak ditemukan.'];
    }

    $stmt = db()->prepare('UPDATE services SET is_active = :active WHERE id = :id');
    $stmt->execute(['active' => $active ? 'true' : 'false', 'id' => $serviceId]);

    return $stmt->rowCount() > 0
        ? ['ok' => true, 'error' => null]
        : ['ok' => false, 'error' => 'Poli tidak ditemukan.'];
}

