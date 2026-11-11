<?php

declare(strict_types=1);

/**
 * /admin/rekap-bulanan: monthly recap of registered visits per poli.
 *
 * The month comes from ?bulan=Y-m and falls back to the current Asia/Jakarta
 * month. Weeks are 7 day chunks of the month and cancelled visits are excluded.
 * The PDF export lives in admin.rekap-bulanan.unduh.php.
 */
return [
    '_middleware' => ['role:admin'],

    'GET' => static function (Request $request): string {
        $bounds = report_month_bounds(request_input('bulan'));
        $snapshot = report_build_snapshot($bounds['start'], $bounds['end'], null);
        $weeks = is_array($snapshot['weeks'] ?? null) ? $snapshot['weeks'] : [];

        return view('admin.rekap-bulanan', [
            'title' => 'Laporan Rekap Bulanan',
            'role' => (string) $request->attribute('role'),
            'token' => csrf_token(),
            'minggu' => $weeks,
            'bulan' => $bounds['label'],
        ]);
    },
];
