-- 0027_doctor_attendance_id.down.sql

DROP TRIGGER IF EXISTS doctor_attendance_audit ON doctor_attendance;

ALTER TABLE doctor_attendance
    DROP CONSTRAINT IF EXISTS doctor_attendance_doctor_date_key;

ALTER TABLE doctor_attendance
    DROP CONSTRAINT doctor_attendance_pkey;

ALTER TABLE doctor_attendance
    DROP COLUMN id;

ALTER TABLE doctor_attendance
    ADD PRIMARY KEY (doctor_id, attendance_date);
