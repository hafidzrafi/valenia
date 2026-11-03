<?php

declare(strict_types=1);
use BaconQrCode\Common\ErrorCorrectionLevel;
use BaconQrCode\Encoder\Encoder;
use BaconQrCode\Encoder\QrCode;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;

/**
 * Patient registration and portal helpers.
 *
 * Route files stay thin; identity validation, patient lookup, queue issuance,
 * and portal queries live here because they are shared by several public routes.
 */

/**
 * @return array<string, mixed>
 */
function registration_config(): array
{
    static $config = null;

    return $config ??= require app_path('app/config/registration.php');
}

/**
 * @return array{ok: bool, type: ?string, error: ?string}
 */
function validate_identity(string $number): array
{
    $number = trim($number);

    if ('' === $number) {
        return ['ok' => false, 'type' => null, 'error' => 'NIM/NIP wajib diisi.'];
    }

    if (!ctype_digit($number)) {
        return ['ok' => false, 'type' => null, 'error' => 'NIM/NIP hanya boleh berisi angka.'];
    }

    foreach (registration_config()['identity_patterns'] as $type => $pattern) {
        if (1 === preg_match($pattern, $number)) {
            return ['ok' => true, 'type' => $type, 'error' => null];
        }
    }

    $length = strlen($number);

    if ($length < 12) {
        return ['ok' => false, 'type' => null, 'error' => 'NIM harus terdiri dari 12 digit.'];
    }

    if ($length > 18) {
        return ['ok' => false, 'type' => null, 'error' => 'NIP tidak boleh lebih dari 18 digit.'];
    }

    return ['ok' => false, 'type' => null, 'error' => 'NIM harus 12 digit atau NIP harus 18 digit.'];
}

/**
 * @param array<string, mixed> $input
 *
 * @return array{ok: bool, error: ?string}
 */
function validate_registration_input(array $input): array
{
    $identity = validate_identity((string) ($input['identity_number'] ?? ''));

    if (!$identity['ok']) {
        return ['ok' => false, 'error' => $identity['error']];
    }

    $birthDate = trim((string) ($input['birth_date'] ?? ''));
    $parsed = DateTimeImmutable::createFromFormat('!Y-m-d', $birthDate);
    $errors = DateTimeImmutable::getLastErrors();

    if (false === $parsed || (false !== $errors && ($errors['warning_count'] > 0 || $errors['error_count'] > 0))) {
        return ['ok' => false, 'error' => 'Tanggal lahir tidak valid.'];
    }

    if ($parsed > new DateTimeImmutable('today')) {
        return ['ok' => false, 'error' => 'Tanggal lahir tidak boleh di masa depan.'];
    }

    $fullName = trim((string) ($input['full_name'] ?? ''));

    if (strlen($fullName) < 2 || strlen($fullName) > 120) {
        return ['ok' => false, 'error' => 'Nama lengkap wajib diisi (2-120 karakter).'];
    }

    if (!in_array($input['gender'] ?? '', ['L', 'P'], true)) {
        return ['ok' => false, 'error' => 'Jenis kelamin wajib dipilih.'];
    }

    $contact = trim((string) ($input['contact'] ?? ''));

    if (strlen($contact) > 30) {
        return ['ok' => false, 'error' => 'Kontak maksimal 30 karakter.'];
    }

    $complaint = trim((string) ($input['complaint'] ?? ''));

    if (strlen($complaint) < 3 || strlen($complaint) > 2000) {
        return ['ok' => false, 'error' => 'Keluhan wajib diisi (3-2000 karakter).'];
    }

    return ['ok' => true, 'error' => null];
}

/**
 * Format a stored integer queue number as a human-readable code.
 */
function queue_code(int $number, string $prefix): string
{
    $prefix = strtoupper(trim($prefix));

    if ('' === $prefix) {
        return (string) $number;
    }

    return $prefix.'-'.str_pad((string) $number, 2, '0', STR_PAD_LEFT);
}

