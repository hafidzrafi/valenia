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

        $diagnosis = (string) (request_input('diagnosis') ?? '');
        $treatment = (string) (request_input('treatment') ?? '');
        $note = request_input('note');

        $_SESSION['exam_input'] = [
            'diagnosis' => $diagnosis,
            'treatment' => $treatment,
            'note' => is_string($note) ? $note : '',
        ];

        $result = complete_staff_visit(
            $visitId,
            (string) $request->attribute('user_id'),
            $diagnosis,
            $treatment,
            is_string($note) ? $note : null
        );

        if (!$result['ok']) {
            // Missing fields go back to the input page with values retained.
            // Any other failure (unknown visit, wrong day, wrong status) goes
            // to the dashboard, since the input page only serves visits that
            // are being served right now.
            if ('' === trim($diagnosis) || '' === trim($treatment)) {
                $_SESSION['exam_error'] = (string) $result['error'];

                return redirect('/admin/periksa?visit='.rawurlencode($visitId));
            }

            unset($_SESSION['exam_input']);
            staff_flash((string) $result['error']);

            return redirect('/admin');
        }

        unset($_SESSION['exam_input']);
        staff_flash('Hasil tersimpan.');

        return redirect('/admin/kunjungan?visit='.rawurlencode($visitId));
    },
];
