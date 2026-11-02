<?php

declare(strict_types=1);

/**
 * /admin/selesai: close a visit that is currently being served.
 *
 * Delegates to complete_staff_visit(), which re-checks the status inside a
 * transaction and writes the examination row through record_examination().
 */
return [
    '_middleware' => ['role:admin,perawat'],

    'POST' => static function (Request $request): Response {
        $visitId = (string) (request_input('visit_id') ?? '');

        if ('' === $visitId) {
            staff_flash('Antrean tidak dikenal.');

            return redirect('/admin');
        }

        $result = complete_staff_visit($visitId, (string) $request->attribute('user_id'));
        staff_flash($result['ok'] ? 'Antrean diselesaikan.' : (string) $result['error']);

        return redirect('/admin');
    },
];
