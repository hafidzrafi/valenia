<?php

declare(strict_types=1);

function list_services_with_count(): array {
    return db()->query(
        'SELECT s.id, s.name, s.queue_prefix, s.is_active,
                COUNT(d.id) AS dokter,
                COALESCE(BOOL_OR(a.doctor_id IS NOT NULL), FALSE) AS recorded,
                COALESCE(BOOL_OR(COALESCE(a.is_present, FALSE)), FALSE) AS hadir
         FROM services s
         LEFT JOIN doctors d
                ON d.service_id = s.id AND d.is_active
         LEFT JOIN doctor_attendance a
                ON a.doctor_id = d.id AND a.attendance_date = app_today()
         GROUP BY s.id, s.name, s.queue_prefix, s.is_active
         ORDER BY s.id'
    )->fetchAll();
}

function list_doctors(): array {
    return db()->query(
        'SELECT d.id, d.full_name, d.str_number, d.is_active,
                s.id AS service_id, s.name AS service_name,
                COALESCE(a.is_present, FALSE) AS is_present,
                (a.doctor_id IS NOT NULL) AS recorded
         FROM doctors d
         JOIN services s ON s.id = d.service_id
         LEFT JOIN doctor_attendance a
                ON a.doctor_id = d.id AND a.attendance_date = app_today()
         ORDER BY d.is_active DESC, s.id, d.full_name'
    )->fetchAll();
}