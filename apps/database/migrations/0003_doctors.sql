-- 0003_doctors.sql
-- Master dokter, jadwal mingguan, dan kehadiran harian (PRD Bab 6).

CREATE TABLE doctors (
    id          UUID PRIMARY KEY DEFAULT uuidv7(),
    full_name   VARCHAR(120) NOT NULL,
    service_id  SMALLINT     NOT NULL REFERENCES services(id),
    is_active   BOOLEAN      NOT NULL DEFAULT TRUE,
    created_at  TIMESTAMPTZ  NOT NULL DEFAULT now()
);

CREATE TABLE doctor_schedules (
    id           UUID PRIMARY KEY DEFAULT uuidv7(),
    doctor_id    UUID     NOT NULL REFERENCES doctors(id) ON DELETE CASCADE,
    day_of_week  SMALLINT NOT NULL CHECK (day_of_week BETWEEN 1 AND 7), -- ISO: 1=Senin .. 7=Minggu
    start_time   TIME     NOT NULL,
    end_time     TIME     NOT NULL,
    CHECK (end_time > start_time)
);

CREATE TABLE doctor_attendance (
    doctor_id        UUID     NOT NULL REFERENCES doctors(id) ON DELETE CASCADE,
    attendance_date  DATE     NOT NULL DEFAULT CURRENT_DATE,
    is_present       BOOLEAN  NOT NULL,
    note             TEXT,
    recorded_by      UUID     REFERENCES users(id),
    created_at       TIMESTAMPTZ NOT NULL DEFAULT now(),
    PRIMARY KEY (doctor_id, attendance_date)
);

CREATE INDEX doctors_service_id_idx        ON doctors (service_id);
CREATE INDEX doctor_schedules_doctor_idx   ON doctor_schedules (doctor_id);