/**
 * @param array<string, mixed> $visit
 *
 * @return array<string, mixed>
 */
function add_queue_code(array $visit): array
{
    $visit['queue_code'] = queue_code(
        (int) ($visit['queue_number'] ?? 0),
        (string) ($visit['queue_prefix'] ?? ''),
    );

    return $visit;
}

/**
 * @return array{ok: bool, service: ?array<string, mixed>, error: ?string}
 */
function resolve_service(string $key): array
{
    $key = strtoupper(trim($key));
    $name = registration_config()['services'][$key] ?? null;

    if (!is_string($name)) {
        return ['ok' => false, 'service' => null, 'error' => 'Layanan tidak tersedia.'];
    }

    $stmt = db()->prepare('SELECT id, name, queue_prefix FROM services WHERE name = :name AND is_active = TRUE');
    $stmt->execute(['name' => $name]);
    $service = $stmt->fetch();

    if (!is_array($service)) {
        return ['ok' => false, 'service' => null, 'error' => 'Layanan tidak tersedia.'];
    }

    return ['ok' => true, 'service' => $service, 'error' => null];
}

/**
 * @return null|array<string, mixed>
 */
function find_patient(string $identityNumber): ?array
{
    $stmt = db()->prepare(
        'SELECT id, identity_type, identity_number, full_name, birth_date, gender, contact
         FROM patients WHERE identity_number = :number'
    );
    $stmt->execute(['number' => $identityNumber]);
    $patient = $stmt->fetch();

    return is_array($patient) ? $patient : null;
}

/**
 * @param array<string, mixed> $input
 *
 * @return array{ok: bool, patient: ?array<string, mixed>, created: bool, error: ?string}
 */
function find_or_create_patient(array $input): array
{
    $identity = validate_identity((string) ($input['identity_number'] ?? ''));

    if (!$identity['ok']) {
        return ['ok' => false, 'patient' => null, 'created' => false, 'error' => $identity['error']];
    }

    $number = trim((string) $input['identity_number']);
    $birthDate = (string) $input['birth_date'];
    $patient = find_patient($number);

    if (null !== $patient) {
        if ((string) $patient['birth_date'] !== $birthDate) {
            return [
                'ok' => false,
                'patient' => null,
                'created' => false,
                'error' => 'Data pasien tidak ditemukan atau tidak cocok.',
            ];
        }

        return ['ok' => true, 'patient' => $patient, 'created' => false, 'error' => null];
    }

    $contact = trim((string) ($input['contact'] ?? ''));
    $stmt = db()->prepare(
        'INSERT INTO patients (identity_type, identity_number, full_name, birth_date, gender, contact)
         VALUES (:type, :number, :name, :birth, :gender, :contact)
         RETURNING id, identity_type, identity_number, full_name, birth_date, gender, contact'
    );

    try {
        $stmt->execute([
            'type' => $identity['type'],
            'number' => $number,
            'name' => trim((string) $input['full_name']),
            'birth' => $birthDate,
            'gender' => (string) $input['gender'],
            'contact' => '' === $contact ? null : $contact,
        ]);
        $created = $stmt->fetch();

        return ['ok' => true, 'patient' => is_array($created) ? $created : null, 'created' => true, 'error' => null];
    } catch (PDOException $exception) {
        if ('23505' !== $exception->getCode()) {
            throw $exception;
        }

        $existing = find_patient($number);

        if (null !== $existing && (string) $existing['birth_date'] === $birthDate) {
            return ['ok' => true, 'patient' => $existing, 'created' => false, 'error' => null];
        }

        return [
            'ok' => false,
            'patient' => null,
            'created' => false,
            'error' => 'Data pasien tidak ditemukan atau tidak cocok.',
        ];
    }
}

/**
 * @return null|array<string, mixed>
 */
