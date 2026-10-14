<?php

declare(strict_types=1);

/**
 * /admin/sesi — active session management (FR-AUTH-06).
 *
 * Lists active sessions and revokes any of them; a revoked session ends
 * immediately (its next request fails validation). Revoking your own current
 * session logs you out.
 */
return [
    '_middleware' => ['auth', 'role:admin', 'password'],

    'GET' => static function (): string {
        $flash = $_SESSION['_flash'] ?? null;
        unset($_SESSION['_flash']);

        $current = null;

        if (session_id() !== '') {
            $sel = db()->prepare('SELECT id FROM sessions WHERE token_hash = :hash');
            $sel->execute(['hash' => hash('sha256', session_id())]);
            $found = $sel->fetch();
            $current = is_array($found) ? (string) $found['id'] : null;
        }

        return view('admin.sesi', [
            'sessions' => list_sessions(),
            'current' => $current,
            'token' => csrf_token(),
            'flash' => $flash,
        ]);
    },

    'POST' => static function (Request $request): string|Response {
        $id = request_input('id') ?? '';

        if ($id === '') {
            return new Response(view('admin.sesi', [
                'sessions' => list_sessions(),
                'current' => null,
                'token' => csrf_token(),
                'error' => 'Sesi tidak dikenal.',
            ]), 422);
        }

        $me = (string) $request->attribute('user_id');

        try {
            $current = null;

            if (session_id() !== '') {
                $sel = db()->prepare('SELECT id FROM sessions WHERE token_hash = :hash');
                $sel->execute(['hash' => hash('sha256', session_id())]);
                $found = $sel->fetch();
                $current = is_array($found) ? (string) $found['id'] : null;
            }

            if ($current !== null && $id === $current) {
                logout_user();

                return redirect('/login');
            }

            if (!revoke_session($id, $me)) {
                return new Response(view('admin.sesi', [
                    'sessions' => list_sessions(),
                    'current' => $current,
                    'token' => csrf_token(),
                    'error' => 'Sesi tidak ditemukan.',
                ]), 422);
            }

            $_SESSION['_flash'] = 'Sesi dicabut.';

            return redirect('/admin/sesi');
        } catch (Throwable $exception) {
            app_log('error', 'Cabut sesi gagal: ' . $exception->getMessage());

            return new Response(view('admin.sesi', [
                'sessions' => list_sessions(),
                'current' => null,
                'token' => csrf_token(),
                'error' => 'Terjadi kesalahan. Coba lagi.',
            ]), 500);
        }
    },
];
