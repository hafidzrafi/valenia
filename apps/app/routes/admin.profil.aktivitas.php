<?php

declare(strict_types=1);

/**
 * /admin/profil/aktivitas: riwayat aktivitas akun yang sedang login (VALENIA-44).
 *
 * Tampilan saja. Data masih dummy sampai backend menyediakan fungsi riwayat
 * aktivitas per akun (dari auth_events dan audit_logs).
 */
return [
    '_middleware' => ['role:admin'],

    'GET' => static function (Request $request): string {
        // TODO(backend): ganti dengan riwayat aktivitas asli milik akun ini.
        $kelompok = [
            [
                'label' => 'Hari ini',
                'baris' => [
                    ['ikon' => 'ph-sign-in', 'judul' => 'Login ke sistem', 'detail' => 'Akses dari perangkat Chrome, Windows', 'waktu' => 'Hari ini, 08:15'],
                    ['ikon' => 'ph-user-plus', 'judul' => 'Menambahkan antrian pasien baru', 'detail' => 'Antrian Poli Umum, nomor A-012', 'waktu' => 'Hari ini, 08:32'],
                ],
            ],
            [
                'label' => 'Kemarin',
                'baris' => [
                    ['ikon' => 'ph-calendar-dots', 'judul' => 'Mengubah jadwal tenaga medis', 'detail' => 'Jadwal praktik Poli Umum', 'waktu' => 'Kemarin, 15:40'],
                    ['ikon' => 'ph-database', 'judul' => 'Memperbarui data master', 'detail' => 'Data dokter', 'waktu' => 'Kemarin, 10:05'],
                ],
            ],
            [
                'label' => '14 November 2026',
                'baris' => [
                    ['ikon' => 'ph-chart-bar', 'judul' => 'Mengunduh rekap bulanan', 'detail' => 'Rekap bulan Oktober', 'waktu' => '14 Nov, 09:20'],
                    ['ikon' => 'ph-lock-key', 'judul' => 'Mengganti password', 'detail' => 'Password akun berhasil diperbarui', 'waktu' => '14 Nov, 08:02'],
                    ['ikon' => 'ph-sign-out', 'judul' => 'Logout dari sistem', 'detail' => 'Sesi berakhir dari perangkat Chrome, Windows', 'waktu' => '14 Nov, 07:40'],
                ],
            ],
        ];

        return view('admin.profil.aktivitas', [
            'title' => 'Riwayat Aktivitas',
            'role' => (string) $request->attribute('role'),
            'token' => csrf_token(),
            'kelompok' => $kelompok,
        ]);
    },
];