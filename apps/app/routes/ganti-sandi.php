<?php

declare(strict_types=1);

/**
 * /ganti-sandi — required password change (FR-AUTH-08).
 *
 * Protected by the global `auth` guard; the global `password` gate exempts this
 * path so it does not loop.
 */
return [
    'GET' => static fn (): string => view('ganti-sandi', ['token' => csrf_token()]),

    'POST' => static function (Request $request): string|Response {
        $post = static fn (string $key): string => isset($_POST[$key]) && is_string($_POST[$key])
            ? $_POST[$key]
            : '';

        $current = $post('kata_sandi_saat_ini');
        $new = $post('kata_sandi_baru');
        $confirm = $post('konfirmasi_kata_sandi');

        if ($new !== $confirm) {
            return new Response(view('ganti-sandi', [
                'token' => csrf_token(),
                'error' => 'Konfirmasi kata sandi tidak cocok.',
            ]), 422);
        }

        try {
            $result = change_password((string) $request->attribute('user_id'), $current, $new);
        } catch (Throwable $exception) {
            app_log('error', 'Ganti sandi gagal: ' . $exception->getMessage());

            return new Response(view('ganti-sandi', [
                'token' => csrf_token(),
                'error' => 'Terjadi kesalahan. Coba lagi.',
            ]), 500);
        }

        if (!$result['ok']) {
            return new Response(view('ganti-sandi', [
                'token' => csrf_token(),
                'error' => $result['error'],
            ]), 422);
        }

        $user = load_session_user((string) $request->attribute('user_id'));

        return redirect(target_for_role($user === null ? 'perawat' : $user['role']));
    },
];
