<?php

/**
 * Standalone PDF template for the monthly recap.
 *
 * Rendered by dompdf, so there is no Tailwind and no shared layout. Every value
 * is escaped and every number is cast to int before output.
 *
 * @var array<string, mixed> $snapshot
 * @var string               $generatedAt
 * @var string               $actorName
 */
$period = $snapshot['period'] ?? null;
$periodStart = is_array($period) && isset($period['start']) && is_string($period['start']) ? $period['start'] : '';
$periodEnd = is_array($period) && isset($period['end']) && is_string($period['end']) ? $period['end'] : '';

$service = $snapshot['service'] ?? null;
$serviceSlug = is_array($service) && isset($service['slug']) && is_string($service['slug']) ? $service['slug'] : null;
$serviceName = is_array($service) && isset($service['name']) && is_string($service['name'])
    ? $service['name']
    : 'Semua Poli';
$columns = null !== $serviceSlug && isset(REPORT_SERVICES[$serviceSlug])
    ? [$serviceSlug => REPORT_SERVICES[$serviceSlug]]
    : REPORT_SERVICES;

$weeksSource = $snapshot['weeks'] ?? null;
$weeks = [];

if (is_array($weeksSource)) {
    foreach ($weeksSource as $week) {
        if (!is_array($week)) {
            continue;
        }

        $weeks[] = [
            'label' => (string) ($week['label'] ?? ''),
            'start' => (string) ($week['start'] ?? ''),
            'end' => (string) ($week['end'] ?? ''),
            'umum' => (int) ($week['umum'] ?? 0),
            'gigi' => (int) ($week['gigi'] ?? 0),
            'konseling' => (int) ($week['konseling'] ?? 0),
            'total' => (int) ($week['total'] ?? 0),
        ];
    }
}

$totalsSource = $snapshot['totals'] ?? null;
$totals = is_array($totalsSource) ? $totalsSource : [];

$periodLabel = '' === $periodStart
    ? '-'
    : report_date_label($periodStart).' sampai '.report_date_label($periodEnd);
$totalAll = (int) ($totals['total'] ?? 0);
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Rekap Kunjungan</title>
    <style>
        @page { margin: 18mm 16mm; }
        body { font-family: "DejaVu Sans", sans-serif; font-size: 10pt; color: #111827; }
        .brand { margin: 0 0 2pt; font-size: 8pt; letter-spacing: 0.08em; text-transform: uppercase; color: #0f766e; }
        h1 { margin: 0 0 10pt; font-size: 16pt; }
        table.meta { width: 100%; margin-bottom: 14pt; border-collapse: collapse; }
        table.meta td { padding: 2pt 0; font-size: 9pt; vertical-align: top; }
        table.meta td.label { width: 80pt; color: #6b7280; }
        table.data { width: 100%; border-collapse: collapse; }
        table.data th, table.data td { border: 0.6pt solid #94a3b8; padding: 5pt 6pt; font-size: 9pt; }
        table.data th { background: #f1f5f9; text-align: left; }
        table.data .num { text-align: right; }
        table.data tfoot td { font-weight: bold; background: #f8fafc; }
        .span { color: #6b7280; font-size: 8pt; }
        .note { margin-top: 10pt; font-size: 8pt; color: #6b7280; }
    </style>
</head>
<body>
    <p class="brand">Poliklinik Politeknik Negeri Malang</p>
    <h1>Laporan Rekap Kunjungan</h1>

    <table class="meta">
        <tr><td class="label">Periode</td><td><?php echo e($periodLabel); ?></td></tr>
        <tr><td class="label">Poli</td><td><?php echo e($serviceName); ?></td></tr>
        <tr><td class="label">Dicetak</td><td><?php echo e($generatedAt); ?></td></tr>
        <tr><td class="label">Oleh</td><td><?php echo e($actorName); ?></td></tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th>Periode</th>
                <?php foreach ($columns as $name) { ?>
                    <th class="num"><?php echo e($name); ?></th>
                <?php } ?>
                <th class="num">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($weeks as $week) { ?>
                <tr>
                    <td>
                        <?php echo e($week['label']); ?><br>
                        <span class="span">
                            <?php echo e(report_date_label($week['start'])); ?>
                            sampai
                            <?php echo e(report_date_label($week['end'])); ?>
                        </span>
                    </td>
                    <?php foreach ($columns as $slug => $name) { ?>
                        <?php
                        $count = 'umum' === $slug
                            ? $week['umum']
                            : ('gigi' === $slug ? $week['gigi'] : $week['konseling']);
                        ?>
                        <td class="num"><?php echo $count; ?></td>
                    <?php } ?>
                    <td class="num"><?php echo $week['total']; ?></td>
                </tr>
            <?php } ?>
        </tbody>
        <tfoot>
            <tr>
                <td>Total</td>
                <?php foreach ($columns as $slug => $name) { ?>
                    <?php
                    $count = 'umum' === $slug
                        ? (int) ($totals['umum'] ?? 0)
                        : ('gigi' === $slug ? (int) ($totals['gigi'] ?? 0) : (int) ($totals['konseling'] ?? 0));
                    ?>
                    <td class="num"><?php echo $count; ?></td>
                <?php } ?>
                <td class="num"><?php echo $totalAll; ?></td>
            </tr>
        </tfoot>
    </table>

    <?php if (0 === $totalAll) { ?>
        <p class="note">Belum ada kunjungan pada periode ini.</p>
    <?php } ?>

    <p class="note">
        Dokumen dihasilkan otomatis oleh VALENIA. Angka dihitung dari kunjungan terdaftar per tanggal
        antrean, di luar kunjungan yang dibatalkan.
    </p>
</body>
</html>
