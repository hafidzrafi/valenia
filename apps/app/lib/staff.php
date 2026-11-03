<?php

declare(strict_types=1);

/**
 * Staff area helpers: one time flash messages and (below) the dashboard,
 * attendance, and visit completion queries.
 *
 * All queue and attendance dates use app_today() so the staff area follows the
 * Asia/Jakarta day boundary regardless of the container timezone.
 */

/** Queue one flash message for the next staff page render. */
function staff_flash(string $message): void
{
    $_SESSION['staff_flash'] = $message;
}

/** Read and clear the pending staff flash message. */
function take_staff_flash(): ?string
{
    $message = $_SESSION['staff_flash'] ?? null;
    unset($_SESSION['staff_flash']);

    return is_string($message) && '' !== $message ? $message : null;
}

/**
 * Today's queue rows with patient and service context.
 *
 * Cancelled visits are excluded so the dashboard table only shows work that is
 * still relevant to staff.
 *
 * @return list<array<string, mixed>>
 */
function staff_today_visits(): array
{
    $rows = db()->query(
        "SELECT v.id, v.queue_number, v.queue_date, v.status, v.created_at, v.served_at,
                v.complaint,
                s.id AS service_id, s.name AS service_name, s.queue_prefix,
                p.full_name, p.identity_number
         FROM visits v
         JOIN services s ON s.id = v.service_id
         JOIN patients p ON p.id = v.patient_id
         WHERE v.queue_date = app_today()
           AND v.status <> 'batal'
         ORDER BY s.id, v.queue_number"
    )->fetchAll();

    return array_map(
        static fn (array $row): array => add_queue_code($row),
        $rows
    );
}

/**
 * Dashboard summary: total active rows plus per service and per status
 * breakdowns, all derived from the same already fetched rows so the cards can
 * never disagree with the table below them.
 *
 * @param null|list<array<string, mixed>> $visits
 *
 * @return array{total: int, by_service: array<string, int>, by_status: array<string, int>}
 */
function staff_summary(?array $visits = null): array
{
    $visits ??= staff_today_visits();
    $byService = [];
    $byStatus = ['menunggu' => 0, 'dilayani' => 0, 'selesai' => 0];

    foreach ($visits as $visit) {
        $service = (string) $visit['service_name'];
        $byService[$service] = ($byService[$service] ?? 0) + 1;

        $status = (string) $visit['status'];

        if (array_key_exists($status, $byStatus)) {
            ++$byStatus[$status];
        }
    }

    return ['total' => count($visits), 'by_service' => $byService, 'by_status' => $byStatus];
}

/**
 * Active doctors with today's attendance state.
 *
 * @return list<array<string, mixed>>
 */
function staff_attendance_today(): array
{
    return db()->query(
        'SELECT d.id, d.full_name,
                s.id AS service_id, s.name AS service_name,
                COALESCE(a.is_present, FALSE) AS is_present,
                (a.doctor_id IS NOT NULL) AS recorded,
                sch.start_time, sch.end_time
         FROM doctors d
         JOIN services s ON s.id = d.service_id
         LEFT JOIN doctor_attendance a
                ON a.doctor_id = d.id AND a.attendance_date = app_today()
         LEFT JOIN LATERAL (
                SELECT sc.start_time, sc.end_time
                FROM doctor_schedules sc
                WHERE sc.doctor_id = d.id
                  AND sc.day_of_week = EXTRACT(ISODOW FROM app_today())::int
                ORDER BY sc.start_time
                LIMIT 1
         ) sch ON TRUE
         WHERE d.is_active
         ORDER BY s.id, d.full_name'
    )->fetchAll();
}

/**
 * Record whether a doctor is present today (one row per doctor per day).
 *
 * Returns false when the doctor id is unknown or malformed.
 */
