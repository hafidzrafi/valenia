<?php

declare(strict_types=1);

/**
 * /admin: staff dashboard entry point (FR-AUTH-03/04/06).
 *
 * Both admin and perawat land here after login. Account and session management
 * stay admin only (see /admin/pengguna and /admin/sesi).
 */
return [
    '_middleware' => ['role:admin,perawat'],

    'GET' => static fn (Request $request): string => view('admin', [
        'title' => 'Dashboard',
        'user' => (string) $request->attribute('user'),
        'role' => (string) $request->attribute('role'),
        'token' => csrf_token(),
        'flash' => take_staff_flash(),
    ]),
];
