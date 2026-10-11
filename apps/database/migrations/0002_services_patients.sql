-- 0002_services_patients.sql
-- Master poli dan data identitas pasien (PRD Bab 6).

CREATE TABLE services (
    id         SMALLINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    name       VARCHAR(60) NOT NULL UNIQUE,
    is_active  BOOLEAN     NOT NULL DEFAULT TRUE
);

CREATE TABLE patients (
    id               UUID PRIMARY KEY DEFAULT uuidv7(),
    identity_type    VARCHAR(3)  NOT NULL CHECK (identity_type IN ('NIM', 'NIP')),
    identity_number  VARCHAR(20) NOT NULL UNIQUE,
    full_name        VARCHAR(120) NOT NULL,
    birth_date       DATE        NOT NULL,
    gender           VARCHAR(1)  NOT NULL CHECK (gender IN ('L', 'P')),
    contact          VARCHAR(30),
    created_at       TIMESTAMPTZ NOT NULL DEFAULT now()
);

-- Pencarian nama pasien (prioritas S, PRD Bab 6 "Indeks").
CREATE INDEX patients_full_name_tsv_idx
    ON patients USING GIN (to_tsvector('simple', full_name));
