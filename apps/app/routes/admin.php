<?php

declare(strict_types=1);

/**
 * /admin - demonstrator guarded by auth + role:admin.
 */
return [
    '_middleware' => ['auth', 'role:admin'],

    'GET' => static fn (Request $request): string => view('admin', [
        'user' => (string) $request->attribute('user'),
    ]),
];
