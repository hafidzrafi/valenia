<?php

declare(strict_types=1);

return [
    'POST' => static function (Request $request): Response {
        $patient = portal_patient();

        if (!is_array($patient)) {
            return redirect('/form-riwayat');
        }

        $visitId = (string) request_input('visit_id', '');
        $cancelled = '' !== $visitId && cancel_portal_visit((string) $patient['id'], $visitId);

        $_SESSION['portal_flash'] = $cancelled
            ? 'Antrean berhasil dibatalkan.'
            : 'Antrean tidak dapat dibatalkan.';

        if ('' === $visitId) {
            return redirect('/portal-pasien');
        }

        return redirect('/portal-pasien?visit='.rawurlencode($visitId));
    },
];
