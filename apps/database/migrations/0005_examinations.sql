-- 0005_examinations.sql
-- Rekam pelayanan dan hasil pemeriksaan, satu rekam per kunjungan (PRD Bab 6).

CREATE TABLE examinations (
    id             UUID PRIMARY KEY DEFAULT uuidv7(),
    visit_id       UUID        NOT NULL UNIQUE REFERENCES visits(id) ON DELETE RESTRICT,
    examiner_role  VARCHAR(10) NOT NULL CHECK (examiner_role IN ('dokter', 'perawat')),
    doctor_id      UUID        REFERENCES doctors(id) ON DELETE RESTRICT,
    recorded_by    UUID        NOT NULL REFERENCES users(id),
    complaint      TEXT,
    findings       TEXT,
    diagnosis      TEXT,
    treatment      TEXT,
    created_at     TIMESTAMPTZ NOT NULL DEFAULT now(),
    updated_at     TIMESTAMPTZ NOT NULL DEFAULT now(),
    CHECK ((examiner_role = 'dokter' AND doctor_id IS NOT NULL)
        OR (examiner_role = 'perawat' AND doctor_id IS NULL))
);
