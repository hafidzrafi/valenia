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

/** doctors.id is UUID: a forged id raises 22P02 instead of missing. */
function valid_medic_id(string $medicId): bool
{
    return 1 === preg_match(
        '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i',
        $medicId
    );
}

/** The STR format is not validated: a guessed pattern would reject valid numbers. */
function medic_error(string $fullName, string $strNumber, int $serviceId): ?string
{
    $fullName = trim($fullName);

    if (strlen($fullName) < 2 || strlen($fullName) > 120) {
        return 'Nama tenaga medis wajib diisi (2-120 karakter).';
    }

    if (strlen(trim($strNumber)) > 32) {
        return 'Nomor STR maksimal 32 karakter.';
    }

    if ($serviceId <= 0) {
        return 'Poli wajib dipilih.';
    }

    if (!valid_service_id($serviceId)) {
        return 'Poli tidak ditemukan.';
    }

    return null;
}

/** Map a constraint violation to a message, or null so the caller rethrows. */
function medic_write_error(Throwable $exception): ?string
{
    if (!$exception instanceof PDOException) {
        return null;
    }

    return match ((string) $exception->getCode()) {
        '23505' => 'Nomor STR sudah dipakai tenaga medis lain.',
        '23503' => 'Poli tidak ditemukan.',
        default => null,
    };
}

/**
 * An empty STR is stored as NULL: the unique index is partial on IS NOT NULL.
 *
 * @return array{ok: bool, error: ?string}
 */
function create_medic(string $fullName, string $strNumber, int $serviceId, string $actorId): array
{
    $error = medic_error($fullName, $strNumber, $serviceId);

    if (null !== $error) {
        return ['ok' => false, 'error' => $error];
    }

    $fullName = trim($fullName);
    $strNumber = trim($strNumber);
    $storedStr = '' === $strNumber ? null : $strNumber;

    $pdo = db();
    $pdo->beginTransaction();

    try {
        $insert = $pdo->prepare(
            'INSERT INTO doctors (full_name, str_number, service_id)
             VALUES (:name, :str, :service)
             RETURNING id'
        );
        $insert->execute(['name' => $fullName, 'str' => $storedStr, 'service' => $serviceId]);
        $medicId = (string) $insert->fetchColumn();

        audit_log('doctors', $medicId, 'INSERT', $actorId, null, json_encode([
            'full_name' => $fullName,
            'str_number' => $storedStr,
            'service_id' => $serviceId,
        ]) ?: null);

        $pdo->commit();
    } catch (Throwable $exception) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        $mapped = medic_write_error($exception);

        if (null !== $mapped) {
            return ['ok' => false, 'error' => $mapped];
        }

        throw $exception;
    }

    return ['ok' => true, 'error' => null];
}

/**
 * The row is read FOR UPDATE so the audit holds the values actually replaced.
 *
 * @return array{ok: bool, error: ?string}
 */
function update_medic(
    string $medicId,
    string $fullName,
    string $strNumber,
    int $serviceId,
    string $actorId,
): array {
    if (!valid_medic_id($medicId)) {
        return ['ok' => false, 'error' => 'Tenaga medis tidak ditemukan.'];
    }

    $error = medic_error($fullName, $strNumber, $serviceId);

    if (null !== $error) {
        return ['ok' => false, 'error' => $error];
    }

    $fullName = trim($fullName);
    $strNumber = trim($strNumber);
    $storedStr = '' === $strNumber ? null : $strNumber;

    $pdo = db();
    $pdo->beginTransaction();

    try {
        $find = $pdo->prepare(
            'SELECT full_name, str_number, service_id FROM doctors WHERE id = :id FOR UPDATE'
        );
        $find->execute(['id' => $medicId]);
        $old = $find->fetch();

        if (!is_array($old)) {
            $pdo->rollBack();

            return ['ok' => false, 'error' => 'Tenaga medis tidak ditemukan.'];
        }

        $pdo->prepare(
            'UPDATE doctors
                SET full_name = :name, str_number = :str, service_id = :service
              WHERE id = :id'
        )->execute([
            'name' => $fullName,
            'str' => $storedStr,
            'service' => $serviceId,
            'id' => $medicId,
        ]);

        audit_log('doctors', $medicId, 'UPDATE', $actorId, json_encode([
            'full_name' => (string) $old['full_name'],
            'str_number' => $old['str_number'],
            'service_id' => (int) $old['service_id'],
        ]) ?: null, json_encode([
            'full_name' => $fullName,
            'str_number' => $storedStr,
            'service_id' => $serviceId,
        ]) ?: null);

        $pdo->commit();
    } catch (Throwable $exception) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        $mapped = medic_write_error($exception);

        if (null !== $mapped) {
            return ['ok' => false, 'error' => $mapped];
        }

        throw $exception;
    }

    return ['ok' => true, 'error' => null];
}

/**
 * Deactivation is the delete path: examinations reference doctors with ON DELETE RESTRICT.
 *
 * @return array{ok: bool, error: ?string}
 */
function set_medic_active(string $medicId, bool $active, string $actorId): array
{
    if (!valid_medic_id($medicId)) {
        return ['ok' => false, 'error' => 'Tenaga medis tidak ditemukan.'];
    }

    $pdo = db();
    $pdo->beginTransaction();

    try {
        $find = $pdo->prepare('SELECT is_active FROM doctors WHERE id = :id FOR UPDATE');
        $find->execute(['id' => $medicId]);
        $row = $find->fetch();

        if (!is_array($row)) {
            $pdo->rollBack();

            return ['ok' => false, 'error' => 'Tenaga medis tidak ditemukan.'];
        }

        $pdo->prepare('UPDATE doctors SET is_active = :active WHERE id = :id')
            ->execute(['active' => $active ? 'true' : 'false', 'id' => $medicId])
        ;

        audit_log(
            'doctors',
            $medicId,
            'UPDATE',
            $actorId,
            json_encode(['is_active' => (bool) $row['is_active']]) ?: null,
            json_encode(['is_active' => $active]) ?: null,
        );

        $pdo->commit();
    } catch (Throwable $exception) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        throw $exception;
    }

    return ['ok' => true, 'error' => null];
}
