<?php

declare(strict_types=1);

/**
 * /admin/rekap-bulanan: laporan rekap kunjungan per minggu (VALENIA-42).
 *
 * Tampilan saja. Data masih dummy sampai query laporan dari backend siap.
 */
return [
    '_middleware' => ['role:admin'],

    'GET' => static function (Request $request): string {
        // TODO(backend): ganti dengan hasil query kunjungan per minggu.
        $minggu = [
            ['label' => 'Minggu 1', 'umum' => 18, 'gigi' => 0, 'konseling' => 6],
            ['label' => 'Minggu 2', 'umum' => 25, 'gigi' => 0, 'konseling' => 9],
            ['label' => 'Minggu 3', 'umum' => 14, 'gigi' => 0, 'konseling' => 5],
        ];

        return view('admin.rekap-bulanan', [
            'title' => 'Laporan Rekap Bulanan',
            'role' => (string) $request->attribute('role'),
            'token' => csrf_token(),
            'minggu' => $minggu,
        ]);
    },
];