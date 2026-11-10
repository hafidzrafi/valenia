<?php

declare(strict_types=1);

/**
 * /admin/profil: profil pengguna yang sedang login (VALENIA-44).
 *
 * Menampilkan data akun dan dialog ganti password. Ganti password memakai
 * change_password() dari lib/auth.php. Ubah profil masih tampilan saja karena
 * belum ada fungsi penyimpanannya.
 */
$render = static function (Request $request, ?string $error = null): string {
    $user = current_user() ?? [];

    $stmt = db()->prepare('SELECT email FROM users WHERE id = :id');
    $stmt->execute(['id' => (string) $request->attribute('user_id')]);
    $email = $stmt->fetchColumn();

    return view('admin.profil', [
        'title' => 'Profil Pengguna',
        'role' => (string) $request->attribute('role'),
        'token' => csrf_token(),
        'namaLengkap' => (string) ($user['full_name'] ?? ''),
        'username' => (string) $request->attribute('user'),
        'email' => is_string($email) ? $email : '',
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

        staff_flash('Password berhasil diubah. Gunakan password baru saat login berikutnya.');

        return redirect('/admin/profil');
    },
];