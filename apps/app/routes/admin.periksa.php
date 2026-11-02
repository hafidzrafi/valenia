<?php

declare(strict_types=1);

/**
 * /admin/periksa: examination input page for one visit being served today.
 *
 * Renders the Data Pasien card plus the Hasil Pemeriksaan form. Any other state
 * (unknown id, another day, not yet called) redirects to the dashboard with a
 * flash instead of rendering a half empty form.
 */
return [
    '_middleware' => ['role:admin,perawat'],

    'GET' => static function (Request $request): Response|string {
        $visit = staff_visit_for_input((string) (request_input('visit') ?? ''));

        if (null === $visit && '' !== (string) (request_input('visit') ?? '')) {
            staff_flash('Kunjungan tidak ditemukan.');

            return redirect('/admin');
        }

        if (null === $visit) {
            $boards = queue_boards();
            $first = null;

            foreach ($boards as $board) {
                if (null !== $board['current_visit_id']) {
                    $first = $board;

                    break;
                }
            }

            if (null === $first) {
                staff_flash('Belum ada antrean yang sedang dilayani.');

                return redirect('/admin/antrean');
            }

            return redirect('/admin/periksa?visit='.urlencode((string) $first['current_visit_id']));
        }

        if ('dilayani' !== $visit['status']) {
            staff_flash('Panggil antrean terlebih dahulu sebelum mengisi hasil.');

            return redirect('/admin');
        }

        $input = $_SESSION['exam_input'] ?? [];
        unset($_SESSION['exam_input']);

        $error = $_SESSION['exam_error'] ?? null;
        unset($_SESSION['exam_error']);

        return view('admin.periksa', [
            'title' => 'Input Hasil Penanganan',
            'visit' => $visit,
            'input' => $input,
            'error' => $error,
            'role' => (string) $request->attribute('role'),
            'token' => csrf_token(),
        ]);
    },
];
