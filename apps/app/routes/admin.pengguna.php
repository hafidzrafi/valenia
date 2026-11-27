<?php

declare(strict_types=1);

/**
 * /admin/pengguna - account management (FR-AUTH-04).
 *
 * Lists, creates, edits, enables/disables accounts and resets passwords (which
 * forces a change on next login and ends every existing session). Search, role
 * filter and paging run in PHP over the whole account list: staff accounts
 * number in the dozens, so filtering in SQL would add a query per keystroke for
 * no gain. Every action redirects with a flash; validation failures render 422
 * in place.
 */
$render = static function (Request $request, ?string $flash = null, ?string $error = null, array $old = []): array {
    $semua = list_users();
    $me = (string) $request->attribute('user_id');

    $q = trim((string) (request_input('q') ?? ''));
    $peran = (string) (request_input('peran') ?? 'semua');

    if (!in_array($peran, ['admin', 'perawat'], true)) {
        $peran = 'semua';
    }

    $cocok = array_values(array_filter($semua, static function (array $row) use ($q, $peran): bool {
        if ('semua' !== $peran && (string) $row['role'] !== $peran) {
            return false;
        }

        if ('' === $q) {
            return true;
        }

        $cari = mb_strtolower($q);

        foreach (['full_name', 'username', 'email'] as $kolom) {
            $nilai = $row[$kolom] ?? null;

            if (is_string($nilai) && str_contains(mb_strtolower($nilai), $cari)) {
                return true;
            }
        }

        return false;
    }));

    $perHalaman = 8;
    $halamanTotal = max(1, (int) ceil(count($cocok) / $perHalaman));
    $halaman = min(max(1, (int) (request_input('halaman') ?? 1)), $halamanTotal);

    // The temporary password is shown once, right after the reset that made it.
    $sementara = $_SESSION['akun_temp_password'] ?? null;
    unset($_SESSION['akun_temp_password']);

    $aktif = count(array_filter($semua, static fn (array $row): bool => (bool) $row['is_active']));

    // A rejected submission reopens its dialog with the entered values, so the
    // admin corrects one field instead of retyping the whole form.
    $form = null;

    if ([] !== $old) {
        $aksi = (string) ($old['aksi'] ?? '');
        $id = (string) ($old['id'] ?? '');
        $baris = null;

        foreach ($semua as $row) {
            if ((string) $row['id'] === $id) {
                $baris = $row;

                break;
            }
        }

        if ('ubah' === $aksi && null !== $baris) {
            $form = [
                'aksi' => 'ubah',
                'id' => $id,
                'nama' => (string) ($old['full_name'] ?? ''),
                'username' => (string) ($old['username'] ?? ''),
                'email' => (string) ($old['email'] ?? ''),
                'peran' => (string) ($old['role'] ?? $baris['role']),
                'aktif' => (bool) $baris['is_active'],
                'sendiri' => $id === $me,
            ];
        } elseif ('tambah' === $aksi) {
            $form = [
                'aksi' => 'tambah',
                'id' => '',
                'nama' => (string) ($old['full_name'] ?? ''),
                'username' => (string) ($old['username'] ?? ''),
                'email' => (string) ($old['email'] ?? ''),
                'peran' => (string) ($old['role'] ?? 'perawat'),
                'aktif' => true,
                'sendiri' => false,
            ];
        }
    }

    return [
        'title' => 'Kelola Akun',
        'role' => (string) $request->attribute('role'),
        'token' => csrf_token(),
        'users' => array_slice($cocok, ($halaman - 1) * $perHalaman, $perHalaman),
        'total' => count($semua),
        'aktif' => $aktif,
        'nonaktif' => count($semua) - $aktif,
        'cocok' => count($cocok),
        'q' => $q,
        'peran' => $peran,
        'halaman' => $halaman,
        'halamanTotal' => $halamanTotal,
        'perHalaman' => $perHalaman,
        'me' => $me,
        'flash' => $flash,
        'error' => $error,
        'tempPassword' => is_array($sementara) ? $sementara : null,
        'form' => $form,
    ];
};

