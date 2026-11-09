<?php

declare(strict_types=1);

use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * Render the monthly recap PDF from a snapshot.
 *
 * The bytes are returned in memory and never written to disk. The log row
 * created by report_persist() is the durable record; the PDF is derived data.
 *
 * @param array<string, mixed> $snapshot
 */
function report_render_pdf(array $snapshot, string $generatedAt, string $actorName): string
{
    $html = render(app_path('app/views/reports/rekap-bulanan-pdf.php'), [
        'snapshot' => $snapshot,
        'generatedAt' => $generatedAt,
        'actorName' => $actorName,
    ]);

    $options = new Options();
    $options->setChroot(app_path());
    $options->setTempDir(report_pdf_temp_dir());
    $options->setIsRemoteEnabled(false);
    $options->setDefaultFont('DejaVu Sans');

    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html, 'UTF-8');
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    return $dompdf->output();
}

/**
 * Writable scratch directory for dompdf font caching.
 *
 * The runtime image runs as a non-root user, so this stays under the system
 * temp directory instead of the application tree.
 */
function report_pdf_temp_dir(): string
{
    $dir = sys_get_temp_dir().'/valenia-dompdf';

    if (is_dir($dir)) {
        return $dir;
    }

    if (!mkdir($dir, 0o777, true) && !is_dir($dir)) {
        throw new RuntimeException('Direktori sementara PDF tidak dapat dibuat.');
    }

    return $dir;
}
