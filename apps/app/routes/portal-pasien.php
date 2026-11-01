<?php

declare(strict_types=1);

return [
    'GET' => static function (Request $request): Response|string {
        $patient = portal_patient();

        if (!is_array($patient)) {
            return redirect('/form-riwayat');
        }

        $page = max(1, (int) (request_input('page', '1') ?? '1'));
        $visit = request_input('visit');
        $data = portal_data((string) $patient['id'], $visit, $page);
        $flash = $_SESSION['portal_flash'] ?? null;
        unset($_SESSION['portal_flash']);

        $pagination = $data['pagination'];

        if ($page > $pagination['pages']) {
            $query = ['page' => (string) $pagination['pages']];

            if (is_string($visit) && '' !== $visit) {
                $query['visit'] = $visit;
            }

            return redirect('/portal-pasien?'.http_build_query($query));
        }

        return view('portal-pasien', [
            'title' => 'Portal Pasien',
            'patient' => $patient,
            'active' => $data['active'],
            'selected' => $data['selected'],
            'history' => $data['history'],
            'pagination' => $pagination,
            'selectedVisitId' => is_string($visit) ? $visit : null,
            'boards' => queue_boards(),
            'flash' => is_string($flash) ? $flash : null,
        ]);
    },

    'POST' => static function (Request $request): Response {
        $identity = (string) request_input('nim_nip', '');
        $birthDate = (string) request_input('tanggal_lahir', '');
        $patient = find_patient_for_portal($identity, $birthDate);

        if (!is_array($patient)) {
            $_SESSION['portal_old'] = ['nim_nip' => $identity, 'tanggal_lahir' => $birthDate];
            $_SESSION['portal_error'] = 'Data pasien tidak ditemukan atau tidak cocok.';

            return redirect('/form-riwayat');
        }

        unset($_SESSION['portal_old'], $_SESSION['portal_error']);
        unlock_portal((string) $patient['id']);

        return redirect('/portal-pasien');
    },
];
