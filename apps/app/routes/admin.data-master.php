<?php

declare(strict_types=1);

/**
 * Master data for poli and medical staff. The tab lives in the query string;
 * every action redirects with a flash instead of re-rendering 422 in place.
 */
return [
    '_middleware' => ['role:admin'],

    'GET' => static function (Request $request): string {
        $tab = 'tenaga-medis' === request_input('tab') ? 'tenaga-medis' : 'poli';

        return view('admin.data-master', [
            'title' => 'Data Master',
            'role' => (string) $request->attribute('role'),
            'tab' => $tab,
            'services' => list_services_with_counts(),
            'medics' => list_medics(),
            'flash' => take_staff_flash(),
        ]);
    },

    'POST' => static function (Request $request): Response {
        $actor = (string) $request->attribute('user_id');
        $aksi = (string) (request_input('aksi') ?? '');
        $tab = 'tenaga-medis' === request_input('tab') ? 'tenaga-medis' : 'poli';

        $done = static function (string $notice) use ($tab): Response {
            staff_flash($notice);

            return redirect('/admin/data-master?tab='.$tab);
        };

        $notice = '';
        $result = ['ok' => false, 'error' => 'Aksi tidak dikenal.'];

        try {
            switch ($aksi) {
                case 'ubah-poli':
                    $notice = 'Nama poli diperbarui.';
                    $result = update_service(
                        (int) (request_input('id') ?? 0),
                        (string) (request_input('nama') ?? ''),
                    );

                    break;

                case 'aktif-poli':
                case 'nonaktif-poli':
                    $active = 'aktif-poli' === $aksi;
                    $notice = $active ? 'Poli diaktifkan.' : 'Poli dinonaktifkan.';
                    $result = set_service_active((int) (request_input('id') ?? 0), $active);

                    break;

                case 'tambah-medis':
                    $notice = 'Tenaga medis ditambahkan.';
                    $result = create_medic(
                        (string) (request_input('nama') ?? ''),
                        (string) (request_input('str_number') ?? ''),
                        (int) (request_input('service_id') ?? 0),
                        $actor,
                    );

                    break;

                case 'ubah-medis':
                    $notice = 'Data tenaga medis diperbarui.';
                    $result = update_medic(
                        (string) (request_input('id') ?? ''),
                        (string) (request_input('nama') ?? ''),
                        (string) (request_input('str_number') ?? ''),
                        (int) (request_input('service_id') ?? 0),
                        $actor,
                    );

                    break;

                case 'aktif-medis':
                case 'nonaktif-medis':
                    $active = 'aktif-medis' === $aksi;
                    $notice = $active ? 'Tenaga medis diaktifkan.' : 'Tenaga medis dinonaktifkan.';
                    $result = set_medic_active((string) (request_input('id') ?? ''), $active, $actor);

                    break;
            }
        } catch (Throwable $exception) {
            app_log('error', 'Data master gagal: '.$exception->getMessage());

            return $done('Terjadi kesalahan. Coba lagi.');
        }

        return $done($result['ok'] ? $notice : (string) $result['error']);
    },
];
