<?php

declare(strict_types=1);

return [
    '_middleware' => ['role:admin,perawat'],

    'GET' => static function (Request $request): string {
        $boards = queue_boards();

        if ('true' === ($_SERVER['HTTP_HX_REQUEST'] ?? '')) {
            return render(app_path('app/views/partials/queue-boards.php'), [
                'boards' => $boards,
            ]);
        }

        $flash = $_SESSION['admin_flash'] ?? null;
        unset($_SESSION['admin_flash']);

        return view('admin.antrean', [
            'title' => 'Antrean',
            'boards' => $boards,
            'flash' => is_string($flash) ? $flash : null,
        ]);
    },

    'POST' => static function (Request $request): Response {
        $serviceId = (int) (request_input('service_id', '0') ?? '0');
        $result = advance_queue($serviceId, (string) $request->attribute('user_id'));

        $_SESSION['admin_flash'] = $result['advanced']
            ? 'Memanggil '.$result['queue_code'].'.'
            : 'Tidak ada antrean menunggu.';

        return redirect('/admin/antrean');
    },
];
