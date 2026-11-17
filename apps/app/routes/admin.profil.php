<?php

declare(strict_types=1);

/**
 * /admin/profil: profil pengguna yang sedang login (VALENIA-44).
 *
 * Menampilkan data akun, aktivitas terbaru, dan dialog ganti password. Ganti
 * password memakai change_password() dari lib/auth.php. Ubah profil dan daftar
 * aktivitas masih tampilan saja karena belum ada fungsi dari backend.
 */
$render = static function (Request $request, ?string $error = null): string {
    $user = current_user() ?? [];

    $stmt = db()->prepare(
        'SELECT u.email, u.is_active, u.created_at, c.last_login_at
         FROM users u
         LEFT JOIN credentials c ON c.user_id = u.id AND c.provider = \'credential\'
         WHERE u.id = :id'
    );
    $stmt->execute(['id' => (string) $request->attribute('user_id')]);
    $akun = $stmt->fetch();
    $akun = is_array($akun) ? $akun : [];

    $zona = new DateTimeZone('Asia/Jakarta');
    $bulan = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
        7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];
    $keWaktu = static fn (mixed $nilai): ?DateTimeImmutable => is_string($nilai) && '' !== $nilai
        ? (new DateTimeImmutable($nilai))->setTimezone($zona)
        : null;
    $tanggalPanjang = static fn (DateTimeImmutable $waktu): string => $waktu->format('j').' '.$bulan[(int) $waktu->format('n')].' '.$waktu->format('Y');

    $bergabung = $keWaktu($akun['created_at'] ?? null);
    $login = $keWaktu($akun['last_login_at'] ?? null);
    $hariIni = (new DateTimeImmutable('now', $zona))->format('Y-m-d');
    $kemarin = (new DateTimeImmutable('yesterday', $zona))->format('Y-m-d');

    $loginTerakhir = '-';

    if (null !== $login) {
        $hari = $login->format('Y-m-d');
        $loginTerakhir = match (true) {
            $hari === $hariIni => 'Hari ini, '.$login->format('H:i'),
            $hari === $kemarin => 'Kemarin, '.$login->format('H:i'),
            default => $tanggalPanjang($login),
        };
    }

    // TODO(backend): ganti dengan aktivitas asli milik akun ini.
    $aktivitas = [
        ['ikon' => 'ph-sign-in', 'judul' => 'Login ke sistem', 'detail' => 'Akses dari perangkat Chrome, Windows', 'waktu' => 'Hari ini, 08:15'],
        ['ikon' => 'ph-user-plus', 'judul' => 'Menambahkan antrian pasien baru', 'detail' => 'Antrian Poli Umum, nomor A-012', 'waktu' => 'Hari ini, 08:32'],
        ['ikon' => 'ph-calendar-dots', 'judul' => 'Mengubah jadwal tenaga medis', 'detail' => 'Jadwal praktik Poli Umum', 'waktu' => 'Kemarin, 15:40'],
        ['ikon' => 'ph-database', 'judul' => 'Memperbarui data master', 'detail' => 'Data dokter', 'waktu' => 'Kemarin, 10:05'],
        ['ikon' => 'ph-chart-bar', 'judul' => 'Mengunduh rekap bulanan', 'detail' => 'Rekap bulan Oktober', 'waktu' => '14 Nov, 09:20'],
    ];

    return view('admin.profil', [
        'title' => 'Profil Pengguna',
        'role' => (string) $request->attribute('role'),
        'token' => csrf_token(),
        'namaLengkap' => (string) ($user['full_name'] ?? ''),
        'username' => (string) $request->attribute('user'),
        'email' => is_string($akun['email'] ?? null) ? $akun['email'] : '',
        'aktif' => (bool) ($akun['is_active'] ?? true),
        'bergabung' => null === $bergabung ? '-' : $tanggalPanjang($bergabung),
        'loginTerakhir' => $loginTerakhir,
        'aktivitas' => $aktivitas,
        'error' => $error,
        'flash' => take_staff_flash(),
    ]);
};

return [
    '_middleware' => ['role:admin'],

    'GET' => static fn (Request $request): string => $render($request),

    'POST' => static function (Request $request) use ($render): Response {
        $post = static fn (string $key): string => isset($_POST[$key]) && is_string($_POST[$key])
            ? $_POST[$key]
            : '';

        $current = $post('kata_sandi_saat_ini');
        $new = $post('kata_sandi_baru');
        $confirm = $post('konfirmasi_kata_sandi');

        if ($new !== $confirm) {
            return new Response($render($request, 'Konfirmasi password baru tidak cocok.'), 422);
        }

        try {
            $result = change_password((string) $request->attribute('user_id'), $current, $new);
        } catch (Throwable $exception) {
            app_log('error', 'Ganti password dari profil gagal: '.$exception->getMessage());

            return new Response($render($request, 'Terjadi kesalahan. Coba lagi.'), 500);
        }

        if (!$result['ok']) {
            return new Response($render($request, (string) $result['error']), 422);
        }

        // Tanda "|" memisahkan judul dan keterangan pemberitahuan di view profil.
        staff_flash('Password berhasil diubah|Gunakan password baru saat login berikutnya.');

        return redirect('/admin/profil');
    },
];