function set_staff_attendance(string $doctorId, bool $present, string $actorId): bool
{
    try {
        $stmt = db()->prepare(
            'INSERT INTO doctor_attendance (doctor_id, attendance_date, is_present, recorded_by)
             VALUES (:doctor, app_today(), :present, :actor)
             ON CONFLICT (doctor_id, attendance_date)
             DO UPDATE SET is_present = EXCLUDED.is_present,
                           recorded_by = EXCLUDED.recorded_by'
        );
        $stmt->execute([
            'doctor' => $doctorId,
            'present' => $present ? 'true' : 'false',
            'actor' => $actorId,
        ]);
    } catch (Throwable $exception) {
        app_log('warning', 'Kehadiran dokter gagal disimpan: '.$exception->getMessage());

        return false;
    }

    return true;
}

/**
 * Today's queue activity, derived from the append-only audit trail.
 *
 * Only INSERT and UPDATE rows on visits are considered. Labels are mapped in
 * PHP so the database holds no display text, and the actor falls back to a
 * neutral label because audit_logs.actor_id has no foreign key.
 *
 * @return list<array{time: string, code: string, patient: string, event: string, actor: string}>
 */
function staff_queue_activity(int $limit = 20): array
{
    $limit = max(1, min($limit, 100));

    $stmt = db()->prepare(
        "SELECT a.created_at, a.action,
                a.old_data->>'status' AS old_status,
                a.new_data->>'status' AS new_status,
                v.queue_number,
                s.queue_prefix,
                p.full_name,
                u.username
         FROM audit_logs a
         JOIN visits v ON v.id = a.row_id
         JOIN services s ON s.id = v.service_id
         JOIN patients p ON p.id = v.patient_id
         LEFT JOIN users u ON u.id = a.actor_id
         WHERE a.table_name = 'visits'
           AND a.action IN ('INSERT', 'UPDATE')
           AND (a.created_at AT TIME ZONE 'Asia/Jakarta')::date = app_today()
         ORDER BY a.created_at DESC, a.id DESC
         LIMIT :take"
    );
    $stmt->bindValue('take', $limit, PDO::PARAM_INT);
    $stmt->execute();

    $rows = $stmt->fetchAll();
    $events = [];

    foreach ($rows as $row) {
        if ('INSERT' === $row['action']) {
            $event = 'Didaftarkan';
        } else {
            $event = match ((string) ($row['new_status'] ?? '')) {
                'dilayani' => 'Dipanggil',
                'selesai' => 'Selesai',
                'batal' => 'Dibatalkan',
                default => 'Diperbarui',
            };
        }

        $time = new DateTimeImmutable((string) $row['created_at']);
        $time = $time->setTimezone(new DateTimeZone('Asia/Jakarta'));

        $events[] = [
            'time' => $time->format('H:i'),
            'code' => queue_code((int) $row['queue_number'], (string) $row['queue_prefix']),
            'patient' => (string) $row['full_name'],
            'event' => $event,
            'actor' => null !== $row['username'] ? (string) $row['username'] : 'akun dihapus',
        ];
    }

    return $events;
}

/**
 * A visit plus the context the examination input page needs: patient, service,
 * queue code, complaint, and the doctor on duty for display.
 *
 * Returns null when the id is not a UUID, the visit is missing, or it is not a
 * today visit. Callers decide which statuses they accept.
 *
 * @return null|array<string, mixed>
 */
function staff_visit_for_input(string $visitId): ?array
{
    if (1 !== preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $visitId)) {
        return null;
    }

    $stmt = db()->prepare(
        'SELECT v.id, v.queue_number, v.queue_date, v.status, v.created_at,
                v.complaint, s.id AS service_id,
                s.name AS service_name, s.queue_prefix,
                p.full_name, p.identity_number,
                (v.queue_date = app_today()) AS is_today
         FROM visits v
         JOIN services s ON s.id = v.service_id
         JOIN patients p ON p.id = v.patient_id
         WHERE v.id = :visit'
    );
    $stmt->execute(['visit' => $visitId]);
    $visit = $stmt->fetch();

    if (!is_array($visit) || !$visit['is_today']) {
        return null;
    }

    $visit = add_queue_code($visit);
    $visit['doctor_on_duty'] = staff_doctor_on_duty((int) $visit['service_id']);

    return $visit;
}

