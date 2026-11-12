-- 0027_doctor_attendance_id.sql
-- Give doctor_attendance a UUID row id so audit_change() can record changes.
--
-- The composite primary key (doctor_id, attendance_date) cannot be used as
-- audit_logs.row_id, which is UUID. The unique doctor and date rule is kept.

ALTER TABLE doctor_attendance
    ADD COLUMN id UUID NOT NULL DEFAULT uuidv7();

ALTER TABLE doctor_attendance
    DROP CONSTRAINT doctor_attendance_pkey;

ALTER TABLE doctor_attendance
    ADD PRIMARY KEY (id);

ALTER TABLE doctor_attendance
    ADD CONSTRAINT doctor_attendance_doctor_date_key UNIQUE (doctor_id, attendance_date);

CREATE TRIGGER doctor_attendance_audit
    AFTER INSERT OR UPDATE OR DELETE ON doctor_attendance
    FOR EACH ROW EXECUTE FUNCTION audit_change();
