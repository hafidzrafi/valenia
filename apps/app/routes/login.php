<?php

declare(strict_types=1);

/**
 * /login — username + password login (FR-AUTH-01).
 *
 * Success creates a database session, regenerates the token, and redirects by
 * role (or to /ganti-sandi when a password change is forced). Failure renders
 * the form again with a generic message (FR-AUTH-01, UAT 2).
 */
return [
    'GET' => static function (): string|Response {
        start_app_session();

        $raw = $_SESSION['user_id'] ?? null;

        if (is_string($raw) && $raw !== '' && ($user = load_session_user($raw)) !== null) {
            return redirect(target_for_role($user['role']));
        }

        return view('login', ['token' => csrf_token()]);
    },

    'POST' => static function (): string|Response {
        $username = request_input('username') ?? '';
        $password = isset($_POST['password']) && is_string($_POST['password'])
            ? $_POST['password']
            : '';

        try {
            $result = authenticate($username, $password);
        } catch (Throwable $exception) {
            app_log('error', 'Login gagal: ' . $exception->getMessage());

            return new Response(view('login', [
                'token' => csrf_token(),
                'error' => 'Terjadi kesalahan. Coba lagi.',
            ]), 500);
        }

        if (!$result['ok']) {
            return view('login', [
                'token' => csrf_token(),
                'username' => $username,
                'error' => $result['locked']
                    ? 'Akun dikunci sementara. Coba lagi nanti.'
                    : 'Nama pengguna atau kata sandi salah.',
            ]);
        }

        login_user($result['user']);

        return $result['must_change']
            ? redirect('/ganti-sandi')
            : redirect(target_for_role($result['user']['role']));
    },
];
