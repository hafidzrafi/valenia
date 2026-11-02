<?php

declare(strict_types=1);

return [
    'GET' => static function (Request $request): Response|string {
        $key = strtoupper((string) ($_SESSION['heroform'] ?? ''));
        $service = resolve_service($key);

        if (!$service['ok']) {
            return redirect('/');
        }

        $old = $_SESSION['registration_old'] ?? [];
        $error = $_SESSION['registration_error'] ?? null;
        unset($_SESSION['registration_old'], $_SESSION['registration_error']);

        return render(app_path('app/views/form.php'), [
            'title' => 'Form '.$service['service']['name'],
            'heroform' => $key,
            'service' => $service['service'],
            'old' => is_array($old) ? $old : [],
            'error' => is_string($error) ? $error : null,
        ]);
    },

    'POST' => static function (Request $request): Response {
        $key = strtoupper((string) request_input('heroform', ''));
        $service = resolve_service($key);

        if (!$service['ok']) {
            return new Response(render(app_path('app/views/home.php'), [
                'title' => 'VALENIA',
                'error' => $service['error'],
            ]), 422);
        }

        $_SESSION['heroform'] = $key;

        return redirect('/form');
    },
];
