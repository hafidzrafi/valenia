<?php
declare(strict_types=1);

return [
    'GET' => static function (Request $request): string {
        $heroform = $_SESSION['heroform'] ?? 'Default';

        return render(app_path('app/views/form.php'), [
            'title'    => 'Form Pendaftaran',
            'heroform' => $heroform,
        ]);
    },

    'POST' => static function (Request $request): Response {
        $heroform = request_input('heroform', 'Default');
        $_SESSION['heroform'] = $heroform;

        return redirect('/form');
    },
];