function has_active_visit_today(string $patientId, int $serviceId): ?array
{
    $stmt = db()->prepare(
        "SELECT v.id, v.queue_number, v.queue_date, v.status, s.name AS service_name, s.queue_prefix
         FROM visits v
         JOIN services s ON s.id = v.service_id
         WHERE v.patient_id = :patient
           AND v.service_id = :service
           AND v.queue_date = app_today()
           AND v.status IN ('menunggu', 'dilayani')
         ORDER BY v.created_at DESC
         LIMIT 1"
    );
    $stmt->execute(['patient' => $patientId, 'service' => $serviceId]);
    $visit = $stmt->fetch();

    return is_array($visit) ? $visit : null;
}

/**
 * @return array<string, mixed>
 */
function issue_guest_ticket(string $patientId, int $serviceId, string $complaint): array
{
    $stmt = db()->prepare('SELECT * FROM register_guest_visit(:patient, :service, :complaint)');
    $stmt->execute([
        'patient' => $patientId,
        'service' => $serviceId,
        'complaint' => $complaint,
    ]);
    $ticket = $stmt->fetch();

    if (!is_array($ticket)) {
        throw new RuntimeException('Tiket tidak dapat diterbitkan.');
    }

    $patient = db()->prepare(
        'SELECT id, identity_number, full_name, birth_date, gender FROM patients WHERE id = :id'
    );
    $patient->execute(['id' => $patientId]);

    $service = db()->prepare('SELECT id, name, queue_prefix FROM services WHERE id = :id');
    $service->execute(['id' => $serviceId]);
    $serviceRow = $service->fetch();

    return [
        'visit_id' => (string) $ticket['visit_id'],
        'queue_number' => (int) $ticket['queue_number'],
        'queue_code' => queue_code((int) $ticket['queue_number'], (string) (is_array($serviceRow) ? ($serviceRow['queue_prefix'] ?? '') : '')),
        'ticket_token' => (string) $ticket['ticket_token'],
        'patient' => $patient->fetch(),
        'service' => $serviceRow,
        'created_at' => date('Y-m-d H:i:s'),
    ];
}

/**
 * @return null|array<string, mixed>
 */
function find_patient_for_portal(string $identityNumber, string $birthDate): ?array
{
    if (!validate_identity($identityNumber)['ok']) {
        return null;
    }

    $patient = find_patient(trim($identityNumber));

    if (null === $patient || (string) $patient['birth_date'] !== trim($birthDate)) {
        return null;
    }

    return $patient;
}

function unlock_portal(string $patientId): void
{
    $_SESSION['portal_patient_id'] = $patientId;
    $_SESSION['portal_unlocked_at'] = time();
}

function lock_portal(): void
{
    unset($_SESSION['portal_patient_id'], $_SESSION['portal_unlocked_at']);
}

/**
 * @return null|array<string, mixed>
 */
function portal_patient(): ?array
{
    $patientId = $_SESSION['portal_patient_id'] ?? null;
    $unlockedAt = $_SESSION['portal_unlocked_at'] ?? null;

    if (!is_string($patientId) || '' === $patientId || !is_int($unlockedAt)) {
        return null;
    }

    $ttl = (int) (registration_config()['portal_ttl_seconds'] ?? 900);

    if (time() - $unlockedAt > $ttl) {
        lock_portal();

        return null;
    }

    $stmt = db()->prepare(
        'SELECT id, identity_type, identity_number, full_name, birth_date, gender, contact
         FROM patients WHERE id = :id'
    );
    $stmt->execute(['id' => $patientId]);
    $patient = $stmt->fetch();

    if (!is_array($patient)) {
        lock_portal();

        return null;
    }

    return $patient;
}

/**
 * Build the single QR matrix used by both the inline SVG and PNG download.
 */
function ticket_qr_object(string $token): ?QrCode
{
    if ('' === $token) {
        return null;
    }

    try {
        return Encoder::encode($token, ErrorCorrectionLevel::M());
    } catch (Throwable $exception) {
        app_log('error', 'QR encoding failed: '.$exception->getMessage());

        return null;
    }
}

/**
 * Render the ticket token as an SVG QR code, server-side.
 */
