<?php

declare(strict_types=1);

/**
 * Monthly report helpers: period parsing, week bucketing, aggregation, and the
 * export metadata payload.
 *
 * A period is any start/end pair. Weeks are 7 day chunks counted from the
 * start, so a full month yields Minggu 1 (1-7), Minggu 2 (8-14), and so on.
 * This matches the labels used by the report table without ISO weeks bleeding
 * into adjacent months.
 *
 * Counts are registered visits per queue_date, excluding 'batal'. A patient who
 * registered and is still waiting did come, so 'menunggu' is included.
 *
 * The breakdown covers the three master polis in REPORT_SERVICES. Any other
 * service is ignored in the pivot; making the columns dynamic is a separate
 * change.
 */

/** Slug to service name, the single source of truth for the report columns. */
const REPORT_SERVICES = [
    'umum' => 'Poli Umum',
    'gigi' => 'Poli Gigi',
    'konseling' => 'Konseling/Psikologi',
];

/** Short labels used in the download filename. */
const REPORT_LABELS = [
    'semua' => 'Semua',
    'umum' => 'Umum',
    'gigi' => 'Gigi',
    'konseling' => 'Konseling',
];

/** Indonesian month names, indexed by the 1-based month number. */
const REPORT_MONTHS = [
    1 => 'Januari',
    2 => 'Februari',
    3 => 'Maret',
    4 => 'April',
    5 => 'Mei',
    6 => 'Juni',
    7 => 'Juli',
    8 => 'Agustus',
    9 => 'September',
    10 => 'Oktober',
    11 => 'November',
    12 => 'Desember',
];

function report_timezone(): DateTimeZone
{
    return new DateTimeZone(env('APP_TIMEZONE', 'Asia/Jakarta') ?? 'Asia/Jakarta');
}

/** @return array<string, string> */
function report_services(): array
{
    return REPORT_SERVICES;
}

/** The current month in the application timezone, formatted as Y-m. */
function report_current_month(): string
{
    return (new DateTimeImmutable('now', report_timezone()))->format('Y-m');
}

/**
 * Resolve a Y-m month to its first and last date.
 *
 * Invalid or missing input falls back to the current month, so the page never
 * fails on a hand-edited query string.
 *
 * @return array{start: string, end: string, label: string, value: string}
 */
function report_month_bounds(?string $month): array
{
    $timezone = report_timezone();
    $value = $month ?? report_current_month();
    $parsed = DateTimeImmutable::createFromFormat('!Y-m', $value, $timezone);

    if (!$parsed instanceof DateTimeImmutable || $parsed->format('Y-m') !== $value) {
        $parsed = new DateTimeImmutable('first day of this month', $timezone);
        $value = $parsed->format('Y-m');
    }

    return [
        'start' => $parsed->format('Y-m-01'),
        'end' => $parsed->format('Y-m-t'),
        'label' => REPORT_MONTHS[(int) $parsed->format('n')].' '.$parsed->format('Y'),
        'value' => $value,
    ];
}

/** Strict Y-m-d parse. Returns null when the value is not a real date. */
function report_parse_date(?string $value): ?string
{
    if (null === $value || 1 !== preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
        return null;
    }

    $parsed = DateTimeImmutable::createFromFormat('!Y-m-d', $value, report_timezone());

    if (!$parsed instanceof DateTimeImmutable || $parsed->format('Y-m-d') !== $value) {
        return null;
    }

    return $value;
}

/** Human readable Indonesian date, for example "1 Juni 1987". */
function report_date_label(string $date): string
{
    $parsed = DateTimeImmutable::createFromFormat('!Y-m-d', $date, report_timezone());

    if (!$parsed instanceof DateTimeImmutable || $parsed->format('Y-m-d') !== $date) {
        return $date;
    }

    return $parsed->format('j').' '.REPORT_MONTHS[(int) $parsed->format('n')].' '.$parsed->format('Y');
}

/** Human readable Indonesian timestamp, for example "9 November 2026 13:05". */
function report_timestamp(?DateTimeImmutable $now = null): string
{
    $now ??= new DateTimeImmutable('now', report_timezone());

    return $now->format('j').' '.REPORT_MONTHS[(int) $now->format('n')].' '.$now->format('Y H:i');
}

/**
 * Split a period into 7 day buckets counted from the start.
 *
 * @return list<array{week: int, label: string, start: string, end: string}>
 */
