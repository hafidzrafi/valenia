<?php

declare(strict_types=1);

/**
 * /admin/antrean: per poli live boards with the staff "serve next" action.
 *
 * Rendered inside the shared staff shell. The htmx polling branch still returns
 * only the boards partial so the swap stays cheap.
 */
return [
    '_middleware' => ['role:admin,perawat'],

    'GET' => static function (Request $request): string {
        $boards = queue_boards();

        if ('true' === ($_SERVER['HTTP_HX_REQUEST'] ?? '')) {
            return render(app_path('app/views/partials/queue-boards.php'), [
                'boards' => $boards,
            ]);
        }

        return view('admin.antrean', [
            'title' => 'Antrean',
            'boards' => $boards,
            'role' => (string) $request->attribute('role'),
            'token' => csrf_token(),
            'flash' => take_staff_flash(),
        ]);
    },

    'POST' => static function (Request $request): Response {
        $serviceId = (int) (request_input('service_id', '0') ?? '0');
        $result = advance_queue($serviceId, (string) $request->attribute('user_id'));

        staff_flash($result['advanced']
            ? 'Memanggil '.$result['queue_code'].'.'
            : 'Tidak ada antrean menunggu.');

        return redirect('/admin/antrean');
    },
];