return [
    '_middleware' => ['role:admin'],

    'GET' => static function (Request $request) use ($render): string {
        return view('admin.pengguna', $render($request, take_staff_flash()));
    },

    'POST' => static function (Request $request) use ($render): Response {
        $me = (string) $request->attribute('user_id');
        $aksi = (string) (request_input('aksi') ?? '');
        $id = (string) (request_input('id') ?? '');

        $fail = static fn (string $error): Response => new Response(
            view('admin.pengguna', $render($request, null, $error, [
                'aksi' => $aksi,
                'id' => $id,
                'full_name' => (string) (request_input('full_name') ?? ''),
                'username' => (string) (request_input('username') ?? ''),
                'email' => (string) (request_input('email') ?? ''),
                'role' => (string) (request_input('role') ?? ''),
            ])),
            422,
        );

        $done = static function (string $notice): Response {
            staff_flash($notice);

            // Returning to the same filter keeps the row the admin just acted on
            // in view instead of dumping them on page one of everything.
            $kembali = [];

            $q = trim((string) (request_input('q') ?? ''));

            if ('' !== $q) {
                $kembali['q'] = $q;
            }

            $peran = (string) (request_input('peran') ?? '');

            if (in_array($peran, ['admin', 'perawat'], true)) {
                $kembali['peran'] = $peran;
            }

            $halaman = (int) (request_input('halaman') ?? 1);

            if ($halaman > 1) {
                $kembali['halaman'] = $halaman;
            }

            return redirect('/admin/pengguna'.([] === $kembali ? '' : '?'.http_build_query($kembali)));
        };

        try {
            switch ($aksi) {
                case 'tambah':
                    $password = (string) (request_input('password') ?? '');

                    if ($password !== (string) (request_input('password_confirm') ?? '')) {
                        return $fail('Kata sandi dan konfirmasi tidak sama.');
                    }

                    $nama = (string) (request_input('full_name') ?? '');
                    $result = create_user(
                        (string) (request_input('username') ?? ''),
                        $nama,
                        (string) (request_input('role') ?? ''),
                        $password,
                        $me,
                        (string) (request_input('email') ?? ''),
                    );

                    return $result['ok']
                        ? $done('Akun '.$nama.' berhasil ditambahkan dan kini dapat login.')
                        : $fail((string) $result['error']);

                case 'ubah':
                    $nama = (string) (request_input('full_name') ?? '');
                    $result = update_user(
                        $id,
                        (string) (request_input('username') ?? ''),
                        $nama,
                        (string) (request_input('role') ?? ''),
                        (string) (request_input('email') ?? ''),
                        $me,
                    );

                    return $result['ok']
                        ? $done('Perubahan akun '.$nama.' disimpan.')
                        : $fail((string) $result['error']);

                case 'nonaktif':
                case 'aktif':
                    if ('' === $id || $id === $me) {
                        return $fail('Tidak dapat mengubah status akun sendiri.');
                    }

                    $result = set_user_active($id, 'aktif' === $aksi, $me);

                    return $result['ok']
                        ? $done('aktif' === $aksi
                            ? 'Akun diaktifkan kembali dan dapat login.'
                            : 'Akun dinonaktifkan dan tidak dapat login sampai diaktifkan kembali.')
                        : $fail((string) $result['error']);

                case 'reset':
                    if ('' === $id || $id === $me) {
                        return $fail('Gunakan halaman Ganti Sandi untuk akun sendiri.');
                    }

                    $nama = '';

                    foreach (list_users() as $row) {
                        if ((string) $row['id'] === $id) {
                            $nama = (string) $row['full_name'];

                            break;
                        }
                    }

                    $sementara = generate_temporary_password();
                    $result = reset_user_password($id, $sementara, $me);

                    if (!$result['ok']) {
                        return $fail((string) $result['error']);
                    }

                    $_SESSION['akun_temp_password'] = ['nama' => $nama, 'password' => $sementara];

                    return $done('Kata sandi sementara dibuat. Salin dan berikan ke pemilik akun sekarang.');

                default:
                    return $fail('Aksi tidak dikenal.');
            }
        } catch (Throwable $exception) {
            app_log('error', 'Kelola pengguna gagal: '.$exception->getMessage());

            return $fail('Terjadi kesalahan. Coba lagi.');
        }
    },
];