function ticket_qr_svg(string $token, int $size = 220): ?string
{
    $qrCode = ticket_qr_object($token);

    if (null === $qrCode) {
        return null;
    }

    try {
        $renderer = new ImageRenderer(
            new RendererStyle($size, 1),
            new SvgImageBackEnd(),
        );

        return $renderer->render($qrCode);
    } catch (Throwable $exception) {
        app_log('error', 'QR generation failed: '.$exception->getMessage());

        return null;
    }
}

/**
 * Build one PNG chunk.
 */
function qr_png_chunk(string $type, string $data): string
{
    return pack('N', strlen($data))
        .$type
        .$data
        .pack('N', crc32($type.$data));
}

/**
 * Render the ticket token as an 8-bit grayscale PNG, server-side.
 *
 * The PNG is written directly from the QR matrix, so the runtime image needs
 * neither GD nor Imagick.
 */
function ticket_qr_png(string $token, int $size = 220): ?string
{
    if ('' === $token) {
        return null;
    }

    try {
        $qrCode = ticket_qr_object($token);

        if (null === $qrCode) {
            return null;
        }

        $matrix = $qrCode->getMatrix();
        $modules = $matrix->getWidth();
        $margin = 4;
        $scale = max(1, intdiv($size, $modules + ($margin * 2)));
        $dimension = ($modules + ($margin * 2)) * $scale;

        $raw = '';

        for ($y = 0; $y < $dimension; ++$y) {
            $moduleY = intdiv($y, $scale) - $margin;
            $row = "\x00";

            for ($x = 0; $x < $dimension; ++$x) {
                $moduleX = intdiv($x, $scale) - $margin;
                $black = $moduleX >= 0
                    && $moduleY >= 0
                    && $moduleX < $modules
                    && $moduleY < $modules
                    && 1 === $matrix->get($moduleX, $moduleY);

                $row .= $black ? "\x00" : "\xff";
            }

            $raw .= $row;
        }

        $compressed = gzcompress($raw, 9);

        if (!is_string($compressed)) {
            return null;
        }

        $ihdr = pack('NNCCCCC', $dimension, $dimension, 8, 0, 0, 0, 0);

        return "\x89PNG\r\n\x1a\n"
            .qr_png_chunk('IHDR', $ihdr)
            .qr_png_chunk('IDAT', $compressed)
            .qr_png_chunk('IEND', '');
    } catch (Throwable $exception) {
        app_log('error', 'PNG QR generation failed: '.$exception->getMessage());

        return null;
    }
}

/**
 * @return list<array<string, mixed>>
 */
function queue_boards(): array
{
    return db()->query('SELECT * FROM queue_boards()')->fetchAll();
}

/**
 * @return array{advanced: bool, queue_code: ?string, visit_id: ?string}
 */
function advance_queue(int $serviceId, string $actorId): array
{
    $stmt = db()->prepare('SELECT advanced, queue_code, visit_id FROM advance_queue(:service, :actor)');
    $stmt->execute(['service' => $serviceId, 'actor' => $actorId]);
    $row = $stmt->fetch();

    if (!is_array($row)) {
        return ['advanced' => false, 'queue_code' => null, 'visit_id' => null];
    }

    return [
        'advanced' => (bool) $row['advanced'],
        'queue_code' => null !== $row['queue_code'] ? (string) $row['queue_code'] : null,
        'visit_id' => null !== $row['visit_id'] ? (string) $row['visit_id'] : null,
    ];
}

/**
 * @return array{active: null|array<string, mixed>, selected: null|array<string, mixed>, history: list<array<string, mixed>>, pagination: array{page: int, per_page: int, total: int, pages: int}}
 */
