<?php

declare(strict_types=1);

/**
 * /admin/rekap-bulanan/unduh: stream the recap PDF and log the export.
 *
 * The PDF is rendered in memory and never written to disk. A report_exports row
 * keeps the period, actor, filename, byte size, aggregate snapshot, and
 * checksum, so an identical report can be re-rendered later without storing
 * bytes.
 */
return [
    '_middleware' => ['role:admin'],

    'POST' => static function (Request $request): Response {
        $range = report_range(request_input('dari'), request_input('sampai'));

        if (null === $range) {
            return new Response('Rentang tanggal tidak valid.', 400);
        }

        $poli = request_input('poli', 'semua') ?? 'semua';
        $allowed = array_merge(['semua', ''], array_keys(REPORT_SERVICES));

        if (!in_array($poli, $allowed, true)) {
            return new Response('Poli tidak dikenal.', 400);
        }

        $slug = ('semua' === $poli || '' === $poli) ? null : $poli;
        $service = null === $slug ? null : report_service($slug);

        if (null !== $slug && null === $service) {
            return new Response('Poli tidak tersedia.', 400);
        }

        $snapshot = report_build_snapshot($range['start'], $range['end'], $slug);

        $actorId = (string) $request->attribute('user_id');
        $user = current_user();
        $actorName = is_array($user) && isset($user['full_name']) && is_string($user['full_name'])
            ? $user['full_name']
            : (string) $request->attribute('user');

        try {
            $pdf = report_render_pdf($snapshot, report_timestamp(), $actorName);
        } catch (Throwable $exception) {
            app_log('error', 'Render PDF rekap gagal: '.$exception->getMessage());

            return new Response('Gagal membuat laporan.', 500);
        }

        $filename = report_filename($snapshot);

        try {
            report_persist($snapshot, $service['id'] ?? null, $actorId, $filename, strlen($pdf));
        } catch (Throwable $exception) {
            // The file is already valid; do not fail the download over the log.
            app_log('error', 'Simpan log ekspor gagal: '.$exception->getMessage());
        }

        return new Response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Content-Length' => (string) strlen($pdf),
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    },
];
