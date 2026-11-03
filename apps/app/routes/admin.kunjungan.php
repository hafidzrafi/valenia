<?php

declare(strict_types=1);

/**
 * /admin/kunjungan: read-only detail of one visit from today.
 *
 * Shows the stored examination (diagnosis, treatment, note) alongside the
 * patient and queue context. Used as the landing page after saving, and as a
 * history view for completed visits.
 */
return [
    '_middleware' => ['role:admin,perawat'],

    'GET' => static function (Request $request): Response|string {
        $visit = staff_visit_for_input((string) (request_input('visit') ?? ''));

        if (null === $visit) {
            staff_flash('Kunjungan tidak ditemukan.');

            return redirect('/admin');
        }

        $exam = null;

        $stmt = db()->prepare(
            'SELECT diagnosis, treatment, note, created_at FROM examinations WHERE visit_id = :visit'
        );
        $stmt->execute(['visit' => $visit['id']]);
        $found = $stmt->fetch();

        if (is_array($found)) {
            $exam = $found;
        }

        return view('admin.kunjungan', [
            'title' => 'Detail Kunjungan',
            'visit' => $visit,
            'exam' => $exam,
            'role' => (string) $request->attribute('role'),
            'token' => csrf_token(),
            'flash' => take_staff_flash(),
        ]);
    },
];
