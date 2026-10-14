<?php

declare(strict_types=1);

/**
 * /test/login — development entry point to the login page.
 *
 * Exists only so the login flow can be reached without touching the landing
 * page design. Remove before hardening (Minggu 8).
 */
return [
    'GET' => static fn (): string => view('test.login', ['title' => 'Uji Masuk']),
];
