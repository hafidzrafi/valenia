<?php

declare(strict_types=1);

return [
    'GET' => static function (Request $request): Response|string {
        $draft = $_SESSION['registration_draft'] ?? null;

        if (!is_array($draft)) {
            return redirect('/form');
        }

        return view('ambil-tiket', [
            'title' => 'Ambil Tiket',
            'draft' => $draft,
        ]);
    },

    'POST' => static function (Request $request): Response {
        $key = strtoupper((string) ($_SESSION['heroform'] ?? ''));
        $service = resolve_service($key);
        $input = [
            'identity_number' => (string) request_input('nim_nip', ''),
            'birth_date' => (string) request_input('tanggal_lahir', ''),
            'full_name' => (string) request_input('nama_lengkap', ''),
            'gender' => (string) request_input('jenis_kelamin', ''),
            'contact' => (string) request_input('kontak', ''),
            'complaint' => (string) request_input('keluhan', ''),
        ];

        $validation = validate_registration_input($input);

        if (!$service['ok'] || !$validation['ok']) {
            $_SESSION['registration_old'] = $input;
            $_SESSION['registration_error'] = $validation['error'] ?? $service['error'];

            return redirect('/form');
        }

        $patient = find_or_create_patient($input);

        if (!$patient['ok'] || !is_array($patient['patient'])) {
            $_SESSION['registration_old'] = $input;
            $_SESSION['registration_error'] = $patient['error'];

            return redirect('/form');
        }

        $duplicate = has_active_visit_today((string) $patient['patient']['id'], (int) $service['service']['id']);

        $_SESSION['registration_draft'] = [
            'patient_id' => (string) $patient['patient']['id'],
            'patient_name' => (string) $patient['patient']['full_name'],
            'service_id' => (int) $service['service']['id'],
            'service_name' => (string) $service['service']['name'],
            'complaint' => $input['complaint'],
            'duplicate' => null !== $duplicate,
        ];

        return redirect('/ambil-tiket');
    },
];