function portal_data(string $patientId, ?string $selectedVisitId = null, int $page = 1, int $perPage = 5): array
{
    $active = db()->prepare(
        "SELECT v.id, v.queue_number, v.queue_date, v.status, v.created_at, v.ticket_token,
                s.name AS service_name, s.queue_prefix, TRUE AS is_active
         FROM visits v
         JOIN services s ON s.id = v.service_id
         WHERE v.patient_id = :patient
           AND v.queue_date = app_today()
           AND v.status IN ('menunggu', 'dilayani')
         ORDER BY v.created_at DESC
         LIMIT 1"
    );
    $active->execute(['patient' => $patientId]);
    $activeVisit = $active->fetch();
    $activeVisit = is_array($activeVisit) ? add_queue_code($activeVisit) : null;

    $selectedVisit = null;

    $isUuid = is_string($selectedVisitId)
        && 1 === preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $selectedVisitId);

    if ($isUuid) {
        $selected = db()->prepare(
            "SELECT v.id, v.queue_number, v.queue_date, v.status, v.created_at, v.ticket_token,
                    s.name AS service_name, s.queue_prefix,
                    (v.status IN ('menunggu', 'dilayani') AND v.queue_date = app_today()) AS is_active
             FROM visits v
             JOIN services s ON s.id = v.service_id
             WHERE v.id = :visit AND v.patient_id = :patient
             LIMIT 1"
        );
        $selected->execute(['visit' => $selectedVisitId, 'patient' => $patientId]);
        $selectedVisit = $selected->fetch();
        $selectedVisit = is_array($selectedVisit) ? add_queue_code($selectedVisit) : null;
    }

    if (null === $selectedVisit) {
        $selectedVisit = $activeVisit;
    }

    $page = max(1, $page);
    $perPage = min(max(1, $perPage), 50);

    $history = db()->prepare(
        'SELECT visit_id AS id, queue_number, queue_date, status, created_at,
                service_name, queue_prefix, queue_code, total
         FROM portal_history_page(:patient, :page, :per_page)'
    );
    $history->execute(['patient' => $patientId, 'page' => $page, 'per_page' => $perPage]);
    $rows = $history->fetchAll();

    $total = 0;

    if ([] !== $rows && isset($rows[0]['total'])) {
        $total = (int) $rows[0]['total'];
    }

    $pages = max(1, (int) ceil($total / $perPage));

    if ($page > $pages) {
        $page = $pages;
        $history->execute(['patient' => $patientId, 'page' => $page, 'per_page' => $perPage]);
        $rows = $history->fetchAll();
    }

    return [
        'active' => $activeVisit,
        'selected' => $selectedVisit,
        'history' => array_map('add_queue_code', $rows),
        'pagination' => [
            'page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'pages' => $pages,
        ],
    ];
}

/**
 * Cancel a portal visit through the database state-machine function.
 */
function cancel_portal_visit(string $patientId, string $visitId): bool
{
    $stmt = db()->prepare('SELECT cancelled FROM cancel_guest_visit(:visit, :patient)');
    $stmt->execute([
        'visit' => $visitId,
        'patient' => $patientId,
    ]);

    return (bool) $stmt->fetchColumn();
}

/**
 * Resolve the ticket available for PNG download.
 *
 * Prefers the fresh registration ticket, then falls back to the active portal
 * visit. Both paths are session-bound: either the ticket just issued in this
 * browser session, or a portal session unlocked via NIM/NIP plus birth date.
 *
 * @return null|array<string, mixed>
 */
function downloadable_ticket(): ?array
{
    $ticket = $_SESSION['ticket'] ?? null;

    if (is_array($ticket) && isset($ticket['ticket_token'])) {
        return $ticket;
    }

    $patient = portal_patient();

    if (!is_array($patient)) {
        return null;
    }

    $data = portal_data((string) $patient['id']);
    $active = $data['active'] ?? null;

    if (!is_array($active) || !isset($active['ticket_token'])) {
        return null;
    }

    return [
        'visit_id' => (string) $active['id'],
        'queue_number' => (int) $active['queue_number'],
        'queue_code' => (string) ($active['queue_code'] ?? ''),
        'ticket_token' => (string) $active['ticket_token'],
        'patient' => $patient,
        'service' => ['name' => $active['service_name']],
        'created_at' => (string) $active['created_at'],
    ];
}
