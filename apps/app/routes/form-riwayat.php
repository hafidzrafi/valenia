<?php

declare(strict_types=1);

return [
    'GET' => static function (Request $request): string {
        $old = $_SESSION['portal_old'] ?? [];
        $error = $_SESSION['portal_error'] ?? null;
        unset($_SESSION['portal_old'], $_SESSION['portal_error']);

        return view('form-riwayat', [
            'title' => 'Masuk Portal',
            'old' => is_array($old) ? $old : [],
            'error' => is_string($error) ? $error : null,
        ]);
    },
];
