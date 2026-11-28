<?php

declare(strict_types=1);

return [
    'GET' => static function (Request $request): string {
        $schedules = db()->query(
            'SELECT DISTINCT ON (s.id)
                    s.id AS service_id,
                    s.name AS service_name,
                    d.full_name,
                    sc.start_time,
                    sc.end_time
             FROM services s
             JOIN doctors d ON d.service_id = s.id AND d.is_active
             LEFT JOIN doctor_schedules sc ON sc.doctor_id = d.id
             ORDER BY s.id, sc.day_of_week, sc.start_time'
        )->fetchAll();

        return view('viewdoctor', [
            'title'      => 'Jadwal Dokter',
            'boards'     => queue_boards(),
            'attendance' => staff_attendance_today(),
            'schedules'  => $schedules,
        ]);
    },
];
