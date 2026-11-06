<?php

declare(strict_types=1);

/**
 * /admin/antrean: per poli live boards, the today table, and the staff actions.
 *
 * Rendered inside the shared staff shell. The htmx polling branch returns only
 * the boards partial, so the swap stays cheap and the modals above it are never
 * replaced mid interaction.
 */
return [
    '_middleware' => ['role:admin,perawat'],

    'GET' => static function (Request $request): string {
        $boards = queue_boards();
        $visits = staff_today_visits();

        if ('true' === ($_SERVER['HTTP_HX_REQUEST'] ?? '')) {
            return render(app_path('app/views/partials/queue-boards.php'), [
                'boards' => $boards,
            ]);
        }

        return view('admin.antrean', [
            'title' => 'Antrean',
            'boards' => $boards,
            'visits' => $visits,
            'role' => (string) $request->attribute('role'),
            'token' => csrf_token(),
            'flash' => take_staff_flash(),
        ]);
    },

    'POST' => static function (Request $request): Response {
        $actorId = (string) $request->attribute('user_id');
        $action = (string) (request_input('action') ?? 'advance');

        if ('serve' === $action || 'cancel' === $action) {
            $visitId = (string) (request_input('visit_id') ?? '');

            $result = 'serve' === $action
                ? serve_staff_visit($visitId, $actorId)
                : cancel_staff_visit($visitId, $actorId);

            staff_flash($result['ok']
                ? ('serve' === $action
                    ? 'Memanggil '.$result['queue_code'].'.'
                    : 'Antrean '.$result['queue_code'].' dibatalkan.')
                : (string) $result['error']);

            return redirect('/admin/antrean');
        }

        $serviceId = (int) (request_input('service_id', '0') ?? '0');
        $advanced = advance_queue($serviceId, $actorId);

        staff_flash($advanced['advanced']
            ? 'Memanggil '.$advanced['queue_code'].'.'
            : 'Tidak ada antrean menunggu.');

        return redirect('/admin/antrean');
    },
];
