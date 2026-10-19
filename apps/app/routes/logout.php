<?php

declare(strict_types=1);

/**
 * /logout — destroys the database session and returns to /login (FR-AUTH-02).
 * POST only (state-changing, CSRF-protected by the global guard).
 */
return [
    'POST' => static function (): Response {
        logout_user();

        return redirect('/login');
    },
];
