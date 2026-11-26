<?php

declare(strict_types=1);

/**
 * Weekly schedule page (FR-DOC-04). Perawat may write here; the PRD says
 * read-only and the user overruled it. Every action redirects with a flash.
 */
return [
    '_middleware' => ['role:admin,perawat'],

    'GET' => static function (Request $request): string {
        return view('admin.jadwal', [
            'title' => 'Jadwal Tenaga Medis',
            'role' => (string) $request->attribute('role'),
            'token' => csrf_token(),
            'doctors' => list_weekly_schedule(),
            'days' => SCHEDULE_DAYS,
            'todayDow' => (int) date('N'),
            'flash' => take_staff_flash(),
        ]);
    },

    'POST' => static function (Request $request): Response {
        $actor = (string) $request->attribute('user_id');
        $aksi = (string) (request_input('aksi') ?? '');

        try {
            switch ($aksi) {
                case 'tambah-jadwal':
                    $result = create_schedule_slot(
                        (string) (request_input('doctor_id') ?? ''),
                        (int) (request_input('day_of_week') ?? 0),
                        (string) (request_input('start_time') ?? ''),
                        (string) (request_input('end_time') ?? ''),
                        $actor,
                    );
                    $notice = $result['ok'] ? 'Jadwal ditambahkan.' : (string) $result['error'];

                    break;

                case 'hapus-jadwal':
                    $result = delete_schedule_slot((string) (request_input('id') ?? ''), $actor);
                    $notice = $result['ok'] ? 'Jadwal dihapus.' : (string) $result['error'];

                    break;

                default:
                    $notice = 'Aksi tidak dikenal.';
            }
        } catch (Throwable $exception) {
            app_log('error', 'Jadwal tenaga medis gagal: '.$exception->getMessage());

            $notice = 'Terjadi kesalahan. Coba lagi.';
        }

        staff_flash($notice);

        return redirect('/admin/jadwal');
    },
];
