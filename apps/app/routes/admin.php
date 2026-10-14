<?php

declare(strict_types=1);

/**
 * /admin — staff dashboard (FR-AUTH-03/04/06 entry point).
 */
return [
    '_middleware' => ['auth', 'role:admin', 'password'],

    'GET' => static fn (Request $request): string => view('admin', [
        'user' => (string) $request->attribute('user'),
        'role' => (string) $request->attribute('role'),
        'token' => csrf_token(),
    ]),
];
