-- 0004_queue_visits.sql
-- Kunjungan/pendaftaran dan penghitung antrean harian per poli (PRD Bab 6).
-- Nomor antrean diterbitkan lewat atomic upsert pada queue_counters.

CREATE TABLE visits (
    id                UUID PRIMARY KEY DEFAULT uuidv7(),
    patient_id        UUID        NOT NULL REFERENCES patients(id) ON DELETE RESTRICT,
    service_id        SMALLINT    NOT NULL REFERENCES services(id),
    queue_number      INT         NOT NULL,
    queue_date        DATE        NOT NULL DEFAULT CURRENT_DATE,
    status            VARCHAR(12) NOT NULL DEFAULT 'menunggu'
        CHECK (status IN ('menunggu', 'dilayani', 'selesai', 'batal')),
    handled_by_nurse  BOOLEAN     NOT NULL DEFAULT FALSE,
    created_by        UUID        NOT NULL REFERENCES users(id),
    created_at        TIMESTAMPTZ NOT NULL DEFAULT now(),
    UNIQUE (service_id, queue_date, queue_number)
);

CREATE TABLE queue_counters (
    service_id   SMALLINT NOT NULL REFERENCES services(id),
    queue_date   DATE     NOT NULL DEFAULT CURRENT_DATE,
    last_number  INT      NOT NULL DEFAULT 0,
    PRIMARY KEY (service_id, queue_date)
);

-- Papan antrean harian dan riwayat pasien (PRD Bab 6 "Indeks").
CREATE INDEX visits_queue_board_idx      ON visits (queue_date, service_id, status);
CREATE INDEX visits_patient_history_idx  ON visits (patient_id, created_at DESC);
CREATE INDEX visits_patient_id_idx       ON visits (patient_id);