/**
 * The doctor shown as penanggung jawab medis: the active doctor of the poli
 * who is marked hadir today, falling back to the first active doctor of the
 * poli. Display context only; nothing is stored.
 *
 * @return null|array{full_name: string}
 */
function staff_doctor_on_duty(int $serviceId): ?array
{
    $stmt = db()->prepare(
        'SELECT d.full_name
         FROM doctors d
         LEFT JOIN doctor_attendance a
                ON a.doctor_id = d.id AND a.attendance_date = app_today()
         WHERE d.service_id = :service
           AND d.is_active
         ORDER BY COALESCE(a.is_present, FALSE) DESC, d.full_name
         LIMIT 1'
    );
    $stmt->execute(['service' => $serviceId]);
    $row = $stmt->fetch();

    return is_array($row) ? ['full_name' => (string) $row['full_name']] : null;
}

/**
 * Close a visit: only a visit that is currently being served today may be
 * completed. Writes the examination row (diagnosis, treatment, optional note)
 * and flips the status to selesai.
 *
 * Diagnosis and treatment are required; the note is optional and stored in
 * examinations.note (nullable, added in migration 0020).
 *
 * The actor context is set inside the same transaction so the audit trigger and
 * any future RLS policy see the acting user.
 *
 * @return array{ok: bool, error: ?string}
 */
function complete_staff_visit(string $visitId, string $actorId, string $diagnosis = '', string $treatment = '', ?string $note = null): array
{
    $diagnosis = trim($diagnosis);
    $treatment = trim($treatment);
    $note = null === $note || '' === trim($note) ? null : trim($note);

    if ('' === $diagnosis || '' === $treatment) {
        return ['ok' => false, 'error' => 'Diagnosis dan tindakan wajib diisi.'];
    }

    $pdo = db();
    $pdo->beginTransaction();

    try {
        $stmt = $pdo->prepare(
            'SELECT status, complaint, (queue_date = app_today()) AS is_today
             FROM visits
             WHERE id = :id
             FOR UPDATE'
        );
        $stmt->execute(['id' => $visitId]);
        $visit = $stmt->fetch();

        if (!is_array($visit)) {
            $pdo->rollBack();

            return ['ok' => false, 'error' => 'Antrean tidak ditemukan.'];
        }

        if (!$visit['is_today']) {
            $pdo->rollBack();

            return ['ok' => false, 'error' => 'Hanya antrean hari ini yang dapat diselesaikan.'];
        }

        if ('selesai' === $visit['status']) {
            $pdo->rollBack();

            return ['ok' => false, 'error' => 'Antrean ini sudah diselesaikan.'];
        }

        if ('dilayani' !== $visit['status']) {
            $pdo->rollBack();

            return ['ok' => false, 'error' => 'Panggil antrean terlebih dahulu sebelum menyelesaikan.'];
        }

        $pdo->prepare("SELECT set_config('app.actor_id', :actor, true)")
            ->execute(['actor' => $actorId])
        ;

        // examiner_role 'perawat' is the only non doctor value the schema
        // allows, and it requires doctor_id to be NULL. recorded_by keeps the
        // real actor, whether that account is an admin or a perawat.
        $pdo->prepare(
            "SELECT record_examination(:visit, 'perawat', NULL, :actor, :complaint, :findings, :diagnosis, :treatment)"
        )->execute([
            'visit' => $visitId,
            'actor' => $actorId,
            'complaint' => $visit['complaint'],
            'findings' => null,
            'diagnosis' => $diagnosis,
            'treatment' => $treatment,
        ]);

        $pdo->prepare('UPDATE examinations SET note = :note WHERE visit_id = :visit')
            ->execute(['note' => $note, 'visit' => $visitId])
        ;

        $pdo->commit();
    } catch (Throwable $exception) {
        $pdo->rollBack();
        app_log('error', 'Selesaikan antrean gagal: '.$exception->getMessage());

        return ['ok' => false, 'error' => 'Terjadi kesalahan. Coba lagi.'];
    }

    return ['ok' => true, 'error' => null];
}