function report_week_spans(string $start, string $end): array
{
    $startDate = report_parse_date($start);
    $endDate = report_parse_date($end);

    if (null === $startDate || null === $endDate || $endDate < $startDate) {
        return [];
    }

    $cursor = new DateTimeImmutable($startDate);
    $limit = new DateTimeImmutable($endDate);
    $weeks = [];
    $number = 1;

    while ($cursor <= $limit) {
        $weekEnd = $cursor->modify('+6 days');

        if ($weekEnd > $limit) {
            $weekEnd = $limit;
        }

        $weeks[] = [
            'week' => $number,
            'label' => 'Minggu '.$number,
            'start' => $cursor->format('Y-m-d'),
            'end' => $weekEnd->format('Y-m-d'),
        ];

        $cursor = $weekEnd->modify('+1 day');
        ++$number;
    }

    return $weeks;
}

/** Inclusive day count between two valid Y-m-d dates, or 0 when invalid. */
function report_days_between(string $start, string $end): int
{
    $startDate = report_parse_date($start);
    $endDate = report_parse_date($end);

    if (null === $startDate || null === $endDate) {
        return 0;
    }

    return (int) (new DateTimeImmutable($startDate))->diff(new DateTimeImmutable($endDate))->days;
}

/**
 * Normalise the export form inputs into a period.
 *
 * Both empty falls back to the current month. Only one side set means a single
 * day. Returns null when the order is reversed or the span exceeds 366 days.
 *
 * @return null|array{start: string, end: string}
 */
function report_range(?string $startInput, ?string $endInput): ?array
{
    $start = report_parse_date($startInput);
    $end = report_parse_date($endInput);

    if (null === $start && null === $end) {
        $bounds = report_month_bounds(null);

        return ['start' => $bounds['start'], 'end' => $bounds['end']];
    }

    if (null === $start) {
        $start = $end;
    }

    if (null === $end) {
        $end = $start;
    }

    if ($end < $start) {
        return null;
    }

    if (report_days_between($start, $end) > 366) {
        return null;
    }

    return ['start' => $start, 'end' => $end];
}

/**
 * Resolve a service slug to its database row.
 *
 * @return null|array{id: int, slug: string, name: string}
 */
function report_service(string $slug): ?array
{
    $name = REPORT_SERVICES[$slug] ?? null;

    if (null === $name) {
        return null;
    }

    $stmt = db()->prepare('SELECT id FROM services WHERE name = :name');
    $stmt->execute(['name' => $name]);
    $id = $stmt->fetchColumn();

    if (!is_numeric($id)) {
        return null;
    }

    return ['id' => (int) $id, 'slug' => $slug, 'name' => $name];
}

/**
 * Build the export snapshot for a period and optional service.
 *
 * @return array<string, mixed>
 */
function report_build_snapshot(string $start, string $end, ?string $serviceSlug): array
{
    $service = null;
    $serviceId = null;

    if (null !== $serviceSlug) {
        $service = report_service($serviceSlug);

        if (null === $service) {
            throw new InvalidArgumentException('Layanan laporan tidak dikenal.');
        }

        $serviceId = $service['id'];
    }

    $weeks = [];

    foreach (report_week_spans($start, $end) as $span) {
        $weeks[] = $span + ['umum' => 0, 'gigi' => 0, 'konseling' => 0, 'total' => 0];
    }

    $index = [];

    foreach ($weeks as $position => $week) {
        $index[$week['week']] = $position;
    }

    $sql = 'SELECT ((v.queue_date - CAST(:bucket_start AS date)) / 7) + 1 AS week_no,
                   s.name AS service_name,
                   count(*) AS total
            FROM visits v
            JOIN services s ON s.id = v.service_id
            WHERE v.queue_date BETWEEN CAST(:range_start AS date) AND CAST(:range_end AS date)
              AND v.status <> :status';
    $params = [
        'bucket_start' => $start,
        'range_start' => $start,
        'range_end' => $end,
        'status' => 'batal',
    ];

    if (null !== $serviceId) {
        $sql .= ' AND v.service_id = :service_id';
        $params['service_id'] = $serviceId;
    }

    $sql .= ' GROUP BY 1, 2 ORDER BY 1, 2';

    $stmt = db()->prepare($sql);
    $stmt->execute($params);

    foreach ($stmt->fetchAll() as $row) {
        $weekNumber = (int) ($row['week_no'] ?? 0);
        $serviceName = (string) ($row['service_name'] ?? '');
        $slug = array_search($serviceName, REPORT_SERVICES, true);

        if (!isset($index[$weekNumber]) || !is_string($slug)) {
            continue;
        }

        $weeks[$index[$weekNumber]][$slug] += (int) ($row['total'] ?? 0);
    }

    foreach ($weeks as $position => $week) {
        $weeks[$position]['total'] = $week['umum'] + $week['gigi'] + $week['konseling'];
    }

    return [
        'report' => 'rekap_bulanan',
        'period' => ['start' => $start, 'end' => $end],
        'service' => null === $service ? null : ['slug' => $service['slug'], 'name' => $service['name']],
        'weeks' => $weeks,
        'totals' => report_totals($weeks),
    ];
}

