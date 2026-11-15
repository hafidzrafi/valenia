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

        $duplicateError = 'Pasien sudah memiliki antrean aktif untuk poli ini hari ini. Selesaikan atau batalkan antrean tersebut terlebih dahulu.';

        if (($draft['duplicate'] ?? false) === true) {
            return new Response(view('ambil-tiket', [
                'title' => 'Ambil Tiket',
                'draft' => $draft,
                'error' => $duplicateError,
            ]), 422);
        }

        try {
            $ticket = issue_guest_ticket(
                (string) $draft['patient_id'],
                (int) $draft['service_id'],
                (string) $draft['complaint'],
            );
        } catch (PDOException $exception) {
            if ('23505' === $exception->getCode()
                && str_contains($exception->getMessage(), 'visits_one_active_per_patient_service_day')) {
                app_log('warning', 'Penerbitan tiket ditolak: antrean aktif sudah ada');

                return new Response(view('ambil-tiket', [
                    'title' => 'Ambil Tiket',
                    'draft' => $draft,
                    'error' => $duplicateError,
                ]), 422);
            }

            app_log('error', 'Penerbitan tiket gagal: '.$exception->getMessage());

            return new Response(view('ambil-tiket', [
                'title' => 'Ambil Tiket',
                'draft' => $draft,
                'error' => 'Gagal menerbitkan nomor antrean. Coba lagi.',
            ]), 500);
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
