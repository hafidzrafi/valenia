<?php

declare(strict_types=1);

return [
    'GET' => static function (Request $request): Response|string {
        if ('1' === request_input('download')) {
            $ticket = downloadable_ticket();

            if (null === $ticket) {
                return redirect('/form');
            }

            $png = ticket_qr_png((string) ($ticket['ticket_token'] ?? ''));

            if (null === $png) {
                return new Response('QR tidak tersedia.', 500);
            }

            return new Response($png, 200, [
                'Content-Type' => 'image/png',
                'Content-Disposition' => 'attachment; filename="valenia-tiket.png"',
            ]);
        }

        $ticket = $_SESSION['ticket'] ?? null;

        if (!is_array($ticket)) {
            return redirect('/form');
        }

        return view('muncul-qr', [
            'title' => 'Tiket Antrean',
            'ticket' => $ticket,
        ]);
    },

    'POST' => static function (Request $request): Response {
        $draft = $_SESSION['registration_draft'] ?? null;

        if (!is_array($draft)) {
            return redirect('/form');
        }

        if (($draft['duplicate'] ?? false) === true && '1' !== request_input('confirm_duplicate')) {
            return redirect('/ambil-tiket');
        }

        try {
            $ticket = issue_guest_ticket(
                (string) $draft['patient_id'],
                (int) $draft['service_id'],
                (string) $draft['complaint'],
            );
        } catch (Throwable $exception) {
            app_log('error', 'Penerbitan tiket gagal: '.$exception->getMessage());

            return new Response(view('ambil-tiket', [
                'title' => 'Ambil Tiket',
                'draft' => $draft,
                'error' => 'Gagal menerbitkan nomor antrean. Coba lagi.',
            ]), 500);
        }

        unset($_SESSION['registration_draft']);
        $_SESSION['ticket'] = $ticket;
        unlock_portal((string) $draft['patient_id']);

        return redirect('/muncul-qr');
    },
];
