-- 0030_seed_reference_data.sql
-- Reference data required by the public registration form and the doctor panel.
--
-- database/seed.php cannot bootstrap a production database: it refuses to run
-- when APP_ENV=production. Without these rows resolve_service() fails, so
-- GET /form redirects home and POST /form answers "Layanan tidak tersedia."
-- Keeping the master poli (and the placeholder roster) in a migration makes a
-- fresh deploy self-sufficient after `php database/migrate.php up`.
--
-- Idempotent: safe on databases that already ran database/seed.php.

INSERT INTO services (name, queue_prefix) VALUES
    ('Poli Umum', 'PU'),
    ('Poli Gigi', 'PG'),
    ('Konseling/Psikologi', 'PK')
ON CONFLICT (name) DO UPDATE SET queue_prefix = EXCLUDED.queue_prefix;

INSERT INTO doctors (full_name, service_id)
SELECT v.full_name, s.id
FROM (VALUES
    ('dr. Ayu Pramesti', 'Poli Umum'),
    ('drg. Bagas Wardana', 'Poli Gigi'),
    ('Rani Kusuma, M.Psi.', 'Konseling/Psikologi')
) AS v(full_name, service_name)
JOIN services s ON s.name = v.service_name
WHERE NOT EXISTS (
    SELECT 1
    FROM doctors d
    WHERE d.full_name = v.full_name
      AND d.service_id = s.id
);

INSERT INTO doctor_schedules (doctor_id, day_of_week, start_time, end_time)
SELECT d.id, v.day_of_week, v.start_time::time, v.end_time::time
FROM (VALUES
    ('dr. Ayu Pramesti', 1::smallint, '08:00', '15:00'),
    ('dr. Ayu Pramesti', 2::smallint, '08:00', '15:00'),
    ('dr. Ayu Pramesti', 3::smallint, '08:00', '15:00'),
    ('dr. Ayu Pramesti', 4::smallint, '08:00', '15:00'),
    ('dr. Ayu Pramesti', 5::smallint, '08:00', '15:00'),
    ('drg. Bagas Wardana', 1::smallint, '08:00', '15:00'),
    ('drg. Bagas Wardana', 2::smallint, '08:00', '15:00'),
    ('drg. Bagas Wardana', 3::smallint, '08:00', '15:00'),
    ('drg. Bagas Wardana', 4::smallint, '08:00', '15:00'),
    ('drg. Bagas Wardana', 5::smallint, '08:00', '15:00'),
    ('Rani Kusuma, M.Psi.', 2::smallint, '09:00', '13:00'),
    ('Rani Kusuma, M.Psi.', 5::smallint, '09:00', '13:00')
) AS v(full_name, day_of_week, start_time, end_time)
JOIN doctors d ON d.full_name = v.full_name
WHERE NOT EXISTS (
    SELECT 1
    FROM doctor_schedules ds
    WHERE ds.doctor_id = d.id
      AND ds.day_of_week = v.day_of_week
);
