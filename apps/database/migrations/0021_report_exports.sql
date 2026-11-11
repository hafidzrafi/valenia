-- 0021_report_exports.sql
-- Operational log for generated reports (VALENIA-43).
--
-- Only metadata and an aggregate snapshot are stored. The PDF bytes are
-- derived data and are deliberately never persisted: no object storage, no
-- file volume, no bytea column. Re-rendering from `snapshot` reproduces the
-- report even when the underlying visits are later corrected.
--
-- The snapshot contains aggregate counts only, so this table holds no patient
-- level data.

CREATE TABLE report_exports (
    id            UUID PRIMARY KEY DEFAULT uuidv7(),
    report_type   VARCHAR(40)  NOT NULL DEFAULT 'rekap_bulanan'
                  CHECK (report_type IN ('rekap_bulanan')),
    period_start  DATE         NOT NULL,
    period_end    DATE         NOT NULL,
    service_id    SMALLINT     REFERENCES services(id),
    generated_by  UUID         NOT NULL REFERENCES users(id),
    format        VARCHAR(10)  NOT NULL DEFAULT 'pdf' CHECK (format IN ('pdf')),
    filename      VARCHAR(160) NOT NULL,
    byte_size     INT          NOT NULL DEFAULT 0 CHECK (byte_size >= 0),
    snapshot      JSONB        NOT NULL,
    checksum      CHAR(64)     NOT NULL,
    created_at    TIMESTAMPTZ  NOT NULL DEFAULT now(),
    CHECK (period_end >= period_start)
);

CREATE INDEX report_exports_created_at_idx ON report_exports (created_at DESC);
CREATE INDEX report_exports_period_idx     ON report_exports (period_start, period_end);
