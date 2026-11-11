<?php

declare(strict_types=1);

/**
 * /admin: staff dashboard entry point (FR-AUTH-03/04/06).
 *
 * Both admin and perawat land here after login. Account and session management
 * stay admin only (see /admin/pengguna and /admin/sesi).
 */
return [
    '_middleware' => ['role:admin,perawat'],

    'GET' => static function (Request $request): string {
        $visits = staff_today_visits();
        $role = (string) $request->attribute('role');

        $served = null;

        foreach (array_reverse($visits) as $visit) {
            if ('dilayani' === (string) $visit['status']) {
                $served = $visit;

                break;
            }
        }

        if (null === $served) {
            foreach (queue_boards() as $board) {
                if (null !== $board['current_visit_id']) {
                    $served = [
                        'queue_code' => $board['current_queue_code'],
                        'full_name' => null,
                        'identity_number' => null,
                        'service_name' => $board['service_name'],
                        'complaint' => null,
                    ];

                    break;
                }
            }
        }

        return view('admin', [
            'title' => 'Dashboard',
            'user' => (string) $request->attribute('user'),
            'role' => $role,
            'token' => csrf_token(),
            'visits' => $visits,
            'summary' => staff_summary($visits),
            'served' => $served,
            'staffName' => staff_display_name((string) $request->attribute('user_id')),
            'staffRole' => 'admin' === $role ? 'Administrator' : 'Perawat',
            'attendance' => staff_attendance_today(),
            'activity' => staff_queue_activity(),
            'flash' => take_staff_flash(),
        ]);
    },
];
