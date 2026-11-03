<?php

declare(strict_types=1);

/**
 * /admin/kehadiran: toggle a doctor's attendance for today.
 *
 * Attendance is one row per doctor per day (doctor_attendance primary key), so
 * the action is an upsert rather than an insert.
 */
return [
    '_middleware' => ['role:admin,perawat'],

    'POST' => static function (Request $request): Response {
        $doctorId = (string) (request_input('doctor_id') ?? '');
        $present = '1' === (string) (request_input('present') ?? '0');

        if ('' === $doctorId) {
            staff_flash('Tenaga medis tidak dikenal.');

            return redirect('/admin');
        }

        $ok = set_staff_attendance($doctorId, $present, (string) $request->attribute('user_id'));
        staff_flash($ok ? 'Kehadiran diperbarui.' : 'Tenaga medis tidak ditemukan.');

        return redirect('/admin');
    },
];