/**
 * Sum the weekly rows into one totals row.
 *
 * @param list<array<string, int>> $weeks
 *
 * @return array{umum: int, gigi: int, konseling: int, total: int}
 */
function report_totals(array $weeks): array
{
    $totals = ['umum' => 0, 'gigi' => 0, 'konseling' => 0, 'total' => 0];

    foreach ($weeks as $week) {
        $totals['umum'] += (int) ($week['umum'] ?? 0);
        $totals['gigi'] += (int) ($week['gigi'] ?? 0);
        $totals['konseling'] += (int) ($week['konseling'] ?? 0);
    }

    $totals['total'] = $totals['umum'] + $totals['gigi'] + $totals['konseling'];

    return $totals;
}

/**
 * Stable fingerprint of the snapshot content.
 *
 * This detects accidental drift between the rendered numbers and the logged
 * snapshot. It is not a signature and does not prove who wrote the row.
 *
 * @param array<string, mixed> $snapshot
 */
function report_checksum(array $snapshot): string
{
    $json = json_encode($snapshot, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);

    return hash('sha256', $json);
}

/**
 * Deterministic download filename, for example
 * Rekap_Kunjungan_Semua_Juni_1987.pdf.
 *
 * @param array<string, mixed> $snapshot
 */
function report_filename(array $snapshot): string
{
    $period = $snapshot['period'] ?? null;

    if (!is_array($period) || !isset($period['start']) || !is_string($period['start'])) {
        throw new InvalidArgumentException('Snapshot periode tidak valid.');
    }

    $service = $snapshot['service'] ?? null;
    $slug = is_array($service) && isset($service['slug']) && is_string($service['slug'])
        ? $service['slug']
        : 'semua';
    $label = REPORT_LABELS[$slug] ?? REPORT_LABELS['semua'];

    $parsed = DateTimeImmutable::createFromFormat('!Y-m-d', $period['start'], report_timezone());

    if (!$parsed instanceof DateTimeImmutable) {
        throw new InvalidArgumentException('Snapshot periode tidak valid.');
    }

    $month = REPORT_MONTHS[(int) $parsed->format('n')];

    return 'Rekap_Kunjungan_'.$label.'_'.$month.'_'.$parsed->format('Y').'.pdf';
}

/**
 * Append one export row. The PDF bytes are never stored.
 *
 * @param array<string, mixed> $snapshot
 */
function report_persist(
    array $snapshot,
    ?int $serviceId,
    string $actorId,
    string $filename,
    int $byteSize,
): string {
    $period = $snapshot['period'] ?? null;

    if (!is_array($period) || !isset($period['start'], $period['end'])
        || !is_string($period['start']) || !is_string($period['end'])) {
        throw new InvalidArgumentException('Snapshot periode tidak valid.');
    }

    $json = json_encode($snapshot, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);

    $stmt = db()->prepare(
        "INSERT INTO report_exports (
            report_type, period_start, period_end, service_id, generated_by,
            format, filename, byte_size, snapshot, checksum
         ) VALUES (
            'rekap_bulanan', CAST(:period_start AS date), CAST(:period_end AS date),
            :service_id, :actor_id, 'pdf', :filename, :byte_size,
            CAST(:snapshot AS jsonb), :checksum
         ) RETURNING id"
    );
    $stmt->execute([
        'period_start' => $period['start'],
        'period_end' => $period['end'],
        'service_id' => $serviceId,
        'actor_id' => $actorId,
        'filename' => $filename,
        'byte_size' => $byteSize,
        'snapshot' => $json,
        'checksum' => report_checksum($snapshot),
    ]);

    return (string) $stmt->fetchColumn();
}
