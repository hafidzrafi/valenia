<?php

declare(strict_types=1);

return [
    'GET' => static function (Request $request): string {
        return view('viewdoctor', [
            'title' => 'Jadwal Dokter',
            'boards' => queue_boards(),
            'attendance' => staff_attendance_today(),
        ]);
    },
];
