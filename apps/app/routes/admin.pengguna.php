<?php

declare(strict_types=1);

/**
 * /admin/pengguna — account management (FR-AUTH-04).
 *
 * List users, create accounts, enable/disable, and reset passwords (which
 * forces a change on next login). Success actions use POST → redirect → GET
 * with a one-time flash notice; validation failures render 422 in place.
 */
return [
    '_middleware' => ['role:admin'],

    'GET' => static function (): string {
        $flash = $_SESSION['_flash'] ?? null;
        unset($_SESSION['_flash']);

        return view('admin.pengguna', [
            'users' => list_users(),
            'token' => csrf_token(),
            'flash' => $flash,
        ]);
    },

    'POST' => static function (Request $request): string|Response {
        $me = (string) $request->attribute('user_id');
        $aksi = request_input('aksi') ?? '';
        $id = request_input('id') ?? '';

        $fail = static fn (string $error): Response => new Response(view('admin.pengguna', [
            'users' => list_users(),
            'token' => csrf_token(),
            'error' => $error,
        ]), 422);

        $done = static function (string $notice): Response {
            $_SESSION['_flash'] = $notice;

            return redirect('/admin/pengguna');
        };

        try {
            if ($aksi === 'tambah') {
                $password = isset($_POST['password']) && is_string($_POST['password']) ? $_POST['password'] : '';
                $result = create_user(
                    request_input('username') ?? '',
                    request_input('full_name') ?? '',
                    request_input('role') ?? '',
                    $password,
                    $me,
                );

                return $result['ok']
                    ? $done('Akun dibuat. Pengguna wajib mengganti kata sandi saat login pertama.')
                    : $fail((string) $result['error']);
            }

            if ($aksi === 'nonaktif' || $aksi === 'aktif') {
                if ($id === '' || $id === $me) {
                    return $fail('Tidak dapat mengubah akun sendiri.');
                }

                return set_user_active($id, $aksi === 'aktif', $me)
                    ? $done($aksi === 'aktif' ? 'Akun diaktifkan.' : 'Akun dinonaktifkan.')
                    : $fail('Akun tidak ditemukan.');
            }

            if ($aksi === 'reset') {
                if ($id === '' || $id === $me) {
                    return $fail('Gunakan halaman Ganti Sandi untuk akun sendiri.');
                }

                $temp = reset_user_password($id, $me);

                return $done('Kata sandi direset. Sandi sementara: ' . $temp);
            }

            return $fail('Aksi tidak dikenal.');
        } catch (Throwable $exception) {
            app_log('error', 'Kelola pengguna gagal: ' . $exception->getMessage());

            return $fail('Terjadi kesalahan. Coba lagi.');
        }
    },
];
